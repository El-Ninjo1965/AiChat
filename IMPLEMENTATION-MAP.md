# IMPLEMENTATION-MAP — Executable dependency map for target B

This map turns `B-BLUEPRINT.md` into small, independently verifiable work packages. It is planning only: no run has implemented anything.

**Reconciled per AGENT_TASK v4 (C2 blueprint reconciliation):** the packages, dependencies and gates follow WORKSPACE D6–D12. Provenance of the corrections is in §11; the original v3 text is preserved in Git (`11497e7`).

**Sources (pinned):**
- AiChat main `6fb71ff` (WORKSPACE.md D1–D12, AGENT_TASK.md v4); earlier `28f9fff` (v3);
- AiChat audit `899515c` (AUDIT-FINDINGS.md AUD-01..28);
- Lea main `757880a`;
- Lea-App main `450ff9d` (FROZEN / STRICTLY READ-ONLY at the time of writing).

**Status of this document:**
- ARCHITECTURE PROPOSAL for C2 review by Lea and a decision by L;
- **not** an authorisation to implement;
- **no PASS claim**.

Labels follow `B-BLUEPRINT.md` §0.1 (FACT / INFERENCE / TO VERIFY / RECOMMENDATION / SETTLED (Dn) / GOVERNANCE / L-DECISION-LATER). The decision register is `B-BLUEPRINT.md` §0.4. Only LDL-08, LDL-09 (retention part) and LDL-10 (narrowed) are OPEN.

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
| **M2** | Keys and store | Server-side key infra (classes C, P-N), M3 schema (V1/V2 convergence, D11), memory service v3, multi-generation backup + verified restore drill (data + keys, D10) | G-M2: C2 (A3) + restore drill evidence |
| **M3** | Experience cycle | Evidence/model/prediction/review/revision APIs, promotion rules, V1 writes frozen (sources retained), V2→M3 migration with per-item class, P-E2E recovery code, V1 semantic import (IM-M10) | G-M3: C2 + L test "Lea remembers across sessions and modes" |
| **M4** | Migration | Lea repo (and optionally A exports) imported item by item with provenance, D7 status and protection class; domain state machine; the first domain B_AUTHORITATIVE | G-M4: Lea sample review + C2 + L approval per domain |
| **M5** | Capabilities | Monitor UI (visible by default, D9), Incognito (D6), idle, vision/visual memory, presence, autonomy/gates, connectors, external AI, frontend IA, PWA, jobs, retention | G-M5: per package C2; the Hosting Gate before the working tool |
| **M6** | Independence | B runs normal operation without A | **IM-X01 Independence Gate** |
| **M7** | Retirement (optional, per domain) | A/Lea can be archived for a domain | **IM-X02 Retirement Gate** (explicit L approval; D5 gate) |

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
[G-M1] ──> IM-M01 server keys C/P-N ──> IM-M02 M3 schema ──> IM-M03 memory service v3 ──> IM-M09 backup gens + restore drill ──> [G-M2]
[G-M2] ──> IM-M04 entity APIs ──> IM-M05 promotion ──> IM-M07 V1 freeze
          IM-S07 key non-extractable ──> IM-M08 P-E2E recovery code ──> IM-M06 V2→M3 (per-item class) ──> [G-M3]
          IM-M05 + IM-M09 ──> IM-M10 V1 semantic import ──> [G-M3]
[G-M3] ──> IM-MG01 item-level import dry-run ──> IM-MG02 import commit (needs IM-M09 fresh snapshot) ──> IM-MG03 domain transition ──> [G-M4]
                                     IM-MG04 A export import (optional, parallel after IM-MG01)
[G-M3] + IM-M09 ──> IM-C07 risk classes R0–R3 ──┬─> IM-C08 connectors ──> IM-C09 external AI (policy LDL-10)
                                                 ├─> IM-C11 processing mechanisms
                                                 └─> IM-H01 Hosting Gate (evidence) ──> IM-C10 working tool
[G-M1] ──> IM-C03 idle ; IM-C12 frontend IA ; IM-C13 PWA
[G-M3] ──> IM-C01 monitor UI ; IM-C04 vision ──> IM-C05 visual memory
[G-M3] + IM-O03 + IM-M09 ──> IM-C02 Incognito
[G-M3] ──> IM-C06 presence ; IM-C14 jobs ──> IM-C15 retention
[G-M4] + all M5 packages required for normal operation ──> IM-X01 Independence Gate
IM-X01 + IM-M09 (recent) ──> IM-X02 Retirement Gate (per domain)
```

---

## 3. Active-development package scope protocol

Lea-App is in **ACTIVE DEVELOPMENT** (WORKSPACE D18; FREEZE-LOG development-state transition 2026-09-27). The former routine freeze/unfreeze cycle is retired.

- Every implementation change still belongs to a named package with explicit scope, prerequisites, tests and recovery/rollback.
- CI scope enforcement must reflect the currently authorised package scope without requiring a new global freeze/unfreeze ceremony.
- Package-sized commits/branches/checkpoints remain mandatory for rollbackability.
- Independent packages may run in parallel under D17.
- Existing L decisions are reused under D18; manual acceptance tests do not prevent prior implementation unless the package explicitly requires an unresolved design decision.
- Production/deployment, destructive migration/cutover/retirement, external infrastructure mutation and other true R3 actions retain their explicit L/recovery gates.
- C2 remains an independent acceptance/review gate and may be prepared/run in parallel; the implementer does not self-award C2.
- FREEZE-LOG remains historical evidence for the former freeze period and the transition to ACTIVE DEVELOPMENT; its old unfreeze template is not the current per-package operating procedure.

## 4. Work packages

> **Note on file names:** "Likely files" reference current Lea-App paths (FACT at `450ff9d`). New files are proposals (RECOMMENDATION). An implementing agent must re-verify paths at its base SHA.

### M0 — Safe foundation

#### IM-G01 — CI pipeline and branch protection
- **Objective:** Automated tests and a secret scan on every PR; protected `main` (GOVERNANCE recommendation / admin action; formerly LDL-16).
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
- **Objective:** Seed `AUTHORITY-REGISTRY.md` from `B-BLUEPRINT.md` §1.2/§31 (CTL-01; GOVERNANCE, formerly LDL-14).
- **Prerequisites:** none.
- **Scope:** AiChat only; one table domain → authority → migration state (§8 state machine).
- **Likely files:** `AiChat/AUTHORITY-REGISTRY.md` (new).
- **Forbidden scope:** WORKSPACE decisions; Lea; Lea-App.
- **Acceptance:** every store in blueprint §31 is listed exactly once, with state `SOURCE_ONLY` or `N/A`. V1/V2 are listed as migration sources (D11); the Incognito buffer is listed as non-store.
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

#### IM-H00 — Hosting capability register (facts) — SUBSTANTIALLY COMPLETE 2026-09-28
- **Objective:** Fill the blueprint §36 register with FACTs (PHP version, cron, files outside the web root, DB grants, staging subdomain, backups).
- **Prerequisites:** satisfied for the core questionnaire by L-provided cPanel evidence/answers; remaining narrow unknowns are recorded in `HOSTING-CAPABILITIES.md` and are verified only where a consuming package needs them.
- **Scope:** documentation in AiChat.
- **Authoritative fact register:** `HOSTING-CAPABILITIES.md`.
- **Forbidden scope:** any hosting change; recording credentials, hostnames or unnecessary infrastructure identifiers/paths (P-rule).
- **Acceptance:** core register rows have FACT/DECIDED TARGET or explicit UNKNOWN + method/consumer for later verification. Current confirmed capabilities include PHP 8.5, one-minute cron, protected storage outside the public web root, full DB privileges for the Lea-App database, and cPanel/JetBackup restore/download capability. Actual proxy/CDN presence, exact production DB version and staging capability remain explicit UNKNOWNs rather than assumptions.
- **Security consequence:** until a trusted proxy is technically proven/configured, IM-S02 must fail closed and not trust client-supplied forwarding headers; L's desired architecture is direct end-device → Internet/HTTPS → cPanel-hosted Lea-App.
- **Automated tests:** none. **Manual L test:** core checklist completed 2026-09-28; package-specific UNKNOWNs are verified by their consuming package.
- **Rollback:** n/a. **Risk:** low. **Agent:** A1. **Gates:** no remaining global L gate for the core IM-H00 questionnaire.

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
- **Rollback:** remove staging. **Risk:** medium. **Agent:** A2. **Gates:** L (R3: infrastructure).

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
  - a server-side conversation buffer (class C; short TTL with a justification per blueprint §45; initial storage: a DB table or session-bound storage; RECOMMENDATION DB). It is designed so that Incognito conversations (IM-C02) can later use a separate transient buffer that is excluded from backups;
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
- **Forbidden scope:** deleting existing V1 rows (V1 is a migration source until verified transfer, D11; erasure is IM-C15); the work-state API itself.
- **Acceptance:** after a text message, no new V1 row contains message content.
- **Automated tests:** a JS unit test with a fetch mock asserting no work-state POST containing content; a DB canary test in the harness.
- **Manual L test:** none.
- **Rollback:** revert. **Risk:** low. **Agent:** A2. **Gates:** C2.
- **Note:** existing plaintext rows in V1 remain as a migration source (D11). Deleting them is an R3 action requiring explicit L approval after verified transfer, and is **not** part of this package.

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
- **Forbidden scope:** password hashing change (only if a defect is found, as a separate package); P-E2E crypto.
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
- **Forbidden scope:** changing the KDF/cipher parameters or the AAD format (those are security-package decisions per D12, not part of this fix).
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
- **Objective:** Draft the IC v1 content from Lea VISION/PROCESSING category-C rules (**OPEN: LDL-08 content approval**).
- **Prerequisites:** none (draft); L decision LDL-08 (final).
- **Scope:** a text document in AiChat (draft), reviewed by Lea; then delivered as data to IM-I01.
- **Likely files:** `AiChat/IDENTITY-CORE-DRAFT.md` (new).
- **Forbidden scope:** workshop shortcuts (P10); sensitive personal material (class P belongs in M3 records, not in the IC, which is class C and always in context).
- **Acceptance:**
  - every statement is traceable to a source anchor;
  - the reality-labelling rules are included;
  - it fits the budget.
- **Automated tests:** none (a token count script may be used in the CI of IM-I01).
- **Manual L test:** L and Lea approve the content.
- **Rollback:** previous version. **Risk:** medium (identity). **Agent:** A3 (drafting) + Lea review. **Gates:** Lea C2 + L (R3).

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
- **Objective:** Blueprint §30 event schema, emitted by orchestrator steps (I-MO-1). Two independent controls (D9): monitor visibility and telemetry persistence. Deliver a **retention proposal with a technical/privacy justification** as the input to the OPEN L decision LDL-09 (retention part). There is no 90-day default.
- **Prerequisites:** IM-O01.
- **Scope:** events table + emission; no UI (IM-C01).
- **Likely files:** migration `database/migrations/006_telemetry.sql` (proposal), `lib/telemetry.php`.
- **Forbidden scope:** any content/chain-of-thought field.
- **Acceptance:**
  - each turn has events for the steps actually executed;
  - a schema test rejects unknown fields;
  - the persistence switch works independently of visibility;
  - Incognito turns persist only content-free security/cost counters (blueprint §20);
  - the retention proposal document exists. Until L decides, persistence uses the shortest technically useful value, stated as provisional.
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
- **Forbidden scope:** persistent raw transcript storage (blueprint §13; transcripts follow the text session/promotion rules); idle logic (IM-C03).
- **Acceptance:** voice session instructions include the same IC hash as text (I-ID-1).
- **Automated tests:** a mock provider asserts the instructions hash.
- **Manual L test:** voice and text give consistent self-descriptions.
- **Rollback:** revert. **Risk:** medium. **Agent:** A2. **Gates:** C2; L.

**G-M1 gate:** C2 over M1; L manual continuity test ("same Lea in text and voice"); no persona literals; telemetry present.

### M2 — Keys and store

#### IM-M01 — Server-side key infrastructure (classes C and P-N)
- **Objective:** Blueprint §28 (D12): a server master key outside the web root; separate data keys for class C and class P-N; `key_ref`; a rotation procedure. The concrete algorithms/KDF/hierarchy are **RECOMMENDATIONS to be verified** in this package's security review, not frozen requirements.
- **Prerequisites:** G-M1; IM-H00 (files outside the web root: FACT required).
- **Scope:** a key loading library + rotation doc; **no data yet**.
- **Likely files:** new `lib/keys.php` (proposal), a deploy doc.
- **Forbidden scope:** storing keys in the DB or repo; P-E2E changes.
- **Acceptance:**
  - encrypt/decrypt round trip;
  - a wrong key fails loudly;
  - a P-N key is not usable from non-owner contexts (access-path test);
  - the key file is not web-accessible (manual check);
  - the rotation procedure has been tested in staging.
- **Automated tests:** unit tests; IV uniqueness sampling.
- **Manual L test:** L verifies the key backup is stored offline.
- **Rollback:** feature flag off. **Risk:** high. **Agent:** A3. **Gates:** C2 (crypto design review); L (R3).

#### IM-M02 — M3 schema
- **Objective:** Blueprint §4.1/§4.2 tables: records, relations, import batches, schema_migrations. M3 is the D11 convergence of V1 semantic concepts and the V2 envelope/append-only foundations. The exact schema is decided **in this package's C2 review**; blueprint §4 is the working target concept. Includes the `protection_class` field and the D7 status values.
- **Prerequisites:** IM-M01.
- **Scope:** forward-only migrations + verification queries.
- **Likely files:** `database/migrations/007_m3_records.sql`, `008_m3_relations.sql` (proposals).
- **Forbidden scope:** altering or deleting V1/V2 tables.
- **Acceptance:**
  - a V1/V2 concept mapping table shows each proven V1 semantic concept and V2 mechanism as kept, merged, or explicitly superseded with a reason (no silent discard, D11);
  - the migration applies cleanly on a copy of the prod schema in staging;
  - verification queries pass;
  - the checksum is recorded.
- **Automated tests:** migration tests in CI MySQL.
- **Manual L test:** none. **Rollback:** a new drop migration (the tables are empty at this point).
- **Risk:** medium. **Agent:** A3. **Gates:** C2.

#### IM-M03 — Memory service v3 (records/relations API)
- **Objective:** Blueprint §4/§32: create, list, revise and relate with the envelope validation invariants I-M-1..6; IC migrates to `kind=IC`. The API contract is finalised in this package's C2 review (D11).
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

#### IM-M09 — Backup generations and restore drill (recovery-by-design, D10)
- **Objective:** Blueprint §38. Multi-generation encrypted backups covering the full recovery scope (DB/schema, M3 data, relevant config, visual-memory files, and the keys for C/P-N stored offline), plus a documented, **verified** restore into staging. Also a pre-change snapshot procedure for R2/R3 actions.
- **Prerequisites:** IM-M02, IM-E01, IM-M01.
- **Scope:**
  - a backup script/procedure with generation rotation;
  - a pre-change snapshot procedure;
  - the drill report;
  - a drill schedule (recommended quarterly and after changes to backup tooling or keys).
- **Likely files:** deploy docs; optional `tools/backup.*` outside the web root (TO VERIFY hosting).
- **Forbidden scope:** restoring into production; storing backups in Git; including the Incognito buffer in backups.
- **Acceptance:**
  - ≥ 2 generations exist, and restoring an **older** generation is shown to work;
  - the drill report shows counts equal, integrity hashes OK, and decrypt samples OK for C and P-N, **using keys restored from their offline backup** (not the live key file);
  - config and files are restored;
  - a pre-change snapshot is taken and verified once;
  - Git rollback is documented as code-only.
- **Automated tests:** a backup-exclusion test (no Incognito buffer tables in the dump); a generation-rotation test.
- **Manual L test:** L confirms the off-site copy and the offline key backup exist.
- **Rollback:** n/a. **Risk:** medium. **Agent:** A2 (A3 for the key part). **Gates:** L; C2 of the report.
- **Repeat:** this drill is repeated before IM-MG02, before any autonomy-scope expansion (IM-C07), and within 30 days before IM-X02.

**G-M2 gate:** C2 (A3 reviewer) over key handling and the store; verified restore drill evidence (data + keys, older generation); L approval.

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
- **Forbidden scope:** Incognito (IM-C02 must land before any Incognito UI exists; until then there is no Incognito mode); autonomy beyond R1 LOCAL promotion.
- **Acceptance:**
  - only allowed kinds are promoted, each with a protection class;
  - retrieval reads have no side effects on salience/weights (preparation for D6);
  - every promotion has a receipt;
  - CC manifests contain IDs and labels only.
- **Automated tests:** behavioural scenarios (remember-this; correction; nothing-to-promote).
- **Manual L test:** "Lea remembers X told in text, in the next voice session."
- **Rollback:** flag off (retrieval and promotion separately).
- **Risk:** high. **Agent:** A3. **Gates:** C2; L.

#### IM-M07 — Freeze V1 writes
- **Objective:** Dual-master path 2 (blueprint §8): V1 write endpoints are disabled; V1 data stays readable for the import (IM-M10) and remains a migration source until verified transfer (D11).
- **Prerequisites:** IM-M05, IM-S04.
- **Scope:** V1 write routes return 410; the client uses v3 only.
- **Likely files:** V1 endpoint files, `.htaccess`, `frontend/app.js`.
- **Forbidden scope:** deleting V1 data; unrouting V1 reads before IM-M10 has verified the transfer.
- **Acceptance:** a write-path inventory test shows no V1 INSERT from runtime.
- **Automated tests:** inventory + 410 tests.
- **Manual L test:** app works normally.
- **Rollback:** re-enable routes (flag). **Risk:** medium. **Agent:** A2. **Gates:** C2.

#### IM-M08 — P-E2E recovery code and multi-device transfer
- **Objective:** Blueprint §28 recovery for P-E2E (D12: recovery includes keys). The mechanism is a RECOMMENDATION verified in this package's review.
- **Prerequisites:** IM-S07.
- **Scope:** client-side second wrap; a one-time display; the device add flow.
- **Likely files:** `frontend/memory-crypto.js`, `frontend/app.js`, tests.
- **Forbidden scope:** server escrow; KDF changes without a separate security-package decision.
- **Acceptance:** unlock via the recovery code works; a wrong code fails; the code is never sent to the server (network test).
- **Automated tests:** unit + network assertion tests.
- **Manual L test:** L stores the code offline and tests recovery on a second device.
- **Rollback:** flag off (existing wrap remains). **Risk:** high. **Agent:** A3. **Gates:** C2; L.

#### IM-M06 — V2 → M3 migration with per-item class (client-driven)
- **Objective:** Blueprint §7/§8 (D11/D12): re-home existing V2 records into M3 without the server seeing plaintext, and drop the `session_id` ownership (OBS-05). Each item is classified: it stays P-E2E by default. L may reclassify an item to P-N or C in the client (an explicit per-item action; the client re-encrypts; downgrades are R3).
- **Prerequisites:** IM-M03, IM-M08, IM-M09 (fresh snapshot).
- **Scope:** a client migration tool; the server accepts the envelope with `IMPORT_V2` provenance; V2 is then read-only.
- **Likely files:** `frontend/*migration*` (proposal), `v3/memory` import route.
- **Forbidden scope:** server-side decryption; V2 deletion (V2 remains a migration source until verified transfer, D11); bulk reclassification.
- **Acceptance:** count/hash parity per record; idempotent re-run.
- **Automated tests:** fixture migration test.
- **Manual L test:** L runs the migration in an unlocked browser and sees their items.
- **Rollback:** tombstone the batch. **Risk:** high. **Agent:** A3. **Gates:** C2; L.

#### IM-M10 — V1 semantic import (IMPORT_V1)
- **Objective:** D11/D7: import V1 evidence/model/relation content into M3 as items with `IMPORT_V1` provenance, a D7 status and a protection class. V1 semantic concepts are carried forward, not discarded.
- **Prerequisites:** IM-M05, IM-M07, IM-M09 (fresh snapshot).
- **Scope:** an item-level V1 → M3 import job with dry-run (same pipeline design as IM-MG01); a verification report.
- **Forbidden scope:** whole-table blind copy; deleting V1 rows; importing redundant/obsolete work-state rows without an item-level reason.
- **Acceptance:**
  - every V1 row is either mapped, or listed as redundant/obsolete with a reason;
  - the round-trip hash test passes;
  - the batch is tombstonable.
- **Automated tests:** fixture import tests; idempotency.
- **Manual L test:** L spot-checks the imported items.
- **Rollback:** tombstone the batch. **Risk:** high. **Agent:** A3. **Gates:** C2; L.

**G-M3 gate:** C2; L test "Lea remembers across sessions and modes"; V1 writes frozen; V1/V2 transfer verified (IM-M06, IM-M10); the recovery test passed.

### M4 — Migration of Lea/A continuity

#### IM-MG01 — Import pipeline and dry-run (Lea repo)
- **Objective:** Blueprint §7 steps 1–4 for a pinned Lea SHA; dry-run report only.
- **Prerequisites:** G-M3. There is no scope decision pending: migration is experience-oriented and item-level (D7).
- **Eligible sources:** all Lea sources, **including** PERSONAL/HEALTH/LEGAL-CONTEXT.md. Selection is per item by relevance to Lea's continuity/development; no inclusion or exclusion by filename.
- **Scope:** parser/classifier tool; report in AiChat.
- **Likely files:** `tools/import/*` (proposal; not web-reachable) or an offline tool; `AiChat/IMPORT-DRYRUN-<sha>.md` (report, content-minimised: counts and anchors, no private text).
- **Forbidden scope:** writing to Lea; writing to prod M3.
- **Acceptance:**
  - every section is mapped to items, or listed as not imported with a reason (redundant/obsolete/irrelevant);
  - every candidate item has a source anchor, a D7 status (CURRENT / HISTORICAL / SUPERSEDED / REJECTED / UNCERTAIN) and a protection class (C / P-N / P-E2E) (blueprint I-MG-6);
  - no whole-file copy;
  - anchors are resolvable;
  - sensitive items are minimised.
- **Automated tests:** parser tests on fixtures; idempotency hash tests.
- **Manual L test:** L reviews the sensitive-item sample (classification and minimisation). **Lea review:** yes (C2 sample review).
- **Rollback:** n/a. **Risk:** medium. **Agent:** A3. **Gates:** Lea C2.

#### IM-MG02 — Import commit
- **Objective:** Commit the reviewed batch into prod M3 with the per-item protection class (C / P-N / P-E2E). P-E2E items are encrypted client-side in an unlocked L session.
- **Prerequisites:** IM-MG01 approved; a fresh verified snapshot (IM-M09 procedure).
- **Scope:** a one-shot import job with a batch ID.
- **Likely files:** the import tool; the audit log.
- **Forbidden scope:** items not in the reviewed dry-run; re-import without a new dry-run.
- **Acceptance:** counts match the report; the round-trip hash test passes; the batch is tombstonable.
- **Automated tests:** post-import verification script.
- **Manual L test:** L asks Lea about migrated items in text and voice.
- **Rollback:** tombstone the batch (or restore the pre-change snapshot). **Risk:** high. **Agent:** A3. **Gates:** C2; L (R3).

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
- **Risk:** high (governance). **Agent:** A2 + Lea. **Gates:** Lea C2; L (R3).

#### IM-MG04 — A (ChatGPT) export import (optional)
- **Objective:** Import L-provided A exports via the same pipeline.
- **Prerequisites:** IM-MG01; L provides the export (format TO VERIFY).
- **Scope/acceptance:** as IM-MG01/02 (item-level, D7 status + class), with `IMPORT_A` provenance.
- **Risk:** medium. **Agent:** A3. **Gates:** Lea C2; L.

### M5 — Capabilities (each is its own unfreeze)

The cards below are condensed. Every package additionally inherits these rules:
- default forbidden scope: other capabilities, the crypto format, the identity core content;
- default rollback: a feature flag off;
- default gates: C2 + an L manual test in staging.

| ID | Objective (blueprint §) | Prerequisites | Scope / likely files | Specific forbidden scope | Acceptance (key) | Automated tests | Manual L test | Risk | Agent |
|---|---|---|---|---|---|---|---|---|---|
| IM-C01 | Monitor + protocol UI (§30, §33; D9) | IM-O03, G-M3 | `frontend/*` monitor view; `v3/monitor/events` | any content in events; chain-of-thought | visible by default; the UI shows only real events; categories per ROADMAP; the visibility switch does not change processing | UI unit tests; API contract | L sees the module timeline for a turn | low | A2 |
| IM-C02 | Incognito (§20; D6) | IM-M05, IM-O03, IM-M09 | orchestrator flag; transient Incognito buffer; the single L item-promotion endpoint; UI indicator | a client-only enforcement; any read restriction | **read test:** Incognito context equals normal context for the same query (IC + C + P-N + unlocked P-E2E); **no read-side effects:** weights/salience/counters unchanged; **no writes:** DB diff = 0 new M3/relation/model/prediction rows, no jobs enqueued; the buffer is discarded on exit/TTL; **backup exclusion:** the buffer is absent from dumps; telemetry persists only content-free counters; **promotion:** only an explicit L action on one item creates exactly one record with `INCOGNITO_PROMOTED` provenance | read-equality, side-effect, DB diff, backup-exclusion and promotion tests | L uses Incognito, checks that Lea knows existing memory, and verifies nothing new remains afterwards except an explicitly promoted item | high | A3 |
| IM-C03 | Idle state machine (§14; OBS-09) | IM-O05 | `frontend/app.js` voice state; config | wake word | ≤5 reminders, then INACTIVE; the provider session is closed | fake-clock unit tests | L leaves voice idle | low | A2 |
| IM-C04 | Vision input (§15) | IM-S05, G-M3 | `v3/vision/analyze`; client capture + downscale | biometric features; silent capture | auth required; size/format validation; labelled OBSERVATION | HTTP + validation tests | L photographs an object | medium | A2 |
| IM-C05 | Visual memory (§16) | IM-C04, IM-M03 | VM kind + blob store + counters | public blob URLs | counters 5/6/10 enforced server-side; blob access requires auth | counter + access tests | L asks for an image twice (retrieval first) | medium | A2 |
| IM-C06 | Presence / known persons (§17; D8) | IM-S06, IM-M03 | owner principal; presence model; PX known-person profiles (no accounts) | biometrics; accounts for known persons | a declared known person gets no access to L's protected data (P-N/P-E2E withheld when non-owner presence is declared); known person ≠ authenticated principal; presence is shown | scope + presence tests | L declares a known person | high | A3 |
| IM-C07 | Capability gate + risk classes R0–R3 + Settings (§11; D10) | IM-O02, G-M3, IM-M09 | `lib/capabilities.php`; the rating registry (impact, reversibility, recoverability, scope, external consequence); the settings API/UI | a self-raise of the ceiling; a blanket write-disable | R3 AUTOMATISCH is rejected server-side; R1/R2 AUTOMATISCH is rejected without a verified recovery reference; R2/R3 require a pre-action verified snapshot; Ü stops all | policy matrix tests | L grants a bounded R1 scope in Settings | high | A3 |
| IM-C08 | Connectors registry + first connector (GitHub) (§21; D10) | IM-C07, IM-M09; §27 vault | connector registry; credential vault; per-capability risk ratings | tokens to the client; R2/R3 capabilities without their gates | reads work; writes are rated per R-class (no blanket disable); R1 writes (e.g. a PR branch) are AUTOMATISCH only in an L-granted scope with a verified recovery path; injection content is not executed | gate + injection tests | L grants GitHub read and one R1 scope | high | A3 |
| IM-C09 | External AI dialogue (§22; **OPEN LDL-10**) | IM-C08 | connector type `external_ai`; dialogue UI | class P to external without per-dialogue consent; any autonomy beyond NACHFRAGEN before LDL-10 is decided | limits enforced; transcript visible; stored as EXTERNAL | limit tests | L runs one bounded dialogue | medium | A2 |
| IM-C10 | Working tool (§23) | **IM-H01**, IM-C07 | workspace service; tool agent allowlist | prod write access; shell passthrough | traversal blocked; allowlist enforced; receipts | traversal + allowlist tests | L works on a sample project | high | A3 |
| IM-C11 | Processing mechanisms depth/K/G/X/T/P (§10) | IM-O03, IM-M05 | orchestrator steps + policy config | shortcut letters in UI; CoT in events | each mechanism emits events; outcome classes per ROADMAP | trace-vs-event; policy tests | L sees a G-check outcome in the monitor | medium | A2 |
| IM-C12 | Frontend IA + accessibility (§33) | G-M1 | navigation, views, aria | colour-only status | nav per ROADMAP; the a11y scan passes | a11y scan (tool TO VERIFY) | phone + desktop + tablet check | low | A2 |
| IM-C13 | PWA/offline outbox (§34) | IM-S03 | `sw.js`, outbox | caching `/api/*` | the SW never caches the API; the outbox resends idempotently | SW unit tests | L goes offline and online | medium | A2 |
| IM-C14 | Job runner (§35) | IM-H00, IM-M03 | job table + cron runner | R3 jobs; jobs from Incognito conversations | lease prevents double runs; jobs audited | concurrency tests | none | medium | A2 |
| IM-C15 | Retention + deletion/erasure (§45) | IM-C14, IM-M09 | purge jobs; admin erasure procedure (technical RECOMMENDATION) | deleting without confirmation; purging V1/V2 before verified transfer; a telemetry TTL before LDL-09 is decided | TTLs enforced per justified proposals; erasure is R3 and leaves an audit without content | purge tests | L erases a test person profile | high | A3 |
| IM-C16 | Relationships & appearance (§18, §19) | IM-C06, IM-C11 | REL scope; appearance spec records; G-check hook | fixed persona imagery | changes are R2 NACHFRAGEN with a G-check outcome | policy tests | L reviews an appearance change proposal | medium | A2 |

#### IM-H01 — Hosting Gate (evidence; settled strategy)
- **Objective:** Apply the settled strategy (blueprint §36): stay on the current shared hosting while it is sufficient; move only on a demonstrated need. This package records from IM-H00 facts whether shared hosting satisfies the §36 register for the remaining packages (especially IM-C10, IM-C14 and the IM-M09 backup/drill needs).
- **Prerequisites:** IM-H00; the M5 needs are known.
- **Scope:** an evidence record (AiChat). If a need is demonstrated: the alternatives (VPS/container; hybrid worker) with a cost/risk comparison.
- **Forbidden scope:** migrating hosting within this package.
- **Acceptance:** the evidence is recorded. If a move is needed, L approves it as a change (R3) and a new package series `IM-HM*` is defined.
- **Risk:** medium. **Agent:** A2. **Gates:** L (only if a move is proposed).

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

#### IM-X02 — Migration/Recovery and Retirement Gate (per domain; D5 gate)

Blueprint §46 criteria:
- ≥ 4 weeks B_AUTHORITATIVE without divergence defects;
- import verification;
- multi-generation backups and a **restore drill (data + keys) within the last 30 days** (IM-M09 repeated);
- continuity test in text + voice;
- C2;
- L decision.

The result is **archive** (read-only) with pointers. Deletion happens only with explicit L approval (D5), never silently.

---

## 5. Parallel vs strictly sequential

| Can run in parallel (disjoint paths/responsibilities) | Strictly sequential |
|---|---|
| IM-G02, IM-G03, IM-H00, IM-I02 (docs, AiChat) alongside M0 code packages | IM-G01 → IM-T01 → any Lea-App code package |
| IM-S02 ∥ IM-S04 ∥ IM-S07 (disjoint files; each needs its own unfreeze) | IM-S01 → IM-S03 (both touch `chat.php`) |
| IM-O02 ∥ IM-O03 after IM-O01 (different new files; `orchestrator.php` touch is sequenced by merge order) | IM-S01 → IM-S05 / IM-S06 |
| IM-M04 ∥ IM-M08 | IM-I01 + IM-I02 → IM-O04 → IM-O05 |
| M5: IM-C03 ∥ IM-C12 ∥ IM-C04 (disjoint) | IM-M01 → IM-M02 → IM-M03 → IM-M05 → IM-M07 → IM-M10 |
| IM-MG04 ∥ IM-MG02 (after IM-MG01, different batches) | IM-M08 → IM-M06 |
| | IM-MG01 → IM-MG02 → IM-MG03 |
| | IM-M09 → IM-C07 → IM-C08 → IM-C09; IM-H01 → IM-C10; IM-M09 → IM-C02 |
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
| R3 packages (S01/S03/S06 prod deploy, I02, M01, MG02, MG03, C07 rating defaults, a hosting move if proposed) | package | L | explicit approval text |
| Autonomy-scope expansion (R1/R2 AUTOMATISCH grants) | per grant | L | a verified restore reference (IM-M09) for the affected state |
| OPEN L decisions | LDL-08 (IM-I02), LDL-09 retention (IM-O03 proposal), LDL-10 (IM-C09) | L | decision text in WORKSPACE/AGENT_TASK |
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
   - V2 is migrated into M3 by the client with a per-item class (IM-M06);
   - V1 semantic content is imported into M3 item by item with `IMPORT_V1` provenance (IM-M10; D11/D7; redundant/obsolete rows are listed with a reason, not blindly copied).
5. **M4:** the Lea repo (and optionally A) is imported item by item (D7); the domain becomes authoritative.
6. **Later (explicit L approval, R3):** after verified transfer, V1/V2 tables are archived (dump, covered by the backup generations) and only then dropped; the JSON files are removed.

**No step uses dual writes** (forbidden F-10). Each switch is a cut-over behind a flag with a rollback.

---

## 9. Decision status affecting packages (reconciled)

The authoritative register is `B-BLUEPRINT.md` §0.4.

| ID | Affects | Status for this map |
|---|---|---|
| LDL-01 | IM-M02, IM-M03, IM-M06, IM-M07, IM-M10 | SETTLED (D11): convergence; V1/V2 stay sources until verified transfer; schema in package C2 |
| LDL-02 | IM-H01, IM-C10, IM-C14, IM-E01 | SETTLED strategy: shared hosting while sufficient; move on demonstrated need |
| LDL-03 | IM-M01, IM-M06, IM-M08, IM-S07 | SETTLED (D12): classes C / P-N / P-E2E; crypto parameters are recommendations to verify |
| LDL-04 | IM-G01 | SETTLED (D5); Git rollback = code only (D10) |
| LDL-05 | IM-C02 | SETTLED (D6): normal reads, no writes/learning, discard, single L promotion |
| LDL-06 | IM-C06 | SETTLED (D8): owner + known persons without accounts |
| LDL-07 | IM-MG01, IM-MG02, IM-MG04, IM-M10 | SETTLED (D7): item-level, sensitive sources eligible, status + class per item |
| **LDL-08** | IM-I02 → IM-O04 | **OPEN:** IC v1 content approval (Lea + L) |
| **LDL-09** (retention part) | IM-O03, IM-C15 | Visibility SETTLED (D9). **OPEN:** telemetry persistence retention after a justified proposal; no 90-day default |
| **LDL-10** (narrowed) | IM-C09 | **OPEN:** external-AI autonomy and which classes may leave B; provisional NACHFRAGEN |
| LDL-11 | IM-C07, IM-C08 | SETTLED (D10): R0–R3; no blanket write-disable |
| LDL-12 | IM-X02 | SETTLED (D5) as a gate |
| LDL-13 | — | SETTLED (D5): out of scope |
| LDL-14 | IM-G02 | GOVERNANCE |
| LDL-15 | IM-G03 | SETTLED (D1/D3): Lea-App changes are ordinary change packages |
| LDL-16 | IM-G01 | GOVERNANCE / admin action |
| LDL-17 | IM-C15 | technical RECOMMENDATION |
| LDL-18 | IM-O05, IM-C03 | derivable (blueprint §13) |

**Independence of M0:** no M0 package depends on an open L decision. IM-G01 needs an admin action (branch protection), not a design decision.

**Rule for later agents:** a package may start when its prerequisites are met. A package that touches an OPEN item (IM-I02 final, the IM-O03 retention value, IM-C09 autonomy/class sharing) stops at that point with its recommendation, and does not improvise.

---

## 10. Non-claims

- No package has been implemented or tested.
- All "Likely files" are based on Lea-App `450ff9d` and must be re-verified.
- IM-H00 core hosting facts are recorded in `HOSTING-CAPABILITIES.md`; only the explicitly listed package-specific unknowns remain TO VERIFY.
- Provider API features are TO VERIFY at implementation time.
- No PASS is claimed for any part of the project.

---

## 11. Reconciliation provenance (AGENT_TASK v4)

The v3 map (`11497e7`) used Claude's provisional defaults. Lea's independent C2 review (recorded by L as WORKSPACE D6–D12) corrected them. Changes in this map:

| Decision | v3 map (superseded) | Reconciled map |
|---|---|---|
| D6 Incognito | IM-C02: "IC + Lea Tier-C reads only"; only no-write tests | normal reads; read-equality, no-side-effect, backup-exclusion, telemetry and single-promotion tests; depends on IM-O03 and IM-M09 |
| D7 Migration | IM-MG01 default excluded PERSONAL/HEALTH/LEGAL; file-level mapping | item-level; all sources eligible; D7 status + class per item; L sensitive-sample review; IM-M10 added for V1 |
| D8 People | IM-C06 "guest"; principal model | owner principal + known persons without accounts; no access to protected data |
| D9 Monitor | LDL-09 "off-by-default detail, 90-day raw" | visible by default; separate persistence control; retention proposal → OPEN L decision |
| D10 Autonomy/recovery | IM-C07 tiers "conservative"; IM-C08 "default AUS"; IM-M09 single drill | R0–R3 risk rating; no blanket write-disable; autonomy expansion needs verified recovery; multi-generation backups with full scope + keys; repeated drills |
| D11 Canonical memory | M3 "on the V2 envelope; V1 frozen legacy" | convergence of V1 semantics + V2 foundations; V1/V2 sources until verified transfer; schema in package C2 |
| D12 Protection | two tiers with fixed crypto (C server / P client-E2E) | layered classes C / P-N / P-E2E; crypto parameters are recommendations to verify; recovery includes keys |

The full reconciliation record, including the v4 adversarial pass, is in `B-BLUEPRINT.md` §51.
