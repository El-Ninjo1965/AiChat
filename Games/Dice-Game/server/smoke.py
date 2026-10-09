#!/usr/bin/env python3
"""Live smoke test for a deployed Dice Game.

Usage: python3 dice-game/server/smoke.py [--static-only] <base-url-ending-with-slash>

Checks that the served static files are byte-identical to the repository and
runs one short online game lifecycle against the real API (create, join,
full-game refusal, seat-hijack and stale-write refusal, close). The smoke game
is closed again at the end so it never stays in the public game list.
--static-only stops after the file checks (used for the raw account origin,
where the PHP handler of the dicegame.pntr.dev docroot does not apply).
--public is used for https://dicegame.pntr.dev/, which is proxied by Cloudflare:
Cloudflare may inject scripts into HTML responses, so HTML pages (including
the bare "/" start URL) must carry every element id and src/href of the
repository page instead of being byte-identical. All other files stay exact.
"""
import re
import json
import os
import secrets
import sys
import time
import urllib.error
import urllib.request

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
UA = {"User-Agent": "dice-game-ci-smoke"}
STATIC_FILES = ("index.html", "app.js", "cpu.js", "style.css", "sw.js", "manifest.webmanifest",
                "icon.svg", "icon-192.png", "icon-512.png", "icon-maskable-512.png")


def request(url, method="GET", body=None, retries=3, with_headers=False):
    data = None if body is None else json.dumps(body).encode()
    headers = dict(UA)
    if data is not None:
        headers["Content-Type"] = "application/json"
    last = None
    for attempt in range(retries):
        req = urllib.request.Request(url, data=data, method=method, headers=headers)
        try:
            with urllib.request.urlopen(req, timeout=30) as r:
                out = (r.status, r.read(), r.headers)
        except urllib.error.HTTPError as e:
            out = (e.code, e.read(), e.headers)
        except (urllib.error.URLError, TimeoutError) as e:
            last = e
            time.sleep(5 * (attempt + 1))
            continue
        return out if with_headers else out[:2]
    raise SystemExit(f"FAIL {method} {url}: {last}")


def api(base, query, method="GET", body=None):
    status, raw = request(base + "api/game.php?" + query, method, body)
    try:
        return status, json.loads(raw)
    except ValueError:
        raise SystemExit(f"FAIL {query}: HTTP {status}, not JSON: {raw[:200]!r}")


def html_fingerprint(html):
    text = html.decode("utf-8", "replace")
    return set(re.findall(r'\b(?:id|src|href)="[^"]*"', text)) | set(re.findall(r"<title>[^<]*</title>", text))


def html_matches(local, live):
    """Return (ok, detail). Exact match, or every id/src/href/title of the repository page present live."""
    if live == local:
        return True, "identical"
    missing = sorted(html_fingerprint(local) - html_fingerprint(live))
    if missing:
        return False, "missing " + ", ".join(missing[:8])
    i = next((n for n, (a, b) in enumerate(zip(local, live)) if a != b), min(len(local), len(live)))
    injected = live[i:i + 160].decode("utf-8", "replace")
    return True, f"same page, proxy-modified at byte {i}: {injected!r}"


def check(cond, msg):
    if not cond:
        raise SystemExit("FAIL " + msg)
    print("OK " + msg)


def main():
    args = sys.argv[1:]
    static_only = "--static-only" in args
    public = "--public" in args
    args = [a for a in args if a not in ("--static-only", "--public")]
    if len(args) != 1 or not args[0].endswith("/"):
        raise SystemExit("usage: smoke.py [--static-only|--public] <base-url-ending-with-slash>")
    base = args[0]
    rev = os.environ.get("GITHUB_SHA") or secrets.token_hex(6)

    for f in (("",) if public else ()) + STATIC_FILES:
        status, live, headers = request(f"{base}{f}?v={rev}", with_headers=True)
        with open(os.path.join(ROOT, f or "index.html"), "rb") as fh:
            local = fh.read()
        if public and (f == "" or f.endswith(".html")):
            ok, detail = html_matches(local, live)
            check(status == 200 and ok, f"{base}{f} serves the deployed start page (HTTP {status}, {detail})")
            continue
        check(status == 200 and live == local, f"{f} served and identical to repository (HTTP {status})")
        if f.endswith(".js"):
            ctype = headers.get("Content-Type", "")
            check("javascript" in ctype, f"{f} served with a JavaScript MIME type ({ctype})")

    manifest = json.loads(open(os.path.join(ROOT, "manifest.webmanifest"), encoding="utf-8").read())
    sizes = {i.get("sizes") for i in manifest["icons"] if i.get("type") == "image/png"}
    check({"192x192", "512x512"} <= sizes and manifest.get("display") == "standalone"
          and manifest.get("start_url") and manifest.get("scope") == "./", "manifest meets PWA installability fields")

    if static_only:
        return

    status, j = api(base, "action=list")
    check(status == 200 and j.get("ok") is True, f"list endpoint ({len(j.get('games', []))} active games)")

    status, j = api(base, "action=join&code=ZZZZZZ", "POST", {"client_id": "0" * 32, "name": "smoke"})
    check(status == 404 and j.get("error") == "not_found", "join endpoint rejects unknown game")

    status, j = api(base, "action=duel", "POST", {"client_id": "0" * 32, "username": "Pat"})
    check(status == 401 and j.get("error") == "login_required", "1-vs-1 invitation requires a login (no clientId-only invite)")

    status, j = api(base, "action=presence", "POST", {"lobby": True})
    check(status == 401, "lobby presence list requires a login")

    host, guest, late = (secrets.token_hex(16) for _ in range(3))
    seat = lambda cid=None: {"name": "", "nameMode": "auto", "scores": {}, "fiveCount": 0, "pendingZero": False, "entries": 0, **({"clientId": cid} if cid else {})}
    state = {"turn": 0, "start": 0, "started": False, "players": [seat(host), seat()]}
    status, j = api(base, "action=create", "POST", {"state": state, "dice_mode": "digital", "client_id": host})
    check(status == 200 and j.get("ok") and j.get("code") and j.get("host_token"), "create online game")
    code, token = j["code"], j["host_token"]
    try:
        status, j = api(base, f"action=join&code={code}", "POST", {"client_id": late, "name": "L"})
        check(status == 403 and j.get("error") == "identity_reserved", "guest cannot join as reserved account L")

        status, j = api(base, f"action=join&code={code}", "POST", {"client_id": guest, "name": "Guest"})
        check(status == 200 and j.get("seat") == 1 and j.get("full") is True, "second client joins free seat; game now full")
        game = j["game"]
        check(isinstance(game["state"]["players"][0]["scores"], dict), "empty score map stays a JSON object")
        check(game["state"]["players"][0].get("clientId") != host, "other seats' client ids are not disclosed")

        status, j = api(base, f"action=join&code={code}", "POST", {"client_id": late, "name": "Late"})
        check(status == 409 and j.get("error") == "game_full", "third client refused: game_full")

        status, j = api(base, f"action=list&client={host}")
        row = next((g for g in j.get("games", []) if g.get("game_code") == code), None)
        check(row is not None and row.get("full") is True and row.get("mine") is True, "list marks smoke game full (2/2)")

        status, j = api(base, f"action=start&code={code}", "POST", {"client_id": guest})
        check(status == 403 and j.get("error") == "not_host", "only the host can start the game")
        status, j = api(base, f"action=start&code={code}", "POST", {"client_id": host})
        check(status == 200 and j.get("ok") and j["game"]["state"].get("locked") is True, "host starts the game; roster locked")
        game = j["game"]

        st = game["state"]
        st["players"][0]["scores"] = {"ones": "3"}
        st["players"][0]["entries"] = 1
        st["turn"] = 1
        status, j = api(base, f"action=update&code={code}", "POST", {"state": st, "version": game["version"], "client_id": late})
        check(status == 403 and j.get("error") == "not_participant", "update from a non-participant is refused")
        status, j = api(base, f"action=update&code={code}", "POST", {"state": st, "version": game["version"], "client_id": guest})
        check(status == 403 and j.get("error") == "not_your_turn", "update for another player's turn is refused")
        status, j = api(base, f"action=update&code={code}", "POST", {"state": st, "version": game["version"], "client_id": host})
        check(status == 200 and j.get("ok"), "score update accepted")
        version = j["version"]

        lost = json.loads(json.dumps(st))
        lost["players"][0]["scores"] = {}
        status, j = api(base, f"action=update&code={code}", "POST", {"state": lost, "version": version, "client_id": host})
        check(status in (403, 409), "update that drops a stored score is refused")

        status, j = api(base, f"action=update&code={code}", "POST", {"state": st, "version": version - 1, "client_id": host})
        check(status == 409, "stale version is refused")

        hijack = json.loads(json.dumps(st))
        hijack["players"][1]["clientId"] = late
        status, j = api(base, f"action=update&code={code}", "POST", {"state": hijack, "version": version, "client_id": guest})
        check(status in (403, 409), "seat hijack via update is refused")

        status, j = api(base, f"code={code}")
        check(status == 200 and j["game"]["state"]["players"][0]["scores"].get("ones") == "3", "server state keeps the score")
    finally:
        status, j = api(base, f"action=close&code={code}", "POST", {"host_token": token})
        check(status == 200 and j.get("ok"), "smoke game closed")


if __name__ == "__main__":
    main()
