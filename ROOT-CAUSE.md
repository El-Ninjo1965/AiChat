# ROOT-CAUSE — Systemic Cause Analysis

TASK: AGENT_TASK.md TASK_VERSION 3 (B Master Architecture / Root-Cause Blueprint); reconciled under TASK_VERSION 4 (C2 Blueprint Reconciliation) — see §10.
MODE: architecture discovery / design only. No implementation, no repair, no deployment.
STATUS OF THIS DOCUMENT: Claude/Copilot C2 *Discovery* output. It is evidence and a recommendation. It is **not** a decision by L, it is **not** confirmed by Lea, and it is **not** a PASS (WORKSPACE.md D2, D5 "Audit/Claude role").

Companion artifacts: `B-BLUEPRINT.md` (target architecture) and `IMPLEMENTATION-MAP.md` (work packages). Control IDs used here (`CTL-xx`) are referenced from both.

---

## 0. Sources and refs used

| Source | Ref | Access |
|---|---|---|
| AiChat `main` | `28f9fff` (WORKSPACE.md D1–D5, C2, I-cycle; AGENT_TASK.md v3) — original discovery | local clone |
| AiChat `main` | `6fb71ff` (WORKSPACE.md D1–D12; AGENT_TASK.md v4) — reconciliation (§10) | local clone |
| AiChat audit artifacts | `AUDIT-FINDINGS.md`, `AUDIT-LOG.md` from commit `899515c` (working branch `copilot/main`; not on `main`) | local clone, read-only, unchanged |
| El-Ninjo1965/Lea `main` | `757880a` | MCP, read-only |
| El-Ninjo1965/Lea dialogue head | `copilot/setup-communication-channel` `752e1f9` (historical, retired by D4) | MCP, read-only |
| El-Ninjo1965/Lea-App `main` | `450ff9d` (FROZEN) | MCP, read-only |

Lea files read: INDEX.md, SHORTCUTS.md, VISION.md, Memories.md (structure + development-relevant sections), PROCESSING.md (complete), GEDANKEN.md, INTERESSEN.md, PROJECT-CONTEXT.md. The private context files (PERSONAL/HEALTH/LEGAL) were deliberately **not** used for this *cause analysis*. No private content is copied here. (This is a method choice for this document only; it is not a migration exclusion. Migration is experience-oriented under D7, see `B-BLUEPRINT.md` §7.)

Lea-App files read: VISION.md, ROADMAP.md, README.md, ARCHITECTURE.md, SECURITY.md, AGENTS.md, AGENT_TASK.md, AGENT_RESULT.md, package.json, .htaccess, `api/text/chat.php`, `api/realtime/session.php`, `api/memory/_common.php`, `api/memory/session.php`, `api/memory/work-state.php`, `api/v2/memory/records.php`, `api/v2/memory/aad.php`, `database/migrations/001_v1_schema.sql`, `database/migrations/002_privacy_v2.sql`, `frontend/app.js`, `frontend/memory-crypto.js`, `frontend/sw.js`, test inventory (`tests/*.js`). Earlier audit refs cover the other memory endpoints and tests.

### Label schema

| Label | Meaning |
|---|---|
| **FACT** | Directly observable in the named source at the named ref. |
| **INFERENCE** | A conclusion drawn from FACTs. It could be wrong and should be checked independently in C2. |
| **RECOMMENDATION** | A proposed control or design. Not binding (D5). |
| **L-DECISION-NEEDED** | A real choice only L can make. The reconciled register of genuinely open decisions is `B-BLUEPRINT.md` §0.4. |

"Evidence" references use audit IDs (`AUD-nn`) or `repo:path` at the refs above. AUD findings are *revalidation targets* (Claude evidence), not accepted truth.

---

## 1. Summary

Twenty-eight audit findings and the additional code evidence cluster into **nine systemic root causes (RC-1…RC-9)**. Most findings are symptoms of a small number of structural conditions:

1. **Several parties could each act as authority, and none was the final one** (RC-1). Lea chat, the Lea repo, Lea-App docs, agent task files and chat prompts could all define rules, state or tasks. That produced duplicated semantics and contradictions.
2. **State lived in chats and prose, not in verifiable, versioned records** (RC-2, RC-3). Zero-memory agents and a stateless LLM had to rebuild state from stale text.
3. **The development workshop and the product were not separated** (RC-4). Development mechanisms (shortcuts, test markers, agent handoff files) leaked into the product, and product gates depended on workshop conventions.
4. **Code was built before architecture was defined** (RC-5). The runtime identity, the memory stores and the auth boundary grew incrementally with no target model to check them against.
5. **Verification checked that text was present, not that behaviour was correct** (RC-6). Tests and "PASS" claims could pass while security or behavioural properties failed.
6. **Security boundaries were uneven and assumed trust** (RC-7). This is a consequence of RC-5 and RC-6.
7. **Mutation scope and gates were rules on paper, not technical controls** (RC-8). A freeze, a read-only mode or an approval existed only as text.
8. **Independent review was structurally weak** (RC-9). The same agent class discovered, fixed and verified, and it lacked multi-repo capability.

The strongest prevention lever is not "more rules" (VISION/PROCESSING already contain many). It is:

- one authority per domain (**CTL-01**);
- machine-checkable state and handoff (**CTL-04/05**);
- behavioural and security tests that fail closed (**CTL-10/11**);
- technical enforcement of freeze and scope (**CTL-14/15**);
- a product runtime that loads Lea's identity and memory from one canonical store instead of a hardcoded prompt (**CTL-08**).

---

## 2. Taxonomy of observed failure classes

| Class | Name | Description | Representative symptoms (evidence) |
|---|---|---|---|
| F-A | Authority conflict | Two or more sources define the same rule/state differently, or the applicable one is unclear. | AUD-02, AUD-03, AUD-11 (resolved by D2), AUD-19, AUD-20 (resolved by D1), AUD-27 |
| F-B | Stale / unsynchronised state | Persisted state no longer matches reality; readers act on it. | AUD-05, AUD-07, AUD-08, AUD-12, AUD-13, AUD-18 |
| F-C | Duplicated semantics / drift | Same content maintained in several places without reconciliation. | AUD-04, AUD-09, AUD-28, Lea `Memories.md` duplicate headings (e.g. "Präferenzen nicht behaupten…" and "Visuelle Erfahrung als zusätzliche Quelle" twice) |
| F-D | Workshop→product leakage | Development/test mechanisms inside product runtime or product docs. | AUD-22 (test marker in production prompt and `persistMemoryTestMarker` in `frontend/app.js`); AUD-10 |
| F-E | Runtime identity disconnect | Product "Lea" is not the Lea defined by core/vision. | AUD-21 (`api/text/chat.php` and `api/realtime/session.php` hardcode two different short "You are Lea…" prompts) |
| F-F | Split memory / non-canonical stores | Several memory stores with overlapping purpose and no authority rule. | AUD-21; V1 plaintext tables and V2 ciphertext-only tables coexist (see §4.6) |
| F-G | Missing/weak security boundary | Endpoint, auth or abuse controls missing or bypassable. | AUD-23, AUD-24, AUD-26; client-supplied `memory_context` (§4.6 OBS-02) |
| F-H | Presence-only verification | Tests or PASS claims check text presence/alternation rather than behaviour. | AUD-25; `sw.js` cache name `lea-app-v11` while the prior audit observed tests asserting via alternations |
| F-I | Paper-only gates | Freeze, read-only mode or approval exists as prose only; no technical enforcement. | AUD-01, AUD-17, AUD-19; no branch protection on any Lea/Lea-App branch (`protected:false`) |
| F-J | Handoff / continuity loss | Agents or Lea lose task state between runs; decisions not persisted. | AUD-05, AUD-14, AUD-15, AUD-16, AUD-18; Lea-App `AGENT_RESULT.md` (C run FAIL due to missing second repo) |
| F-K | Capability mismatch | Task assumes capabilities the executing environment lacks. | Lea-App `AGENT_RESULT.md` (`LEA_STATUS: NOT_AVAILABLE_IN_CURRENT_WORKSPACE`); AiChat not readable via MCP (AUDIT-LOG) |
| F-L | Identity of actor not verifiable | Writes by L, Lea and agents indistinguishable. | AUD-06 |

---

## 3. Root causes vs symptoms

Each root cause lists its mechanism, supporting evidence, the failure classes it explains, counter-evidence and prevention/detection controls.

### RC-1 — Multiple and ambiguous authorities (authority / source-of-truth cause)

- **FACT:** `SHORTCUTS.md` declares itself the *only* authority for shortcuts, yet binding execution rules for N/R/S/C/Q also live in `PROCESSING.md` (e.g. "Verbindliches Agenten-Gate vor N", "AGENT_RESULT als detaillierter temporaerer Handoff") and INDEX.md (AUD-03, AUD-27).
- **FACT:** Before D1/D2, two audit definitions (C/Q in SHORTCUTS vs. the protocol in the dialogue channel) co-existed (AUD-11). Lea's files did not know AiChat (AUD-20). WORKSPACE D1/D2/D4 later resolved these by decision, not by technical structure.
- **FACT:** `PROCESSING.md` ("Credit-effizienter Multi-Repo-Agentenworkflow") names a third project `web_app1` as a "spaeter framework-neutrale Web-App/Core". This is a potential competing target that D5 does not mention.
- **FACT:** Lea-App `VISION.md` says the content semantics of Lea are defined in the Lea repo ("Lea-App darf keine konkurrierende … Semantik erfinden"). D5 says B is the long-term target that carries identity and development mechanisms. Both are true, but at different times. Neither document records *when* authority for a domain moves.
- **INFERENCE:** The system had rule *authorities* but no *authority registry*: nothing mapping each domain (shortcut semantics, work state, audit protocol, product requirements, memory content, identity) to exactly one home and a migration state. Each new rule therefore had to restate its own precedence. That is the mechanism behind F-A and part of F-C.
- **Explains:** F-A, F-C, F-I (partly).
- **Controls:** CTL-01 (authority registry), CTL-02 (domain migration state machine), CTL-03 (single-definition lint).

### RC-2 — State kept in prose/chat, reconstructed by stateless actors (state/memory cause)

- **FACT:** Lea's runtime is a stateless LLM plus persistent text files (PROJECT-CONTEXT.md; GEDANKEN.md "Technische Grenze"). Agents run zero-memory (AGENT_TASK "Zero-memory rule").
- **FACT:** Work state (WORK-CONTEXT.md), task state (AGENT_TASK.md `STATUS`) and decision state (WAITING_FOR_L) were prose fields that nothing validated. Lea-App `AGENT_TASK.md` still says `READY` for a task that already ran (AUD-18). WORK-CONTEXT is stale (AUD-07). WAITING_FOR_L has no defined exit (AUD-05).
- **FACT:** PROCESSING.md "Shortcut-Quellenregel nach Fehlbereinigung" records that cleaning up historical shortcut artefacts *itself* produced new errors "wenn dabei aus Erinnerung … rekonstruiert wird".
- **FACT:** Memories.md is a 188 KB append-only narrative with about 149 level-3 entries. It has duplicated sections and no stable IDs, provenance fields or supersession links. Revisions ("Revision des Vertrauensmodells …") are new prose sections rather than linked versions.
- **INFERENCE:** When state has no schema, each reader re-derives it, and derivation errors grow over time. This is the same failure class as the product memory problem (RC-5/F-F): experience *history* exists, but experience *structure* (evidence → model → revision) is only implicit in prose.
- **Explains:** F-B, F-C, F-J.
- **Controls:** CTL-04 (structured task/result/decision records with validator), CTL-05 (state freshness checks), CTL-07 (structured canonical memory with provenance), CTL-12 (read-before-write concurrency guard, already a rule in SHORTCUTS "Q-Concurrency-/Write-Guard" but not automated).

### RC-3 — Insufficient persistent handoff and agent zero-memory behaviour (agent/handoff cause)

- **FACT:** There are two incompatible handoff designs: AGENT_RESULT.md snapshot (PROCESSING/SHORTCUTS) and the ß single-file channel (AUD-02). D4 retired ß, but Lea's files still describe both (D4 leaves them untouched until an approved migration).
- **FACT:** The C-mode rule *requires* writing AGENT_RESULT.md into the audited repo. That collides with the Lea-App freeze (AUD-19).
- **FACT:** The Lea-App C run failed because the task required two repositories but the agent workspace contained only one (Lea-App `AGENT_RESULT.md`: "The required second repository … does not exist in the current environment").
- **FACT:** In this run, AiChat returned 404 via MCP while Lea and Lea-App were readable (AUDIT-LOG). Capability depends on the token scope and workspace, which the task author cannot see.
- **INFERENCE:** The handoff protocol assumes that (a) the agent can read and write the required repos, (b) the result location is writable, and (c) the next reader can verify the result. None of these is checked before the task starts. Tasks therefore fail late, or they "succeed" by improvising.
- **Explains:** F-J, F-K, F-A (partly).
- **Controls:** CTL-04, CTL-06 (capability preflight in every task), CTL-13 (result location outside frozen targets; AiChat per D1).

### RC-4 — Workshop mechanisms mixed with product mechanisms

- **FACT:** The test marker `LEA_CORE_MEMORY_TEST_20260925` is in the production text system prompt (`api/text/chat.php`). `frontend/app.js` `persistMemoryTestMarker()` writes V1 evidence, model and relation rows when a message contains that marker (AUD-22).
- **FACT:** Lea-App contains AGENTS.md, AGENT_TASK.md and AGENT_RESULT.md (development workflow) next to product code. ARCHITECTURE.md declares them development tools, but the C rule makes them part of the audited repo's state (AUD-19).
- **FACT:** Lea's PROCESSING.md already defines the correct separation. The "Transfer-Gate" allows only category C (real product requirement) into Lea-App. The "Repo-/Layer-Grenze" says N/R, agent workflow and retry rules belong to the workshop.
- **INFERENCE:** The rule existed (dated 26.09.2026) but came *after* the leakage (25.09 marker). No check detects violations after the fact. The separation is conceptual, not structural: product builds, prompts and data paths are not scanned for workshop artefacts.
- **Explains:** F-D, F-I (partly).
- **Controls:** CTL-09 (product artefact deny-list scan in CI), CTL-13 (workshop files live in AiChat, not the product repo; migration only via approved package).

### RC-5 — Implementation before architecture definition (architecture cause)

- **FACT:** The Lea-App ROADMAP phases 1→6 are *technical* steps (voice prototype → instructions → read Lea memory → write Lea repo → autonomous processing → sessions/UI). Phases 3 and 4 (read Lea memory, write to the Lea repo) have no status marker. D5 now makes B the target system, which makes phase 4 (writing into the Lea repo) directionally obsolete.
- **FACT:** Runtime identity is two hardcoded one-line prompts (`chat.php`: "You are Lea, a concise, helpful German-speaking assistant…"; `session.php`: "You are Lea, a concise German-speaking voice assistant…"). Neither references the VISION, PROCESSING or memory content (AUD-21).
- **FACT:** Voice has no memory context at all. `startSession()` in `frontend/app.js` POSTs to `api/realtime/session` with no body, and the server sends fixed instructions. Text receives a client-assembled memory summary. This contradicts Lea-App VISION "Text, Voice … sollen dieselbe Lea, Identitaet, Session-/Presence-Logik und Memory-Basis verwenden".
- **FACT:** Three memory representations exist:
  - V1 MySQL plaintext tables (`evidence.content`, `models.body`, `work_state.state_value`, …);
  - V2 ciphertext-only records (`privacy_v2_records`);
  - Lea's Markdown memories in Git.
  No component defines which one is canonical. V1 `work_state` receives the plaintext first 255 characters of each user message as `ACTIVE_TOPIC` (`sendTextMessage()`), while V2 promises that "Semantische Klartext-Payloads duerfen nicht als normale serverseitige Memory-Daten persistiert werden" (ARCHITECTURE.md).
- **FACT:** The V1 schema defines `predictions`, `prediction_results`, `review_queue` and `model_model_relations`. No API endpoint exists for them (`.htaccess` routes and the `api/memory/` listing). README lists "Predictions, Review-Queue" as part of the current "Funktionsstand".
- **FACT:** V2 records are keyed by `session_id` (NOT NULL, FK to `sessions`). Long-term experience is therefore scoped to a chat session row rather than to Lea or a person.
- **FACT:** `writeMemoryV2Record` is exported only on `globalThis.LeaMemoryV2`. No UI or chat flow in `app.js` calls it. The V2 write path exists but no product flow uses it.
- **INFERENCE:** Each increment was locally reasonable and tested, but no target data/identity model existed to check whether an increment moved toward or away from "one Lea, one memory". This is the dominant architecture root cause, and the reason this run exists.
- **Explains:** F-E, F-F, part of F-G and F-C (README vs code).
- **Controls:** CTL-07, CTL-08, CTL-16 (architecture conformance review in C2 for each package), and `B-BLUEPRINT.md` as reference.

### RC-6 — Verification checks presence, not behaviour (testing cause)

- **FACT:** The test suite is `node --test` over `tests/*.js`. Most assertions are regex or presence checks on source text. Many use alternations that pass on either branch (AUD-25). There is no automated test proving `chat.php` or `session.php` rejects unauthenticated calls (AUD-23/25).
- **FACT:** Lea-App has no `.github/` directory at the repository root (MCP root listing), so there is no CI workflow and tests only run when an agent or human runs them.
- **FACT:** Lea's own rule set already says a technical PASS is insufficient (SHORTCUTS "Q prueft Zielerreichung"; Lea-App VISION "Lokaler Test-PASS ist kein Produktions-PASS").
- **INFERENCE:** Presence-tests are cheap for zero-memory agents to write and to satisfy. Behavioural tests need a running PHP runtime, a DB fixture and HTTP harnesses, which the environment did not provide by default. The incentive gradient therefore pushed toward presence-tests.
- **Explains:** F-H, and allowed F-G to persist.
- **Controls:** CTL-10 (behavioural test harness: PHP built-in server + ephemeral DB), CTL-11 (security test suite that fails closed), CTL-17 (test-quality rule: every assertion must be falsifiable by a mutation of the behaviour, not only of text).

### RC-7 — Uneven server-side security boundary (security cause)

- **FACT:** `api/text/chat.php` and `api/realtime/session.php` perform no auth check, while `api/memory/*` and `api/v2/memory/*` call `lea_memory_require_auth()` (AUD-23). Both unauthenticated endpoints spend the server-side model API key.
- **FACT:** The login rate limit keys on the first `X-Forwarded-For` value when present (`lea_memory_client_ip()` in `_common.php`). That value is client-controlled unless a trusted proxy overwrites it (AUD-24).
- **FACT:** `chat.php` accepts `memory_context` (≤4000 chars) and `history` (roles including `system`) from the client and inserts them into the model prompt. The server therefore does not decide what memory Lea sees, and any caller of the unauthenticated endpoint controls the "memory".
- **FACT:** The HMAC memory token (`lea_memory_issue_memory_auth_token`) is stateless with a 900 s TTL. Logout clears the cookie session but cannot revoke an issued token.
- **FACT:** The session store and login-attempt store are JSON files rewritten whole on each change (`lea_memory_write_json_file`). There is no cross-request lock on the read-modify-write sequence.
- **FACT:** `memory-crypto.js` imports the master key as `extractable: true`, so script running in the page can export it.
- **INFERENCE:** The security model was designed per endpoint family as each feature was added (memory first, then text/voice). There was no endpoint inventory with a required policy per endpoint. Combined with RC-6, the missing checks were never detected.
- **Explains:** F-G.
- **Controls:** CTL-11, CTL-18 (endpoint policy manifest: every route must declare auth, rate/cost limit and input policy; CI fails if a route lacks one), CTL-19 (server-assembled context only), CTL-20 (trusted-proxy configuration for client IP).

### RC-8 — Broad mutation scope and paper-only gates (process / governance cause)

- **FACT:** The Lea-App freeze exists in WORKSPACE D3 and in Lea's conversation state, but not in Lea-App itself. There is no branch protection, no CODEOWNERS and no required reviews: `list_branches` shows `protected:false` for all branches of Lea and Lea-App (AUD-17).
- **FACT:** SHORTCUTS Q authorises autonomous repair of *both* repos ("Befunde autonom beheben/bereinigen") with only prose gates. The three-tier autonomy rule is explicitly "nicht mathematisch eindeutig" (PROCESSING "Drei Autonomiestufen").
- **FACT:** Obsolete branches accumulate in both repos (AUD-13). Lea-App currently has two `copilot/*` branches pointing at the same SHA as `main`.
- **INFERENCE:** The gates depend on every actor reading and obeying the same prose. With zero-memory agents and several authorities (RC-1/RC-2), a gate is only as strong as the most recent context the actor happened to load.
- **Explains:** F-I, F-L, part of F-A.
- **Controls:** CTL-14 (technical freeze: branch protection + required review + CODEOWNERS on Lea-App `main`; package-scoped unfreeze), CTL-15 (per-package allowed-path manifest checked in CI), CTL-21 (signed/attributed commits or distinct bot identities).

### RC-9 — Insufficient independent review and capability limits (review / hosting cause)

- **FACT:** Before D2, discovery, fix and verification could be done by the same agent class in the same run (Q). D2 now requires independent review, a cross-check and two methodically different zero-finding audits.
- **FACT:** The review tools available to this agent are limited:
  - the Pulls API returns 403 for this token, so PR #1 cannot be read;
  - AiChat returns 404 via MCP;
  - the previous `parallel_validation` code review failed with "model missing" (AUDIT-LOG).
- **FACT:** Production is shared hosting (static + PHP, "ohne dauerhaft erforderlichen Node-Prozess", ARCHITECTURE.md). This excludes long-running workers, queues and WebSockets from the current runtime (Lea-App ROADMAP "Lea als Working Tool … VPS/Managed Server sinnvoller").
- **INFERENCE:** Review quality was limited by *tool reach* as much as by process. Some checks were impossible (PR state, production exposure) and therefore silently skipped rather than recorded as a blocker. Hosting limits push features (idle follow-ups, background processing, memory consolidation) into the client or into prompt instructions, where they cannot be enforced.
- **Explains:** F-K, and why F-G/F-H persisted.
- **Controls:** CTL-06, CTL-22 (review record must list checks that were impossible, as BLOCKED/NOT-VERIFIABLE, never omitted), CTL-23 (hosting-capability register in the blueprint; features that need a worker are gated on it).

### Additional causes checked (task list) — result

| Candidate cause | Verdict | Evidence / reasoning |
|---|---|---|
| multiple/ambiguous authorities | **Confirmed → RC-1** | §3 RC-1 |
| stale context and memory | **Confirmed → RC-2** | AUD-05/07/12/18 |
| duplicated semantics | **Confirmed → RC-1/RC-2** | AUD-03/04/28; Memories.md duplicate sections |
| agent zero-memory behaviour | **Confirmed as amplifier → RC-2/RC-3** | Zero-memory is by design and is not itself a defect. It turns every unschematised state into a risk. |
| insufficient persistent handoff | **Confirmed → RC-3** | AUD-02/19; Lea-App AGENT_RESULT |
| broad mutation scope | **Confirmed → RC-8** | Q authorises repairs across two repos |
| missing preconditions/gates | **Confirmed → RC-3/RC-8** | No capability preflight; freeze not enforced |
| documentation/code drift | **Confirmed → RC-5** | README lists predictions/review queue as function state, but there is no API. ARCHITECTURE says V2 prevents plaintext, but V1 stores message text. |
| presence-only tests | **Confirmed → RC-6** | AUD-25 |
| missing E2E/security tests | **Confirmed → RC-6/RC-7** | No auth test for chat/realtime; no CI |
| runtime identity not linked to Lea-Core | **Confirmed → RC-5** | AUD-21 |
| development vs product mechanisms mixed | **Confirmed → RC-4** | AUD-22 |
| premature implementation | **Confirmed → RC-5** | Roadmap phases technical-first; schema tables without APIs |
| FACT vs assumption vs vision vs state not separated | **Partly confirmed** | Lea-App VISION.md explicitly separates vision from status (good counterexample). README mixes schema existence with capability ("Funktionsstand"). Memories.md mixes observation/interpretation in prose, although PROCESSING rule 4 demands separation. → part of RC-2/RC-5 |
| insufficient independent review | **Confirmed → RC-9** | See RC-9 |
| hosting/runtime limitations | **Confirmed as constraint → RC-9** | Shared hosting; no worker |
| **Additional: capability mismatch between task and environment** | **New → RC-3/RC-9** | Lea-App AGENT_RESULT; AiChat MCP 404 |
| **Additional: success criteria defined after work starts** | **Inference only** | PROCESSING "Inkrementelle Entwicklung …" (26.09) introduced milestone gates *after* several deployments. This cannot be confirmed without the deployment history. → INFERENCE, to be checked by Lea in C2. |
| **Additional: cost pressure shaping verification depth** | **Inference only** | PROCESSING "Aktuelle GitHub-Ressourcenlage" (90% of Codespaces quota used) and credit-routing rules. Cheaper routes favour presence-tests. Plausible, not proven. |

### Counterexamples (things that worked, to preserve)

These show that the root causes are not universal. Keep these patterns:

- **FACT:** Privacy V2 is designed with care:
  - AES-256-GCM, 96-bit IV;
  - PBKDF2-SHA-256 with 600 000 iterations;
  - canonical, length-prefixed AAD `LEA-V2-AAD-1`;
  - the server rejects semantic plaintext fields (`lea_v2_reject_semantic_fields`);
  - no UPDATE/DELETE path.
- **FACT:** Secrets are read from files outside the web root, not from the repo. SECURITY.md forbids memory content in logs.
- **FACT:** Lea-App VISION.md explicitly separates target from status and forbids fake processing animation.
- **FACT:** PROCESSING "Verbindlicher Wahrheits- und Speichernachweis" requires a commit SHA as proof of a save. This is a strong verifiable-claim pattern to carry into product audit logs.
- **FACT:** The V1 schema already models evidence → model → relation → prediction → result → review queue, i.e. Lea's experience cycle. The *data model idea* is sound; what is missing is canonical use and exposure.

---

## 4. Evidence mapping (AUD → root cause → control)

| AUD | Short | Root cause(s) | Class | Status after D1–D5 | Primary control(s) |
|---|---|---|---|---|---|
| AUD-01 | Freeze absent where shortcuts defined | RC-8, RC-1 | F-I | open | CTL-14 |
| AUD-02 | Conflicting handoff rules | RC-3, RC-1 | F-A | partly resolved (D4 retires ß; Lea texts unchanged) | CTL-01, CTL-13 |
| AUD-03 | INDEX defines S semantics | RC-1 | F-A | open | CTL-03 |
| AUD-04 | Protocol duplicated on two branches | RC-1, RC-2 | F-C | historical after D4 | CTL-02 (archive) |
| AUD-05 | L decisions not persisted | RC-2, RC-3 | F-B/F-J | open | CTL-04 |
| AUD-06 | Writers indistinguishable | RC-8 | F-L | open | CTL-21 |
| AUD-07 | WORK-CONTEXT stale | RC-2 | F-B | open (D4 keeps file untouched) | CTL-05, CTL-02 |
| AUD-08 | PR #1 description stale | RC-2 | F-B | not verifiable (403) | CTL-22 |
| AUD-09 | INDEX registry incomplete | RC-2, RC-1 | F-C | open | CTL-03, CTL-05 |
| AUD-10 | Dev material in private/core files | RC-4 | F-D | open | CTL-02, CTL-13 |
| AUD-11 | Two audit definitions | RC-1 | F-A | **resolved by D2** | — (record in CTL-01) |
| AUD-12 | SHORTCUTS "Stand" stale | RC-2 | F-B | open | CTL-05 |
| AUD-13 | Obsolete branches | RC-8 | F-B | open | CTL-24 (branch hygiene) |
| AUD-14 | Protocol trigger form differs | RC-3 | F-J | historical after D4 | — |
| AUD-15 | PR description may change | RC-3 | F-J | not verifiable | CTL-22 |
| AUD-16 | Concurrent writes untested | RC-6, RC-2 | F-H | open | CTL-12, CTL-10 |
| AUD-17 | Freeze not stated/enforced in Lea-App | RC-8 | F-I | open | CTL-14 |
| AUD-18 | Lea-App AGENT_TASK stale READY | RC-2, RC-3 | F-B | open | CTL-04, CTL-13 |
| AUD-19 | C result target collides with freeze | RC-3, RC-1 | F-A | open (D1 implies AiChat as result location; not yet reflected in SHORTCUTS) | CTL-13 |
| AUD-20 | AiChat unknown to Lea | RC-1 | F-A | **resolved by D1** at decision level; Lea texts still unaware | CTL-01, CTL-02 |
| AUD-21 | Hardcoded runtime identity; split memory | RC-5 | F-E/F-F | open | CTL-07, CTL-08 |
| AUD-22 | Test marker in production | RC-4 | F-D | open | CTL-09 |
| AUD-23 | Chat/realtime unauthenticated | RC-7, RC-6 | F-G | open (production exposure unverified) | CTL-11, CTL-18 |
| AUD-24 | XFF-keyed rate limit | RC-7 | F-G | open | CTL-20 |
| AUD-25 | Weak tests | RC-6 | F-H | open | CTL-10, CTL-17 |
| AUD-26 | Infra identifiers hardcoded | RC-7, RC-5 | F-G | open | CTL-25 (config externalisation) |
| AUD-27 | Binding rules outside SHORTCUTS | RC-1 | F-A | open | CTL-01, CTL-03 |
| AUD-28 | Near-duplicate PROCESSING section | RC-2 | F-C | open | CTL-03 |

### 4.6 Additional code-level observations (not in AUD list; new evidence for C2 revalidation)

These are recorded as **observations** for C2 to revalidate. They are not new AUD findings; the audit artefacts are not modified.

| Obs | FACT (source @ ref) | Root cause | Relevance |
|---|---|---|---|
| OBS-01 | `frontend/app.js` `sendTextMessage()` POSTs `work-state` `ACTIVE_TOPIC` with `cleaned.slice(0,255)`, i.e. plaintext of the user message, into V1 MySQL. | RC-5 | Conflicts with the V2 privacy intent (ARCHITECTURE.md "Privacy V2"). |
| OBS-02 | `api/text/chat.php` accepts client `memory_context` and `history[].role` in {user, assistant, system}; values are concatenated into the user prompt. | RC-7 | Client-controlled context; prompt-injection surface; server does not own memory selection. |
| OBS-03 | `api/realtime/session.php` receives no memory/identity context; `startSession()` sends an empty POST. | RC-5 | Voice and text diverge; VISION "eine Lea" not met. |
| OBS-04 | `writeMemoryV2Record` is exported only via `globalThis.LeaMemoryV2`; no flow writes V2. | RC-5 | The V2 encrypted store exists but no product flow uses it. |
| OBS-05 | `privacy_v2_records.session_id` NOT NULL → sessions; memory scoped per session row. | RC-5 | Long-term memory tied to chat session; blocks a cross-session canonical model. |
| OBS-06 | V1 tables `predictions`, `prediction_results`, `review_queue`, `model_model_relations` have no API route (`.htaccess`). README lists them as current function state. | RC-5, RC-2 | Doc/code drift; the experience cycle is not reachable. |
| OBS-07 | `memory-crypto.js` `importMasterKey(..., extractable=true)`. | RC-7 | XSS could exfiltrate the unlocked master key. |
| OBS-08 | HMAC memory token is not revocable on logout. JSON-file session store has no cross-request lock. | RC-7, RC-6 | Session hygiene; concurrency. |
| OBS-09 | There is no idle follow-up counter or cap in the client. Idle behaviour is only server VAD `idle_timeout_ms: 15000` plus a prompt instruction. PROCESSING "Inaktivitaet" requires a maximum of 5 unanswered attempts, then INACTIVE. | RC-5 | A documented behaviour rule exists without implementation or enforcement. |
| OBS-10 | No CI workflow directory in Lea-App root. | RC-6 | Tests are not enforced. |
| OBS-11 | `api/realtime/session.php` relays the upstream status and body on error. | RC-7 | Low: may leak upstream error detail to the caller. |
| OBS-12 | V2 `records.php` computes `lea_v2_canonical_aad($aad)` only as validation and stores `aad_json` (non-canonical JSON). The client re-derives canonical AAD from metadata (`memoryV2CanonicalMetadataFromRecord`). | — | Not a defect. The invariant "the server never needs the canonical AAD" is worth preserving and testing. |

---

## 5. Why prior safeguards failed or were insufficient

| Safeguard (where) | Intended effect | Why insufficient | Evidence |
|---|---|---|---|
| "SHORTCUTS.md is the only authority" | Prevent semantic drift | Covered only shortcut *letters*. Execution rules and handoff rules stayed in other files, and no check detects a second definition. | AUD-03, AUD-27 |
| Q-Schutz (pre-write checks, two zero-finding checks) | Catch inconsistent writes | Performed by the same actor who writes, from the context that actor loaded. Without independent reviewers or tooling it cannot find what its context lacks. | RC-9; PROCESSING "Shortcut-Quellenregel nach Fehlbereinigung" |
| C (read-only audit) | Independent verification | It is required to write AGENT_RESULT into the audited repo, which contradicts the freeze. It needs both repos but was run in a single-repo environment. | AUD-19; Lea-App AGENT_RESULT |
| Transfer-Gate A/B/C | Keep workshop out of product | Introduced after the leakage, and no retroactive scan was done. | AUD-22 (25.09) vs. rule (26.09) |
| Three autonomy tiers | Scale gates to impact | Explicitly not deterministic; the actor self-classifies. | PROCESSING "Drei Autonomiestufen" |
| "Local test PASS ≠ production PASS" | Prevent false completion | No behavioural or production test harness existed to produce the stronger evidence. | AUD-25, OBS-10 |
| Freeze (D3) | Prevent product mutation | Declared in AiChat only; not visible or enforced in Lea-App. | AUD-17 |
| Privacy V2 "no plaintext" | Protect memory content | Enforced only on the V2 endpoints. V1 endpoints in the same app accept plaintext, and the UI writes message text to V1. | OBS-01 |
| Commit-SHA proof for saves | Prevent false "saved" claims | Works for repo writes. There is no equivalent for DB writes or production changes. | PROCESSING "Wahrheits- und Speichernachweis" |

**Pattern (INFERENCE):** Nearly every safeguard was a *rule for an actor*. Almost none was a *property of the system* (a schema, CI check, branch protection, server policy or typed record). When rules are enforced by the rule-follower, failures correlate with context gaps. Zero-memory agents and a stateless LLM guarantee such gaps.

---

## 6. Prevention and detection controls

`P` = prevents, `D` = detects. Placement: **RT** product runtime, **WF** development workflow (AiChat/C2), **TS** automated tests/CI, **GOV** repository governance.

| ID | Control | P/D | Placement | Addresses | Notes |
|---|---|---|---|---|---|
| CTL-01 | **Authority registry**: one table (in AiChat) mapping every domain to exactly one authoritative home plus migration state; referenced by every task. | P | WF/GOV | RC-1 | Seed in `B-BLUEPRINT.md` §1.2. |
| CTL-02 | **Domain migration state machine** per domain: `SOURCE_ONLY → IMPORTING → B_CANDIDATE → B_AUTHORITATIVE → SOURCE_ARCHIVED → SOURCE_RETIRED`; exactly one authoritative home at every state. | P | WF/RT | RC-1, RC-5 | Prevents dual master. |
| CTL-03 | **Single-definition lint** for rule/shortcut semantics: a CI or agent check that fails if a rule keyword is defined in more than one authoritative file. | D | TS/WF | RC-1 | For Lea repo only with an approved package; for AiChat directly. |
| CTL-04 | **Structured task/result/decision records** (front-matter or JSON block: task id, status enum, preconditions, allowed paths, decision refs, result SHA) with a validator. L decisions stored as records with ID and date. | P/D | WF | RC-2, RC-3 | Replaces prose status fields. |
| CTL-05 | **Freshness checks**: every "Stand"/status field has a date and an owner; CI/agent flags entries older than their referenced source. | D | TS/WF | RC-2 | |
| CTL-06 | **Capability preflight** at the start of each task: verify read/write reach for each repo and tool required. If any is missing, return BLOCKED before any work. | P | WF | RC-3, RC-9 | Proven useful in the zero-memory capability test. |
| CTL-07 | **Canonical structured memory in B** (evidence → model → relation → prediction → revision) with provenance, stable IDs and supersession; no plaintext outside the encrypted payload. | P | RT | RC-2, RC-5 | See `B-BLUEPRINT.md` §4–§6. |
| CTL-08 | **Server-assembled identity & context**: runtime identity core, versioned and loaded server-side for text *and* voice; no hardcoded persona strings. | P | RT | RC-5 | See `B-BLUEPRINT.md` §3, §9, §13. |
| CTL-09 | **Product artefact deny-list scan**: CI fails on workshop markers (test markers, shortcut letters as commands, agent-file references) in product runtime paths. | D | TS | RC-4 | |
| CTL-10 | **Behavioural test harness**: PHP built-in server + ephemeral MySQL/MariaDB (or SQLite adapter, to verify) + HTTP-level tests; run in CI. | D | TS | RC-6 | Compatibility of SQLite with MySQL-specific schema: TO VERIFY. |
| CTL-11 | **Security test suite (fail-closed)**: unauthenticated requests to every non-public route must return 401. Covers rate-limit spoofing, CSRF/Origin checks, oversized input, secret-leak grep and plaintext-leak checks for V2. | D | TS | RC-6, RC-7 | |
| CTL-12 | **Optimistic concurrency** on persistent writes (expected version/ETag); already a Q rule, to be made technical. | P | RT/WF | RC-2 | |
| CTL-13 | **Workshop/product separation**: agent task/result files for LEA-WORK live in AiChat (D1); product repo contains no workshop state; C/R result location = AiChat. | P | GOV/WF | RC-3, RC-4 | Requires an approved Lea-App package to remove files (freeze). |
| CTL-14 | **Technical freeze**: branch protection + required review + CODEOWNERS on Lea-App `main`; a visible FREEZE notice; unfreeze = a time-boxed, package-scoped branch plus an approval record. | P | GOV | RC-8 | Repository settings are outside the file system. Performing them needs L or admin action. |
| CTL-15 | **Allowed-path manifest per package**: CI diff check rejects changes outside the package's declared paths. | P/D | TS/GOV | RC-8 | |
| CTL-16 | **Architecture conformance check** in C2 per package: does the change move toward blueprint invariants? | D | WF | RC-5 | |
| CTL-17 | **Test-quality rule**: no alternation-based "either passes" assertions for security/behaviour; mutation-style review of new tests. | D | TS/WF | RC-6 | |
| CTL-18 | **Endpoint policy manifest**: every route declares `auth`, `csrf/origin`, `rate`, `cost_budget`, `input_limits`; a router/bootstrap enforces it; CI fails on undeclared routes. | P/D | RT/TS | RC-7 | |
| CTL-19 | **Server-owned context assembly**: client never supplies system/memory context; server selects memory. | P | RT | RC-7, RC-5 | For client-side-decrypted V2 memory, see `B-BLUEPRINT.md` §6.4 trade-off. |
| CTL-20 | **Trusted-proxy client IP** configuration; rate limits keyed on REMOTE_ADDR unless the proxy is trusted; plus per-account limits. | P | RT | RC-7 | Hosting proxy behaviour TO VERIFY. |
| CTL-21 | **Actor attribution**: distinct identities or commit trailers for L, Lea (runtime), and each agent. | D | GOV | RC-8 | |
| CTL-22 | **Explicit NOT-VERIFIABLE recording**: every audit/review lists impossible checks as BLOCKED with reason. | D | WF | RC-9 | Used in AUDIT-LOG. |
| CTL-23 | **Hosting capability register**: which features need workers, WebSockets or cron; features gated on it. | P | WF/RT | RC-9 | `B-BLUEPRINT.md` §36. |
| CTL-24 | **Branch hygiene** rule: merged or obsolete branches recorded and removed only after verification. | D | GOV | RC-8 | Deletion needs an approval. |
| CTL-25 | **Config externalisation**: infrastructure paths and identifiers only via environment/config outside the repo, with safe defaults that fail closed. | P | RT | RC-7 | |
| CTL-26 | **Product audit log with verifiable receipts**: every memory write, connector action and deployment yields an ID, timestamp and outcome. The UI claims success only after a receipt exists (product analogue of the commit-SHA rule). | P/D | RT | RC-2, RC-7 | |

### 6.1 Placement summary

- **Product runtime (RT):** CTL-02 (runtime side), 07, 08, 12, 18, 19, 20, 25, 26.
- **Development workflow (WF, AiChat/C2):** CTL-01, 02, 04, 05, 06, 13, 16, 22, 23.
- **Tests/CI (TS):** CTL-03, 09, 10, 11, 15, 17, 18 (check side).
- **Repository governance (GOV):** CTL-13, 14, 15, 21, 24.

---

## 7. Residual risks and trade-offs

| Risk | Why it remains | Mitigation / acceptance |
|---|---|---|
| R-1 LLM non-determinism | Identity continuity cannot be proven by one test (VISION "Kein einzelner Test beweist …"). | Pattern-over-time evaluation (`B-BLUEPRINT.md` §30); do not claim identity proof. |
| R-2 Upstream provider dependency | B still needs an external LLM for text/voice. "Independent from A" does not mean independent from any model provider. | Provider adapter (`B-BLUEPRINT.md` §9.5); independence gate defines "no ChatGPT project/A needed", not "no provider". |
| R-3 Client-side decryption vs server-side context | End-to-end encrypted memory means the server cannot select memory without the key. Server-side context assembly (CTL-19) is then limited to what the client decrypts or to a server-held key. | Resolved in principle by **D12** (layered protection: core/experience encrypted at rest but server-runtime-readable; sensitive personal/relationship/identity narrower, client-held/E2E where functionally compatible). `B-BLUEPRINT.md` §6.4/§28. Residual: a server compromise can expose the server-readable class; concrete crypto remains a verified security package. |
| R-4 Shared hosting limits | Background workers may be unavailable. Consolidation, idle follow-ups and queues must be request-driven or cron-driven. | Hosting capability register; strategy (settled, ROADMAP/D5 portability): stay on shared hosting while sufficient, keep code portable, move only on a demonstrated need (Hosting Gate). |
| R-5 Governance controls need admin action | Branch protection and CODEOWNERS are GitHub settings that agents here cannot set. | Package IM-G01 requires L/admin action; until then, the freeze stays paper-only (known risk). |
| R-6 Migration fidelity | Prose memories → structured records loses nuance, or misclassifies interpretation as observation. | Import keeps the original text as a source artefact with provenance; mapping is reviewable; nothing is deleted. |
| R-7 Over-control | Too many gates freeze legitimate development (SHORTCUTS "Q-Schutz schuetzt Integritaet, nicht Stillstand"). | Controls target *process* and *security*, not content; revision is first-class in the memory model. |
| R-8 Single-user assumptions | Current auth is a single shared password. Multi-person presence requires a new identity model. | Resolved in principle by **D8** (L = authenticated owner; known persons without accounts; known ≠ authenticated). `B-BLUEPRINT.md` §17. |
| R-9 This analysis is single-source (Claude) | D2 requires independent review. | Lea's independent C2 review has since taken place and led to D6–D12; several of Claude's provisional defaults were corrected (§10). Further C2 rounds remain required; no acceptance is claimed. |
| R-10 Restore claims without drills | D10: a backup is not reliable merely because it exists. | Restore verification is a gate criterion (IMPLEMENTATION-MAP IM-M09, IM-X02) and a precondition for expanding autonomy (IM-C07). |

---

## 8. L-DECISION-NEEDED items raised by root-cause analysis (reconciled)

The original run listed four items (LDN-RC-1..4). On reconciliation (§10) none of them remains an open L decision:

| ID | Topic | Reconciled status |
|---|---|---|
| LDN-RC-1 | Authority registry location (CTL-01) | **Organisational, not an L decision.** AiChat is the work authority (D1). RECOMMENDATION: a separate AiChat file `AUTHORITY-REGISTRY.md`, created by package IM-G02 and approved like any AiChat work artefact. |
| LDN-RC-2 | Branch protection/CODEOWNERS (CTL-14) | **Governance recommendation / admin action**, not an architecture decision. It needs L/admin to change GitHub settings (agents lack the rights). Package IM-G01. |
| LDN-RC-3 | `web_app1` | **Out of scope** under D5 (Lea-App is the target system); non-authoritative for Lea. |
| LDN-RC-4 | Workshop files in Lea-App | **Settled by D1/D3:** AiChat is the work authority; Lea-App is frozen. Any future change to Lea-App workshop files is an ordinary D3 change package (and, under Lea-App AGENTS.md, a high-impact rule change needing its own pre-checks). |

---

## 9. Non-claims

- No PASS is claimed for any part of the project.
- No audit finding was changed, withdrawn or re-scored in `AUDIT-FINDINGS.md`. Observations OBS-01…12 are new evidence for C2, not accepted findings.
- Production exposure (whether endpoints are reachable unauthenticated in the live deployment) was **not** verified. No production access was attempted.

---

## 10. Reconciliation provenance (AGENT_TASK v4)

- **What happened:** Lea performed the independent C2 review (D2 step 2) of the three original artifacts. The outcome was recorded by L as authoritative decisions **D6–D12** in WORKSPACE.md (AiChat `main` `6fb71ff`).
- **Finding recorded here, not hidden:** the independent review showed that several of Claude's provisional defaults did **not** match the settled vision or were presented as open when they were already settled. Specifically:
  - Incognito (reads restricted to the identity core; corrected by D6);
  - migration (file-based exclusion of PERSONAL/HEALTH/LEGAL; corrected by D7);
  - people (the default "owner + declared guests" was broadly aligned, but it framed other people mainly as session guests; D8 clarifies that known persons are relationship/context identities and that a known person ≠ an authenticated user);
  - monitor (off by default + a 90-day retention presented as default; corrected by D9);
  - connector autonomy (blanket "write connectors AUS"; corrected by D10);
  - canonical store (framed as a V2-based replacement; reframed as V1+V2 convergence by D11);
  - encryption (two tiers with fixed crypto parameters; reframed as layered protection with crypto as a verified security package by D12).
- **Root-cause relevance (INFERENCE):** this is itself an instance of RC-5/RC-9. A single-source design agent filled open points with plausible defaults. Without independent review, those defaults could have been implemented as if they were requirements. The C2 lifecycle caught this before any implementation.
- **Effect on this document:**
  - §0, §7 and §8 were updated; the causes RC-1..RC-9, the AUD mapping and the controls are unchanged.
  - The original text is preserved in Git history (commits `39f7331`, `11497e7`).
- No PASS is claimed.
