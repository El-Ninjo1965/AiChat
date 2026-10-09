/* Computer opponent strategy. Pure decision logic only: it never produces or alters dice values.
   Levels influence HOLD choices and category choice; dice always come from the game's crypto RNG. */
(function (root) {
  "use strict";
  var CATS = ["ones", "twos", "threes", "fours", "fives", "sixes", "three", "four", "full", "small", "large", "five", "chance"];
  var UPPER = { ones: 1, twos: 2, threes: 3, fours: 4, fives: 5, sixes: 6 };
  var LEVELS = ["beginner", "normal", "expert"];
  // Typical value a category still yields when kept open; used as opportunity cost.
  var PAR = { ones: 2, twos: 5, threes: 7.5, fours: 10, fives: 12.5, sixes: 15, three: 16, four: 9, full: 17, small: 20, large: 15, five: 9, chance: 22 };
  // Expert values hard combinations closer to what they are worth over a whole game, so it dumps them less eagerly.
  var PAR_EXPERT = { ones: 2, twos: 5, threes: 7.5, fours: 10, fives: 12.5, sixes: 15, three: 16, four: 9, full: 17, small: 25, large: 25, five: 15, chance: 22 };

  function level(v) { return LEVELS.indexOf(v) >= 0 ? v : "normal"; }

  function scoreOptions(d) {
    var cnt = [0, 0, 0, 0, 0, 0, 0], sum = 0, i;
    for (i = 0; i < d.length; i++) { cnt[d[i]]++; sum += d[i]; }
    var max = Math.max.apply(null, cnt), run = 0, best = 0;
    for (i = 1; i <= 6; i++) { run = cnt[i] ? run + 1 : 0; if (run > best) best = run; }
    return {
      ones: cnt[1], twos: cnt[2] * 2, threes: cnt[3] * 3, fours: cnt[4] * 4, fives: cnt[5] * 5, sixes: cnt[6] * 6,
      three: max >= 3 ? sum : 0, four: max >= 4 ? sum : 0,
      full: cnt.indexOf(3) >= 0 && cnt.indexOf(2) >= 0 ? 25 : 0,
      small: best >= 4 ? 30 : 0, large: best >= 5 ? 40 : 0,
      five: max === 5 ? 50 : 0, chance: sum
    };
  }

  function freeCats(scores) { return CATS.filter(function (k) { return !scores || scores[k] === undefined; }); }

  function upperSum(scores) {
    var s = 0;
    for (var k in UPPER) if (scores && scores[k] !== undefined && scores[k] !== "") s += Number(scores[k]) || 0;
    return s;
  }

  // Strategic value of writing v into category k: score minus what the open category is still worth, plus upper-bonus effects.
  var HARD = { four: 1, full: 1, small: 1, large: 1, five: 1 };
  // Strategy depth per level: normal only grabs the bonus when it is reached; expert also paces the upper section,
  // uses whole-game category values and discounts hard combinations near the end.
  function gain(k, v, scores, free, expert) {
    var table = expert ? PAR_EXPERT : PAR;
    var par = expert && HARD[k] ? table[k] * Math.min(1, (free.length - 1) / 4) : table[k];
    var g = v - par;
    if (UPPER[k]) {
      var S = upperSum(scores), f = UPPER[k];
      if (S < 63) {
        if (S + v >= 63) g += 35;
        else {
          var rest = 0;
          for (var i = 0; i < free.length; i++) if (UPPER[free[i]] && free[i] !== k) rest += 5 * UPPER[free[i]];
          // Bonus still reachable: reward staying on pace (3 of a face) and penalise falling behind.
          if (expert && S + v + rest >= 63) g += (v - 3 * f) * 1.2 + (v >= 3 * f ? 4 : 0);
        }
      }
    }
    return g;
  }

  function bestCategory(d, scores, useGain, expert) {
    var free = freeCats(scores), vals = scoreOptions(d), bestK = free[0], bestV = -Infinity;
    for (var i = 0; i < free.length; i++) {
      var k = free[i], v = useGain ? gain(k, vals[k], scores, free, expert) : vals[k];
      if (v > bestV) { bestV = v; bestK = k; }
    }
    return { k: bestK, v: bestV };
  }

  // Exact distribution of re-rolling m dice, as sorted multisets with probabilities.
  var outcomeCache = {};
  function outcomes(m) {
    if (outcomeCache[m]) return outcomeCache[m];
    var map = {}, total = Math.pow(6, m);
    for (var n = 0; n < total; n++) {
      var a = [], x = n;
      for (var j = 0; j < m; j++) { a.push((x % 6) + 1); x = Math.floor(x / 6); }
      a.sort();
      var key = a.join("");
      map[key] = (map[key] || 0) + 1;
    }
    var list = Object.keys(map).map(function (key) {
      return { d: key ? key.split("").map(Number) : [], p: map[key] / total };
    });
    return (outcomeCache[m] = list);
  }

  // Static tables over dice multisets (sorted digit strings): every kept multiset (size 0-5) with its
  // re-roll transitions to five-dice results, and every five-dice result with its possible keeps.
  var tables = null;
  function build() {
    var keepIds = {}, keepList = [], fiveIds = {}, fiveList = [];
    function addKeep(key) { if (keepIds[key] === undefined) { keepIds[key] = keepList.length; keepList.push(key); } return keepIds[key]; }
    function gen(size, min, prefix) {
      if (prefix.length === size) { addKeep(prefix); return; }
      for (var v = min; v <= 6; v++) gen(size, v, prefix + v);
    }
    for (var size = 0; size <= 5; size++) gen(size, 1, "");
    keepList.forEach(function (key) { if (key.length === 5) { fiveIds[key] = fiveList.length; fiveList.push(key); } });
    var trans = keepList.map(function (key) {
      var kept = key ? key.split("").map(Number) : [];
      return outcomes(5 - kept.length).map(function (o) { return [fiveIds[kept.concat(o.d).sort().join("")], o.p]; });
    });
    var subKeeps = fiveList.map(function (key) {
      var d = key.split("").map(Number), seen = {}, out = [];
      for (var mask = 0; mask < 32; mask++) {
        var k = "";
        for (var i = 0; i < 5; i++) if (mask & (1 << i)) k += d[i];
        if (!seen[k]) { seen[k] = 1; out.push(keepIds[k]); }
      }
      return out;
    });
    return { keepList: keepList, fiveList: fiveList, trans: trans, subKeeps: subKeeps };
  }

  // Expected strategic value of every possible keep with `depth` re-rolls left (depth 1 or 2).
  function keepValues(scores, depth, expert) {
    var t = tables || (tables = build()), V = t.fiveList.map(function (key) { return bestCategory(key.split("").map(Number), scores, true, expert).v; }), E;
    for (var r = 0; r < depth; r++) {
      E = t.trans.map(function (tr) { var s = 0; for (var i = 0; i < tr.length; i++) s += tr[i][1] * V[tr[i][0]]; return s; });
      if (r + 1 < depth) V = t.subKeeps.map(function (ks) { var b = -Infinity; for (var i = 0; i < ks.length; i++) if (E[ks[i]] > b) b = E[ks[i]]; return b; });
    }
    return E;
  }

  function bestKeep(d, depth, scores, expert) {
    var t = tables || (tables = build()), E = keepValues(scores, depth, expert), key = d.slice().sort().join(""), seen = {}, best = "", bestE = -Infinity;
    for (var mask = 0; mask < 32; mask++) {
      var k = "";
      for (var i = 0; i < 5; i++) if (mask & (1 << i)) k += key[i];
      if (seen[k]) continue;
      seen[k] = 1;
      var e = E[t.keepList.indexOf(k)];
      // Prefer keeping more dice on ties (fewer pointless re-rolls).
      if (e > bestE + 1e-9 || (Math.abs(e - bestE) <= 1e-9 && k.length > best.length)) { bestE = e; best = k; }
    }
    return best ? best.split("").map(Number) : [];
  }

  function maskFor(d, kept) {
    var need = {}, held = [];
    kept.forEach(function (x) { need[x] = (need[x] || 0) + 1; });
    for (var i = 0; i < d.length; i++) {
      if (need[d[i]]) { need[d[i]]--; held.push(true); } else held.push(false);
    }
    return held;
  }

  // Simple pattern holds: most frequent face, otherwise consecutive dice.
  function simpleHolds(d) {
    var cnt = {};
    d.forEach(function (x) { cnt[x] = (cnt[x] || 0) + 1; });
    var best = Object.keys(cnt).sort(function (a, b) { return cnt[b] - cnt[a] || b - a; })[0];
    if (cnt[best] >= 2) return d.map(function (x) { return String(x) === best; });
    var sorted = Object.keys(cnt).map(Number).sort(function (a, b) { return a - b; }), keep = {};
    for (var i = 0; i < sorted.length - 1; i++) if (sorted[i + 1] === sorted[i] + 1) { keep[sorted[i]] = 1; keep[sorted[i + 1]] = 1; }
    return d.map(function (x) { return !!keep[x]; });
  }

  function chooseHolds(d, rollsLeft, scores, lvl) {
    lvl = level(lvl);
    if (lvl === "beginner") return simpleHolds(d);
    var depth = lvl === "expert" ? Math.max(1, Math.min(2, rollsLeft)) : 1;
    return maskFor(d, bestKeep(d, depth, scores || {}, lvl === "expert"));
  }

  function chooseCategory(d, scores, lvl, rng) {
    lvl = level(lvl);
    rng = rng || Math.random;
    scores = scores || {};
    if (lvl !== "beginner") return bestCategory(d, scores, true, lvl === "expert").k;
    // Beginner: takes the highest raw score, but now and then settles for the runner-up.
    var vals = scoreOptions(d), free = freeCats(scores).sort(function (a, b) { return vals[b] - vals[a]; });
    if (free.length > 1 && vals[free[1]] > 0 && rng() < 0.3) return free[1];
    return free[0];
  }

  var api = { CATS: CATS, LEVELS: LEVELS, scoreOptions: scoreOptions, chooseHolds: chooseHolds, chooseCategory: chooseCategory, level: level };
  if (typeof module === "object" && module.exports) module.exports = api;
  else root.DiceCpu = api;
})(typeof self !== "undefined" ? self : this);
