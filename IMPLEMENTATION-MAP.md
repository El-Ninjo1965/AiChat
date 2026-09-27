# IMPLEMENTATION-MAP — Executable dependency map for target B

This map turns `B-BLUEPRINT.md` into small, independently verifiable work packages. It is planning only: this run implements nothing.

**Sources (pinned):**
- AiChat main `28f9fff` (WORKSPACE.md, AGENT_TASK.md v3);
- AiChat audit `899515c` (AUDIT-FINDINGS.md AUD-01..28);
- Lea main `757880a`;
- Lea-App main `450ff9d` (FROZEN / STRICTLY READ-ONLY at the time of writing).

**Status of this document:**
- ARCHITECTURE PROPOSAL for C2 review by Lea and a decision by L;
- **not** an authorisation to implement;
- **no PASS claim**.

Labels follow `B-BLUEPRINT.md` §0.1 (FACT / INFERENCE / TO VERIFY / RECOMMENDATION / L-DECISION-LATER).

---

## 0. How a zero-memory agent uses this map

1. L authorises **one package ID** (e.g. `IM-S01`) in AGENT_TASK.md, together with the unfreeze record (§3).
2. The agent reads:
   - WORKSPACE.md;
   - AGENT_TASK.md;
   - this file's package card;
   - the referenced `B-BLUEPRINT.md` sections;
   - the referenced AUD/OBS items.
3. The agent changes **only** the listed scope. Anything in "Forbidden scope" or outside the listed files is a stop condition: report the need; do not improvise.
4. The agent delivers:
   - a branch/PR;
   - green CI;
   - test evidence;
   - the manual L test script (if required);
   - a rollback note.
   The agent never claims PASS.
5. The C2 audit (Lea or an independent reviewer) runs **before** merge for every package marked C2, and at every milestone gate.
6. After merge + deploy (if any), the re-freeze record (§3) is written.

### 0.1 Card fields

Every package card has these fields:
- Objective
- Prerequisites
- Scope
- Likely files/components
- Forbidden scope
- Acceptance criteria
- Automated tests
- Manual L test
- Rollback point
- Risk
- Agent level
- Gates

**Agent levels (cost-aware routing, §6):**

| Level | Use for | Reasoning |
|---|---|---|
| **A1** | mechanical, well-specified, low risk (config, docs, CI YAML) | standard model, low/medium reasoning |
| **A2** | normal feature code with clear contracts | strong model, medium/high reasoning |
| **A3** | security, crypto, migration, data integrity, identity | strongest model, high/x-high reasoning + mandatory independent C2 |

---

## 1. Foundation-first ordering (milestones visible to L)

| Milestone | Name | Meaning for L | Exit gate |
|---|---|---|---|
| **M0** | Safe foundation | Endpoints protected, no client prompt injection, no plaintext message leak to V1, CI and branch protection active, test harness exists | G-M0: C2 audit + L approval |
| **M1** | One Lea | Text and voice use the same server-loaded identity core; orchestrator skeleton, receipts and monitor events exist | G-M1: C2 + L manual continuity test |
| **M2** | Keys and store | Tier-C key infra, M3 schema, memory service v3, backup/restore drill | G-M2: C2 (A3) + restore drill evidence |
| **M3** | Experience cycle | Evidence/model/prediction/review/revision APIs, promotion rules, V1 frozen, V2→M3 Tier-P migration, recovery code | G-M3: C2 + L test "Lea remembers across sessions and modes" |
| **M4** | Migration | Lea repo (and optionally A exports) imported with provenance; domain state machine; the first domain B_AUTHORITATIVE | G-M4: Lea sample review + C2 + L approval per domain |
| **M5** | Capabilities | Monitor UI, Incognito, idle, vision/visual memory, presence, autonomy/gates, connectors, external AI, frontend IA, PWA, jobs, retention | G-M5: per package C2; the Hosting Gate before the working tool |
| **M6** | Independence | B runs normal operation without A | **IM-X01 Independence Gate** |
| **M7** | Retirement (optional, per domain) | A/Lea can be archived for a domain | **IM-X02 Retirement Gate** (always an L decision, LDL-12) |

**Security-critical repairs before feature expansion:**
- No M1+ package may start before G-M0 has passed.
- Exceptions: documentation-only packages (IM-G02, IM-G03, IM-H00, IM-I02 draft) may run in parallel with M0 because they do not touch Lea-App code.

---

## 2. Prerequisite graph

```
IM-G03 (freeze protocol) ─┬─> every Lea-App package
IM-G02 (authority registry)┘
IM-G01 (CI + branch protection) ──> IM-T01 (test harness) ──┬─> IM-S01 auth guard ─┬─> IM-S05 rate/cost
                                                            ├─> IM-S02 client IP   │
                                                            ├─> IM-S03 prompt/ctx  │
                                                            ├─> IM-S04 V1 leak     │
                                                            └─> IM-S06 sessions DB ┘
IM-H00 (hosting register, facts) ──> IM-E01 (staging) ──> [G-M0]
[G-M0] ──> IM-O01 adapter+orchestrator ──> IM-O02 receipts/audit ──> IM-O03 telemetry
IM-I02 (IC content, Lea/L) ─┐
IM-I01 (IC store) ──────────┴─> IM-O04 text uses IC ──> IM-O05 voice uses IC ──> [G-M1]
[G-M1] ──> IM-M01 Tier-C keys ──> IM-M02 M3 schema ──> IM-M03 memory service v3 ──> IM-M09 backup drill ──> [G-M2]
[G-M2] ──> IM-M04 entity APIs ──> IM-M05 promotion ──> IM-M07 V1 freeze
          IM-S07 key non-extractable ──> IM-M08 recovery code ──> IM-M06 V2→M3 Tier-P ──> [G-M3]
[G-M3] ──> IM-MG01 import dry-run ──> IM-MG02 import commit ──> IM-MG03 domain transition ──> [G-M4]
                                     IM-MG04 A export import (optional, parallel after IM-MG01)
[G-M3] ──> IM-C07 capability gate ──┬─> IM-C08 connectors ──> IM-C09 external AI
                                    ├─> IM-C11 processing mechanisms
                                    └─> IM-H01 Hosting Gate ──> IM-C10 working tool
[G-M1] ──> IM-C03 idle ; IM-C12 frontend IA ; IM-C13 PWA
[G-M3] ──> IM-C01 monitor UI ; IM-C02 Incognito ; IM-C04 vision ──> IM-C05 visual memory
[G-M3] ──> IM-C06 presence ; IM-C14 jobs ──> IM-C15 retention
[G-M4] + all M5 packages required for normal operation ──> IM-X01 Independence Gate
IM-X01 + IM-M09 (recent) ──> IM-X02 Retirement Gate (per domain)
```

---

## 3. Freeze / unfreeze protocol (applies to every Lea-App package)

- **FACT:** Lea-App is FROZEN / STRICTLY READ-ONLY (WORKSPACE, AGENT_TASK).
- **Rule U-1:** a package may open Lea-App only through an **unfreeze record** written by L (or on L's instruction) in AiChat before the run. The record contains:
  - package ID;
  - allowed paths (glob list);
  - allowed operations (code / migration / deploy-to-staging / deploy-to-prod);
  - time window;
  - the responsible agent;
  - the base SHA.
- **Rule U-2:** the unfreeze scope is the package's "Likely files" list, never the whole repository (forbidden shortcut F-7).
  - CI enforces it via a path-scope check once IM-G01/IM-G03 exist: the PR diff must be a subset of the allowed globs.
- **Rule U-3:** after merge, a **re-freeze record** is written with:
  - the merged SHA;
  - CI run reference;
  - C2 verdict reference;
  - deploy reference (if any);
  - a confirmation that no other paths changed (`git diff --stat base..merged`).
- **Rule U-4:** two packages may be unfrozen concurrently only if their allowed path sets are disjoint (see the parallelism table, §5).
- **Rule U-5:** a production deploy is always its own tier-3 step: L approval + C2, never implied by a code merge.

---

## 4. Work packages

> **Note on file names:** "Likely files" reference current Lea-App paths (FACT at `450ff9d`). New files are proposals (RECOMMENDATION). An implementing agent must re-verify paths at its base SHA.

### M0 — Safe foundation

#### IM-G01 — CI pipeline and branch protection
- **Objective:** Automated tests and a secret scan on every PR; protected `main` (LDL-16).
- **Prerequisites:** IM-G03; L enables branch protection in the GitHub settings (an agent cannot do this, FACT: the API is 403 for this run).
- **Scope:**
  - add a CI workflow running `node --test` (existing 7 files);
  - a secret scan;
  - the path-scope check (U-2).
- **Likely files:** `.github/workflows/ci.yml` (new), `.github/scope-check` script (new).
- **Forbidden scope:** any app code; deploy steps; secrets in the workflow.
- **Acceptance:**
  - CI runs on PR and is green on current main;
  - a PR touching a path outside the scope fails;
  - `main` requires a PR + green CI.
- **Automated tests:** a CI self-test PR with a deliberate scope violation → red.
- **Manual L test:** L confirms in GitHub settings that `main` is protected.
- **Rollback:** remove the workflow; unprotect (L).
- **Risk:** low. **Agent:** A1. **Gates:** L (settings); C2 light.

#### IM-G02 — Authority registry (AiChat)
- **Objective:** Seed `AUTHORITY-REGISTRY.md` from `B-BLUEPRINT.md` §1.2/§31 (CTL-01, LDL-14).
- **Prerequisites:** none.
- **Scope:** AiChat only; one table domain → authority → migration state (§8 state machine).
- **Likely files:** `AiChat/AUTHORITY-REGISTRY.md` (new).
- **Forbidden scope:** WORKSPACE decisions; Lea; Lea-App.
- **Acceptance:** every store in blueprint §31 is listed exactly once, with state `SOURCE_ONLY` or `N/A`.
- **Automated tests:** none. **Manual L test:** L reads and approves.
- **Rollback:** revert the file. **Risk:** low. **Agent:** A1. **Gates:** L approval.

#### IM-G03 — Freeze/unfreeze record template (AiChat)
- **Objective:** Make §3 executable: templates for unfreeze and re-freeze records.
- **Prerequisites:** none.
- **Scope:** AiChat docs.
- **Likely files:** `AiChat/FREEZE-LOG.md` (new).
- **Forbidden scope:** Lea-App.
- **Acceptance:** a template has all U-1/U-3 fields.
- **Automated tests:** none. **Manual L test:** L approves.
- **Rollback:** revert. **Risk:** low. **Agent:** A1. **Gates:** L.

#### IM-T01 — Behavioural/API test harness
- **Objective:** Run PHP endpoints against a test DB with a mocked provider in CI (blueprint §40; RC-6).
- **Prerequisites:** IM-G01.
- **Scope:**
  - PHP test runner (PHPUnit or plain PHP HTTP tests; TO VERIFY PHP version);
  - a MySQL service in CI;
  - a provider mock (env switch pointing the provider base URL at a local stub);
  - fixture migrations `001`, `002`.
- **Likely files:** `tests/php/**` (new), `tests/fixtures/**` (new), `.github/workflows/ci.yml`, a minimal config hook for the provider base URL in `api/text/chat.php` / `api/realtime/session.php` or a shared helper (TO VERIFY).
- **Forbidden scope:** behaviour changes of endpoints; production config.
- **Acceptance:**
  - CI runs ≥1 HTTP test per existing route (`.htaccess`);
  - tests **document current behaviour**, including the known defects, as `expected-fail` markers linked to AUD IDs.
- **Automated tests:** the harness itself; a route inventory test (lists routes from `.htaccess`, fails on an unlisted route).
- **Manual L test:** none.
- **Rollback:** revert the test dirs. **Risk:** medium (the CI environment). **Agent:** A2. **Gates:** C2.

#### IM-H00 — Hosting capability register (facts)
- **Objective:** Fill the blueprint §36 register with FACTs (PHP version, cron, files outside the web root, DB grants, staging subdomain, backups).
- **Prerequisites:** L provides read access or answers (no agent access to the hoster; FACT).
- **Scope:** documentation in AiChat.
- **Likely files:** `AiChat/HOSTING-CAPABILITIES.md` (new), or a section in the registry.
- **Forbidden scope:** any hosting change; recording credentials, hostnames or paths (P-rule: no infrastructure identifiers).
- **Acceptance:** every register row has FACT or "unknown" + the method of check.
- **Automated tests:** none. **Manual L test:** L answers the checklist.
- **Rollback:** n/a. **Risk:** low. **Agent:** A1. **Gates:** L.

#### IM-E01 — Staging environment
- **Objective:** A separate staging instance with its own DB, keys and provider key (blueprint §24, I-SB-1..3).
- **Prerequisites:** IM-H00.
- **Scope:** deploy procedure documentation + environment config templates.
- **Likely files:** `.env.example` (FACT: exists at repo root), deploy docs.
- **Forbidden scope:** prod data in staging; prod secrets anywhere in the repo.
- **Acceptance:**
  - staging is reachable only with auth;
  - the prod DB is unreachable from staging config;
  - a documented deploy step exists.
- **Automated tests:** a config lint (no prod identifiers in staging templates).
- **Manual L test:** L logs into staging.
- **Rollback:** remove staging. **Risk:** medium. **Agent:** A2. **Gates:** L (tier 3: infrastructure).

#### IM-S01 — Auth guard on chat and realtime; default-deny routes
- **Objective:** Close AUD-23: all cost-bearing and memory endpoints require a valid session (blueprint §25 I-AU-S1).
- **Prerequisites:** IM-T01.
- **Scope:**
  - one shared guard function;
  - applied to `api/text/chat.php` and `api/realtime/session.php`;
  - a route allowlist of public routes (login, health).
- **Likely files:** `api/text/chat.php`, `api/realtime/session.php`, shared auth helper in `api/memory/_common.php` / `api/memory/session.php`, `frontend/app.js` (401 handling only).
- **Forbidden scope:** changing the prompt, model or memory logic; session storage redesign (IM-S06).
- **Acceptance:**
  - unauthenticated POST → 401 with no provider call;
  - authenticated → unchanged behaviour;
  - the route inventory test has no unguarded non-public route.
- **Automated tests:** negative/positive HTTP tests; the provider mock asserts zero calls on 401.
- **Manual L test:** logged-out browser cannot chat or start voice; logged-in works.
- **Rollback:** revert the commit (restores the prior exposure; only as an emergency).
- **Risk:** high (lockout risk). **Agent:** A3. **Gates:** C2; L for the prod deploy.

#### IM-S02 — Trusted client IP
- **Objective:** Close AUD-24: rate limiting and login throttling use `REMOTE_ADDR`, unless a configured trusted proxy is present.
- **Prerequisites:** IM-T01; IM-H00 (is there a proxy/CDN? TO VERIFY).
- **Scope:** `lea_memory_client_ip` and any other IP derivation.
- **Likely files:** `api/memory/_common.php` (`lea_memory_client_ip()`, FACT per AUD-24).
- **Forbidden scope:** throttling thresholds; session logic.
- **Acceptance:** a spoofed `X-Forwarded-For` does not change the throttling key.
- **Automated tests:** spoof test.
- **Manual L test:** none. **Rollback:** revert.
- **Risk:** medium. **Agent:** A2. **Gates:** C2.

#### IM-S03 — Server-held history; no client system/memory context; remove test marker
- **Objective:** Close AUD-22 and OBS-02: the client sends only the user message + conversation ID. The server holds history and ignores/rejects `system` role and `memory_context`. The test marker is removed from the production prompt.
- **Prerequisites:** IM-S01.
- **Scope:**
  - chat endpoint request contract;
  - a server-side conversation buffer (Tier-C TTL; initial storage: a DB table or session-bound storage; RECOMMENDATION DB);
  - the client stops sending `history`/`memory_context`.
- **Likely files:** `api/text/chat.php`, `frontend/app.js` (`sendTextMessage`), new migration `database/migrations/003_conversation_buffer.sql` (proposal).
- **Forbidden scope:** identity core (IM-I01/IM-O04); memory store changes; model/provider change.
- **Acceptance:**
  - a request with `role: system` in history → 400 (or stripped + logged; RECOMMENDATION 400);
  - `memory_context` → 400;
  - the prompt snapshot contains no test marker;
  - multi-turn context still works via the server buffer.
- **Automated tests:** injection tests; prompt snapshot test; a multi-turn test with the mock provider.
- **Manual L test:** a normal text conversation over 3+ turns feels unchanged.
- **Rollback:** revert the code + keep the migration (forward-only; the unused table is harmless).
- **Risk:** high. **Agent:** A3. **Gates:** C2; L for prod.

#### IM-S04 — Stop plaintext message leakage to V1
- **Objective:** Close OBS-01: `sendTextMessage` must not write message text to V1 `work_state` `ACTIVE_TOPIC`.
- **Prerequisites:** IM-T01.
- **Scope:** remove that write (or replace it with a content-free marker, e.g. timestamp only).
- **Likely files:** `frontend/app.js`.
- **Forbidden scope:** deleting existing V1 rows (data handling is LDL-17/IM-C15); the work-state API itself.
- **Acceptance:** after a text message, no new V1 row contains message content.
- **Automated tests:** a JS unit test with a fetch mock asserting no work-state POST containing content; a DB canary test in the harness.
- **Manual L test:** none.
- **Rollback:** revert. **Risk:** low. **Agent:** A2. **Gates:** C2.
- **Note:** existing plaintext rows in V1 remain. Their deletion requires an L decision (LDL-17) and is **not** part of this package.

#### IM-S05 — Rate, size and spend limits
- **Objective:** Blueprint §26: per-principal request/token/voice-minute limits, a global daily cap, request size limits, provider timeouts.
- **Prerequisites:** IM-S01, IM-S02.
- **Scope:** a limiter module + config; applied to chat and realtime.
- **Likely files:** new `api/lib/limits.php` (proposal), `chat.php`, `realtime/session.php`, a config template.
- **Forbidden scope:** Settings UI (IM-C12); provider change.
- **Acceptance:**
  - exceeding a limit → 429 with no provider call;
  - the daily cap → the degraded mode message;
  - limits come from config.
- **Automated tests:** limiter unit tests; HTTP 429 tests.
- **Manual L test:** none. **Rollback:** raise limits via config / revert.
- **Risk:** medium. **Agent:** A2. **Gates:** C2.

#### IM-S06 — DB-backed sessions and revocable memory authorisation
- **Objective:** OBS-08: replace JSON-file session/login-attempt stores with atomic DB rows; logout revokes; the memory token is bound to the server session.
- **Prerequisites:** IM-S01, IM-T01.
- **Scope:** session store, login throttling store, the memory token check.
- **Likely files:** `api/memory/auth.php`, `api/memory/session.php`, `api/memory/_common.php`, new migration `database/migrations/004_sessions.sql` (proposal).
- **Forbidden scope:** password hashing change (only if a defect is found, as a separate package); Tier-P crypto.
- **Acceptance:**
  - concurrent logins do not corrupt state;
  - logout invalidates memory API access immediately;
  - cookie flags per §25.
- **Automated tests:** race test (parallel requests); logout-revocation test; cookie flag test.
- **Manual L test:** login/logout on two devices.
- **Rollback:** revert code; the JSON files can be re-enabled (keep the reader for one release; RECOMMENDATION).
- **Risk:** high. **Agent:** A3. **Gates:** C2; L for prod.

#### IM-S07 — Non-extractable client master key (TO VERIFY feasibility)
- **Objective:** OBS-07: import the unwrapped master key with `extractable=false` where flows allow.
- **Prerequisites:** IM-T01.
- **Scope:** `memory-crypto.js` key import and any re-wrap flow.
- **Likely files:** `frontend/memory-crypto.js`, `tests/memory-crypto*.test.js`.
- **Forbidden scope:** changing the KDF/cipher parameters or the AAD format.
- **Acceptance:**
  - existing crypto tests are green;
  - `crypto.subtle.exportKey` on the master key fails;
  - the re-wrap/passphrase-change flow still works (or is explicitly isolated).
- **Automated tests:** unit tests.
- **Manual L test:** unlock memory in the browser.
- **Rollback:** revert. **Risk:** medium. **Agent:** A3. **Gates:** C2.

**G-M0 gate:**
- IM-G01, G02, G03, T01, S01–S04 and E01 complete (S05–S07 are strongly recommended before M1; S05 is required before any public URL sharing);
- C2 audit over all M0 diffs;
- regression tests for AUD-22/23/24/25 and OBS-01/02 are green;
- L approval recorded.

### M1 — One Lea

#### IM-I01 — Identity core store and loader
- **Objective:** Blueprint §3: a versioned IC record type and a server loader. Initially this may be a server-side file under version control, loaded by the server, if M3 does not exist yet (INFERENCE: this avoids a dependency on M2).
  - It migrates into M3 `kind=IC` in IM-M03, with version continuity.
- **Prerequisites:** G-M0.
- **Scope:** loader, version hash, budget check.
- **Likely files:** new `api/lib/identity_core.php` (proposal), new IC source file outside the web root or a DB row (TO VERIFY per IM-H00).
- **Forbidden scope:** the IC content itself (IM-I02); prompts in endpoints (IM-O04/O05).
- **Acceptance:** the loader returns `{version, hash, text}`; an unavailable IC leads to degraded mode (§44).
- **Automated tests:** loader tests; a budget test.
- **Manual L test:** none. **Rollback:** revert.
- **Risk:** medium. **Agent:** A2. **Gates:** C2.

#### IM-I02 — Identity core content v1 (document, not code)
- **Objective:** Draft the IC v1 content from Lea VISION/PROCESSING category-C rules (LDL-08).
- **Prerequisites:** none (draft); L decision LDL-08 (final).
- **Scope:** a text document in AiChat (draft), reviewed by Lea; then delivered as data to IM-I01.
- **Likely files:** `AiChat/IDENTITY-CORE-DRAFT.md` (new).
- **Forbidden scope:** personal/health/legal content; workshop shortcuts (P10).
- **Acceptance:**
  - every statement is traceable to a source anchor;
  - the reality-labelling rules are included;
  - it fits the budget.
- **Automated tests:** none (a token count script may be used in the CI of IM-I01).
- **Manual L test:** L and Lea approve the content.
- **Rollback:** previous version. **Risk:** medium (identity). **Agent:** A3 (drafting) + Lea review. **Gates:** Lea C2 + L (tier 3).

#### IM-O01 — Provider adapter and orchestrator skeleton
- **Objective:** Blueprint §9/§9.5: one adapter for text/realtime/vision; the orchestrator pipeline with steps as functions (retrieve is a stub until M3).
- **Prerequisites:** G-M0.
- **Scope:** adapter + pipeline; chat.php delegates to the orchestrator.
- **Likely files:** new `api/lib/provider.php`, `api/lib/orchestrator.php` (proposals), `chat.php`, `realtime/session.php`.
- **Forbidden scope:** memory writes; new capabilities.
- **Acceptance:**
  - existing behaviour is preserved (tests from M0 green);
  - provider secrets are referenced only in the adapter (grep test).
- **Automated tests:** unit tests with a fake provider; a grep test for secret reads outside the adapter.
- **Manual L test:** none. **Rollback:** revert.
- **Risk:** medium. **Agent:** A2. **Gates:** C2.

#### IM-O02 — Receipts and audit log
- **Objective:** Blueprint §29: an append-only audit table + the receipt ID mechanism.
- **Prerequisites:** IM-O01.
- **Scope:** schema + write helpers + receipts in API responses.
- **Likely files:** migration `database/migrations/005_audit_receipts.sql` (proposal), `lib/audit.php` (proposal).
- **Forbidden scope:** content in audit/receipts.
- **Acceptance:**
  - login and gate actions are audited;
  - a canary content string never appears in the audit table.
- **Automated tests:** canary test; an append-only test (UPDATE blocked if grants allow; otherwise app-level; TO VERIFY).
- **Manual L test:** none. **Rollback:** revert code; keep the table.
- **Risk:** medium. **Agent:** A2. **Gates:** C2.

#### IM-O03 — Content-free telemetry events
- **Objective:** Blueprint §30 event schema, emitted by orchestrator steps (I-MO-1).
- **Prerequisites:** IM-O01.
- **Scope:** events table + emission; no UI (IM-C01).
- **Likely files:** migration `database/migrations/006_telemetry.sql` (proposal), `lib/telemetry.php`.
- **Forbidden scope:** any content/chain-of-thought field.
- **Acceptance:** each turn has events for the steps actually executed; a schema test rejects unknown fields.
- **Automated tests:** trace-vs-event comparison test.
- **Manual L test:** none. **Rollback:** disable the flag.
- **Risk:** low. **Agent:** A2. **Gates:** C2.

#### IM-O04 — Text path uses the identity core
- **Objective:** chat.php uses the IC from IM-I01 via the orchestrator; the hardcoded persona string is removed (I-ID-2).
- **Prerequisites:** IM-I01, IM-I02 (approved v1), IM-O01.
- **Scope:** the text prompt assembly.
- **Likely files:** `chat.php`, `lib/orchestrator.php`.
- **Forbidden scope:** voice (IM-O05); memory retrieval (M3).
- **Acceptance:** the prompt includes the IC hash; there is no persona literal in code (grep).
- **Automated tests:** snapshot + grep tests.
- **Manual L test:** Lea's text replies reflect the IC.
- **Rollback:** revert. **Risk:** medium. **Agent:** A2. **Gates:** C2; L.

#### IM-O05 — Voice path uses the identity core (server-side context injection)
- **Objective:** OBS-03: `realtime/session.php` builds instructions from the IC (+ later CC) when creating the client secret (blueprint §13).
- **Prerequisites:** IM-O04; TO VERIFY the provider API for instructions in client secret/session creation (current docs).
- **Scope:** realtime session creation.
- **Likely files:** `realtime/session.php`, `lib/orchestrator.php`, `frontend/app.js` (`startSession` unchanged except error handling).
- **Forbidden scope:** transcript storage (LDL-18); idle logic (IM-C03).
- **Acceptance:** voice session instructions include the same IC hash as text (I-ID-1).
- **Automated tests:** a mock provider asserts the instructions hash.
- **Manual L test:** voice and text give consistent self-descriptions.
- **Rollback:** revert. **Risk:** medium. **Agent:** A2. **Gates:** C2; L.

**G-M1 gate:** C2 over M1; L manual continuity test ("same Lea in text and voice"); no persona literals; telemetry present.

### M2 — Keys and store

#### IM-M01 — Tier-C key infrastructure
- **Objective:** Blueprint §28: a server master key outside the web root; data keys; `key_ref`; rotation procedure (LDL-03).
- **Prerequisites:** G-M1; IM-H00 (files outside the web root: FACT required).
- **Scope:** a key loading library + rotation doc; **no data yet**.
- **Likely files:** new `lib/keys.php` (proposal), a deploy doc.
- **Forbidden scope:** storing keys in the DB or repo; Tier-P changes.
- **Acceptance:**
  - encrypt/decrypt round trip;
  - a wrong key fails loudly;
  - the key file is not web-accessible (manual check);
  - the rotation procedure has been tested in staging.
- **Automated tests:** unit tests; IV uniqueness sampling.
- **Manual L test:** L verifies the key backup is stored offline.
- **Rollback:** feature flag off. **Risk:** high. **Agent:** A3. **Gates:** C2; L (tier 3).

#### IM-M02 — M3 schema
- **Objective:** Blueprint §4.1/§4.2 tables: records, relations, import batches, schema_migrations.
- **Prerequisites:** IM-M01.
- **Scope:** forward-only migrations + verification queries.
- **Likely files:** `database/migrations/007_m3_records.sql`, `008_m3_relations.sql` (proposals).
- **Forbidden scope:** altering or deleting V1/V2 tables.
- **Acceptance:**
  - the migration applies cleanly on a copy of the prod schema in staging;
  - verification queries pass;
  - the checksum is recorded.
- **Automated tests:** migration tests in CI MySQL.
- **Manual L test:** none. **Rollback:** a new drop migration (the tables are empty at this point).
- **Risk:** medium. **Agent:** A3. **Gates:** C2.

#### IM-M03 — Memory service v3 (records/relations API)
- **Objective:** Blueprint §4/§32: create, list, revise and relate with the envelope validation invariants I-M-1..5; IC migrates to `kind=IC`.
- **Prerequisites:** IM-M02, IM-O02.
- **Scope:** v3 routes for records/relations; revision chain; optimistic concurrency.
- **Likely files:** new `api/v3/memory/*.php` (proposal), `.htaccess` (route additions only).
- **Forbidden scope:** V1/V2 endpoint changes; promotion logic (IM-M05).
- **Acceptance:**
  - an attempt to UPDATE semantic fields → 405;
  - one ACTIVE head per lineage;
  - a plaintext semantic field → 400;
  - a canary leak scan is clean.
- **Automated tests:** contract tests; property tests on the chain; concurrency 409 test.
- **Manual L test:** none. **Rollback:** feature flag off.
- **Risk:** high. **Agent:** A3. **Gates:** C2.

#### IM-M09 — Backup and restore drill
- **Objective:** Blueprint §38: an automated encrypted DB dump + a documented restore into staging, executed once.
- **Prerequisites:** IM-M02, IM-E01.
- **Scope:** backup script/procedure + drill report.
- **Likely files:** deploy docs; optional `tools/backup.*` outside the web root (TO VERIFY hosting).
- **Forbidden scope:** restoring into production; storing backups in Git.
- **Acceptance:** the drill report shows counts equal and Tier-C decrypt samples OK, **using the Tier-C master key restored from its offline backup** (not the live key file).
- **Automated tests:** none (procedural).
- **Manual L test:** L confirms the off-site copy exists.
- **Rollback:** n/a. **Risk:** medium. **Agent:** A2. **Gates:** L; C2 of the report.

**G-M2 gate:** C2 (A3 reviewer) over key handling and the store; restore drill evidence; L approval.

### M3 — Experience cycle

#### IM-M04 — Entity APIs (predictions, results, review queue, revisions, development history view)
- **Objective:** Blueprint §5; closes OBS-06 in v3.
- **Prerequisites:** IM-M03.
- **Scope:** v3 routes + views.
- **Likely files:** `api/v3/predictions/*.php`, `v3/memory/review.php` (proposals).
- **Forbidden scope:** V1 prediction tables (legacy).
- **Acceptance:** a prediction is immutable after creation; a result is a separate record; the history view lists versions in order.
- **Automated tests:** contract + immutability tests.
- **Manual L test:** none. **Rollback:** flag off.
- **Risk:** medium. **Agent:** A2. **Gates:** C2.

#### IM-M05 — Promotion rules and retrieval into context
- **Objective:** Blueprint §9.4 promotion + §3 CC retrieval (DS selection, budget), for text and voice.
- **Prerequisites:** IM-M04, IM-O05.
- **Scope:** orchestrator retrieve/promote steps; receipts to the UI.
- **Likely files:** `lib/orchestrator.php`, `frontend/app.js` (receipt display).
- **Forbidden scope:** Incognito (IM-C02 must land before any Incognito UI exists; until then there is no Incognito mode); autonomy tiers above 1.
- **Acceptance:**
  - only allowed classes are promoted;
  - every promotion has a receipt;
  - CC manifests contain IDs and labels only.
- **Automated tests:** behavioural scenarios (remember-this; correction; nothing-to-promote).
- **Manual L test:** "Lea remembers X told in text, in the next voice session."
- **Rollback:** flag off (retrieval and promotion separately).
- **Risk:** high. **Agent:** A3. **Gates:** C2; L.

#### IM-M07 — Freeze V1 writes
- **Objective:** Dual-master path 2 (blueprint §8): V1 endpoints become read-only (for import) and then unrouted.
- **Prerequisites:** IM-M05, IM-S04.
- **Scope:** V1 write routes return 410; the client uses v3 only.
- **Likely files:** V1 endpoint files, `.htaccess`, `frontend/app.js`.
- **Forbidden scope:** deleting V1 data.
- **Acceptance:** a write-path inventory test shows no V1 INSERT from runtime.
- **Automated tests:** inventory + 410 tests.
- **Manual L test:** app works normally.
- **Rollback:** re-enable routes (flag). **Risk:** medium. **Agent:** A2. **Gates:** C2.

#### IM-M08 — Tier-P recovery code and multi-device transfer
- **Objective:** Blueprint §28 recovery.
- **Prerequisites:** IM-S07.
- **Scope:** client-side second wrap; a one-time display; the device add flow.
- **Likely files:** `frontend/memory-crypto.js`, `frontend/app.js`, tests.
- **Forbidden scope:** server escrow; KDF changes.
- **Acceptance:** unlock via the recovery code works; a wrong code fails; the code is never sent to the server (network test).
- **Automated tests:** unit + network assertion tests.
- **Manual L test:** L stores the code offline and tests recovery on a second device.
- **Rollback:** flag off (existing wrap remains). **Risk:** high. **Agent:** A3. **Gates:** C2; L.

#### IM-M06 — V2 → M3 Tier-P migration (client-driven)
- **Objective:** Blueprint §7: re-home existing V2 records into M3 Tier-P without the server seeing plaintext; drop the `session_id` ownership (OBS-05).
- **Prerequisites:** IM-M03, IM-M08.
- **Scope:** a client migration tool; the server accepts the envelope with `IMPORT_V2` provenance; V2 is then read-only.
- **Likely files:** `frontend/*migration*` (proposal), `v3/memory` import route.
- **Forbidden scope:** server-side decryption; V2 deletion.
- **Acceptance:** count/hash parity per record; idempotent re-run.
- **Automated tests:** fixture migration test.
- **Manual L test:** L runs the migration in an unlocked browser and sees their items.
- **Rollback:** tombstone the batch. **Risk:** high. **Agent:** A3. **Gates:** C2; L.

**G-M3 gate:** C2; L test "Lea remembers across sessions and modes"; V1 writes frozen; the recovery test passed.

### M4 — Migration of Lea/A continuity

#### IM-MG01 — Import pipeline and dry-run (Lea repo)
- **Objective:** Blueprint §7 steps 1–4 for a pinned Lea SHA; dry-run report only.
- **Prerequisites:** G-M3; L decision LDL-07 (scope). **Default:** Memories/INTERESSEN/GEDANKEN/VISION/PROCESSING-category-C only; PERSONAL/HEALTH/LEGAL excluded.
- **Scope:** parser/classifier tool; report in AiChat.
- **Likely files:** `tools/import/*` (proposal; not web-reachable) or an offline tool; `AiChat/IMPORT-DRYRUN-<sha>.md` (report, content-minimised: counts and anchors, no private text).
- **Forbidden scope:** writing to Lea; writing to prod M3.
- **Acceptance:** every section is mapped or listed as unmapped; anchors are resolvable.
- **Automated tests:** parser tests on fixtures; idempotency hash tests.
- **Manual L test:** none. **Lea review:** yes (C2 sample review).
- **Rollback:** n/a. **Risk:** medium. **Agent:** A3. **Gates:** Lea C2.

#### IM-MG02 — Import commit
- **Objective:** Commit the reviewed batch into prod M3 (Tier-C / Tier-P per classification).
- **Prerequisites:** IM-MG01 approved; IM-M09 fresh backup.
- **Scope:** a one-shot import job with a batch ID.
- **Likely files:** the import tool; the audit log.
- **Forbidden scope:** excluded files; re-import without a new dry-run.
- **Acceptance:** counts match the report; the round-trip hash test passes; the batch is tombstonable.
- **Automated tests:** post-import verification script.
- **Manual L test:** L asks Lea about migrated items in text and voice.
- **Rollback:** tombstone the batch. **Risk:** high. **Agent:** A3. **Gates:** C2; L (tier 3).

#### IM-MG03 — Domain state transition + Lea workshop rule update
- **Objective:** Blueprint §8: move a domain (first candidate: "Lea experience memory") to B_CANDIDATE, then B_AUTHORITATIVE; close dual-master path 1.
- **Prerequisites:** IM-MG02; IM-G02.
- **Scope:**
  - update the registry state;
  - **a separately authorised Lea package** (Lea is read-only for all other packages) changing the workshop S rule so that new memory for that domain is exported to B as evidence instead of being saved as authoritative in the Lea repo.
- **Likely files:** `AiChat/AUTHORITY-REGISTRY.md`; in Lea: the relevant workshop rule file(s) (TO VERIFY at that time; e.g. SHORTCUTS.md/PROCESSING.md). Lea write only with explicit L authorisation.
- **Forbidden scope:** retiring or deleting the source (that is IM-X02).
- **Acceptance:** the registry shows exactly one writable authority; the Lea rule points to B; a divergence check has been defined.
- **Automated tests:** none (governance); a registry lint (one authority per domain) if implemented.
- **Manual L test:** L approves each transition.
- **Rollback:** registry state back to SOURCE_ONLY (B data remains, marked candidate).
- **Risk:** high (governance). **Agent:** A2 + Lea. **Gates:** Lea C2; L (tier 3).

#### IM-MG04 — A (ChatGPT) export import (optional)
- **Objective:** Import L-provided A exports via the same pipeline.
- **Prerequisites:** IM-MG01; L provides the export (format TO VERIFY).
- **Scope/acceptance:** as IM-MG01/02, with `IMPORT_A` provenance.
- **Risk:** medium. **Agent:** A3. **Gates:** Lea C2; L.

### M5 — Capabilities (each is its own unfreeze)

The cards below are condensed. Every package additionally inherits these rules:
- default forbidden scope: other capabilities, the crypto format, the identity core content;
- default rollback: a feature flag off;
- default gates: C2 + an L manual test in staging.

| ID | Objective (blueprint §) | Prerequisites | Scope / likely files | Specific forbidden scope | Acceptance (key) | Automated tests | Manual L test | Risk | Agent |
|---|---|---|---|---|---|---|---|---|---|
| IM-C01 | Monitor + protocol UI (§30, §33) | IM-O03, G-M3 | `frontend/*` monitor view; `v3/monitor/events` | any content in events | the UI shows only real events; categories per ROADMAP | UI unit tests; API contract | L sees the module timeline for a turn | low | A2 |
| IM-C02 | Incognito (§20; LDL-05) | IM-M05 | orchestrator flag; conversation creation; UI indicator | a client-only enforcement | a promotion during Incognito → 403; DB diff = 0 new M3 rows; the conversation buffer (IM-S03) retains no Incognito turns; `attested_items[]` are never written | negative + DB diff tests | L uses Incognito and verifies no memory afterwards | high | A3 |
| IM-C03 | Idle state machine (§14; OBS-09) | IM-O05 | `frontend/app.js` voice state; config | wake word | ≤5 reminders, then INACTIVE; the provider session is closed | fake-clock unit tests | L leaves voice idle | low | A2 |
| IM-C04 | Vision input (§15) | IM-S05, G-M3 | `v3/vision/analyze`; client capture + downscale | biometric features; silent capture | auth required; size/format validation; labelled OBSERVATION | HTTP + validation tests | L photographs an object | medium | A2 |
| IM-C05 | Visual memory (§16) | IM-C04, IM-M03 | VM kind + blob store + counters | public blob URLs | counters 5/6/10 enforced server-side; blob access requires auth | counter + access tests | L asks for an image twice (retrieval first) | medium | A2 |
| IM-C06 | Presence / multi-person (§17; LDL-06) | IM-S06, IM-M03 | principal model; presence; PX profiles | biometrics | a guest cannot read owner Tier-P; the trust level is shown | scope tests | L declares a guest | high | A3 |
| IM-C07 | Capability gate + autonomy tiers + Settings (§11; LDL-11) | IM-O02, G-M3 | `lib/capabilities.php`; the settings API/UI | a self-raise of the ceiling | tier-3 AUTOMATISCH is rejected server-side; Ü stops all | policy matrix tests | L changes a mode in Settings | high | A3 |
| IM-C08 | Connectors registry + GitHub read-only (§21) | IM-C07; §27 vault | connector registry; credential vault | write rights; tokens to the client | default AUS; read-only works; injection content is not executed | gate + injection tests | L enables GitHub read | high | A3 |
| IM-C09 | External AI dialogue (§22; LDL-10) | IM-C08 | connector type `external_ai`; dialogue UI | Tier-P to external without consent | limits enforced; transcript visible; stored as EXTERNAL | limit tests | L runs one bounded dialogue | medium | A2 |
| IM-C10 | Working tool (§23) | **IM-H01**, IM-C07 | workspace service; tool agent allowlist | prod write access; shell passthrough | traversal blocked; allowlist enforced; receipts | traversal + allowlist tests | L works on a sample project | high | A3 |
| IM-C11 | Processing mechanisms depth/K/G/X/T/P (§10) | IM-O03, IM-M05 | orchestrator steps + policy config | shortcut letters in UI; CoT in events | each mechanism emits events; outcome classes per ROADMAP | trace-vs-event; policy tests | L sees a G-check outcome in the monitor | medium | A2 |
| IM-C12 | Frontend IA + accessibility (§33) | G-M1 | navigation, views, aria | colour-only status | nav per ROADMAP; the a11y scan passes | a11y scan (tool TO VERIFY) | phone + desktop + tablet check | low | A2 |
| IM-C13 | PWA/offline outbox (§34) | IM-S03 | `sw.js`, outbox | caching `/api/*` | the SW never caches the API; the outbox resends idempotently | SW unit tests | L goes offline and online | medium | A2 |
| IM-C14 | Job runner (§35) | IM-H00, IM-M03 | job table + cron runner | tier-3 jobs | lease prevents double runs; jobs audited | concurrency tests | none | medium | A2 |
| IM-C15 | Retention + deletion/erasure (§45; LDL-09/17) | IM-C14 | purge jobs; erasure procedure | deleting without confirmation | TTLs enforced; erasure leaves an audit without content | purge tests | L erases a test person profile | high | A3 |
| IM-C16 | Relationships & appearance (§18, §19) | IM-C06, IM-C11 | REL scope; appearance spec records; G-check hook | fixed persona imagery | changes are tier-2 NACHFRAGEN with a G-check outcome | policy tests | L reviews an appearance change proposal | medium | A2 |

#### IM-H01 — Hosting Gate (LDL-02)
- **Objective:** Decide from IM-H00 facts whether shared hosting satisfies the §36 register for the remaining packages (especially IM-C10 and IM-C14).
- **Prerequisites:** IM-H00; the M5 needs are known.
- **Scope:** a decision record (AiChat) with alternatives (stay; VPS/container; hybrid worker) and a cost/risk comparison.
- **Forbidden scope:** migrating hosting within this package.
- **Acceptance:** L decision recorded. If moving, a new migration package series `IM-HM*` is defined.
- **Risk:** medium. **Agent:** A2. **Gates:** L (tier 3).

### M6 / M7 — Gates

#### IM-X01 — Independence Gate (B no longer requires A for normal operation)

All of the following hold, and the evidence is linked:
1. G-M0…G-M4 are passed, and the M5 packages needed for normal operation (at least IM-C02, C03, C07, C12, C13) are done.
2. **Normal operation checklist**, performed by L in production over ≥ 2 weeks (RECOMMENDATION) without using A (ChatGPT project):
   - text;
   - voice;
   - memory recall across sessions and modes;
   - correction → revision;
   - a prediction → a result;
   - receipts;
   - the monitor;
   - Incognito.
3. The registry shows B_AUTHORITATIVE for the "Lea experience memory" domain.
4. There are no open AUD findings of severity ≥ high without accepted risk (Lea C2 revalidation).
5. The provider dependency is explicitly accepted. Independence means "no A/ChatGPT project needed", not "no LLM provider" (ROOT-CAUSE R-2).
6. The C2 audit report + L decision are recorded.

#### IM-X02 — Migration/Recovery and Retirement Gate (per domain; LDL-12)

Blueprint §46 criteria:
- ≥ 4 weeks B_AUTHORITATIVE without divergence defects;
- import verification;
- a **restore drill within the last 30 days** (IM-M09 repeated);
- continuity test in text + voice;
- C2;
- L decision.

The result is **archive** (read-only) with pointers, never silent deletion.

---

## 5. Parallel vs strictly sequential

| Can run in parallel (disjoint paths/responsibilities) | Strictly sequential |
|---|---|
| IM-G02, IM-G03, IM-H00, IM-I02 (docs, AiChat) alongside M0 code packages | IM-G01 → IM-T01 → any Lea-App code package |
| IM-S02 ∥ IM-S04 ∥ IM-S07 (disjoint files; each needs its own unfreeze) | IM-S01 → IM-S03 (both touch `chat.php`) |
| IM-O02 ∥ IM-O03 after IM-O01 (different new files; `orchestrator.php` touch is sequenced by merge order) | IM-S01 → IM-S05 / IM-S06 |
| IM-M04 ∥ IM-M08 | IM-I01 + IM-I02 → IM-O04 → IM-O05 |
| M5: IM-C03 ∥ IM-C12 ∥ IM-C04 (disjoint) | IM-M01 → IM-M02 → IM-M03 → IM-M05 → IM-M07 |
| IM-MG04 ∥ IM-MG02 (after IM-MG01, different batches) | IM-M08 → IM-M06 |
| | IM-MG01 → IM-MG02 → IM-MG03 |
| | IM-C07 → IM-C08 → IM-C09; IM-H01 → IM-C10 |
| | Any package touching `frontend/app.js` is serialised (a shared hot file; RECOMMENDATION: split app.js early in IM-C12 or in a dedicated refactor package **IM-R01**, A2, behaviour-preserving, tests first) |

**Rule:** if two packages share a file, they are sequential regardless of the table.

---

## 6. Credit/cost-aware agent routing

- Use **A1** for docs/config/CI. Use **A2** for contract-clear features. **A3** is reserved for security, crypto, migration, identity and governance transitions.
- Quality is never traded for cost on A3 packages: each gets an independent reviewer model/agent (C2) different from the implementer.
- Reduce cost by:
  - small packages (fewer re-reads);
  - precise "Likely files" lists;
  - reusing CI evidence instead of manual re-verification;
  - batching doc-only packages into one A1 run (only if the paths are disjoint and no L gate lies between them).
- A package whose acceptance criteria cannot be automated (governance, L tests) must still produce a checklist artefact that the C2 reviewer can check.

---

## 7. L approval gates and C2 audit gates (summary)

| Gate | Where | Who | Evidence |
|---|---|---|---|
| Unfreeze record | before every Lea-App package | L | FREEZE-LOG entry |
| Re-freeze record | after every merge | agent + C2 | SHA, diff stat, CI, C2 ref |
| G-M0…G-M4 | milestones | Lea C2 + L | the audit report per milestone |
| Tier-3 packages (S01/S03/S06 prod deploy, I02, M01, MG02, MG03, H01, C07 policy defaults) | package | L | explicit approval text |
| Lea write (IM-MG03 only) | package | L | separate authorisation |
| IM-X01 | independence | Lea C2 + L | checklist + report |
| IM-X02 | retirement per domain | L (always) | §46 criteria |

---

## 8. Migration sequence from current B to target B (data view)

1. **Now (FACT):**
   - V1 plaintext tables (partially used);
   - V2 ciphertext tables (unused by flows);
   - JSON session files;
   - client IndexedDB key package.
2. **M0:**
   - stop new plaintext leakage (IM-S04);
   - DB sessions (IM-S06);
   - server conversation buffer (IM-S03).
3. **M2:** M3 tables are created empty; the IC moves into M3.
4. **M3:**
   - new writes go to M3 only (IM-M05);
   - V1 writes are frozen (IM-M07);
   - V2 is migrated into M3 Tier-P by the client (IM-M06);
   - V1 rows are imported into M3 with `IMPORT_V1` provenance (import job, same pipeline as MG; add it as **IM-M10** if L wants V1 content kept — LDL-07; default: import evidence/model rows, not work-state).
5. **M4:** the Lea repo (and optionally A) is imported; the domain becomes authoritative.
6. **Later (L decision):** V1/V2 tables are archived (dump) and dropped; the JSON files are removed.

**No step uses dual writes** (forbidden F-10). Each switch is a cut-over behind a flag with a rollback.

---

## 9. Open L decisions affecting packages

| LDL | Affects | Default used in this map |
|---|---|---|
| LDL-01 | IM-M02..M07 | M3 on the V2 envelope; V1 frozen legacy |
| LDL-02 | IM-H01, IM-C10, IM-C14, IM-E01 | shared hosting until the Hosting Gate |
| LDL-03 | IM-M01, IM-M05, IM-M06 | two tiers (C server key, P client E2E) |
| LDL-04 | IM-G01, §37 | Git = dev/audit/backup, never runtime |
| LDL-05 | IM-C02 | no write/promotion; IC + Lea Tier-C reads only |
| LDL-06 | IM-C06 | owner + declared guests |
| LDL-07 | IM-MG01, IM-M10 | exclude PERSONAL/HEALTH/LEGAL; V1 work-state not imported |
| LDL-08 | IM-I02 | draft by agent from sources, approved by Lea + L |
| LDL-09 | IM-O03, IM-C15 | telemetry off-by-default for detail, 90-day raw |
| LDL-10 | IM-C09 | AUS |
| LDL-11 | IM-C07, IM-C08 | conservative |
| LDL-12 | IM-X02 | always L |
| LDL-13 | — | web_app1 out of scope |
| LDL-14 | IM-G02 | AiChat `AUTHORITY-REGISTRY.md` |
| LDL-15 | IM-G03 | a minimal AGENTS.md pointer in Lea-App (its own tiny unfreeze package if approved; FACT: Lea-App AGENTS.md requires two independent zero-finding pre-checks for changes to AGENTS.md) |
| LDL-16 | IM-G01 | enable branch protection |
| LDL-17 | IM-C15, IM-S04 note | tombstones + an admin erasure procedure |
| LDL-18 | IM-O05, IM-C03 | transcripts ephemeral |

**Independence of M0:** none of the M0 packages depends on an open LDL, except LDL-16 (IM-G01, where the default "enable" is low risk).

---

## 10. Non-claims

- No package has been implemented or tested in this run.
- All "Likely files" are based on Lea-App `450ff9d` and must be re-verified.
- Hosting facts are TO VERIFY (IM-H00).
- Provider API features are TO VERIFY at implementation time.
- No PASS is claimed for any part of the project.
