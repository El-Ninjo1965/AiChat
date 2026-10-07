<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function out($data,int $code=200){http_response_code($code);echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
function body(){return json_decode(file_get_contents('php://input'),true) ?: [];}
function code(){return strtoupper(substr(preg_replace('/[^A-Z0-9]/','',$_GET['code']??''),0,8));}
function db(){
  $cfg=require __DIR__.'/config.php';
  return new PDO('mysql:host='.$cfg['host'].';dbname='.$cfg['name'].';charset=utf8mb4',$cfg['user'],$cfg['pass'],[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
}
$pdo=db();$method=$_SERVER['REQUEST_METHOD'];$action=$_GET['action']??'';
if($method==='POST'&&$action==='create'){
  $b=body();$c='';
  do{$c=strtoupper(substr(bin2hex(random_bytes(4)),0,6));$q=$pdo->prepare('SELECT 1 FROM dice_games WHERE game_code=?');$q->execute([$c]);}while($q->fetch());
  $state=$b['state']??[];$mode=in_array(($b['dice_mode']??''),['real','digital'],true)?$b['dice_mode']:'real';
  $q=$pdo->prepare('INSERT INTO dice_games(game_code,state_json,dice_mode,version,updated_at) VALUES(?,?,?,1,NOW())');
  $q->execute([$c,json_encode($state,JSON_UNESCAPED_UNICODE),$mode]);out(['ok'=>true,'code'=>$c,'version'=>1]);
}
$c=code();if(!$c)out(['ok'=>false,'error'=>'missing_code'],400);
if($method==='GET'){
  $q=$pdo->prepare('SELECT game_code,state_json,dice_mode,version,updated_at FROM dice_games WHERE game_code=?');$q->execute([$c]);$g=$q->fetch();
  if(!$g)out(['ok'=>false,'error'=>'not_found'],404);$g['state']=json_decode($g['state_json'],true);unset($g['state_json']);out(['ok'=>true,'game'=>$g]);
}
if($method==='POST'&&$action==='update'){
  $b=body();$expected=(int)($b['version']??0);$q=$pdo->prepare('UPDATE dice_games SET state_json=?,version=version+1,updated_at=NOW() WHERE game_code=? AND version=?');
  $q->execute([json_encode($b['state']??[],JSON_UNESCAPED_UNICODE),$c,$expected]);
  if(!$q->rowCount())out(['ok'=>false,'error'=>'conflict'],409);
  $q=$pdo->prepare('SELECT version FROM dice_games WHERE game_code=?');$q->execute([$c]);out(['ok'=>true,'version'=>(int)$q->fetchColumn()]);
}
out(['ok'=>false,'error'=>'bad_request'],400);
