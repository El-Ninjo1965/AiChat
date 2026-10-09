<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function out($data,int $code=200){http_response_code($code);echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
function body(){return json_decode(rawBody(),true) ?: [];}
function rawBody(){static $raw=null;if($raw===null)$raw=(string)file_get_contents('php://input');return $raw;}
// Game state must keep JSON objects as objects: decoding to arrays turns empty {} score maps into [] and the client then silently loses scores.
function bodyState(){$b=json_decode(rawBody());return (is_object($b)&&isset($b->state)&&(is_object($b->state)||is_array($b->state)))?$b->state:new stdClass();}
function stateScoresPreserved($old,$new):bool{$op=is_object($old)&&isset($old->players)&&is_array($old->players)?$old->players:[];$np=is_object($new)&&isset($new->players)&&is_array($new->players)?$new->players:[];if(!$op)return true;if(count($op)!==count($np))return false;foreach($op as $i=>$p){$os=is_object($p)&&isset($p->scores)?(array)$p->scores:[];$ns=is_object($np[$i])&&isset($np[$i]->scores)?(array)$np[$i]->scores:[];foreach($os as $k=>$v){if(!array_key_exists($k,$ns))return false;if($k==='five'){if((int)$ns[$k]<(int)$v)return false;}elseif((string)$ns[$k]!==(string)$v)return false;}}return true;}
// Seats are claimed only through action=join (row-locked); a plain update may never add, drop or reassign a seat's clientId, so two clients cannot both take the last free seat.
function stateSeatsPreserved($old,$new):bool{$op=is_object($old)&&isset($old->players)&&is_array($old->players)?$old->players:[];$np=is_object($new)&&isset($new->players)&&is_array($new->players)?$new->players:[];if(!$op)return true;if(count($op)!==count($np))return false;foreach($op as $i=>$p){$a=is_object($p)&&isset($p->clientId)?(string)$p->clientId:'';$b=is_object($np[$i])&&isset($np[$i]->clientId)?(string)$np[$i]->clientId:'';if($a!==$b||seatName($p,$i)!==seatName($np[$i],$i)||seatAccount($p)!==seatAccount($np[$i]))return false;}return true;}
function players($st):array{return is_object($st)&&isset($st->players)&&is_array($st->players)?$st->players:[];}
// Seat types: account (logged-in player, bound server-side), guest (remote device without account), local (named guest on the host's device),
// computer, invite (pending invitation of an account) and open (joinable by code). Legacy states without `type` are mapped on the fly.
function seatType($p):string{if(!is_object($p))return 'open';if(isset($p->type))return (string)$p->type;if(($p->nameMode??'')==='computer')return 'computer';return isset($p->clientId)&&(string)$p->clientId!==''?'guest':'open';}
function seatName($p,int $i):string{$m=is_object($p)?(string)($p->nameMode??'auto'):'auto';if($m==='pat')return 'Pat';if($m==='l')return 'L';if($m==='computer')return 'Computer';$t=seatType($p);if($t!=='open'&&($m==='custom'||$m==='account'||in_array($t,['account','guest','local','invite'],true))&&isset($p->name)&&(string)$p->name!=='')return (string)$p->name;return 'Player '.($i+1);}
// `account` is only ever set by the server from a valid bearer token: it is what makes a seat "L", "Pat" or any other registered player.
function seatAccount($p):string{return is_object($p)&&isset($p->account)?(string)$p->account:'';}
function seatFree($p):bool{return is_object($p)&&seatType($p)==='open';}
// Seats given up with action=leave (End session / logout) keep their scores but no longer bind the account; human = account or remote guest.
function seatLeft($p):bool{return is_object($p)&&!empty($p->left);}
function humanSeat($p):bool{return in_array(seatType($p),['account','guest'],true);}
function allHumansLeft($st):bool{foreach(players($st) as $p)if(humanSeat($p)&&!seatLeft($p))return false;return true;}
function seatInfo($st,string $cid='',string $acct=''):array{$ps=is_object($st)&&isset($st->players)&&is_array($st->players)?$st->players:[];$taken=0;$mine=false;foreach($ps as $p){if(!seatFree($p))$taken++;if($cid!==''&&is_object($p)&&isset($p->clientId)&&(string)$p->clientId===$cid&&(seatType($p)!=='account'||$acct!==''))$mine=true;if($acct!==''&&seatType($p)==='account'&&strcasecmp(seatAccount($p),$acct)===0)$mine=true;}return ['seats_total'=>count($ps),'seats_taken'=>$taken,'full'=>$taken>=count($ps),'mine'=>$mine];}
// Who may act for seat $i: account seats need the bound device AND that account's bearer token (a clientId alone never authenticates an account);
// guest seats need their device; computer and local-guest seats are played by whoever holds the host seat.
function holdsSeat($st,int $i,string $cid,?string $acct):bool{$ps=players($st);if($cid===''||!isset($ps[$i])||!is_object($ps[$i]))return false;$p=$ps[$i];$t=seatType($p);if($t==='computer'||$t==='local'){$h=isset($st->hostSeat)?(int)$st->hostSeat:-1;return $h!==$i&&holdsSeat($st,$h,$cid,$acct);}if((string)($p->clientId??'')!==$cid||seatLeft($p))return false;if($t==='account')return $acct!==null&&strcasecmp(seatAccount($p),$acct)===0;return $t==='guest';}
function holdsAnySeat($st,string $cid,?string $acct):bool{foreach(players($st) as $i=>$p)if(holdsSeat($st,$i,$cid,$acct))return true;return false;}
// Everything that defines who sits where; frozen once the game is started.
function rosterKey($st):string{$r=[];foreach(players($st) as $i=>$p){$t=seatType($p);$r[]=[$t,seatAccount($p),seatName($p,$i),$t==='computer'?(string)($p->cpuLevel??''):'',is_object($p)?(string)($p->clientId??''):''];}return json_encode([$r,(int)($st->hostSeat??-1),!empty($st->locked),(int)($st->model??0),!empty($st->duel)]);}
function progressKey($p):string{$s=is_object($p)&&isset($p->scores)?(array)$p->scores:[];ksort($s);return json_encode([array_map(fn($v)=>is_scalar($v)?(string)$v:'',$s),(int)($p->entries??0),(int)($p->fiveCount??0),!empty($p->pendingZero)]);}
// A seat's clientId is its device credential: other seats' ids are never sent out (masked as "*" = taken) and are restored from the stored state on update.
function publicState($st,string $cid=''){$s=json_decode(json_encode($st));foreach(players($s) as $p){if(!is_object($p))continue;if(isset($p->clientId)&&(string)$p->clientId!==''&&(string)$p->clientId!==$cid)$p->clientId='*';if(seatType($p)==='invite')$p->inviteExpired=(int)($p->inviteExpires??0)<time();}return $s;}
function restoreSeatSecrets($cur,$new){$op=players($cur);foreach(players($new) as $i=>$p){if(!is_object($p)||!isset($op[$i])||!is_object($op[$i]))continue;$stored=(string)($op[$i]->clientId??'');if(isset($p->clientId)&&(string)$p->clientId==='*'){if($stored!=='')$p->clientId=$stored;else unset($p->clientId);}unset($p->inviteExpired);if(seatLeft($op[$i]))$p->left=true;else unset($p->left);}return $new;}
function validClientId($v):string{$v=(string)$v;return preg_match('/^[a-f0-9]{16,64}$/',$v)?$v:'';}
function code(){return strtoupper(substr(preg_replace('/[^A-Z0-9]/','',$_GET['code']??''),0,8));}
function cfg(){static $cfg=null;if($cfg===null)$cfg=require __DIR__.'/config.php';return $cfg;}
function hostToken(string $c):string{$cfg=cfg();return hash_hmac('sha256',$c,(string)$cfg['pass']);}
function db(){
  $cfg=cfg();
  return new PDO('mysql:host='.$cfg['host'].';dbname='.$cfg['name'].';charset=utf8mb4',$cfg['user'],$cfg['pass'],[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
}
$pdo=db();$method=$_SERVER['REQUEST_METHOD'];$action=$_GET['action']??'';
if((int)($_SERVER['CONTENT_LENGTH']??0)>65536)out(['ok'=>false,'error'=>'payload_too_large'],413);
function validUser($v){$v=trim((string)$v);if($v===''||mb_strlen($v)>40||in_array(strtolower($v),['guest','computer'],true)||!preg_match('/^[\\p{L}\\p{N} _.-]+$/u',$v))return '';return $v;}
function displayName($v):string{$v=trim(preg_replace('/\s+/u',' ',preg_replace('/[\x00-\x1F\x7F<>]/u','',(string)$v)));return mb_strlen($v)>40?'':$v;}
// Reserved/registered identities (L, Pat, Computer and every registered account) can only be used by logging in, never by typing or picking the name.
function reservedName(PDO $pdo,string $n):bool{$n=displayName($n);if(in_array(mb_strtolower($n),['l','pat','computer'],true))return true;$q=$pdo->prepare('SELECT 1 FROM dice_users WHERE LOWER(username)=LOWER(?)');$q->execute([$n]);return (bool)$q->fetch();}
function identityRefused(string $n){out(['ok'=>false,'error'=>'identity_reserved','name'=>$n],403);}
const PRESENCE_TTL=70;const INVITE_TTL=600;const BUSY_TTL=900;
// Presence/invitation tables are created on demand (the deploy only uploads files); never call inside a transaction (DDL commits implicitly).
function socialTables(PDO $pdo){static $done=false;if($done)return;$done=true;
  $pdo->exec("CREATE TABLE IF NOT EXISTS dice_presence (user_id INT UNSIGNED PRIMARY KEY, last_seen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX(last_seen)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  $pdo->exec("CREATE TABLE IF NOT EXISTS dice_invites (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, game_code VARCHAR(8) NOT NULL, user_id INT UNSIGNED NOT NULL, host_name VARCHAR(100) NOT NULL DEFAULT '', status ENUM('pending','accepted','declined','cancelled','expired') NOT NULL DEFAULT 'pending', created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, expires_at DATETIME NOT NULL, UNIQUE KEY game_user (game_code,user_id), INDEX(user_id,status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  $pdo->exec("CREATE TABLE IF NOT EXISTS dice_duel_pairs (user_lo INT UNSIGNED NOT NULL, user_hi INT UNSIGNED NOT NULL, game_code VARCHAR(8) NOT NULL, updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY(user_lo,user_hi)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");}
function userId(PDO $pdo,string $name){$q=$pdo->prepare('SELECT id,username FROM dice_users WHERE LOWER(username)=LOWER(?)');$q->execute([$name]);return $q->fetch()?:null;}
function isPresent(PDO $pdo,int $uid):bool{$q=$pdo->prepare('SELECT 1 FROM dice_presence WHERE user_id=? AND last_seen>NOW()-INTERVAL '.PRESENCE_TTL.' SECOND');$q->execute([$uid]);return (bool)$q->fetch();}
function inviteStatus(PDO $pdo,string $code,string $account,string $status){$u=userId($pdo,$account);if($u)$pdo->prepare('UPDATE dice_invites SET status=? WHERE game_code=? AND user_id=?')->execute([$status,$code,$u['id']]);}
function freshSeat(object $p){$p->scores=new stdClass();$p->entries=0;$p->fiveCount=0;$p->pendingZero=false;return $p;}
const CATEGORIES=['ones','twos','threes','fours','fives','sixes','three','four','full','small','large','five','chance'];
function gameOver($st):bool{$ps=players($st);if(!$ps)return false;foreach($ps as $p){if(!is_object($p)||!empty($p->pendingZero))return false;$s=isset($p->scores)?(array)$p->scores:[];foreach(CATEGORIES as $k)if(!array_key_exists($k,$s))return false;}return true;}
// Ends a game for everyone (End game, last player left): no longer active, its pending invitations withdrawn. Caller holds the game row lock.
function endGameRow(PDO $pdo,string $c){$pdo->prepare("UPDATE dice_games SET status='finished',updated_at=NOW() WHERE game_code=? AND status='active'")->execute([$c]);$pdo->prepare("UPDATE dice_invites SET status='cancelled' WHERE game_code=? AND status='pending'")->execute([$c]);}
// End game: an admin, the holder of the host token or the account sitting in the host seat.
function mayClose($st,$u,string $c,string $token):bool{if($u&&$u['role']==='admin')return true;if($token!==''&&hash_equals(hostToken($c),$token))return true;$h=players($st)[(int)($st->hostSeat??0)]??null;return (bool)$u&&seatType($h)==='account'&&strcasecmp(seatAccount($h),$u['username'])===0;}
// Busy = holds a seat it has not left in a started, unfinished, active online game. Left/closed/finished games free the account at once;
// BUSY_TTL is only the fallback for abandoned games (no move for 15 minutes). Presence alone never means "available".
function busyAccounts(PDO $pdo,string $except=''):array{$b=[];foreach($pdo->query("SELECT game_code,state_json FROM dice_games WHERE status='active' AND updated_at>NOW()-INTERVAL ".BUSY_TTL." SECOND ORDER BY updated_at DESC LIMIT 200")->fetchAll() as $r){if($r['game_code']===$except)continue;$st=json_decode($r['state_json']);if(!is_object($st)||empty($st->locked)||gameOver($st))continue;foreach(players($st) as $p)if(seatType($p)==='account'&&seatAccount($p)!==''&&!seatLeft($p))$b[mb_strtolower(seatAccount($p))]=1;}return $b;}
// Direct 1-vs-1: a private model-2 game (state.duel) with the inviting account and one invite seat; it is hidden from the public list,
// has no open seats and accepts no seat changes. Accepting starts it at once with exactly these two accounts.
function isDuel($st):bool{return is_object($st)&&!empty($st->duel);}
// 1-vs-1 starter rotation: consecutive games of the same two accounts alternate the opening player (A, B, A, ...).
// A game counts once anybody has scored in it; an untouched game (accepted or reset, then ended without a score) keeps its opener.
function gamePlayed($st):bool{foreach(players($st) as $p)if(is_object($p)&&((int)($p->entries??0)>0||(isset($p->scores)&&count((array)$p->scores))))return true;return false;}
function nextDuelStart($st):int{$s=(int)($st->start??0)===1?1:0;return gamePlayed($st)?1-$s:$s;}
// Opening seat of a newly accepted duel from the pair's previous duel game (dice_duel_pairs, locked last: users → game → pair); first game: the inviter (seat 0).
function duelOpener(PDO $pdo,int $a,int $b,array $accounts,string $code):int{$lo=min($a,$b);$hi=max($a,$b);$seat=0;
  $q=$pdo->prepare('SELECT game_code FROM dice_duel_pairs WHERE user_lo=? AND user_hi=? FOR UPDATE');$q->execute([$lo,$hi]);$prev=(string)($q->fetchColumn()?:'');
  if($prev!==''&&$prev!==$code){$r=$pdo->prepare('SELECT state_json FROM dice_games WHERE game_code=?');$r->execute([$prev]);$ps=json_decode((string)($r->fetchColumn()?:''));
    if(is_object($ps)){$who=seatAccount(players($ps)[nextDuelStart($ps)]??null);foreach($accounts as $i=>$n)if($who!==''&&strcasecmp((string)$n,$who)===0)$seat=(int)$i;}}
  $pdo->prepare('INSERT INTO dice_duel_pairs(user_lo,user_hi,game_code) VALUES(?,?,?) ON DUPLICATE KEY UPDATE game_code=VALUES(game_code)')->execute([$lo,$hi,$code]);
  return $seat;}
// Locks both accounts' user rows in id order, so every invite / cross-invite / accept of a pair is serialized (and cannot deadlock).
function lockPair(PDO $pdo,int $a,int $b){$q=$pdo->prepare('SELECT id FROM dice_users WHERE id IN (?,?) ORDER BY id FOR UPDATE');$q->execute([min($a,$b),max($a,$b)]);$q->fetchAll();}
// Ends a not yet accepted 1-vs-1 invitation (lock order: game row, then invitation); the game row is closed only if the invitation really was still pending (never a started game).
function closeDuel(PDO $pdo,string $code,string $status):bool{$pdo->prepare('SELECT 1 FROM dice_games WHERE game_code=? FOR UPDATE')->execute([$code]);$q=$pdo->prepare("UPDATE dice_invites SET status=? WHERE game_code=? AND status='pending'");$q->execute([$status,$code]);if(!$q->rowCount())return false;$pdo->prepare("UPDATE dice_games SET status='finished',updated_at=NOW() WHERE game_code=? AND status='active'")->execute([$code]);return true;}
// Pending 1-vs-1 invitations sent by account $from (optionally only those to user $toId); expired ones are closed on the way.
function pendingDuels(PDO $pdo,string $from,?int $toId=null):array{$q=$pdo->prepare("SELECT i.game_code,i.user_id,i.expires_at>NOW() live,g.state_json FROM dice_invites i JOIN dice_games g ON g.game_code=i.game_code AND g.status='active' WHERE i.status='pending' AND i.host_name=?".($toId!==null?' AND i.user_id=?':''));$q->execute($toId!==null?[$from,$toId]:[$from]);$out=[];foreach($q->fetchAll() as $r){if(!isDuel(json_decode($r['state_json'])))continue;if(!(int)$r['live']){closeDuel($pdo,$r['game_code'],'expired');continue;}$out[]=$r;}return $out;}
// Logout / End session: pending 1-vs-1 invitations from and to this account are withdrawn; accepted (running) games are never touched.
function cancelDuelsOf(PDO $pdo,array $u,string $except=''){foreach(pendingDuels($pdo,$u['username']) as $r)if($r['game_code']!==$except)closeDuel($pdo,$r['game_code'],'cancelled');$q=$pdo->prepare("SELECT i.game_code,g.state_json FROM dice_invites i JOIN dice_games g ON g.game_code=i.game_code AND g.status='active' WHERE i.user_id=? AND i.status='pending' AND i.game_code<>?");$q->execute([$u['id'],$except]);foreach($q->fetchAll() as $r)if(isDuel(json_decode($r['state_json'])))closeDuel($pdo,$r['game_code'],'cancelled');}
function newCode(PDO $pdo):string{do{$c=strtoupper(substr(bin2hex(random_bytes(4)),0,6));$q=$pdo->prepare('SELECT 1 FROM dice_games WHERE game_code=?');$q->execute([$c]);}while($q->fetch());return $c;}
function freshDice(){return (object)['open'=>true,'rolls'=>0,'dice'=>[1,1,1,1,1],'held'=>[false,false,false,false,false]];}
// New online games: the host seat is bound to the authenticated account (or a non-reserved guest name). Other seats can only be computers,
// named local guests of the host device or open seats; accounts join exclusively through invitation (or by code into an open seat, logged in).
function prepareSeats(PDO $pdo,$st,$u):int{$bad=fn()=>out(['ok'=>false,'error'=>'invalid_state'],400);$ps=players($st);if(count($ps)<1||count($ps)>6)$bad();$host=-1;
  foreach($ps as $i=>$p){if(!is_object($p))$bad();if(isset($p->clientId)&&(string)$p->clientId!==''){if($host>=0||!validClientId($p->clientId)||($p->nameMode??'')==='computer')$bad();$host=$i;}}
  if($host<0)$bad();$names=[];
  foreach($ps as $i=>$p){$m=(string)($p->nameMode??'auto');foreach(['account','type','inviteExpires','inviteExpired'] as $k)unset($p->$k);freshSeat($p);if($i===$host)continue;unset($p->clientId);
    if($m==='computer'){$p->type='computer';$p->name='';if(!in_array($p->cpuLevel??'',['beginner','normal','expert'],true))$p->cpuLevel='normal';}
    elseif($m==='custom'&&displayName($p->name??'')!==''){$nm=displayName($p->name);if(reservedName($pdo,$nm))identityRefused($nm);$p->type='local';$p->name=$nm;}
    elseif($m==='auto'||$m==='custom'){$p->type='open';$p->nameMode='auto';$p->name='';}
    else identityRefused(seatName($p,$i));}
  $h=$ps[$host];$m=(string)($h->nameMode??'auto');
  if($u){$h->type='account';$h->nameMode='account';$h->name=$u['username'];$h->account=$u['username'];}
  elseif($m==='custom'&&displayName($h->name??'')!==''){$nm=displayName($h->name);if(reservedName($pdo,$nm))identityRefused($nm);$h->type='guest';$h->name=$nm;}
  elseif($m==='auto'||$m==='custom'){$h->type='guest';$h->nameMode='auto';$h->name='';}
  else identityRefused(seatName($h,$host));
  foreach($ps as $i=>$p){if(seatType($p)==='open')continue;$k=mb_strtolower(seatName($p,$i));if($k!=='computer'&&isset($names[$k]))out(['ok'=>false,'error'=>'duplicate_name','name'=>seatName($p,$i)],409);$names[$k]=1;}
  unset($st->duel);$st->model=2;$st->locked=false;$st->hostSeat=$host;$st->turn=0;$st->start=0;$st->started=false;$st->liveDice=(object)['open'=>true,'rolls'=>0,'dice'=>[1,1,1,1,1],'held'=>[false,false,false,false,false]];
  return $host;}
function lockGame(PDO $pdo,string $c){$q=$pdo->prepare("SELECT game_code,host_name,state_json,dice_mode,version,updated_at FROM dice_games WHERE game_code=? AND status='active' FOR UPDATE");$q->execute([$c]);$g=$q->fetch();if(!$g){$pdo->rollBack();out(['ok'=>false,'error'=>'not_found'],404);}$g['st']=json_decode($g['state_json']);return $g;}
function saveGame(PDO $pdo,array $g,$st):int{$next=(int)$g['version']+1;$pdo->prepare("UPDATE dice_games SET state_json=?,version=?,player_count=?,updated_at=NOW() WHERE game_code=?")->execute([json_encode($st,JSON_UNESCAPED_UNICODE),$next,max(1,count(players($st))),$g['game_code']]);return $next;}
function gameOut(array $g,$st,int $version,string $cid,array $extra=[],int $code=200){out(['ok'=>$code<300,'game'=>['game_code'=>$g['game_code'],'dice_mode'=>$g['dice_mode'],'version'=>$version,'state'=>publicState($st,$cid),'now'=>time()]]+$extra,$code);}
// 1-vs-1 accept; the caller has begun the transaction and holds lockPair(host, invitee). Idempotent: a repeated accept only rebinds the device.
function duelAcceptLocked(PDO $pdo,string $c,array $u,string $cid){$g=lockGame($pdo,$c);$st=$g['st'];$acct=$u['username'];$h=(int)($st->hostSeat??0);$slot=-1;
  if(isDuel($st))foreach(players($st) as $i=>$p)if($i!==$h&&in_array(seatType($p),['invite','account'],true)&&strcasecmp(seatAccount($p),$acct)===0)$slot=$i;
  if($slot<0){$pdo->rollBack();out(['ok'=>false,'error'=>'no_invite'],404);}
  $p=$st->players[$slot];
  if(seatType($p)==='account'){$v=(int)$g['version'];if((string)($p->clientId??'')!==$cid||seatLeft($p)){$p->clientId=$cid;unset($p->left);$v=saveGame($pdo,$g,$st);}$pdo->commit();gameOut($g,$st,$v,$cid,['seat'=>$slot,'duel'=>true]);}
  $q=$pdo->prepare('SELECT status FROM dice_invites WHERE game_code=? AND user_id=? FOR UPDATE');$q->execute([$c,$u['id']]);$s=(string)($q->fetchColumn()?:'');
  if($s!=='pending'){$pdo->rollBack();out(['ok'=>false,'error'=>in_array($s,['declined','cancelled','expired'],true)?'invite_'.$s:'no_invite'],410);}
  if((int)($p->inviteExpires??0)<time()){closeDuel($pdo,$c,'expired');$pdo->commit();out(['ok'=>false,'error'=>'invite_expired'],410);}
  $host=seatAccount($st->players[$h]??null);
  $busy=busyAccounts($pdo,$c);if(isset($busy[mb_strtolower($host)])){closeDuel($pdo,$c,'cancelled');$pdo->commit();out(['ok'=>false,'error'=>'busy'],409);}
  if(isset($busy[mb_strtolower($acct)])){$pdo->rollBack();out(['ok'=>false,'error'=>'busy_self'],409);}
  $p->type='account';$p->nameMode='account';$p->name=$acct;$p->account=$acct;$p->clientId=$cid;unset($p->inviteExpires);
  $st->players=[freshSeat($st->players[$h]),freshSeat($p)];$st->hostSeat=0;$st->locked=true;$st->turn=0;$st->start=0;$st->started=false;$st->liveDice=freshDice();
  $pdo->prepare("UPDATE dice_invites SET status='accepted' WHERE game_code=? AND user_id=?")->execute([$c,$u['id']]);
  // Accepting binds this account to the game: its other pending 1-vs-1 invitations (sent and received) are withdrawn, so it never ends up in two duels.
  cancelDuelsOf($pdo,$u,$c);
  $hu=userId($pdo,$host);if($hu){$o=duelOpener($pdo,(int)$hu['id'],(int)$u['id'],[$host,$acct],$c);$st->turn=$o;$st->start=$o;}
  $next=saveGame($pdo,$g,$st);$pdo->commit();gameOut($g,$st,$next,$cid,['seat'=>1,'duel'=>true]);}
// Accept / decline of a 1-vs-1 invitation in its own transaction (pair lock first, then the game row).
function duelReply(PDO $pdo,string $c,array $u,string $cid,bool $accept,string $host){$hu=userId($pdo,$host);if(!$hu)out(['ok'=>false,'error'=>'no_invite'],404);
  $pdo->beginTransaction();lockPair($pdo,(int)$hu['id'],(int)$u['id']);
  if($accept)duelAcceptLocked($pdo,$c,$u,$cid);
  $g=lockGame($pdo,$c);$st=$g['st'];$h=(int)($st->hostSeat??0);
  foreach(players($st) as $i=>$p)if($i!==$h&&strcasecmp(seatAccount($p),$u['username'])===0){if(seatType($p)==='account'){$pdo->rollBack();out(['ok'=>false,'error'=>'already_accepted'],409);}closeDuel($pdo,$c,'declined');$pdo->commit();out(['ok'=>true,'declined'=>true]);}
  $pdo->rollBack();out(['ok'=>false,'error'=>'no_invite'],404);}
function clientKey(){return substr(hash('sha256',(string)($_SERVER['REMOTE_ADDR']??'unknown')),0,32);}
// Fixed-window counter computed entirely by MySQL (window start and "now" from the same clock, so PHP/MySQL time-zone differences can never
// freeze a window), one atomic upsert per key. Over the limit: 429 + Retry-After.
function rateHit(PDO $pdo,string $key,int $max,int $seconds){$pdo->prepare("INSERT INTO dice_rate_limits(rate_key,attempts,window_start) VALUES(?,1,NOW()) ON DUPLICATE KEY UPDATE attempts=IF(window_start<=NOW()-INTERVAL $seconds SECOND,1,attempts+1),window_start=IF(window_start<=NOW()-INTERVAL $seconds SECOND,NOW(),window_start)")->execute([$key]);$q=$pdo->prepare("SELECT attempts,GREATEST(1,$seconds-TIMESTAMPDIFF(SECOND,window_start,NOW())) wait FROM dice_rate_limits WHERE rate_key=?");$q->execute([$key]);$r=$q->fetch();if($r&&(int)$r['attempts']>$max){$w=max(1,min($seconds,(int)$r['wait']));header('Retry-After: '.$w);out(['ok'=>false,'error'=>'rate_limited','retry_after'=>$w],429);}}
// Separate buckets so live sync can never starve recovery actions: [per actor, per IP ceiling, window seconds]. The actor is the account
// (bearer) or the device (client_id), so several devices behind one NAT/IP do not share a budget; the per-IP ceiling keeps the abuse
// protection against rotating client ids.
const RATE_LIMITS=['sync'=>[240,1200,60],'join'=>[40,200,60],'life'=>[40,200,60],'social'=>[60,300,60],'result'=>[20,100,60],'login'=>[8,40,900],'register'=>[8,8,900]];
function rate(PDO $pdo,string $bucket,string $actor=''){[$max,$ipMax,$sec]=RATE_LIMITS[$bucket];rateHit($pdo,$bucket.':ip:'.clientKey(),$ipMax,$sec);if($actor!=='')rateHit($pdo,$bucket.':a:'.substr(hash('sha256',$actor),0,32),$max,$sec);}
// Rate actor of a request: the authenticated account, else the device id from the JSON body.
function actorOf($u,$b):string{if($u)return 'u'.$u['id'];$c=validClientId(is_array($b)?($b['client_id']??''):'');return $c!==''?'c'.$c:'';}
function issueToken(PDO $pdo,int $uid){$raw=bin2hex(random_bytes(32));$pdo->prepare('INSERT INTO dice_auth_tokens(user_id,token_hash,expires_at) VALUES(?,?,DATE_ADD(NOW(),INTERVAL 180 DAY))')->execute([$uid,hash('sha256',$raw)]);return $raw;}
function bearer(){if(preg_match('/^Bearer\\s+(.+)$/i',(string)($_SERVER['HTTP_AUTHORIZATION']??''),$m))return $m[1];return '';}
function currentUser(PDO $pdo){static $memo=[];$raw=bearer();if(!$raw)return null;if(array_key_exists($raw,$memo))return $memo[$raw];$q=$pdo->prepare('SELECT u.id,u.username,u.role,t.id token_id FROM dice_auth_tokens t JOIN dice_users u ON u.id=t.user_id WHERE t.token_hash=? AND (t.expires_at IS NULL OR t.expires_at>NOW())');$q->execute([hash('sha256',$raw)]);$u=$q->fetch();if($u)$pdo->prepare('UPDATE dice_auth_tokens SET last_used_at=NOW() WHERE id=?')->execute([$u['token_id']]);return $memo[$raw]=$u?:null;}
if($method==='GET'&&$action==='account-status'){$name=validUser($_GET['username']??'');if(!$name)out(['ok'=>false,'error'=>'invalid_user'],400);$q=$pdo->prepare('SELECT username,role FROM dice_users WHERE LOWER(username)=LOWER(?)');$q->execute([$name]);$row=$q->fetch();out(['ok'=>true,'exists'=>(bool)$row,'username'=>$row['username']??$name,'role'=>$row['role']??(strtolower($name)==='l'?'admin':'user')]);}
if($method==='POST'&&$action==='register'){rate($pdo,'register');$b=body();$name=validUser($b['username']??'');$pw=(string)($b['password']??'');if(!$name||strlen($pw)<4||strlen($pw)>200)out(['ok'=>false,'error'=>'invalid_registration'],400);$q=$pdo->prepare('SELECT 1 FROM dice_users WHERE LOWER(username)=LOWER(?)');$q->execute([$name]);if($q->fetch())out(['ok'=>false,'error'=>'already_registered'],409);$role=strtolower($name)==='l'?'admin':'user';$hash=password_hash($pw,PASSWORD_DEFAULT);$pdo->prepare('INSERT INTO dice_users(username,password_hash,role) VALUES(?,?,?)')->execute([$name,$hash,$role]);$id=(int)$pdo->lastInsertId();out(['ok'=>true,'username'=>$name,'role'=>$role,'auth_token'=>issueToken($pdo,$id)]);}
if($method==='POST'&&$action==='login'){$b=body();rate($pdo,'login',clientKey().'|'.mb_strtolower(trim((string)($b['username']??''))));$name=validUser($b['username']??'');$pw=(string)($b['password']??'');if(!$name)out(['ok'=>false,'error'=>'invalid_user'],400);$q=$pdo->prepare('SELECT id,username,password_hash,role FROM dice_users WHERE LOWER(username)=LOWER(?)');$q->execute([$name]);$u=$q->fetch();if(!$u||!password_verify($pw,$u['password_hash']))out(['ok'=>false,'error'=>'invalid_login'],401);out(['ok'=>true,'username'=>$u['username'],'role'=>$u['role'],'auth_token'=>issueToken($pdo,(int)$u['id'])]);}
if($method==='GET'&&$action==='me'){$u=currentUser($pdo);out(['ok'=>true,'user'=>$u?['username'=>$u['username'],'role'=>$u['role']]:null]);}
if($method==='POST'&&$action==='logout'){$raw=bearer();$u=$raw?currentUser($pdo):null;if($u){socialTables($pdo);$pdo->prepare('DELETE FROM dice_presence WHERE user_id=?')->execute([$u['id']]);cancelDuelsOf($pdo,$u);}if($raw)$pdo->prepare('DELETE FROM dice_auth_tokens WHERE token_hash=?')->execute([hash('sha256',$raw)]);out(['ok'=>true]);}
// Presence = recent heartbeat of a logged-in user (TTL PRESENCE_TTL); "available:false" (End session) removes it at once and withdraws pending 1-vs-1
// invitations. Answers with the caller's open invitations, the status of its own 1-vs-1 invitations and, with "lobby", every registered player's status.
if($method==='POST'&&$action==='presence'){$u=currentUser($pdo);if(!$u)out(['ok'=>false,'error'=>'login_required'],401);socialTables($pdo);$b=body();
  if(isset($b['available'])&&$b['available']===false){$pdo->prepare('DELETE FROM dice_presence WHERE user_id=?')->execute([$u['id']]);cancelDuelsOf($pdo,$u);out(['ok'=>true,'available'=>false]);}
  $pdo->prepare('INSERT INTO dice_presence(user_id,last_seen) VALUES(?,NOW()) ON DUPLICATE KEY UPDATE last_seen=NOW()')->execute([$u['id']]);
  if(random_int(1,200)===1)$pdo->exec('DELETE FROM dice_presence WHERE last_seen<NOW()-INTERVAL 1 DAY');
  $q=$pdo->prepare("SELECT i.game_code,i.host_name,TIMESTAMPDIFF(SECOND,NOW(),i.expires_at) expires_in,g.state_json FROM dice_invites i JOIN dice_games g ON g.game_code=i.game_code AND g.status='active' WHERE i.user_id=? AND i.status='pending' AND i.expires_at>NOW() ORDER BY i.created_at DESC LIMIT 10");$q->execute([$u['id']]);
  $res=['ok'=>true,'available'=>true,'ttl'=>PRESENCE_TTL,'invites'=>array_map(fn($r)=>['code'=>$r['game_code'],'host'=>$r['host_name'],'expires_in'=>(int)$r['expires_in'],'duel'=>isDuel(json_decode($r['state_json']))],$q->fetchAll())];
  $q=$pdo->prepare("SELECT i.game_code,u.username,i.status,i.expires_at>NOW() live,TIMESTAMPDIFF(SECOND,NOW(),i.expires_at) expires_in,g.state_json FROM dice_invites i JOIN dice_users u ON u.id=i.user_id JOIN dice_games g ON g.game_code=i.game_code WHERE i.host_name=? AND i.created_at>NOW()-INTERVAL ".(INVITE_TTL+300)." SECOND ORDER BY i.created_at DESC LIMIT 10");$q->execute([$u['username']]);
  $res['outgoing']=[];foreach($q->fetchAll() as $r)if(isDuel(json_decode($r['state_json'])))$res['outgoing'][]=['code'=>$r['game_code'],'to'=>$r['username'],'status'=>$r['status']==='pending'&&!(int)$r['live']?'expired':$r['status'],'expires_in'=>max(0,(int)$r['expires_in'])];
  if(!empty($b['lobby'])){$busy=busyAccounts($pdo);$q=$pdo->prepare('SELECT u.username,COALESCE(p.last_seen>NOW()-INTERVAL '.PRESENCE_TTL.' SECOND,0) online FROM dice_users u LEFT JOIN dice_presence p ON p.user_id=u.id WHERE u.id<>? ORDER BY online DESC,u.username LIMIT 100');$q->execute([$u['id']]);
    $res['lobby']=array_map(fn($r)=>['name'=>$r['username'],'status'=>!(int)$r['online']?'offline':(isset($busy[mb_strtolower($r['username'])])?'busy':'available')],$q->fetchAll());}
  out($res);}
if($method==='GET'&&$action==='online-users'){$u=currentUser($pdo);if(!$u)out(['ok'=>false,'error'=>'login_required'],401);socialTables($pdo);$q=$pdo->prepare('SELECT u.username FROM dice_presence p JOIN dice_users u ON u.id=p.user_id WHERE p.last_seen>NOW()-INTERVAL '.PRESENCE_TTL.' SECOND AND u.id<>? ORDER BY u.username LIMIT 50');$q->execute([$u['id']]);$busy=busyAccounts($pdo);out(['ok'=>true,'users'=>array_values(array_filter(array_column($q->fetchAll(),'username'),fn($n)=>!isset($busy[mb_strtolower($n)])))]);}
if($method==='GET'&&$action==='results'){$q=$pdo->query("SELECT id,played_at,players,winner,winning_score,mode FROM dice_results ORDER BY played_at DESC,id DESC LIMIT 100");$games=$q->fetchAll();foreach($games as &$g){$g['players']=json_decode($g['players'],true)?:[];}unset($g);out(['ok'=>true,'games'=>$games]);}
if($method==='GET'&&$action==='records'){$q=$pdo->query("SELECT winner AS name,MAX(winning_score) AS score,MAX(played_at) AS played_at FROM dice_results GROUP BY winner ORDER BY score DESC,played_at ASC LIMIT 10");out(['ok'=>true,'records'=>$q->fetchAll()]);}
if($method==='GET'&&$action==='computer-stats'){$q=$pdo->query("SELECT players,winner FROM dice_results WHERE players LIKE '%Computer%' ORDER BY id DESC LIMIT 1000");$games=0;$humanWins=0;$computerWins=0;$draws=0;$humanBest=0;$computerBest=0;foreach($q->fetchAll() as $r){$ps=json_decode($r['players'],true)?:[];$has=false;$hb=0;$cb=0;foreach($ps as $p){$name=(string)($p['name']??'');$score=(int)($p['score']??0);if($name==='Computer'){$has=true;$cb=max($cb,$score);}else{$hb=max($hb,$score);}}if(!$has)continue;$games++;$humanBest=max($humanBest,$hb);$computerBest=max($computerBest,$cb);if($hb>$cb)$humanWins++;elseif($cb>$hb)$computerWins++;else$draws++;}out(['ok'=>true,'stats'=>['games'=>$games,'wins'=>$humanWins,'losses'=>$computerWins,'draws'=>$draws,'win_rate'=>$games?round($humanWins*100/$games,1):0,'human_best'=>$humanBest,'computer_best'=>$computerBest]]);}
if($method==='POST'&&$action==='result'){rate($pdo,'result');$b=body();$players=$b['players']??[];$winner=substr(trim((string)($b['winner']??'')),0,100);$score=(int)($b['winning_score']??0);$mode=in_array(($b['mode']??''),['real','digital'],true)?$b['mode']:'real';if(!$winner||!is_array($players)||count($players)<1||count($players)>6||$score<0||$score>2000)out(['ok'=>false,'error'=>'invalid_result'],400);$pdo->prepare('INSERT INTO dice_results(players,winner,winning_score,mode) VALUES(?,?,?,?)')->execute([json_encode($players,JSON_UNESCAPED_UNICODE),$winner,$score,$mode]);out(['ok'=>true,'id'=>(int)$pdo->lastInsertId()]);}
if($method==='GET'&&$action==='list'){$cid=validClientId($_GET['client']??'');$lu=bearer()?currentUser($pdo):null;$acct=$lu?$lu['username']:'';
  // Zombie cleanup: games without any move for 12 hours (no longer listed or busy anyway) are finished, never deleted.
  $pdo->exec("UPDATE dice_games SET status='finished' WHERE status='active' AND updated_at<NOW()-INTERVAL 12 HOUR");$q=$pdo->query("SELECT game_code,host_name,dice_mode,player_count,state_json,created_at,updated_at FROM dice_games WHERE status='active' AND updated_at >= (NOW() - INTERVAL 12 HOUR) ORDER BY updated_at DESC LIMIT 30");$games=$q->fetchAll();$out=[];foreach($games as $g){$st=json_decode($g['state_json']);$g+=seatInfo($st,$cid,$acct);$g['started']=!empty($st->locked);if($g['started'])$g['full']=true;$g['duel']=isDuel($st);$g['can_close']=$lu?mayClose($st,$lu,$g['game_code'],''):false;unset($g['state_json']);if($g['duel']&&(!$g['mine']||!$g['started']))continue;$out[]=$g;}out(['ok'=>true,'games'=>$out]);}
if($method==='POST'&&$action==='create'){rate($pdo,'social',actorOf(currentUser($pdo),body()));$b=body();$c=newCode($pdo);$state=bodyState();$mode=in_array(($b['dice_mode']??''),['real','digital'],true)?$b['dice_mode']:'real';if(strlen(json_encode($state))>50000)out(['ok'=>false,'error'=>'state_too_large'],413);$hs=prepareSeats($pdo,$state,currentUser($pdo));$host=seatName($state->players[$hs],$hs);$pc=count($state->players);$pdo->prepare("INSERT INTO dice_games(game_code,host_name,state_json,dice_mode,player_count,status,version,updated_at) VALUES(?,?,?,?,?,'active',1,NOW())")->execute([$c,$host,json_encode($state,JSON_UNESCAPED_UNICODE),$mode,$pc]);out(['ok'=>true,'code'=>$c,'version'=>1,'host_token'=>hostToken($c),'seat'=>$hs,'state'=>publicState($state,(string)$state->players[$hs]->clientId)]);}
// Direct 1-vs-1 invitation (logged-in accounts only). Under the pair lock: self-invite refused; a pending invitation from the other side is
// accepted instead (cross-invite = one game, never two); a repeated click returns the open invitation; only available (present, not busy) targets.
if($method==='POST'&&$action==='duel'){rate($pdo,'social',actorOf(currentUser($pdo),body()));$b=body();$cid=validClientId($b['client_id']??'');if(!$cid)out(['ok'=>false,'error'=>'invalid_client'],400);$u=currentUser($pdo);if(!$u)out(['ok'=>false,'error'=>'login_required'],401);socialTables($pdo);
  $t=userId($pdo,(string)($b['username']??''));if(!$t)out(['ok'=>false,'error'=>'unknown_user'],404);if((int)$t['id']===(int)$u['id'])out(['ok'=>false,'error'=>'invalid_invite'],400);
  $me=$u['username'];$tn=$t['username'];
  $pdo->beginTransaction();lockPair($pdo,(int)$u['id'],(int)$t['id']);
  $cross=pendingDuels($pdo,$tn,(int)$u['id']);if($cross)duelAcceptLocked($pdo,$cross[0]['game_code'],$u,$cid);
  $mine=pendingDuels($pdo,$me);foreach($mine as $r)if((int)$r['user_id']===(int)$t['id']){$pdo->commit();out(['ok'=>true,'existing'=>true,'code'=>$r['game_code'],'to'=>$tn]);}
  if($mine){$pdo->commit();out(['ok'=>false,'error'=>'invite_pending'],409);}
  if(!isPresent($pdo,(int)$t['id'])){$pdo->commit();out(['ok'=>false,'error'=>'not_available'],409);}
  $busy=busyAccounts($pdo);if(isset($busy[mb_strtolower($tn)])){$pdo->commit();out(['ok'=>false,'error'=>'busy'],409);}if(isset($busy[mb_strtolower($me)])){$pdo->commit();out(['ok'=>false,'error'=>'busy_self'],409);}
  $c=newCode($pdo);
  $st=(object)['model'=>2,'duel'=>true,'locked'=>false,'hostSeat'=>0,'turn'=>0,'start'=>0,'started'=>false,'liveDice'=>freshDice(),'players'=>[
    freshSeat((object)['type'=>'account','nameMode'=>'account','name'=>$me,'account'=>$me,'clientId'=>$cid]),
    freshSeat((object)['type'=>'invite','nameMode'=>'auto','name'=>$tn,'account'=>$tn,'inviteExpires'=>time()+INVITE_TTL])]];
  $pdo->prepare("INSERT INTO dice_games(game_code,host_name,state_json,dice_mode,player_count,status,version,updated_at) VALUES(?,?,?,'digital',2,'active',1,NOW())")->execute([$c,$me,json_encode($st,JSON_UNESCAPED_UNICODE)]);
  $pdo->prepare("INSERT INTO dice_invites(game_code,user_id,host_name,status,expires_at) VALUES(?,?,?,'pending',DATE_ADD(NOW(),INTERVAL ".INVITE_TTL." SECOND))")->execute([$c,$t['id'],$me]);
  $pdo->commit();out(['ok'=>true,'code'=>$c,'to'=>$tn,'host_token'=>hostToken($c),'expires_in'=>INVITE_TTL]);}
$c=code();if(!$c)out(['ok'=>false,'error'=>'missing_code'],400);
// The inviting account withdraws its pending 1-vs-1 invitation.
if($method==='POST'&&$action==='duel-cancel'){rate($pdo,'social',actorOf(currentUser($pdo),body()));$u=currentUser($pdo);if(!$u)out(['ok'=>false,'error'=>'login_required'],401);socialTables($pdo);
  $pdo->beginTransaction();$g=lockGame($pdo,$c);$st=$g['st'];
  if(!isDuel($st)||strcasecmp(seatAccount(players($st)[(int)($st->hostSeat??0)]??null),$u['username'])!==0){$pdo->rollBack();out(['ok'=>false,'error'=>'forbidden'],403);}
  if(!empty($st->locked)){$pdo->rollBack();out(['ok'=>false,'error'=>'game_started'],409);}
  closeDuel($pdo,$c,'cancelled');$pdo->commit();out(['ok'=>true,'cancelled'=>true]);}
if($method==='GET'){$q=$pdo->prepare("SELECT game_code,state_json,dice_mode,version,updated_at FROM dice_games WHERE game_code=? AND status='active'");$q->execute([$c]);$g=$q->fetch();if(!$g)out(['ok'=>false,'error'=>'not_found'],404);$g['state']=json_decode($g['state_json']);$g['state']=publicState($g['state'],validClientId($_GET['client']??''));$g['version']=(int)$g['version'];$g['now']=time();unset($g['state_json']);out(['ok'=>true,'game'=>$g]);}
if($method==='POST'&&$action==='close'){$b=body();$u=currentUser($pdo);rate($pdo,'life',actorOf($u,$b));socialTables($pdo);
  $q=$pdo->prepare("SELECT state_json FROM dice_games WHERE game_code=? AND status='active'");$q->execute([$c]);$row=$q->fetch();if(!$row)out(['ok'=>false,'error'=>'not_found'],404);
  if(!mayClose(json_decode($row['state_json']),$u,$c,(string)($b['host_token']??'')))out(['ok'=>false,'error'=>'forbidden'],403);
  $pdo->beginTransaction();$pdo->prepare('SELECT 1 FROM dice_games WHERE game_code=? FOR UPDATE')->execute([$c]);endGameRow($pdo,$c);$pdo->commit();out(['ok'=>true,'closed'=>true]);}
// Leave (End session / logout of a seated player). Lobby: the host's leave closes the game, any other seat becomes open again. Started game:
// the seat is marked left – its scores stay, the account is free at once and can rejoin (same seat) – and the game ends when no human
// player is left in it. Leaving never ends a running game for the others.
if($method==='POST'&&$action==='leave'){$b=body();$u=currentUser($pdo);rate($pdo,'life',actorOf($u,$b));$cid=validClientId($b['client_id']??'');$acct=$u?$u['username']:'';socialTables($pdo);
  $pdo->beginTransaction();$q=$pdo->prepare("SELECT game_code,state_json,version FROM dice_games WHERE game_code=? AND status='active' FOR UPDATE");$q->execute([$c]);$g=$q->fetch();
  if(!$g){$pdo->rollBack();out(['ok'=>true,'left'=>true,'closed'=>true]);}
  $st=json_decode($g['state_json']);$slot=-1;
  foreach(players($st) as $i=>$p){$t=seatType($p);if(($t==='account'&&$acct!==''&&strcasecmp(seatAccount($p),$acct)===0)||($t==='guest'&&$cid!==''&&(string)($p->clientId??'')===$cid)){$slot=$i;break;}}
  if($slot<0){$pdo->rollBack();out(['ok'=>false,'error'=>'not_participant'],403);}
  $p=$st->players[$slot];
  if(!empty($st->model)&&empty($st->locked)){$closed=$slot===(int)($st->hostSeat??0);if(!$closed){if(seatAccount($p)!=='')inviteStatus($pdo,$c,seatAccount($p),'cancelled');foreach(['account','clientId','left'] as $k)unset($p->$k);$p->type='open';$p->nameMode='auto';$p->name='';}}
  else{$p->left=true;if(seatType($p)==='account')$p->clientId='';$closed=allHumansLeft($st);}
  if($closed){endGameRow($pdo,$c);$pdo->commit();out(['ok'=>true,'left'=>true,'closed'=>true]);}
  $next=saveGame($pdo,$g,$st);$pdo->commit();out(['ok'=>true,'left'=>true,'closed'=>false,'version'=>$next]);}
if($method==='POST'&&$action==='join'){rate($pdo,'join',actorOf(currentUser($pdo),body()));$b=body();$cid=validClientId($b['client_id']??'');if(!$cid)out(['ok'=>false,'error'=>'invalid_client'],400);
  $u=currentUser($pdo);$acct=$u?$u['username']:'';$want=$u?$u['username']:displayName($b['name']??'');if($want==='')$want='Guest';if(!$u&&reservedName($pdo,$want))identityRefused($want);if($u)socialTables($pdo);
  $pdo->beginTransaction();
  $q=$pdo->prepare("SELECT game_code,state_json,dice_mode,version,updated_at FROM dice_games WHERE game_code=? AND status='active' FOR UPDATE");$q->execute([$c]);$g=$q->fetch();
  if(!$g){$pdo->rollBack();out(['ok'=>false,'error'=>'not_found'],404);}
  $st=json_decode($g['state_json']);$ps=is_object($st)&&isset($st->players)&&is_array($st->players)?$st->players:[];$v2=!empty($st->model);$locked=!empty($st->locked);
  // Reconnect: a guest gets its seat back on the same device; an account gets its seat back on any device (also after End session / leave), but only with that account's token.
  $slot=-1;$changed=false;
  // Authenticated identity wins over a retained guest device credential. This prevents a device reused by an account from reclaiming the wrong guest seat.
  if($u)foreach($ps as $i=>$p)if(seatType($p)==='account'&&strcasecmp(seatAccount($p),$acct)===0){$slot=$i;if((string)($p->clientId??'')!==$cid||seatLeft($p)){$p->clientId=$cid;unset($p->left);$changed=true;}break;}
  if($slot<0)foreach($ps as $i=>$p){$t=seatType($p);if(($t==='guest'||(!$v2&&$t!=='account'))&&(string)($p->clientId??'')===$cid){$slot=$i;if(seatLeft($p)){unset($p->left);$changed=true;}break;}}
  // An invited account joining by code accepts its invitation.
  if($slot<0&&$u)foreach($ps as $i=>$p){if(seatType($p)==='invite'&&strcasecmp(seatAccount($p),$acct)===0){if(isDuel($st)){$pdo->rollBack();duelReply($pdo,$c,$u,$cid,true,seatAccount($ps[(int)($st->hostSeat??0)]??null));}if((int)($p->inviteExpires??0)<time()){$pdo->rollBack();out(['ok'=>false,'error'=>'invite_expired'],410);}$slot=$i;$p->type='account';$p->nameMode='account';$p->name=$acct;$p->account=$acct;$p->clientId=$cid;unset($p->inviteExpires);$changed=true;inviteStatus($pdo,$c,$acct,'accepted');break;}}
  if($slot>=0){$version=(int)$g['version'];if($changed)$version=saveGame($pdo,$g,$st);$pdo->commit();gameOut($g,$st,$version,$cid,['seat'=>$slot]+seatInfo($st,$cid,$acct));}
  if($locked){$pdo->rollBack();out(['ok'=>false,'error'=>'game_started']+seatInfo($st,$cid,$acct),409);}
  $info=seatInfo($st,$cid,$acct);
  if($slot<0)foreach($ps as $i=>$p){if(seatFree($p)&&seatName($p,$i)===$want){$slot=$i;break;}}
  if($slot<0)foreach($ps as $i=>$p){if(seatFree($p)&&((($p->nameMode??'auto')==='auto')||preg_match('/^(Player|ผู้เล่น) \d+$/u',seatName($p,$i)))){$slot=$i;break;}}
  if($slot<0)foreach($ps as $i=>$p){if(seatFree($p)){$slot=$i;break;}}
  if($slot<0){$pdo->rollBack();out(['ok'=>false,'error'=>'game_full']+$info,409);}
  $taken=function($n)use($ps,$slot){foreach($ps as $i=>$p)if($i!==$slot&&seatType($p)!=='open'&&mb_strtolower(seatName($p,$i))===mb_strtolower($n))return true;return false;};
  $p=$ps[$slot];
  if($u){if($taken($acct)){$pdo->rollBack();out(['ok'=>false,'error'=>'duplicate_name','name'=>$acct],409);}$p->type='account';$p->nameMode='account';$p->name=$acct;$p->account=$acct;}
  else{$nm=$want;if($taken($nm)){$k=2;while($taken($want.' '.$k))$k++;$nm=$want.' '.$k;}unset($p->account);$p->type='guest';$p->nameMode='custom';$p->name=$nm;}
  $p->clientId=$cid;
  $next=saveGame($pdo,$g,$st);$pdo->commit();
  gameOut($g,$st,$next,$cid,['seat'=>$slot]+seatInfo($st,$cid,$acct));}
// Lobby (before start, host only): add computer / local guest / open seat, invite an available account, remove a seat (cancels its invitation).
if($method==='POST'&&$action==='seat'){rate($pdo,'social',actorOf(currentUser($pdo),body()));$b=body();$cid=validClientId($b['client_id']??'');$u=currentUser($pdo);$acct=$u?$u['username']:null;$op=(string)($b['op']??'');socialTables($pdo);
  $target=null;$err=null;if($op==='invite'){if(!$u)out(['ok'=>false,'error'=>'login_required'],401);$target=userId($pdo,(string)($b['username']??''));if(!$target)$err=['unknown_user',404];elseif((int)$target['id']===(int)$u['id'])$err=['invalid_invite',400];elseif(!isPresent($pdo,(int)$target['id']))$err=['not_available',409];elseif(isset(busyAccounts($pdo)[mb_strtolower($target['username'])]))$err=['busy',409];}
  $local='';if($op==='add_local'){$local=displayName($b['name']??'');if($local==='')out(['ok'=>false,'error'=>'invalid_name'],400);if(reservedName($pdo,$local))identityRefused($local);}
  $pdo->beginTransaction();$g=lockGame($pdo,$c);$st=$g['st'];$ps=players($st);
  if(empty($st->model)||!holdsSeat($st,(int)($st->hostSeat??-1),(string)$cid,$acct)){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],(string)$cid,['error'=>'not_host'],403);}
  if(!empty($st->locked)){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],$cid,['error'=>'game_started'],409);}
  if(isDuel($st)){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],$cid,['error'=>'private_game'],409);}
  if($err){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],$cid,['error'=>$err[0]],$err[1]);}
  $has=function($n)use($ps){foreach($ps as $i=>$p)if(seatType($p)!=='open'&&mb_strtolower(seatName($p,$i))===mb_strtolower($n))return true;return false;};
  $add=function($p)use(&$st,$pdo,$g,$cid){if(count($st->players)>=6){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],$cid,['error'=>'game_full'],409);}$st->players[]=freshSeat($p);};
  if($op==='add_computer')$add((object)['type'=>'computer','nameMode'=>'computer','name'=>'','cpuLevel'=>in_array($b['level']??'',['beginner','normal','expert'],true)?$b['level']:'normal']);
  elseif($op==='add_open')$add((object)['type'=>'open','nameMode'=>'auto','name'=>'']);
  elseif($op==='add_local'){if($has($local)){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],$cid,['error'=>'duplicate_name','name'=>$local],409);}$add((object)['type'=>'local','nameMode'=>'custom','name'=>$local]);}
  elseif($op==='invite'){$tn=$target['username'];foreach($ps as $p)if(strcasecmp(seatAccount($p),$tn)===0){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],$cid,['error'=>'already_in_game'],409);}if($has($tn)){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],$cid,['error'=>'duplicate_name','name'=>$tn],409);}
    $add((object)['type'=>'invite','nameMode'=>'auto','name'=>$tn,'account'=>$tn,'inviteExpires'=>time()+INVITE_TTL]);
    $pdo->prepare("INSERT INTO dice_invites(game_code,user_id,host_name,status,expires_at) VALUES(?,?,?,'pending',DATE_ADD(NOW(),INTERVAL ".INVITE_TTL." SECOND)) ON DUPLICATE KEY UPDATE status='pending',host_name=VALUES(host_name),created_at=NOW(),expires_at=VALUES(expires_at)")->execute([$c,$target['id'],$acct]);}
  elseif($op==='remove'){$i=(int)($b['seat']??-1);$h=(int)$st->hostSeat;if(!isset($ps[$i])||$i===$h){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],$cid,['error'=>'invalid_seat'],400);}if(seatAccount($ps[$i])!=='')inviteStatus($pdo,$c,seatAccount($ps[$i]),'cancelled');array_splice($st->players,$i,1);if($i<$h)$st->hostSeat=$h-1;}
  else{$pdo->rollBack();out(['ok'=>false,'error'=>'bad_request'],400);}
  $next=saveGame($pdo,$g,$st);$pdo->commit();gameOut($g,$st,$next,$cid);}
// Accept/decline: only the invited account (bearer token) can answer; a repeated accept is idempotent (and rebinds the seat to this device).
if($method==='POST'&&$action==='invite-reply'){rate($pdo,'social',actorOf(currentUser($pdo),body()));$b=body();$cid=validClientId($b['client_id']??'');if(!$cid)out(['ok'=>false,'error'=>'invalid_client'],400);$u=currentUser($pdo);if(!$u)out(['ok'=>false,'error'=>'login_required'],401);socialTables($pdo);$acct=$u['username'];$accept=!empty($b['accept']);
  $q=$pdo->prepare("SELECT state_json FROM dice_games WHERE game_code=? AND status='active'");$q->execute([$c]);$row=$q->fetch();
  // Closed games: a repeated decline stays a success, anything else reports why the invitation is gone.
  if(!$row){$q=$pdo->prepare('SELECT status FROM dice_invites WHERE game_code=? AND user_id=?');$q->execute([$c,$u['id']]);$s=(string)($q->fetchColumn()?:'');if($s==='declined'&&!$accept)out(['ok'=>true,'declined'=>true]);if(in_array($s,['declined','cancelled','expired'],true))out(['ok'=>false,'error'=>'invite_'.$s],410);out(['ok'=>false,'error'=>'not_found'],404);}
  $pre=json_decode($row['state_json']);if(isDuel($pre))duelReply($pdo,$c,$u,$cid,$accept,seatAccount(players($pre)[(int)($pre->hostSeat??0)]??null));
  $pdo->beginTransaction();$g=lockGame($pdo,$c);$st=$g['st'];$ps=players($st);$slot=-1;
  foreach($ps as $i=>$p)if(in_array(seatType($p),['invite','account'],true)&&strcasecmp(seatAccount($p),$acct)===0){$slot=$i;break;}
  if($slot<0){$pdo->rollBack();out(['ok'=>false,'error'=>'no_invite'],404);}
  $p=$ps[$slot];
  if(seatType($p)==='account'){if(!$accept){$pdo->rollBack();out(['ok'=>false,'error'=>'already_accepted'],409);}$version=(int)$g['version'];if((string)($p->clientId??'')!==$cid||seatLeft($p)){$p->clientId=$cid;unset($p->left);$version=saveGame($pdo,$g,$st);}$pdo->commit();gameOut($g,$st,$version,$cid,['seat'=>$slot]);}
  if((int)($p->inviteExpires??0)<time()){inviteStatus($pdo,$c,$acct,'expired');$pdo->commit();out(['ok'=>false,'error'=>'invite_expired'],410);}
  if($accept){$p->type='account';$p->nameMode='account';$p->name=$acct;$p->account=$acct;$p->clientId=$cid;unset($p->inviteExpires);inviteStatus($pdo,$c,$acct,'accepted');$next=saveGame($pdo,$g,$st);$pdo->commit();gameOut($g,$st,$next,$cid,['seat'=>$slot]);}
  $h=(int)$st->hostSeat;array_splice($st->players,$slot,1);if($slot<$h)$st->hostSeat=$h-1;inviteStatus($pdo,$c,$acct,'declined');$next=saveGame($pdo,$g,$st);$pdo->commit();out(['ok'=>true,'declined'=>true,'version'=>$next]);}
// Start (host only): refused while invitations are unresolved; unfilled open seats are dropped and the roster is frozen server-side.
if($method==='POST'&&$action==='start'){rate($pdo,'life',actorOf(currentUser($pdo),body()));$b=body();$cid=validClientId($b['client_id']??'');$u=currentUser($pdo);$acct=$u?$u['username']:null;
  $pdo->beginTransaction();$g=lockGame($pdo,$c);$st=$g['st'];
  if(empty($st->model)||!holdsSeat($st,(int)($st->hostSeat??-1),(string)$cid,$acct)){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],(string)$cid,['error'=>'not_host'],403);}
  if(!empty($st->locked)){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],$cid,['seat'=>(int)$st->hostSeat]);}
  foreach(players($st) as $p)if(seatType($p)==='invite'){$pdo->rollBack();gameOut($g,$st,(int)$g['version'],$cid,['error'=>'pending_invites'],409);}
  $keep=[];$h=0;foreach(players($st) as $i=>$p){if(seatType($p)==='open')continue;if($i===(int)$st->hostSeat)$h=count($keep);$keep[]=freshSeat($p);}
  $st->players=$keep;$st->hostSeat=$h;$st->locked=true;$st->turn=0;$st->start=0;$st->started=false;$st->liveDice=(object)['open'=>true,'rolls'=>0,'dice'=>[1,1,1,1,1],'held'=>[false,false,false,false,false]];
  $next=saveGame($pdo,$g,$st);$pdo->commit();gameOut($g,$st,$next,$cid,['seat'=>$h]);}
if($method==='POST'&&$action==='update'){rate($pdo,'sync',actorOf(currentUser($pdo),body()));$b=body();$state=bodyState();$json=json_encode($state,JSON_UNESCAPED_UNICODE);if(strlen($json)>50000)out(['ok'=>false,'error'=>'state_too_large'],413);$expected=(int)($b['version']??0);$reset=!empty($b['reset']);
  $pdo->beginTransaction();
  $q=$pdo->prepare("SELECT game_code,state_json,dice_mode,version,updated_at FROM dice_games WHERE game_code=? AND status='active' FOR UPDATE");$q->execute([$c]);$g=$q->fetch();
  if(!$g){$pdo->rollBack();out(['ok'=>false,'error'=>'not_found'],404);}
  $cur=json_decode($g['state_json']);$cid=validClientId($b['client_id']??'');$u=currentUser($pdo);$acct=$u?$u['username']:null;
  $deny=function(string $e)use($pdo,$g,$cur,$cid){$pdo->rollBack();$g['state']=publicState($cur,$cid);$g['version']=(int)$g['version'];unset($g['state_json']);out(['ok'=>false,'error'=>$e,'game'=>$g],403);};
  // Only seated clients may write (account seats additionally need their bearer token); seat identities never change through an update.
  if(!empty($cur->model)?!holdsAnySeat($cur,$cid,$acct):($cid===''||!seatInfo($cur,$cid)['mine']))$deny('not_participant');
  $state=restoreSeatSecrets($cur,$state);if(is_object($state)){if(isDuel($cur))$state->duel=true;else unset($state->duel);}$json=json_encode($state,JSON_UNESCAPED_UNICODE);
  // Authoritative state: reject stale versions and any write that would drop or alter stored scores (except an explicit new-game reset); return the current game so the client can resync.
  if((int)$g['version']!==$expected||!stateSeatsPreserved($cur,$state)||(!$reset&&!stateScoresPreserved($cur,$state))){$pdo->rollBack();$g['state']=publicState($cur,$cid);$g['version']=(int)$g['version'];unset($g['state_json']);out(['ok'=>false,'error'=>'conflict','game'=>$g],409);}
  // Started games: frozen roster; only the active seat may write and only its own progress changes; the host alone may reset (new game, same roster).
  if(!empty($cur->model)){$n=count(players($cur));$t=(int)($cur->turn??0);
    if(empty($cur->locked))$deny('not_started');
    if(rosterKey($cur)!==rosterKey($state))$deny('roster_locked');
    if($reset){if(!holdsSeat($cur,(int)$cur->hostSeat,$cid,$acct))$deny('not_host');$nt=$state->turn??null;if(!is_int($nt)||$nt<0||$nt>=$n)$deny('invalid_turn');$empty=progressKey(freshSeat(new stdClass()));foreach(players($state) as $p)if(progressKey($p)!==$empty)$deny('invalid_reset');
      // 1-vs-1 rematch: the server, not the host device, decides who opens (alternating, see nextDuelStart).
      if(isDuel($cur)){if($nt!==nextDuelStart($cur))$deny('invalid_turn');$state->start=$nt;$json=json_encode($state,JSON_UNESCAPED_UNICODE);}}
    else{if(!holdsSeat($cur,$t,$cid,$acct))$deny('not_your_turn');$nt=$state->turn??null;if(!is_int($nt)||($nt!==$t&&$nt!==($t+1)%$n))$deny('invalid_turn');foreach(players($cur) as $i=>$p)if($i!==$t&&progressKey($p)!==progressKey($state->players[$i]))$deny('not_your_seat');}}
  $next=(int)$g['version']+1;$pdo->prepare("UPDATE dice_games SET state_json=?,version=?,updated_at=NOW() WHERE game_code=?")->execute([$json,$next,$c]);$pdo->commit();out(['ok'=>true,'version'=>$next]);}
out(['ok'=>false,'error'=>'bad_request'],400);
