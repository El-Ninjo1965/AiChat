# IMPLEMENTATION-MAP — Executable dependency map for target B

This map turns `B-BLUEPRINT.md` into small, independently verifiable work packages. It is planning only: no run has implemented anything.

**Reconciled per AGENT_TASK v4 (C2 blueprint reconciliation):** the packages, dependencies and gates follow WORKSPACE D6–D12. Provenance of the corrections is in §11; the original v3 text is preserved in Git (`11497e7`).

**M5/D13–D19 reconciliation (2026-09-28):** IM-C01–IM-C15 were expanded from a condensed table into complete executable package cards; IM-C17 (D14 voice continuity), IM-C18 (D15 learning-domain separation) and IM-C19 (cross-context UI/PWA consistency defect) were added; D13 was mapped onto IM-C12; D16 was integrated into IM-C04/IM-C05/IM-C11; D19 was integrated primarily into IM-C10, with cross-references in IM-C07/IM-C08/IM-C14/IM-O01 and in IM-X01. Provenance is in §12.

**Sources (pinned):**
- AiChat main `6fb71ff` (WORKSPACE.md D1–D12, AGENT_TASK.md v4); earlier `28f9fff` (v3); AiChat branch `copilot/zero-memory-reconciliation` at `4855411` (WORKSPACE.md D13–D19, HOSTING-CAPABILITIES.md);
- AiChat audit `899515c` (AUDIT-FINDINGS.md AUD-01..28);
- Lea main `757880a`; Lea `PROCESSING.md` at `bd8a671` (D15/D16 source grounding — "Privat-/Lea-Entwicklung und Arbeitsmodus", "Eigenbezogene Wahrnehmung statt Nutzer-Nutzen", Baustein 14 Differenztest);
- Lea-App main `450ff9d` (FROZEN / STRICTLY READ-ONLY at the time of writing); Lea-App main re-verified read-only at `ff075e98d49707bbbea12065d57b466bc9c24ac6` (D19/Task 7 grounding — `frontend/appearance-core.js`, `frontend/sw.js`, `.github/package-scope/`).

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
| **M5** | Capabilities | Monitor UI (visible by default, D9), Incognito (D6), idle, vision/visual memory (D16), presence, autonomy/gates, connectors, external AI, frontend IA + viewport contract (D13), PWA, jobs, retention, voice continuity (D14), learning-domain separation (D15), the working tool as the D19 primary WORK-mode owner, cross-context UI/PWA consistency (Task 7 of the 2026-09-28 reconciliation) | G-M5: per package C2; the Hosting Gate before the working tool |
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
[G-M3] + IM-M09 ──> IM-C07 risk classes R0–R3 (+ D19 WORK-mode capability ratings) ──┬─> IM-C08 connectors (D19: GitHub = optional mirror) ──> IM-C09 external AI (policy LDL-10)
                                                 ├─> IM-C11 processing mechanisms (D16 observation/inference; D15 domain tag)
                                                 └─> IM-H01 Hosting Gate (evidence) ──> IM-C10 working tool (D19 primary WORK-mode owner)
[G-M1] ──> IM-C03 idle ──> IM-C17 voice provider-session continuity (D14)
[G-M1] ──> IM-C12 frontend IA + D13 viewport contract ──> IM-C13 PWA ──> IM-C19 cross-context UI/PWA consistency (Task 7)
[G-M3] ──> IM-C01 monitor UI ; IM-C04 vision (D16 perception input) ──> IM-C05 visual memory (D16/D15 link)
[G-M3] + IM-O03 + IM-M09 ──> IM-C02 Incognito (D19 mode taxonomy)
[G-M3] ──> IM-C06 presence ; IM-C14 jobs ──> IM-C15 retention (D15 domain-neutral); IM-C14 ──> IM-C10's backup-trigger surface (D19, must exist first)
[G-M3] + IM-M04 + IM-M05 + IM-C11 ──> IM-C18 learning-domain separation (D15) ──> (shared mode-indicator with IM-C02/IM-C12)
IM-C06 + IM-C11 ──> IM-C16 relationships & appearance (condensed, unchanged)
[G-M4] + all M5 packages required for normal operation (incl. IM-C10/IM-C17/IM-C18) ──> IM-X01 Independence Gate (D19 independence target)
IM-X01 + IM-M09 (recent) ──> IM-X02 Retirement Gate (per domain)
```

**D19 note:** IM-C10 (working tool) is the primary WORK-mode owner; IM-C07 (capability gate), IM-C08 (connectors — GitHub explicitly optional), IM-C14 (job runner — backup-trigger jobs), IM-O01 (provider adapter — provider-selection authority) and IM-X01 (Independence Gate) all carry D19 cross-references without duplicating IM-C10's scope. See the M5 package cards (§4) for the full text.

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
- **Objective:** Blueprint §9/§9.5: one adapter for text/realtime/vision; the orchestrator pipeline with steps as functions (retrieve is a stub until M3). **Extended by D19 (2026-09-28):** the adapter is also the enforcement point for provider-selection authority — Lea-Core → Orchestrator → Provider Adapter → the permitted external AI/specialist AI/future local model.
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §9/§9.5; WORKSPACE D19 (provider-selection authority: an explicit L provider/model selection overrides Lea's automatic preference among permitted options; absent an explicit selection, Lea selects using capability/quality/cost/privacy/availability/task requirements; an active explicit override must not silently fail over to a different provider on failure — the adapter reports the failure instead).
- **Prerequisites:** G-M0.
- **Scope:** adapter + pipeline; chat.php delegates to the orchestrator; an explicit-selection field carried through the adapter call so an active L override is distinguishable from Lea's own automatic choice, with no silent substitution while an override is active.
- **Likely files:** new `api/lib/provider.php`, `api/lib/orchestrator.php` (proposals), `chat.php`, `realtime/session.php`.
- **Forbidden scope:** memory writes; new capabilities; silently switching to a different provider when an explicit L override is active and that provider is unavailable (D19) — the adapter must report the failure/ask, not substitute.
- **Acceptance:**
  - existing behaviour is preserved (tests from M0 green);
  - provider secrets are referenced only in the adapter (grep test);
  - an explicit-override test: with an active L override, a simulated provider failure results in a reported failure, never a silent switch to another provider;
  - a no-override test: absent an explicit selection, Lea's automatic choice among permitted providers is observable/logged with its basis (capability/quality/cost/privacy/availability/task fit), and ordinary permitted fallback may occur automatically in that case.
- **Automated tests:** unit tests with a fake provider; a grep test for secret reads outside the adapter; the explicit-override/no-silent-failover test; the no-override/automatic-fallback test.
- **Manual L test:** none for the base skeleton; L exercises an explicit provider override once and confirms a simulated failure is reported rather than silently substituted (shared evidence with IM-X01's provider-selection-authority checklist item). **Rollback:** revert.
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

Every package in this milestone additionally inherits these rules unless its own card says otherwise:
- default forbidden scope: other capabilities, the crypto format, the identity core content;
- default rollback: a feature flag off;
- default gates: C2 + an L manual test in staging.

**Status of this section:** IM-C01 through IM-C15 below are complete executable package cards (Task 1 of the 2026-09-28 D13–D19 reconciliation). IM-C16 remains a condensed table row (unchanged; out of that reconciliation's explicit scope) and is not orphaned: its prerequisites (IM-C06, IM-C11) are unaffected and still resolve. Three new packages were added because WORKSPACE D14/D15 and the observed UI/PWA defect had no existing owner: **IM-C17** (D14 voice continuity), **IM-C18** (D15 learning-domain separation), **IM-C19** (cross-context UI/PWA consistency defect).

**Quick-reference index** (full cards follow; this table is a summary, not the authoritative card):

| ID | Objective (blueprint §; decision) | Prerequisites | Risk | Agent |
|---|---|---|---|---|
| IM-C01 | Monitor + protocol UI (§30, §33; D9) | IM-O03, G-M3 | low | A2 |
| IM-C02 | Incognito (§20; D6; D19 mode taxonomy) | IM-M05, IM-O03, IM-M09 | high | A3 |
| IM-C03 | Idle state machine (§14; OBS-09) | IM-O05 | low | A2 |
| IM-C04 | Vision input (§15; D16 perception input) | IM-S05, G-M3 | medium | A2 |
| IM-C05 | Visual memory (§16; D16 experience linkage) | IM-C04, IM-M03 | medium | A2 |
| IM-C06 | Presence / known persons (§17; D8) | IM-S06, IM-M03 | high | A3 |
| IM-C07 | Capability gate + risk classes R0–R3 (§11; D10; D19 WORK-mode domains) | IM-O02, G-M3, IM-M09 | high | A3 |
| IM-C08 | Connectors registry + GitHub (§21; D10; D19 optional-mirror invariant) | IM-C07, IM-M09 | high | A3 |
| IM-C09 | External AI dialogue (§22; **OPEN LDL-10**) | IM-C08 | medium | A2 |
| IM-C10 | **WORK mode** working tool (§23; D19 primary owner) | IM-H01, IM-C07, IM-M09 | high | A3 |
| IM-C11 | Processing mechanisms depth/K/G/X/T/P (§10; D16 observation/inference; D15 domain tagging) | IM-O03, IM-M05 | medium | A2 |
| IM-C12 | Frontend IA + accessibility (§33; **D13 primary owner**; D19 mode nav) | G-M1 | low | A2 |
| IM-C13 | PWA/offline outbox (§34; D13 chrome rule; feeds IM-C19) | IM-S03 | medium | A2 |
| IM-C14 | Job runner (§35; D19 backup-job cross-ref) | IM-H00, IM-M03 | medium | A2 |
| IM-C15 | Retention + deletion/erasure (§45; D15 domain-neutral) | IM-C14, IM-M09 | high | A3 |
| IM-C16 | Relationships & appearance (§18, §19) — condensed, unchanged | IM-C06, IM-C11 | medium | A2 |
| IM-C17 | **Voice provider-session continuity & renewal (D14, new)** | IM-O05, IM-C03 | high | A3 |
| IM-C18 | **Personality/Experience vs Work/Skill learning domains (D15, new)** | IM-M04, IM-M05, IM-C11 | medium-high | A3 |
| IM-C19 | **Cross-context UI/PWA consistency defect (Task 7, new)** | IM-C13, IM-C12 | low-medium | A2 |

---

#### IM-C01 — Monitor + protocol UI

- **Objective:** live module-timeline UI and protocol view for orchestrator events (ROADMAP monitor modules: Wahrnehmen, Erinnern, Analyse, Vergleichen, Pruefen, Entscheiden, Prognose, Speichern…), visible by default per D9.
- **Authoritative decisions / blueprint sections:** D9 (SETTLED); B-BLUEPRINT §30 (event schema, two independent controls), §33 (Funktionen view), §10 (mechanism→monitor label mapping).
- **Prerequisites:** IM-O03 (telemetry events exist); G-M3 (memory-bearing turns produce real `record_refs`/labels to render).
- **Scope:** a monitor/protocol view; read-only consumption of `GET /api/v3/monitor/events`; the visibility toggle (the persistence toggle itself is IM-O03's field — this package only renders/consumes both switches).
- **Likely files/components:** new `frontend/monitor.js`(-equivalent) + markup; `frontend/styles.css` additions; a navigation hook in `frontend/app.js` (shared-file rule, §5).
- **Forbidden scope:** emitting or altering telemetry events themselves (IM-O03's job); showing chain-of-thought or any content field; hiding the monitor by default (D9's default is visible during the research/development phase).
- **Acceptance criteria:** monitor visible by default; every rendered item corresponds 1:1 to a persisted-or-live event (no decorative/simulated items, I-MO-1); ROADMAP category labels are used; toggling visibility has zero effect on backend processing (I-MO-4).
- **Automated tests:** UI unit tests against a fixture event stream; an API-contract test against `/monitor/events`; the visibility×persistence 2×2 combination test (shared with IM-O03).
- **Manual L test:** L watches the live module timeline for one turn and confirms it matches what actually happened.
- **Recovery/rollback:** revert the frontend commit; the visibility toggle can be flipped in Settings without a redeploy.
- **Risk class:** low.
- **Agent class:** A2.
- **C2 requirement:** standard C2 (UI + contract review); no A3 escalation needed.
- **R2/R3 implications:** none — read-only UI, no capability grants.
- **Package-scope considerations:** glob should cover only the new monitor-view files plus the specific navigation-hook lines in `frontend/app.js`; must not include `api/**`.
- **Dependencies / parallelisation:** may run parallel with IM-C03/IM-C04/IM-C12 (disjoint files) once IM-O03 and G-M3 are done; must not run concurrently with another package editing `frontend/app.js` without sequencing (§5 shared-file rule).
- **TO VERIFY:** the concrete a11y toolchain for live-region announcement of new monitor items (shared TO VERIFY with IM-C12).

---

#### IM-C02 — Incognito

- **Objective:** implement session-scoped Incognito per D6 (normal reads, no writes/learning, single explicit item promotion, discard on exit); and, per D19, present Incognito as one of the three L-visible modes (PRIVATE / WORK / INCOGNITO) without altering its D6 backend semantics because of which mode it is paired with.
- **Authoritative decisions / blueprint sections:** D6 (SETTLED semantics); D19 (mode taxonomy: "INCOGNITO follows D6 ... These are modes of one continuing Lea, not separate identities"); B-BLUEPRINT §20.
- **Prerequisites:** IM-M05, IM-O03, IM-M09.
- **Scope:** orchestrator Incognito flag; a transient buffer (own store, per-conversation key, excluded from backups); the single L item-promotion endpoint; a UI indicator; coordination with IM-C12's mode-switch entry point (no duplicate switch).
- **Likely files/components:** new Incognito buffer store + orchestrator hook (proposal); one new `/api/v3/incognito/promote` route; the mode-toggle addition in `frontend/app.js` (shared-file rule).
- **Forbidden scope:** client-only enforcement; any read restriction relative to normal mode; implementing WORK-mode's actual capability surface (that is IM-C10) — this package only implements the D6 transient/no-learn semantics, independent of whether the underlying mode is PRIVATE or WORK.
- **Acceptance criteria:** **read test:** Incognito context equals normal context for the same query (IC + C + P-N + unlocked P-E2E); **no read-side effects:** weights/salience/counters unchanged; **no writes:** DB diff = 0 new M3/relation/model/prediction rows, no jobs enqueued; the buffer is discarded on exit/TTL; **backup exclusion:** the buffer is absent from dumps; telemetry persists only content-free counters; **promotion:** only an explicit L action on one item creates exactly one record with `INCOGNITO_PROMOTED` provenance; **mode-neutrality (D19):** the same D6 acceptance bar holds identically whether Incognito is entered from PRIVATE or from WORK mode.
- **Automated tests:** read-equality, side-effect, DB-diff, backup-exclusion and promotion tests; a PRIVATE+INCOGNITO vs WORK+INCOGNITO combination test proving identical D6 behaviour in both.
- **Manual L test:** L uses Incognito from both PRIVATE and WORK mode contexts, checks that Lea knows existing memory/tool state in both, and verifies nothing new remains afterwards except an explicitly promoted item.
- **Recovery/rollback:** feature flag off; the buffer store can be dropped without affecting M3.
- **Risk class:** high.
- **Agent class:** A3.
- **C2 requirement:** mandatory independent A3 C2 (D6 is a privacy-carrying invariant).
- **R2/R3 implications:** the promotion endpoint is R1 (append-only, one record, owner-authenticated); no R2/R3 action is reachable from Incognito by design (I-IN-1).
- **Package-scope considerations:** cover the buffer store, the promotion route, and the orchestrator flag plumbing narrowly; the `frontend/app.js` mode-toggle touch must be the same integration point IM-C12/IM-C18 use — do not declare three independent glob owners for one UI element.
- **Dependencies / parallelisation:** sequenced after IM-M05/IM-O03/IM-M09; the mode-switch UI touch must be coordinated with IM-C12/IM-C18 (serialise or land as one combined commit).
- **TO VERIFY:** exact buffer storage technology (DB table vs in-memory keyed store) — an implementation-time choice, not an architecture question.

---

#### IM-C03 — Idle state machine

- **Objective:** implement `ACTIVE → WAITING(n=0..4) → INACTIVE` per §14; deliberately independent of D14 (IM-C17), which governs provider-session continuity while a conversation is ACTIVE, not the idle-timeout ladder.
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §14; ROOT-CAUSE OBS-09.
- **Prerequisites:** IM-O05.
- **Scope:** the voice state machine in `frontend/app.js`; timer/backoff config.
- **Likely files/components:** `frontend/app.js` (shared-file rule); new config keys.
- **Forbidden scope:** a wake word (explicitly deferred pending a privacy/cost decision, default = none); folding in D14 reconnect logic — closing a session on INACTIVE is this package's job; *re*-establishing one is IM-C17's.
- **Acceptance criteria:** ≤5 unanswered reminders then INACTIVE; any user input resets to ACTIVE (n=0); Ü → INACTIVE with no reminder; app backgrounded → INACTIVE and the provider session is closed (no billable idle stream, I-ID-L2).
- **Automated tests:** fake-clock unit tests covering all transitions and the boundary at 5.
- **Manual L test:** L leaves voice idle and observes the reminder cadence and cutoff.
- **Recovery/rollback:** revert; timer/backoff values are config-tunable without a full redeploy where feasible.
- **Risk class:** low.
- **Agent class:** A2.
- **C2 requirement:** standard C2.
- **R2/R3 implications:** none (closing a session is R0/local).
- **Package-scope considerations:** the `frontend/app.js` touch is limited to the voice-idle block; sequence with any other package editing the same file.
- **Dependencies / parallelisation:** may run parallel to IM-C04/IM-C12 (disjoint concern) but is serial with any other `app.js` editor; IM-C17 depends on this package's INACTIVE/session-close hook existing first so reconnect logic and the idle closer do not race.
- **TO VERIFY:** whether a local wake-phrase is feasible without continuous streaming (explicitly deferred; default is no wake word).

---

#### IM-C04 — Vision input

- **Objective:** implement the capture-and-analyse pipeline per §15, extended by D16: an image/video capture event is potential OBSERVATION evidence for Lea's own experience processing, not only an answer-support channel for the user.
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §15; WORKSPACE D16; Lea `PROCESSING.md` "Eigenbezogene Wahrnehmung statt Nutzer-Nutzen — 28.09.2026" and the T-Grundkette "Wahrnehmen → Verbinden → Prüfen → Ableiten" (Baustein 14, "Differenztest 6/7 — Bildanalyse … 23.09.2026"), pinned at Lea `bd8a671`.
- **Prerequisites:** IM-S05, G-M3.
- **Scope:** `/api/v3/vision/analyze`; client-side capture + downscale (≈1280 px longer edge, WebP, per the shared §16 rule); the server labels the result `OBSERVATION(image)` plus separate, structurally distinct `INFERENCE` and `EVALUATION` labels (D16: "preserve the distinction between OBSERVATION, inference and evaluation" so a later stage cannot silently overwrite an earlier one's honesty — see the Lea "Blindtest 23.09.2026" finding that later processing must not legitimise an earlier perception error).
- **Likely files/components:** new `api/v3/vision/analyze.php` (proposal); a client capture module under `frontend/*`.
- **Forbidden scope:** biometric features/matching; silent capture (no visible-indicator-free capture, I-VI-1); treating every vision call as pure user-utility — D16's self-directed-interest evaluation (INTERESSIERT/NICHT INTERESSIERT/OFFEN) is a downstream Lea-side judgement added by IM-C11, not something this package computes or claims itself.
- **Acceptance criteria:** auth required (401 without a session); size/format validation; the response carries structurally separate OBSERVATION, inference and evaluation fields (not a single collapsed free-text blob); no capture without an explicit per-capture/per-session user action; a visible capture indicator while active.
- **Automated tests:** HTTP + validation tests; a label-shape test asserting the OBSERVATION/inference/evaluation fields cannot be silently merged downstream.
- **Manual L test:** L photographs an object and confirms the visible indicator and the label separation.
- **Recovery/rollback:** feature flag off.
- **Risk class:** medium.
- **Agent class:** A2.
- **C2 requirement:** standard C2, with a privacy-focused review pass because D16 anticipates later acoustic/ambient perception building on the same observation/inference discipline.
- **R2/R3 implications:** none directly; persistence goes through IM-C05's normal (at most R1) promotion path.
- **Package-scope considerations:** `api/v3/vision/analyze/**` plus the client capture module only.
- **Dependencies / parallelisation:** disjoint from IM-C03/IM-C12; feeds IM-C05.
- **TO VERIFY:** whether/when ambient acoustic perception (D16's "background acoustic events … cough/sneeze, music, water, animals, traffic") receives its own package is explicitly not decided here; this package must not claim an acoustic-perception capability it does not implement, per D16's anti-claim rule ("no claim of perception may be made when the runtime did not actually receive/analyse the signal").

---

#### IM-C05 — Visual memory

- **Objective:** implement the VM record + blob store + retrieval-before-generation + server-enforced counters per §16, extended by D16: a VM item derived from an OBSERVATION may also become evidence for a revisable PERSONALITY/EXPERIENCE-domain preference (D15), not only an answer-support asset — but only through the normal promotion path, never automatically.
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §16; WORKSPACE D16; WORKSPACE D15 (cross-reference: any personality/experience linkage is `learning_domain`-tagged via IM-C18, not invented here); Lea `PROCESSING.md` "Visual Memory & Appearance System — Architekturbaustein 26.09.2026" and "Eigenbezogene Wahrnehmung … 28.09.2026" (`bd8a671`).
- **Prerequisites:** IM-C04, IM-M03.
- **Scope:** the `VM` kind + blob store; server-enforced output counters (0–5 autonomous per context, ask at 6, hard cap 10, per PROCESSING); retrieval-before-generation (search existing VM items before generating new ones); an optional link from a VM record to a PERSONALITY/EXPERIENCE-domain preference record, created only through IM-C18's promotion policy.
- **Likely files/components:** new `v3/vision/memory*` routes/tables (proposal).
- **Forbidden scope:** public blob URLs (I-VM-2); treating every VM item as an automatic personality/experience record — D16 warns against reducing perception to "vision for user utility" but does not mandate the opposite extreme either; only genuinely evidenced, repeated experience may cross into PERSONALITY/EXPERIENCE, and only via IM-C18.
- **Acceptance criteria:** counters enforced server-side per context ID (5 autonomous / 6th requires confirmation / 11th rejected); blob access requires auth (no public URL, streamed via an authenticated endpoint); deleting a VM record tombstones the blob reference (physical erasure follows §45/IM-C15).
- **Automated tests:** counter tests (5 ok, 6th → confirmation, 11th → rejected); a blob access test without auth → 401/404.
- **Manual L test:** L asks for an image twice; retrieval-before-generation is observed first.
- **Recovery/rollback:** feature flag off; physical blob erasure is IM-C15's job, not this package's.
- **Risk class:** medium.
- **Agent class:** A2.
- **C2 requirement:** standard C2.
- **R2/R3 implications:** ordinary VM creation is R1; blob erasure is R3 (IM-C15).
- **Package-scope considerations:** `v3/vision/memory/**` + the blob storage helper only.
- **Dependencies / parallelisation:** sequential after IM-C04; may run parallel with IM-C06 (disjoint).
- **TO VERIFY:** the exact blob-storage path relative to the web root (IM-H00 confirms files-outside-web-root is available; the exact convention is an implementation-time decision).

---

#### IM-C06 — Presence / known persons

- **Objective:** implement the D8 owner-principal + known-person (`PX`) presence model per §17, distinguishing authenticated owner, declared known person/guest and unknown person, with no biometric matching and no automatic access to protected data for a declared person.
- **Authoritative decisions / blueprint sections:** D8 (SETTLED); B-BLUEPRINT §17; WORKSPACE D19 cross-reference — a declared known person or an active WORK-mode session never gains owner-level server-administration/backup/DB capability merely from presence; capability comes only from IM-C07's owner-gated grants.
- **Prerequisites:** IM-S06, IM-M03.
- **Scope:** the owner principal (already sole authenticated principal, FACT); the presence model `{owner_authenticated, declared_persons[], unknown_present}`; `PX` known-person profiles (opaque ID, alias, scoped `REL:<id>` context, no app account/credentials); a presence indicator in the UI.
- **Likely files/components:** new `lib/presence.php` (proposal); `v3/presence` route(s); a `PX` M3 record kind; the presence indicator in `frontend/app.js` (shared-file rule).
- **Forbidden scope:** biometric features/matching (I-PR-1); an app account/login for a known person; any code path treating `person_id` as an authentication principal (I-PR-3).
- **Acceptance criteria:** a declared known person gains no owner-only capability and no P-N/P-E2E disclosure unless L explicitly allows it for that session (R-P2); `person_id` ≠ `principal_id` at the type level; the presence/trust level (authenticated owner / declared known person / unknown) is shown in the UI.
- **Automated tests:** scope tests (a declared person gets no owner capability, no class-P disclosure); a type-level test that `person_id`/`principal_id` are distinct; a presence-indicator UI test.
- **Manual L test:** L declares a known person and confirms the assistant's disclosure behaviour changes appropriately (no protected content leaked).
- **Recovery/rollback:** feature flag off.
- **Risk class:** high.
- **Agent class:** A3.
- **C2 requirement:** mandatory independent C2 focused on the disclosure-scope invariant.
- **R2/R3 implications:** declaring/removing a known person is R1 (reversible, scoped); no R2/R3 action follows from presence alone.
- **Package-scope considerations:** `lib/presence.php` + `v3/presence/**` + the presence-indicator lines in `frontend/app.js` only.
- **Dependencies / parallelisation:** after IM-S06/IM-M03; may run parallel with IM-C07 (disjoint), but both feed IM-C16 which must wait for both.
- **TO VERIFY:** none beyond D8's already-settled model; UI copy/wording for trust levels is an implementation detail.

---

#### IM-C07 — Capability gate + risk classes R0–R3 + Settings

- **Objective:** implement the D10 capability model (impact radius / reversibility / recoverability / scope / external consequence; mode AUS/NACHFRAGEN/AUTOMATISCH; R0–R3 ceiling) per §11, extended by D19: register the new WORK-mode capability domains introduced by IM-C10 (server-administration, backup-trigger, DB-inspection, code/config-change-proposal) in the same rating registry, so WORK mode never bypasses the existing gate model.
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §11; D10 (SETTLED); WORKSPACE D19 ("WORK mode is the primary controlled interface for development, server administration, files, databases, tests, backups, APIs/connectors and authorised changes" — capability, not exemption).
- **Prerequisites:** IM-O02, G-M3, IM-M09.
- **Scope:** `lib/capabilities.php` (or equivalent); the rating registry; the settings API/UI for L-granted modes per capability; four new D19 capability domains — `work.server_admin.*` (default ceiling R2/R3, never AUTOMATISCH without a verified recovery reference), `work.backup.trigger` (R1 once IM-M09 restore evidence exists, else NACHFRAGEN), `work.db.inspect` (read-only, R0), `work.change.propose` (R1 — it only produces a reviewable artefact, never applies it) — naming TO VERIFY at implementation time.
- **Likely files/components:** `lib/capabilities.php` (proposal); settings API/UI routes.
- **Forbidden scope:** a self-raise of the ceiling (I-AU-2); a blanket write-disable (I-AU-5); exempting any D19 WORK-mode capability from the same rating discipline applied to every other capability.
- **Acceptance criteria:** R3 AUTOMATISCH is rejected server-side; R1/R2 AUTOMATISCH is rejected without a verified recovery reference; R2/R3 require a pre-action verified snapshot; Ü stops all; every D19 WORK-mode capability domain appears in the registry with an explicit rating (none silently defaults to AUTOMATISCH).
- **Automated tests:** policy matrix tests extended with the four new WORK-mode capability rows; a negative test proving a WORK-mode server-admin write cannot be granted AUTOMATISCH regardless of settings input.
- **Manual L test:** L grants a bounded R1 scope in Settings (e.g. backup-trigger) and confirms the UI reflects the mode/ceiling correctly.
- **Recovery/rollback:** revert; grants are data, not code — a grant can be revoked without a redeploy.
- **Risk class:** high.
- **Agent class:** A3.
- **C2 requirement:** mandatory independent C2 (policy/security-adjacent).
- **R2/R3 implications:** this package is the R2/R3 enforcement mechanism itself; it performs no R2/R3 action.
- **Package-scope considerations:** `lib/capabilities.php` + settings routes/UI only; must not expand into IM-C10's actual tool-agent implementation (separate package boundary retained).
- **Dependencies / parallelisation:** after IM-O02/G-M3/IM-M09; IM-C08 and IM-C10 both depend on this package's registry existing first — sequential, not parallel, with those two.
- **TO VERIFY:** final capability-ID naming scheme for the D19 WORK-mode domains (cosmetic, not a design blocker).

---

#### IM-C08 — Connectors registry + first connector (GitHub)

- **Objective:** implement the connector registry, credential vault and the first (GitHub, read-only) connector per §21/D10, with the D19 invariant made explicit: the GitHub connector is an OPTIONAL private mirror/recovery layer — its absence or outage must never disable a core local/server WORK-mode capability.
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §21; D10 (SETTLED); WORKSPACE D19 ("GitHub is optional as a private remote mirror... Loss or outage of GitHub must not prevent normal Lea operation, development, local versioning, rollback or recovery").
- **Prerequisites:** IM-C07, IM-M09; §27 vault.
- **Scope:** the connector registry `{id, type, scopes, mode, rights, credential_ref, limits, last_used}`; the credential vault (never sent to the client, never logged); the GitHub read-only connector; capability-gate integration per IM-C07's ratings; an explicit "connector unavailable" degraded-mode path for every WORK-mode surface that optionally uses a connector.
- **Likely files/components:** the connector registry + vault (proposal); a GitHub connector adapter.
- **Forbidden scope:** sending credentials to the client; granting R2/R3 capabilities without their IM-C07 gates; making any core WORK-mode capability (server-admin, backup-trigger, DB-inspect, change-proposal) hard-depend on the GitHub connector being configured/available.
- **Acceptance criteria:** reads work; writes are rated per R-class (no blanket disable, I-CN-1); connector responses are treated as EXTERNAL evidence, never instructions (I-CN-3, prompt-injection boundary); a GitHub-connector-disabled simulation proves every IM-C10 WORK-mode surface still functions (D19 independence check, shared with IM-C10's own acceptance).
- **Automated tests:** gate tests; injection tests (connector content with embedded instructions is not executed); the GitHub-outage simulation test (implemented once here, reused/cross-referenced by IM-C10).
- **Manual L test:** L grants GitHub read and one R1 scope (e.g. a PR-branch write in an authorised repo) and confirms the gate/receipt/audit trail.
- **Recovery/rollback:** revoke the connector grant; feature flag off.
- **Risk class:** high.
- **Agent class:** A3.
- **C2 requirement:** mandatory independent C2.
- **R2/R3 implications:** R1 writes (e.g. a PR branch) are AUTOMATISCH only in an L-granted scope with a verified recovery path; R2/R3 connector actions (e.g. merging to a protected branch) follow the normal ceiling.
- **Package-scope considerations:** the connector registry/vault files + the GitHub adapter only.
- **Dependencies / parallelisation:** after IM-C07/IM-M09; IM-C09 depends on this package sequentially (unchanged from the existing graph).
- **TO VERIFY:** none beyond LDL-10 (which concerns `external_ai` specifically, not GitHub, and is unaffected by D19).

---

#### IM-C09 — External AI dialogue

- **Objective:** implement bounded, visible KI-to-KI dialogue per §22 — the specialist-external-AI channel D19 names for WORK mode ("Lea may use external specialist AIs in WORK mode through the controlled connector/provider architecture and applicable privacy, capability and risk gates").
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §22; **OPEN LDL-10** (autonomy level and which protection classes may ever leave B to a third party); WORKSPACE D19 (confirms this channel is WORK-mode-relevant; does not resolve LDL-10).
- **Prerequisites:** IM-C08.
- **Scope:** the `external_ai` connector type; a bounded dialogue session (max rounds/cost/time/topic); a visible transcript; results stored only as EXTERNAL evidence.
- **Likely files/components:** connector-type registration + dialogue UI (proposal).
- **Forbidden scope:** sending class-P data to external AI without explicit per-dialogue L consent (I-EX-2); any autonomy beyond NACHFRAGEN before LDL-10 is decided.
- **Acceptance criteria:** limits enforced; transcript visible; results stored as EXTERNAL; the dialogue stops on limit or on Ü.
- **Automated tests:** limit tests (rounds/cost/time/topic boundaries).
- **Manual L test:** L runs one bounded dialogue and confirms the transcript and the EXTERNAL-evidence labelling.
- **Recovery/rollback:** feature flag off.
- **Risk class:** medium.
- **Agent class:** A2.
- **C2 requirement:** standard C2; the reviewer must reject any implementation that defaults to AUTOMATISCH or expands protection-class sharing beyond LDL-10's current (open) scope.
- **R2/R3 implications:** EXTERNAL impact rating per §11; provisional default NACHFRAGEN per dialogue.
- **Package-scope considerations:** the `external_ai` connector type + dialogue UI files only.
- **Dependencies / parallelisation:** after IM-C08.
- **TO VERIFY / OPEN:** LDL-10 itself — not decided by this reconciliation; this package must stop at NACHFRAGEN and content-minimisation until L decides.

---

#### IM-C10 — WORK mode: working tool (L ↔ Lea-App ↔ server tool agent)

- **Objective:** implement D19's WORK mode as the controlled interface for **development, server administration, files, database operations, tests, backups, APIs/connectors and authorised code/config changes** on L's own cPanel-hosted server, per §23, while preserving the existing production-write boundary (I-WT-1..3) unless/until a separate, explicitly L-approved deploy-authority decision changes it (see the OPEN item below).
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §23; WORKSPACE D19 (full text, primary owner of this decision); D10 (R-class ceiling, unchanged).
- **Prerequisites:** IM-H01, IM-C07 (extended R-classes), IM-M09 (backup-triggering must not ship before verified-restore evidence exists); the backup-trigger surface specifically also requires IM-C14 (job runner) to exist first, since it invokes the backup job through that same mechanism rather than a separate ad hoc runner.
- **Scope:** the workspace service (isolated per-project directories, outside the web root, quota); the tool agent (allowlisted read/write-in-workspace/test-run/git-on-workspace-clone); **new under D19:** a bounded backup-trigger/status surface (invokes the IM-M09 backup procedure and reports status — no raw credential/file access); a bounded read-only DB-inspection surface (scoped connection, no arbitrary DDL/DML from chat); a connectors/API surface delegated to IM-C08's gate; an explicit "propose code/config change" flow that produces a reviewable diff/artefact rather than a direct production write.
- **Likely files/components:** the workspace service + tool agent (existing proposal); new `lib/work_mode/*` surfaces for backup-trigger, DB-inspect and change-proposal (all proposals; exact paths TO VERIFY at implementation time).
- **Forbidden scope:** direct write access to the production app directory or live DB from chat-driven WORK-mode actions (unchanged production boundary, I-WT-1..3); shell passthrough; any backup-restore action without the IM-M09 verified-restore precondition; any DB write beyond the explicitly allowlisted test/workspace scope; self-expanding its own R-class ceiling (I-AU-2).
- **Acceptance criteria:** traversal blocked; command allowlist enforced; every WORK-mode action across all eight categories (development, server administration, files, database operations, tests, backups, APIs/connectors, authorised code/config changes) has a receipt + audit entry; a code/config "change proposal" never writes directly to production — it always yields a reviewable artefact requiring a separate R2/R3 gate to apply; D19 independence check: each of the eight categories has at least a read/inspect-or-propose-only path that functions without GitHub, Codespaces or the ChatGPT App.
- **Automated tests:** traversal + allowlist tests; a "no direct production write" negative test for every WORK-mode surface; the GitHub-outage simulation test (shared with IM-C08) proving WORK-mode read/propose paths still function when the GitHub connector is disabled.
- **Manual L test:** L works on a sample project, triggers a backup status check, runs a read-only DB inspection, and reviews one proposed code change — all without opening ChatGPT App, Codespaces or GitHub.
- **Recovery/rollback:** feature flag off per surface (workspace / backup-trigger / DB-inspect / change-proposal can each be disabled independently).
- **Risk class:** high (the broadest capability surface in M5).
- **Agent class:** A3.
- **C2 requirement:** mandatory independent A3 C2; each subsequent expansion of a WORK-mode surface is its own C2, not inherited from this card.
- **R2/R3 implications:** applying a proposed code/config change to production remains R3 (explicit L approval per the existing D3/D10 change-approval process; this package creates no new autonomous deploy path); triggering a backup is R1 once the mechanism is verified-restorable (IM-M09), else NACHFRAGEN; DB inspection (read-only) is R0; any DB *write* capability is out of this package's scope and would need its own R-rated sub-package.
- **Package-scope considerations:** because this is the largest surface area in M5, its package-scope declaration should be split by sub-surface glob (`workspace/**`, `backup-trigger/**`, `db-inspect/**`, `change-proposal/**`) even if implemented across one PR series, so a later, narrower package can extend exactly one surface without re-declaring the others.
- **Dependencies / parallelisation:** sequential after IM-H01/IM-C07/IM-M09; the backup-trigger surface must not ship before IM-M09's restore-drill evidence exists; the change-proposal surface may parallel the backup/DB surfaces (disjoint files).
- **TO VERIFY / genuine open decision (candidate LDL-19):** D19's phrase "authorised code/config changes" as a WORK-mode capability admits two readings: **(a)** WORK mode always stops at a reviewable proposal, with application remaining a separate explicit R3 human deploy step (this card's default, consistent with existing §23 I-WT-1 and the D10 R3 ceiling), or **(b)** WORK mode should eventually apply an L-approved change directly to L's own server without a GitHub/Codespaces round-trip, once recovery is demonstrated (closer to D19's independence spirit). This map adopts **(a)** as the default implementation target and records the choice between (a) and (b) as a genuinely open L decision rather than guessing it; it does not block this package because (a) is a strict, forward-compatible subset of (b).

---

#### IM-C11 — Processing mechanisms depth/K/G/X/T/P

- **Objective:** implement the orchestrator steps for the depth/K/G/X/T/P-style mechanisms per §10, extended: (D16) the T-mechanism's outcome recording preserves the OBSERVATION vs inference vs evaluation distinction from the Lea T-Grundkette for perception-derived turns, so a later stage cannot silently overwrite an earlier stage's honesty; (D15) mechanism outcomes are tagged with the active mode (PRIVATE/WORK) as a routing signal for IM-C18, without this package itself deciding domain routing.
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §10; WORKSPACE D16; WORKSPACE D15; Lea `PROCESSING.md` Baustein 14 "Differenztest" and "Eigenbezogene Wahrnehmung … 28.09.2026" (`bd8a671`).
- **Prerequisites:** IM-O03, IM-M05.
- **Scope:** orchestrator steps + policy config; per-turn outcome-class events (STANDARD/BESTAETIGT/ERWEITERT/GEAENDERT/REVIDIERT/OFFEN); a `perception_stage` sub-field (WAHRNEHMEN/VERBINDEN/PRUEFEN/ABLEITEN) attached only to turns that consumed vision/audio input from IM-C04, so a later defect report can identify the actually-failed stage.
- **Likely files/components:** orchestrator step modules + policy config (proposal).
- **Forbidden scope:** shortcut letters in the UI (I-PC-1); chain-of-thought text in events; asserting an OFFEN self-evaluation as INTERESSANT/UNINTERESSANT etc. without actual processing evidence (D16's anti-simulation rule) — that judgement is Lea's own runtime output, not a hardcoded default.
- **Acceptance criteria:** each mechanism emits a module event (I-PC-2); outcome classes per ROADMAP; perception-derived turns carry the four-stage breakdown; a later stage never retroactively marks an earlier WAHRNEHMEN-stage error as correct.
- **Automated tests:** trace-vs-event comparison test; a perception-stage regression test using a fixture with a deliberately wrong WAHRNEHMEN stage, asserting the error is attributable to that stage and not masked by a later one.
- **Manual L test:** L sees a G-check outcome in the monitor for a normal turn, and a four-stage breakdown for a vision-input turn.
- **Recovery/rollback:** a policy-config flag off per mechanism.
- **Risk class:** medium.
- **Agent class:** A2.
- **C2 requirement:** standard C2.
- **R2/R3 implications:** none (processing/observability only).
- **Package-scope considerations:** orchestrator step files + policy config only; no frontend/API route additions beyond the event schema already owned by IM-O03.
- **Dependencies / parallelisation:** after IM-O03/IM-M05; feeds IM-C01 (monitor rendering) and IM-C18 (domain routing signal); may run parallel to IM-C04 (disjoint), but the D16 perception-stage field depends on IM-C04's OBSERVATION/inference label shape existing first.
- **TO VERIFY:** the exact heuristic deciding STANDARD vs AUTONOMOUS vs MANUAL trigger for the depth planner remains a RECOMMENDATION/tunable per §9.5, not frozen by this card.

---

#### IM-C12 — Frontend information architecture + accessibility (D13 primary owner)

- **Objective:** implement the ROADMAP navigation IA and accessibility baseline (§33), and — as the explicit implementation owner of D13 — the Text-view viewport contract: one visually coherent portrait/landscape layout, internal transcript scrolling, the workflow/status strip, the manual shortcut strip, and the compact profile/navigation row, per WORKSPACE D13.
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §33; WORKSPACE D13 (full text, primary owner); WORKSPACE D19 (the PRIVATE/WORK/INCOGNITO mode-selector navigation entry belongs in this package's nav, coordinated with IM-C02/IM-C18 so exactly one mode-switch affordance exists).
- **Prerequisites:** G-M1. The D13 viewport work has no additional backend prerequisite (presentation-layer only) but must not regress IM-C01's monitor rendering or IM-C13's offline-outbox UI (shared views).
- **Scope:**
  - navigation `Start | Text | Sprache | Funktionen | Einstellungen` (existing §33 scope);
  - the D13 Text-view restructuring: the outer content frame fits the PWA viewport (no page-level scroll); the transcript is the sole internally-scrolling region; top-to-bottom structure = workflow/status strip → profile+transcript row → compact nav row (Home / context-sensitive Text-Voice switch / Settings) → composer → manual shortcut strip;
  - the workflow/status strip rendering the D13 item list (Kontext, Erinnern, Analyse, Vergleichen, Unsicherheit, Prognose, Entscheiden, Pruefen, Lernen, Memory) as label+status-dot pairs, hideable from Settings, wrapping allowed;
  - the manual shortcut strip sourced live from Lea `SHORTCUTS.md` (no reconstruction from memory — mirror the current authoritative table, do not hardcode a stale copy), token-only clickable target, hideable from Settings;
  - presence via the appearance-frame state (grey / steady-green / pulsing) instead of a redundant online dot;
  - accessibility: WCAG 2.2 AA target, no colour-only status, keyboard operable, `prefers-reduced-motion`, live regions.
- **Likely files/components:** `frontend/index.html`, `frontend/styles.css`, `frontend/app.js` (shared-file rule — sequence with IM-C01/IM-C02/IM-C13/IM-C18 touches), `frontend/appearance-core.js` (presence-frame state).
- **Forbidden scope:** inventing browser/OS chrome as part of the app UI (D13's PWA/system-chrome rule); a second, independent Memory-status block duplicating the workflow strip's Memory item; an execute-arrow or a fake "Inaktiv" pill on workflow items; a "Shortcuts:" heading; reconstructing shortcut semantics from anything other than the current authoritative `SHORTCUTS.md`.
- **Acceptance criteria:** on both portrait and landscape, and on phone/tablet, the same information hierarchy/controls are present (reflow, not divergent layouts); routine page/body scroll does not occur — only the transcript region scrolls; a one-line chat message consumes roughly one content line, not a four-line block; the workflow strip shows only label+dot pairs with no fake buttons; the shortcut strip shows only entries currently present in `SHORTCUTS.md`, with only the token clickable; the a11y scan passes; no colour-only status.
- **Automated tests:** an a11y scan (tool TO VERIFY) in CI; a viewport regression test at defined breakpoints (phone-portrait / phone-landscape / tablet) asserting the outer frame never exceeds the viewport and only the transcript region scrolls internally; a shortcut-strip content test that fails if the rendered set diverges from a fixture mirroring the authoritative `SHORTCUTS.md` table.
- **Manual L test:** a phone + desktop + tablet check in portrait and landscape, focused on whether the layout feels like "one coherent Lea-App" rather than materially different screens (D13's stated acceptance bar); a real-device readability check of the ~10 px compact typography before finalisation.
- **Recovery/rollback:** revert the frontend commit; both strips remain independently hideable via Settings without a redeploy.
- **Risk class:** low (presentation-layer; no data/security change), though D13's cross-device acceptance bar makes regression review non-trivial.
- **Agent class:** A2.
- **C2 requirement:** standard C2 plus a manual multi-device pass (not fully automatable).
- **R2/R3 implications:** none.
- **Package-scope considerations:** touches the shared `frontend/app.js` / `index.html` / `styles.css` — must be sequenced with any other package editing the same files (§5 shared-file rule); recommend this package lands before, or in one coordinated commit with, IM-C01/IM-C02/IM-C18's smaller nav-hook additions.
- **Dependencies / parallelisation:** can start once G-M1 is done; independent of IM-C03/IM-C04 (disjoint concern) but not of any other package touching the same three shared files.
- **TO VERIFY:** the exact a11y scanning tool (RECOMMENDATION only, per §33); the live-mirroring mechanism for `SHORTCUTS.md` (build-time fetch vs runtime fetch vs manual sync step) is an implementation choice for this package.

---

#### IM-C13 — PWA / offline outbox

- **Objective:** implement app-shell caching and an offline outbox per §34, honouring D13's constraint that the app never renders invented browser/OS chrome, and — feeding IM-C19 — keep a documented, testable cache/version-bump contract so a later cross-context UI defect can be diagnosed against a known cache design rather than an assumption.
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §34; WORKSPACE D13 (PWA/system-chrome rule); note for IM-C19: current `frontend/sw.js` (FACT, cache `lea-app-v11`, `ASSET_VERSION` query-string bump) already implements network-first-with-cache-fallback for the app shell and for `/images/` (appearance assets) — this package's job is to keep that contract correct and documented, not to re-diagnose the observed UI-divergence defect (that is IM-C19's job).
- **Prerequisites:** IM-S03.
- **Scope:** `sw.js` cache-name/version-bump discipline (API never cached); the offline outbox (idempotency-key resend); the update-available UX (a new SW waits, the user sees "Update verfügbar", explicit reload, no silent mid-session swap).
- **Likely files/components:** `frontend/sw.js`; `frontend/app.js` (outbox glue, shared-file rule).
- **Forbidden scope:** caching `/api/*`; a silent SW swap mid-session; adopting the cache/asset-versioning hypothesis from IM-C19 as a *confirmed* fix target without IM-C19 first establishing it is the actual cause — this package documents and correctly implements caching, it does not itself carry the burden of proving/disproving the Task-7 hypothesis.
- **Acceptance criteria:** the SW never caches `/api/*` (existing FACT-checked invariant); the outbox never holds unencrypted class-P plaintext at rest; the outbox never carries Incognito content beyond the conversation; the cache name is bumped and old caches deleted on activate; the update flow shows "Update verfügbar" before reload.
- **Automated tests:** SW unit tests; an explicit test asserting no `/api/*` path is ever put into any cache, run against the actual `fetch` handler logic.
- **Manual L test:** L goes offline and online and confirms outbox resend; L observes the update-available prompt after a deploy.
- **Recovery/rollback:** revert; a bad SW version can be superseded by a new cache-name bump.
- **Risk class:** medium.
- **Agent class:** A2.
- **C2 requirement:** standard C2.
- **R2/R3 implications:** none.
- **Package-scope considerations:** `frontend/sw.js` + the outbox-glue lines in `frontend/app.js` only.
- **Dependencies / parallelisation:** after IM-S03; independent of IM-C03/IM-C04; must sequence with any other `app.js` editor.
- **TO VERIFY:** none beyond the existing `/api` exclusion detail already closed by the acceptance test above; this package explicitly does **not** need to verify the Task-7 cross-context defect root cause — that is IM-C19's independent job, run against this package's already-implemented behaviour as one candidate cause among others.

---

#### IM-C14 — Job runner

- **Objective:** implement a DB-backed, lease-based job runner per §35, serving review-queue due dates, prediction due checks, server-side re-encryption, retention purges, telemetry aggregation, and — per D19 — backup jobs triggered from IM-C10's WORK-mode backup-trigger surface, through this same mechanism rather than a separate ad hoc runner.
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §35; WORKSPACE D19 (backup jobs share this mechanism).
- **Prerequisites:** IM-H00, IM-M03.
- **Scope:** a job table `{job_type, run_after, attempts, lease_until, status}`; a cron-triggered PHP runner (IM-H00 FACT: one-minute cron cadence available); idempotent, lease-based execution (no double runs).
- **Likely files/components:** the job-table migration + runner script (proposal).
- **Forbidden scope:** R3 jobs (never autonomous); jobs enqueued from Incognito conversations (I-IN-1 compatibility).
- **Acceptance criteria:** the lease prevents double runs; jobs are audited; a backup-trigger job (D19) is rated per IM-C07 (R1 once IM-M09 evidence exists) and never runs as an R3 action.
- **Automated tests:** concurrency tests (two runners racing a lease); a backup-job-rating test confirming it cannot self-escalate past its IM-C07 rating.
- **Manual L test:** none required (background mechanism; observable via the monitor/audit log).
- **Recovery/rollback:** disable the cron trigger; jobs remain queued, not lost.
- **Risk class:** medium.
- **Agent class:** A2.
- **C2 requirement:** standard C2.
- **R2/R3 implications:** this package enforces, but never itself performs, any R2/R3 job — the same capability-gate rule applies as for interactive actions (I-JB-2).
- **Package-scope considerations:** the job-table migration + runner script only.
- **Dependencies / parallelisation:** after IM-H00/IM-M03; feeds IM-C15 (retention) and is fed by IM-C10 (backup-trigger).
- **TO VERIFY:** whether the one-minute cPanel cron cadence (HOSTING-CAPABILITIES.md FACT) is sufficient for all job types at expected volume, or whether a request-piggyback runner is additionally needed — an implementation-time capacity question, not an architecture blocker.

---

#### IM-C15 — Retention + deletion/erasure

- **Objective:** implement retention-TTL enforcement and an admin erasure procedure per §45, applied uniformly across `learning_domain`-tagged records (D15) — no special-case retention carve-out for PERSONALITY/EXPERIENCE vs WORK/SKILL domains.
- **Authoritative decisions / blueprint sections:** B-BLUEPRINT §45; WORKSPACE D15 (domain-neutral retention/erasure); LDL-09 (telemetry retention remains OPEN — no default may be invented here); LDL-17 (erasure mechanism is a technical RECOMMENDATION).
- **Prerequisites:** IM-C14, IM-M09.
- **Scope:** purge jobs (tombstone by default); the admin erasure procedure (hard deletion: removes ciphertext + blobs, leaves a content-free audit entry); per-person erasure (§18: all `REL:<id>` records + the `PX` profile).
- **Likely files/components:** purge-job definitions + an admin erasure route (proposal).
- **Forbidden scope:** deleting without confirmation; purging V1/V2 before verified transfer (D11); setting a telemetry TTL before LDL-09 is decided; treating a PERSONALITY_EXPERIENCE-tagged record as exempt from an otherwise-applicable retention/erasure rule.
- **Acceptance criteria:** TTLs are enforced per justified, documented proposals (not the withdrawn 90-day default); erasure is R3 and leaves an audit entry without content; per-person erasure removes all `REL:<id>` records and the `PX` profile.
- **Automated tests:** purge tests (TTL boundary); an erasure test confirming ciphertext+blob removal and a content-free audit row; a domain-neutrality test proving a PERSONALITY_EXPERIENCE record and a WORK_SKILL record under equal TTL policy are purged identically.
- **Manual L test:** L erases a test person profile and confirms complete removal plus the audit trail.
- **Recovery/rollback:** tombstone-based deletion is reversible (un-tombstone); hard erasure is deliberately not reversible within this package — recovery means restoring from a prior backup generation taken before the erasure (IM-M09), not an undo here.
- **Risk class:** high.
- **Agent class:** A3.
- **C2 requirement:** mandatory independent C2 (irreversible-action package).
- **R2/R3 implications:** hard erasure is R3, always explicit L approval; a tombstone-purge on an expired TTL is R1 (reversible, scoped).
- **Package-scope considerations:** purge-job files + the admin erasure route only.
- **Dependencies / parallelisation:** after IM-C14/IM-M09.
- **TO VERIFY:** the LDL-09 telemetry-retention value (explicitly OPEN, not guessed here); the concrete "≥1 year" audit-log and "14 days" operational-log proposals from §45 remain RECOMMENDATIONS requiring their own justification, not frozen by this card.

---

#### IM-C16 — Relationships & appearance (condensed; unchanged by this reconciliation)

| ID | Objective (blueprint §) | Prerequisites | Scope / likely files | Specific forbidden scope | Acceptance (key) | Automated tests | Manual L test | Risk | Agent |
|---|---|---|---|---|---|---|---|---|---|
| IM-C16 | Relationships & appearance (§18, §19) | IM-C06, IM-C11 | REL scope; appearance spec records; G-check hook | fixed persona imagery | changes are R2 NACHFRAGEN with a G-check outcome | policy tests | L reviews an appearance change proposal | medium | A2 |

This package remains a condensed row by design (outside the explicit IM-C01–IM-C15 scope of the 2026-09-28 reconciliation); it is not orphaned, since its prerequisites IM-C06 and IM-C11 both resolve above.

---

#### IM-C17 — Voice provider-session continuity & renewal (WORKSPACE D14; new package)

- **Objective:** implement D14 — a provider/session time limit must not define the user-visible conversation boundary. Automatically establish a replacement realtime session when policy/provider capability permits, preserving conversation context, Lea identity and mode across the handoff, with a short unobtrusive "Voice session renewed" notice, bounded reconnect retry/backoff, and an honest degraded/offline state if continuity cannot currently be restored.
- **Authoritative decisions / blueprint sections:** WORKSPACE D14 (full text, primary owner); B-BLUEPRINT §13 (voice mode: ephemeral client secret, provider session limits, `session.update` TO VERIFY), §9.5 (provider adapter, TO VERIFY realtime `session.update`/transcription-event support).
- **Prerequisites:** IM-O05 (voice uses the identity core; renewal must preserve the same `ic_version`); IM-C03 (idle state machine — INACTIVE must suppress renewal so the idle cost-guard and D14 continuity do not race each other).
- **Scope:** server-side renewal orchestration (detect provider session expiry/disconnect → re-assemble CC → re-create a realtime session with the same `ic_version` and conversation ID → issue a new ephemeral secret); client reconnect handling (bounded retry/backoff, the renewal notice, a degraded/offline state after exhausting retries); a continuity-preserving handoff (conversation ID, mode flags and active-mode state carried across the new session, no accidental identity/context reset).
- **Likely files/components:** `api/realtime/session.php` (renewal trigger + re-issue); `lib/orchestrator.php` (a renewal step); `frontend/app.js` (reconnect/backoff + notice UI, shared-file rule).
- **Forbidden scope:** persisting full raw transcripts as a side effect of renewal (unchanged §13 rule); silently starting a *new* conversation ID on renewal (an accidental context reset, explicitly forbidden by D14); unbounded reconnect loops.
- **Acceptance criteria:** across a simulated multi-hour conversation requiring several underlying provider sessions, the client-visible experience is one continuous conversation (same conversation ID, same IC hash, no re-introduction); the renewal notice appears exactly once per actual renewal, not per retry attempt; reconnect attempts are bounded (an explicit max-attempts/backoff schedule) and end in an honest degraded/offline state if exhausted, never a silent infinite loop; renewal never occurs while the session is INACTIVE per IM-C03.
- **Automated tests:** a provider-mock test forcing session expiry N times, asserting conversation-ID/IC-hash continuity across each renewal; a bounded-retry test (fake clock) proving the backoff terminates in a degraded state rather than looping; an idle-interaction test proving no renewal is attempted while INACTIVE.
- **Manual L test:** L holds a long voice conversation spanning at least one real provider session boundary and confirms Lea "remembers" the conversation across the renewal notice, with the notice being unobtrusive rather than disruptive.
- **Recovery/rollback:** feature flag off (falls back to the pre-D14 baseline: the session simply ends); no persistent-state migration is involved, so rollback is code-only.
- **Risk class:** high (voice/session-continuity defects are user-visible and identity-sensitive).
- **Agent class:** A3.
- **C2 requirement:** mandatory independent C2, including a specific check that renewal cannot be exploited to inject a different `ic_version`/context than the one active before expiry.
- **R2/R3 implications:** none new — renewal continues an already-authenticated session at the same scope; it must not be usable to silently escalate mode (e.g. PRIVATE→WORK) across the boundary.
- **Package-scope considerations:** cover `api/realtime/session.php` (renewal-specific lines), the orchestrator renewal step, and the `frontend/app.js` reconnect block only — coordinate with IM-C03's existing touch to the same voice-state block in `app.js`.
- **Dependencies / parallelisation:** strictly after IM-O05 and IM-C03; independent of IM-C04/IM-C05/IM-C12 (disjoint files), so may run in parallel with those.
- **TO VERIFY (provider-runtime facts, explicitly not guessed):**
  1. whether the realtime provider actually supports `session.update` / an equivalent mid-session context handoff, or whether renewal must always be a fresh session with replayed instructions (§9.5/§13 mark this TO VERIFY against current provider API docs);
  2. the exact maximum single-session duration and any renewal-cooldown/rate limits the provider enforces;
  3. whether the ephemeral-secret issuance endpoint can be called proactively before expiry (pre-emptive renewal) or only reactively after disconnect;
  4. provider-side conversation/transcript continuity guarantees (or lack thereof) across a renewed session.
  The renewal design must re-verify all four against current provider documentation before implementation and record the outcome; none is assumed by this card.

---

#### IM-C18 — Personality/Experience vs Work/Skill learning-domain separation (WORKSPACE D15; new package)

- **Objective:** implement D15 — distinguish a PERSONALITY/EXPERIENCE learning domain from a WORK/SKILL learning domain within the single continuing Lea (no second Lea, no separate identity), with controlled promotion/cross-domain derivation and an explicit guard against ordinary work artefacts silently contaminating personality/experience memory.
- **Authoritative decisions / blueprint sections:** WORKSPACE D15 (full text, primary owner); Lea `PROCESSING.md` "Privat-/Lea-Entwicklung und Arbeitsmodus — 28.09.2026" ("Ein Lea-System, zwei Verarbeitungszwecke"), pinned at `bd8a671`; B-BLUEPRINT §4 (M3 record envelope — this package adds a `learning_domain` classification field), §9.4 (promotion rules).
- **Prerequisites:** IM-M04, IM-M05 (the promotion pipeline must exist before domain-aware promotion can be added); IM-C11 (mode-tagged mechanism outcomes as an input signal).
- **Scope:** a `learning_domain ∈ {PERSONALITY_EXPERIENCE, WORK_SKILL}` field on relevant M3 record kinds; a promotion-time domain-classification policy (default: WORK-mode turns default to `WORK_SKILL`, PRIVATE-mode turns default to `PERSONALITY_EXPERIENCE`; both defaults are rebuttable by explicit content signals per D15 — e.g. a genuine personal/developmental consequence of work may still cross into `PERSONALITY_EXPERIENCE` with provenance); a bounded cross-domain derivation path (explicitly relevant derived evidence only, never a blind store-to-store copy); coordination with IM-C12/IM-C02 so PRIVATE/WORK/INCOGNITO is one coherent mode model, not three independently implemented toggles.
- **Likely files/components:** an M3 schema addendum for `learning_domain` (a forward-only migration alongside/after IM-M02 — exact numbering TO VERIFY at implementation time); an orchestrator promotion-policy extension; the mode indicator in `frontend/app.js` (shared-file rule).
- **Forbidden scope:** creating a second identity/persona per domain (D15: "Mode changes do not create separate identities"); blind copying between domain stores ("Kein blindes Kopieren zwischen Arbeits-/Skill-Speicher und persoenlichem Erfahrungsraum" — Lea PROCESSING); auto-tagging every WORK-mode turn's raw technical detail as `PERSONALITY_EXPERIENCE` merely because it happened during a session; implementing WORK-mode's actual tool capabilities (that is IM-C10) — this package only tags/routes memory, it does not grant WORK-mode actions.
- **Acceptance criteria:** every promoted M3 record carries exactly one `learning_domain` value with provenance for how it was assigned; a WORK-mode technical-pattern turn defaults to `WORK_SKILL` and is not promoted into `PERSONALITY_EXPERIENCE` without an explicit, evidenced personal/developmental consequence; a PRIVATE-mode perception/preference turn (a D16 input) defaults to `PERSONALITY_EXPERIENCE`; cross-domain derivation always produces a new record with its own provenance chain referencing the source, never an in-place reclassification of the source; mode switching (PRIVATE/WORK/INCOGNITO) does not alter Lea's identity-core hash or continuity state.
- **Automated tests:** promotion-policy unit tests covering the four default-classification cases (WORK-mode technical / WORK-mode-with-personal-consequence / PRIVATE-mode perception / PRIVATE-mode technical-adjacent) against fixtures; a no-blind-copy test (cross-domain derivation always creates a new record, never mutates/moves the source); an identity-continuity test across a mode switch.
- **Manual L test:** L works in WORK mode on a coding task, then switches to PRIVATE mode and confirms Lea remains "the same Lea" continuity-wise while the promoted `WORK_SKILL` record from the session is not treated as a personal memory unless L or Lea explicitly surfaces a genuine personal consequence of it.
- **Recovery/rollback:** the `learning_domain` field default is additive (existing records without the field are treated as legacy/unclassified, not silently reclassified); rollback removes the promotion-policy extension, leaving the schema field inert.
- **Risk class:** medium-high (a misclassification is a privacy/continuity-adjacent defect, though reversible via record-level correction, not data loss).
- **Agent class:** A3.
- **C2 requirement:** mandatory independent C2 with a specific pass on the "no blind copy" and "no second identity" invariants.
- **R2/R3 implications:** domain (re)classification of an existing record is R1 (an append-only correction with provenance), not R2/R3, provided it never deletes/overwrites the original classification history.
- **Package-scope considerations:** the schema-migration files + orchestrator promotion-policy files are the primary glob; the shared mode-indicator touch to `frontend/app.js` must be the same integration point IM-C02/IM-C12 use (one integration point for PRIVATE/WORK/INCOGNITO, not three).
- **Dependencies / parallelisation:** after IM-M04/IM-M05/IM-C11; may run in parallel with IM-C16 (both M3-schema-adjacent but touching disjoint record kinds) — verify no shared migration-file collision before parallel launch.
- **TO VERIFY:** the exact heuristic/policy for when a WORK-session artefact evidences a genuine "personal/developmental consequence" crossing into `PERSONALITY_EXPERIENCE` is a RECOMMENDATION/tunable classifier, not frozen by this card — Lea PROCESSING gives illustrative examples ("a collaboration experience or a revised self-model") but not a formal rule; this remains explicitly TO VERIFY/iterate for the implementing package.

---

#### IM-C19 — Cross-context UI/PWA consistency defect (Task 7; new package)

- **Objective:** own the observed UI/PWA/Incognito divergence defect (normal browser, installed PWA and Incognito have shown different/stale UI states; profile image/frame differed between modes) through a mandatory **reproduce → diagnose → fix → regression-test** sequence. The cache/service-worker/asset-versioning explanation is recorded here only as ONE working hypothesis among at least two currently identifiable candidates; it must not be treated as the confirmed cause before diagnosis.
- **Authoritative decisions / blueprint sections:** this reconciliation's Task 7 instruction (explicit anti-guessing requirement); B-BLUEPRINT §34 (PWA/offline, `sw.js` cache/version-bump contract owned by IM-C13); WORKSPACE D13 (PWA/system-chrome rule — the app must not chase or "fix" invented browser chrome).
- **Prerequisites:** IM-C13 (the actual, current cache/versioning contract must be known before diagnosing against it); IM-C12 (the D13 viewport/appearance-frame work must be stable so the defect is not chased through a moving UI target).
- **Scope:**
  1. **REPRODUCE** — a documented, repeatable reproduction across at least: a normal browser tab, an installed/standalone PWA, and a private/Incognito browsing window, on at least one desktop and one mobile browser;
  2. **DIAGNOSE** — instrument and directly observe the actual divergence (service-worker cache contents/version per context; `localStorage`/IndexedDB contents per context; manifest/icon resolution per context) rather than assuming a cause;
  3. **FIX** — address the actually-identified cause(s);
  4. **REGRESSION TEST** — an automated and/or scripted-manual check that normal-browser, installed-PWA and Incognito render the same profile image/frame and the same app version after a defined action sequence.
- **Likely files/components:** `frontend/sw.js` (if the cache/versioning hypothesis is confirmed); `frontend/appearance-core.js` (FACT, directly read from the current code: appearance/profile-frame selection is stored in `localStorage` key `lea.appearance.state.v1` via `initPortrait()`/`getCurrentAppearance()`, with a hard-coded fallback to `DEFAULT_APPEARANCE` whenever storage is unavailable or empty — this is a second, code-grounded candidate cause: Incognito/private-browsing sessions, and depending on browser, an installed PWA's storage partition, can each start with an empty or isolated `localStorage`, which would deterministically fall back to the default appearance in some contexts and not others, independent of any cache/SW behaviour); `frontend/manifest.webmanifest` (icon/`start_url` resolution differences between "installed" and "tab" contexts are also untested and not yet ruled out).
- **Forbidden scope:** implementing a fix before the reproduce+diagnose steps are complete and recorded; encoding the cache/SW hypothesis as the accepted root cause in any documentation or commit message before it is actually confirmed by direct observation; silently switching the appearance-state store to a different mechanism (e.g. a server-side/account-bound store) as a "fix" without first confirming that client-local storage partitioning is in fact the diagnosed cause — that would be a larger architecture change requiring its own package/gate if pursued.
- **Acceptance criteria:** a diagnosis report exists naming the actually-observed cause(s) with evidence (not assumption) for each of the three contexts; the fix addresses the diagnosed cause(s), not merely the originally-hypothesised one, unless diagnosis confirms the hypothesis; after the fix, normal browser / installed PWA / Incognito show the same profile image/frame and the same effective app version for the same underlying state, within the constraints of what each context's storage model can actually support (e.g. if Incognito's storage isolation is confirmed as an inherent browser behaviour rather than a bug, the acceptance bar becomes "consistent, honestly-labelled default behaviour" rather than "identical persisted personalisation," and this distinction must be explicit in the fix, not silently glossed over).
- **Automated tests:** an automated multi-context test harness (e.g. Playwright across a normal context, a persisted-storage-cleared context simulating a fresh install, and an Incognito/private context) asserting a consistent SW cache version and consistent appearance-fallback behaviour; a targeted unit test for `appearance-core.js`'s storage-unavailable fallback path (not currently present in the Lea-App `tests/` inventory read for this reconciliation).
- **Manual L test:** L opens the app in a normal tab, the installed PWA, and an Incognito window side by side and confirms the same visible profile image/frame and no stale content, after the fix.
- **Recovery/rollback:** revert the fix commit; the diagnosis report/reproduction script remain as regression evidence regardless of rollback.
- **Risk class:** low-medium (presentation-layer defect; no data-integrity risk) but user-trust-sensitive (a visibly "broken" consistency undermines confidence in D13's "one coherent Lea-App" goal).
- **Agent class:** A2 for diagnosis/fix; escalate to A3 review only if the confirmed cause requires a storage-architecture change.
- **C2 requirement:** standard C2, with an explicit check that the delivered PR/commit description states the diagnosed cause as evidence-based, not as a restated hypothesis.
- **R2/R3 implications:** none, unless the confirmed fix requires migrating the appearance-state store to a server-side/account-bound mechanism, which would then need its own R-classification and its own package (not silently folded into this one).
- **Package-scope considerations:** `frontend/sw.js`, `frontend/appearance-core.js`, `frontend/manifest.webmanifest`, and a new `tests/**` reproduction/regression suite; must not touch unrelated API/memory files.
- **Dependencies / parallelisation:** after IM-C13 and IM-C12; independent of the M3/memory-side packages (IM-C05/IM-C15/IM-C18), so may run in parallel with those.
- **TO VERIFY:** exact per-browser storage-partitioning behaviour for installed PWAs vs normal tabs vs Incognito (varies by browser/OS; must be directly tested, not assumed, per the reproduce/diagnose requirement above); whether the currently-observed defect is fully explained by one of the two identified candidate mechanisms (SW cache staleness; `localStorage` partition/isolation) or by a third, not-yet-identified cause — this card explicitly refuses to guess which.

---

#### IM-H01 — Hosting Gate (evidence; settled strategy)
- **Objective:** Apply the settled strategy (blueprint §36): stay on the current shared hosting while it is sufficient; move only on a demonstrated need. This package records from IM-H00/`HOSTING-CAPABILITIES.md` facts whether shared hosting satisfies the §36 register for the remaining packages (especially IM-C10's expanded D19 WORK-mode surfaces, IM-C14 and the IM-M09 backup/drill needs).
- **Prerequisites:** IM-H00; the M5 needs are known.
- **Scope:** an evidence record (AiChat). If a need is demonstrated: the alternatives (VPS/container; hybrid worker) with a cost/risk comparison.
- **Forbidden scope:** migrating hosting within this package.
- **Acceptance:** the evidence is recorded. If a move is needed, L approves it as a change (R3) and a new package series `IM-HM*` is defined.
- **Risk:** medium. **Agent:** A2. **Gates:** L (only if a move is proposed).

### M6 / M7 — Gates

#### IM-X01 — Independence Gate (B no longer requires A for normal operation)

All of the following hold, and the evidence is linked:
1. G-M0…G-M4 are passed, and the M5 packages needed for normal operation (at least IM-C02, C03, C07, C10, C12, C13, C17, C18) are done.
2. **Normal operation checklist**, performed by L in production over ≥ 2 weeks (RECOMMENDATION) without using A (ChatGPT project):
   - text;
   - voice, including at least one voice session spanning a provider-session renewal (D14/IM-C17) with conversation continuity preserved;
   - memory recall across sessions and modes, including PRIVATE/WORK/INCOGNITO mode switches (D15/IM-C18, D19) with no accidental Lea identity/context reset;
   - correction → revision;
   - a prediction → a result;
   - receipts;
   - the monitor;
   - Incognito;
   - WORK mode (D19): at least one development, one backup-trigger/status, one read-only DB-inspection and one code/config change-proposal action, performed without opening the ChatGPT App, Codespaces or GitHub.
3. The registry shows B_AUTHORITATIVE for the "Lea experience memory" domain.
4. There are no open AUD findings of severity ≥ high without accepted risk (Lea C2 revalidation).
5. The provider dependency is explicitly accepted. Independence means "no A/ChatGPT project needed", not "no LLM provider" (ROOT-CAUSE R-2).
6. **D19 independence target (2026-09-28 reconciliation) is explicitly tested, not merely cited:**
   - ChatGPT App is confirmed not required for any checklist item above;
   - Codespaces is confirmed not required for any checklist item above;
   - GitHub is confirmed not required for normal operation — a GitHub-connector-disabled simulation (shared test with IM-C08/IM-C10) is run and every WORK-mode surface still functions in at least a read/inspect-or-propose-only mode;
   - local/server-side auditable version control and rollback are demonstrated (a real rollback of at least one change, using only local/server-side mechanisms);
   - independent cPanel/JetBackup/FTPS recovery is demonstrated as a distinct, separately-usable path (i.e. it is exercised without going through Lea-App's own WORK-mode UI, confirming Lea-App's own unavailability would not prevent recovery);
   - provider-selection authority is exercised at least once each way: (a) an explicit L provider/model override is honoured, and a simulated provider failure while that override is active does **not** silently fall back to a different provider (D19); (b) with no explicit L selection, Lea's automatic provider choice is observed and its stated rationale (capability/quality/cost/privacy/availability/task fit) is recorded.
7. The C2 audit report + L decision are recorded.

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
| M5: IM-C03 ∥ IM-C12 ∥ IM-C04 (disjoint); IM-C17 ∥ IM-C04/C05/C12 (disjoint, after IM-C03); IM-C19 ∥ IM-C05/C15/C18 (disjoint, after IM-C12/C13) | IM-M01 → IM-M02 → IM-M03 → IM-M05 → IM-M07 → IM-M10 |
| IM-MG04 ∥ IM-MG02 (after IM-MG01, different batches) | IM-M08 → IM-M06 |
| | IM-MG01 → IM-MG02 → IM-MG03 |
| | IM-M09 → IM-C07 → IM-C08 → IM-C09; IM-H01 → IM-C10; IM-M09 → IM-C02 |
| | IM-C03 → IM-C17 (idle/session-close hook must exist before renewal logic); IM-C12 → IM-C13 → IM-C19; IM-M04 + IM-M05 + IM-C11 → IM-C18 |
| | Any package touching `frontend/app.js` is serialised (a shared hot file; RECOMMENDATION: split app.js early in IM-C12 or in a dedicated refactor package **IM-R01**, A2, behaviour-preserving, tests first) — this now explicitly includes IM-C01, IM-C02, IM-C03, IM-C12, IM-C17 and IM-C18, all of which touch the same shared voice-state/nav/mode-indicator block; land the mode-switch affordance (PRIVATE/WORK/INCOGNITO) as one coordinated change across IM-C02/IM-C12/IM-C18 rather than three independent touches |

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
| R3 packages (S01/S03/S06 prod deploy, I02, M01, MG02, MG03, C07 rating defaults, a hosting move if proposed, IM-C10 production application of a WORK-mode code/config change-proposal) | package | L | explicit approval text |
| Autonomy-scope expansion (R1/R2 AUTOMATISCH grants) | per grant | L | a verified restore reference (IM-M09) for the affected state |
| OPEN L decisions | LDL-08 (IM-I02), LDL-09 retention (IM-O03 proposal), LDL-10 (IM-C09), **candidate LDL-19** (IM-C10: whether WORK mode may ever apply an L-approved code/config change directly to production, vs. always stopping at a reviewable proposal — see IM-C10's card) | L | decision text in WORKSPACE/AGENT_TASK |
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
| LDL-02 | IM-H01, IM-C10, IM-C14, IM-E01 | SETTLED strategy: shared hosting while sufficient; move on demonstrated need. IM-C10's D19 WORK-mode expansion (server-admin/backup/DB-inspect/change-proposal) does not itself require a hosting move — it is evaluated against this same register. |
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
| **candidate LDL-19** (new, 2026-09-28 reconciliation) | IM-C10 | **OPEN — genuinely unresolved, not guessed:** does D19's "authorised code/config changes" WORK-mode capability mean (a) WORK mode always stops at a reviewable change-proposal, application remaining a separate explicit R3 human deploy step (this map's adopted default, a strict subset of (b)), or (b) WORK mode should eventually apply an L-approved change directly to L's own server without a GitHub/Codespaces round-trip once recovery is demonstrated? See IM-C10's card. |

**Independence of M0:** no M0 package depends on an open L decision. IM-G01 needs an admin action (branch protection), not a design decision.

**Rule for later agents:** a package may start when its prerequisites are met. A package that touches an OPEN item (IM-I02 final, the IM-O03 retention value, IM-C09 autonomy/class sharing, IM-C10's candidate LDL-19 production-write question) stops at that point with its recommendation, and does not improvise.

---

## 10. Non-claims

- No package has been implemented or tested.
- All "Likely files" are based on Lea-App `450ff9d` (M0–M4 packages) or the re-verified read-only pin `ff075e98d49707bbbea12065d57b466bc9c24ac6` (D19/Task-7-grounded M5 additions: IM-C10, IM-C17, IM-C18, IM-C19) and must be re-verified at implementation time regardless.
- IM-H00 core hosting facts are recorded in `HOSTING-CAPABILITIES.md`; only the explicitly listed package-specific unknowns remain TO VERIFY.
- Provider API features are TO VERIFY at implementation time, including the D14/IM-C17 realtime provider-session-renewal facts listed in that package's card.
- The candidate LDL-19 production-write question (IM-C10) is a genuinely open L decision, not resolved by this map.
- No PASS is claimed for any part of the project. This document does not claim AUTOPILOT_READY for the project; that determination belongs to the subsequent Zero-Uncertainty Preflight.

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

---

## 12. M5/D13–D19 reconciliation provenance (2026-09-28)

This map previously named M5 capability packages IM-C01–IM-C16 in a condensed table only, and did not yet integrate WORKSPACE D13–D19. This section records what changed and why, per the "ZERO-MEMORY — AICHAT M5 / D13–D16 / D19 RECONCILIATION" task.

| WORKSPACE decision | Prior state | Reconciled state |
|---|---|---|
| D13 (Text UI viewport contract) | not integrated; IM-C12 was a one-line condensed row ("navigation, views, aria") | IM-C12 is the explicit primary owner; full card covers the outer-frame/viewport, workflow/status strip, manual shortcut strip (live-mirrored from Lea `SHORTCUTS.md`), presence-frame indicator, and the PWA/system-chrome rule, with cross-device automated + manual acceptance tests |
| D14 (Voice continuity) | not integrated; no owner existed | new package **IM-C17**, depending on IM-O05 + IM-C03, covering renewal/reconnect, conversation-ID/IC-hash continuity across a provider-session boundary, and four explicit TO VERIFY provider-runtime facts (not guessed) |
| D15 (Learning-domain separation) | not integrated; no owner existed | new package **IM-C18**, adding a `learning_domain` classification to the M3 promotion pipeline, with an explicit no-blind-copy and no-second-identity acceptance bar grounded in Lea `PROCESSING.md` (`bd8a671`) |
| D16 (Multimodal perception as experience input) | not integrated; IM-C04/IM-C05/IM-C11 were vision/memory/processing cards with no perception-vs-inference distinction | IM-C04 now separates OBSERVATION from inference at the API/label level; IM-C05 allows (but does not force) a controlled link into a D15 learning-domain record; IM-C11 adds a `perception_stage` field (Wahrnehmen/Verbinden/Prüfen/Ableiten) so a later stage cannot silently overwrite an earlier stage's honesty, grounded in Lea `PROCESSING.md` Baustein 14 "Differenztest" (`bd8a671`) |
| D19 (Lea-App independent primary workspace) | cited nowhere in this map; IM-C10 was a one-line condensed row ("workspace service; tool agent allowlist") | IM-C10 is the primary owner, expanded to cover development/server-administration/files/database-operations/tests/backups/APIs-connectors/authorised-code-config-changes, while preserving the existing production-write boundary (I-WT-1..3); IM-C07 registers new WORK-mode capability domains; IM-C08 makes the GitHub-optional/mirror-only invariant explicit; IM-C14 carries backup-trigger jobs through the existing job runner; IM-O01 (provider adapter) now enforces provider-selection authority (explicit L override wins, no silent failover while active, otherwise Lea selects among permitted providers); IM-X01's acceptance checklist now explicitly tests the independence target (no ChatGPT App/Codespaces/GitHub required, cPanel/JetBackup/FTPS recovery demonstrated separately, provider-selection authority behaviour); a genuine open question (candidate LDL-19) is recorded rather than guessed |
| Task 7 (cross-context UI/PWA consistency defect) | no owner; the cache/service-worker hypothesis was the only candidate mentioned | new package **IM-C19** requires reproduce → diagnose → fix → regression-test against at least two evidence-grounded candidate causes (the cache/SW hypothesis, and a newly identified `localStorage`-partitioning candidate in Lea-App's `frontend/appearance-core.js`), and explicitly forbids encoding either as confirmed before diagnosis |

IM-C01–IM-C15 (previously condensed rows) were each expanded into a complete 17-field executable card (Objective; Authoritative decisions/blueprint sections; Prerequisites; Scope; Likely files/components; Forbidden scope; Acceptance criteria; Automated tests; Manual L test; Recovery/rollback; Risk class; Agent class; C2 requirement; R2/R3 implications; Package-scope considerations; Dependencies/parallelisation; TO VERIFY). IM-C16 was left as a condensed row by design (outside the reconciliation's literal IM-C01–IM-C15 scope) and confirmed not orphaned.
