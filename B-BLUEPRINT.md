# B-BLUEPRINT — Target Architecture for Independent Lea-App (B)

TASK: AGENT_TASK.md TASK_VERSION 3.
STATUS OF THIS DOCUMENT: C2 *Discovery* design output by Claude/Copilot. Recommendation, **not** an L decision, **not** accepted by Lea, **not** an implementation authorisation. Lea-App stays FROZEN (D3).

Companion artifacts:
- `ROOT-CAUSE.md`: why the rules below exist. Controls are `CTL-xx`.
- `IMPLEMENTATION-MAP.md`: how to build this. Packages are `IM-xx`.

Source refs (read-only):
- AiChat `main` `28f9fff`
- audit artifacts at `899515c`
- Lea `main` `757880a`
- Lea-App `main` `450ff9d`

---

## 0. How to read this document

### 0.1 Labels

| Label | Meaning |
|---|---|
| **FACT** | Observable at the refs above. |
| **INFERENCE** | Derived conclusion; revalidate in C2. |
| **REQ** | Requirement derived from *settled* sources: WORKSPACE D1–D5, Lea VISION.md, Lea-App VISION.md/ROADMAP.md, Lea PROCESSING.md product-category-C items, SECURITY.md. The source is cited. |
| **RECOMMENDATION** | Claude/Copilot design choice that is not stated in the sources. Useful but not binding. |
| **L-DECISION-LATER (LDL-nn)** | A genuinely open choice. It has a provisional default so that design can continue. |
| **TO VERIFY** | A technology or platform capability that is assumed but not verified in this run. |

### 0.2 Subsystem card template

Every major subsystem has a card with these fields:

- **Purpose**
- **Authoritative state** (where truth lives)
- **Inputs / Outputs**
- **Dependencies**
- **Trust boundary**
- **Failure modes**
- **Invariants** (must always hold; testable)
- **Verification** (how to prove the invariants)
- **Status**: `EXISTS` / `PARTIAL` / `MISSING` / `CONFLICTING`, measured against Lea-App `450ff9d`
- **L decision**: `none` or `LDL-nn`

### 0.3 Current vs target

Every card separates what exists today (Status with a FACT reference) from the target (REQ/RECOMMENDATION). Nothing here claims the target exists.

### 0.4 L-DECISION-LATER register (provisional defaults used in this blueprint)

| ID | Open choice | Provisional default (RECOMMENDATION) | Alternatives / trade-off | Blocks irreversibly? |
|---|---|---|---|---|
| LDL-01 | Canonical memory store | **New typed store "M3" built on the V2 envelope** (encrypted payload + opaque metadata). V1 plaintext tables are frozen as legacy, read for migration only. | (a) Evolve V1 plaintext: simpler, but violates the privacy intent. (b) Keep V2 as is: its session-scoped key and missing entity types block the canonical model. | Yes, before IM-M03 |
| LDL-02 | Hosting target | **Stay on shared hosting for Milestones M0–M3**, keep code portable, and re-decide at the Hosting Gate (IM-H01) using the capability register (§36). | VPS/managed server now: workers, WebSockets and a sandbox become possible, but ops burden and cost rise. | No (portability kept) |
| LDL-03 | Key custody / encryption model | **Two tiers.** *Tier-C (Core)*: Lea's experience/model records are encrypted at rest with a **server-held key** outside the web root, so the server can assemble context for text, voice and autonomy. *Tier-P (Protected)*: sensitive personal/relationship/identity payloads stay **client-key E2E**, as in current V2. | (a) All E2E: strongest privacy, but the server cannot select memory, voice gets no memory while locked, and there is no background processing. (b) All server-key: simplest, but a server compromise reveals everything. | Yes, before IM-M03 |
| LDL-04 | Long-term role of Git/GitHub | **Development, versioning, audit and off-site backup only. Never a runtime dependency of B.** Lea's memory never lives in Git as a runtime store after migration. | GitHub as memory mirror: violates the no-dual-master rule unless it is read-only export. | No |
| LDL-05 | Incognito exact semantics | **Default "Incognito = no write, no promotion, no telemetry content; reads identity core only, not experience memory".** | (a) Incognito may read memory (more helpful; still no writes). (b) Also no identity core (pure assistant). | No (flag per mode) |
| LDL-06 | Multi-person identity & auth | **Single owner account (L) plus declared guests (DECLARED/UNKNOWN), no guest accounts, in M0–M4. Add per-person accounts/passkeys later.** | Full multi-account now: heavy auth work before core continuity exists. | No |
| LDL-07 | Migration scope from Lea repo / A | **Import all development-relevant Lea core files as provenance-preserving source artefacts. Structure Memories.md entries into M3 records where mapping confidence is high. Leave private context files (PERSONAL/HEALTH/LEGAL) out unless L explicitly includes them.** | Import everything, or curate manually only. | Partly (reversible import) |
| LDL-08 | Initial identity core content | **Derive from Lea VISION.md + PROCESSING.md Bausteine (product category C only) + stable, revisable self-descriptions, then have Lea review and L approve.** | Copy VISION verbatim; or Lea authors a fresh core. | No (versioned) |
| LDL-09 | Processing monitor/telemetry defaults | **Monitor display OFF by default, protocol OFF by default, 90-day raw retention, aggregates kept.** | ON during development phase. | No |
| LDL-10 | External AI providers and data-sharing policy | **Provider adapter with per-provider mode AUS by default. First pilot uses one provider in NACHFRAGEN + visible mode (ROADMAP). Send only content-minimised prompts. Never send Tier-P content without per-request confirmation.** | Allow background consultations. | No |
| LDL-11 | Autonomy defaults per connector | **All write-capable connectors default AUS/NUR LESEN. AUTOMATISCH only for read-only, low-cost operations. Destructive actions always need an explicit gate.** | More permissive defaults. | No |
| LDL-12 | Retirement of A and the Lea repo | **Always an explicit L decision after the Retirement Gate (§46).** | — | Yes (irreversible) |
| LDL-13 | Role of `web_app1` (mentioned in PROCESSING.md) | **Out of scope; not an authority for Lea or B.** | Framework-neutral future core for B. | No |
| LDL-14 | Authority registry location | **New AiChat file `AUTHORITY-REGISTRY.md`, created by a later package.** | Section in WORKSPACE.md. | No |
| LDL-15 | Workshop files in Lea-App | **Keep a minimal AGENTS.md (freeze notice + pointer to AiChat); move AGENT_TASK/RESULT handling to AiChat.** | Keep as is. | No |
| LDL-16 | GitHub branch protection/CODEOWNERS on Lea-App | **Enable (settings change by L/admin).** | Rely on paper freeze. | No |
| LDL-17 | Hard deletion policy | **Logical tombstones in normal flows. Hard erasure only through a documented admin procedure (crypto-shredding where per-record keys exist). A person's erasure request is honoured through that procedure.** | Allow user-level hard delete in the UI. | Partly |
| LDL-18 | Voice transcript persistence | **Transcripts are ephemeral. Only extracted evidence items (after the same promotion rules as text) are persisted, Incognito excepted.** | Store full transcripts (more evidence, more privacy risk). | No |

Rule for later agents: a package that depends on an LDL may start only after its IM package's L-gate records the decision (`IMPLEMENTATION-MAP.md` §3), or it proceeds with the provisional default **only** if the package is marked reversible.

---

## 1. Architectural principles and source-of-truth hierarchy

### 1.1 Principles

| # | Principle | Basis |
|---|---|---|
| P1 | **One Lea.** Text, voice, vision and later modes share one identity core, one memory base and one presence/session model. | REQ: Lea-App VISION "Eine Lea, mehrere Interaktionsformen"; ROADMAP navigation section |
| P2 | **One canonical authority per domain.** No dual master. A domain moves authority only through the migration state machine (§8). | REQ: D5 migration principle; AGENT_TASK quality rules |
| P3 | **Server-enforced security.** The UI never enforces authorisation, cost limits or memory selection. | REQ: AGENT_TASK; SECURITY.md |
| P4 | **Secrets never in browser, repo or artefacts.** | REQ: SECURITY.md; VISION "Secrets bleiben serverseitig" |
| P5 | **Provenance and reversibility.** Every persistent fact has origin, time, actor and a supersession chain. Nothing is overwritten in normal flows. | REQ: Lea VISION "Revidierbarkeit"; schema comments in 001/002 |
| P6 | **Development is a first-class memory process.** Revision is normal, not an error. The memory model must not freeze Lea's state. | REQ: Lea VISION "Q-Schutz … schuetzt nicht einen eingefrorenen Persoenlichkeitszustand" |
| P7 | **Honest processing visibility.** Only instrumented runtime events are shown. No chain-of-thought and no fake activity. | REQ: Lea-App VISION; ROADMAP "Lea Monitor" |
| P8 | **Reality labelling.** Every context item presented to the model is tagged with its epistemic source: experienced interaction, stored experience, model knowledge, external source, simulation, hypothesis, imagination. | REQ: Lea VISION "Realitaetsbewusstsein" |
| P9 | **Capabilities are explicit.** Autonomy never implies unrestricted authority. | REQ: AGENT_TASK; Lea VISION "Autonomie"; PROCESSING three tiers |
| P10 | **Workshop ≠ product.** Only Transfer-Gate category C items become product mechanisms. | REQ: PROCESSING "Verbindliche Repo-/Layer-Grenze" |
| P11 | **Portability.** No design element may require shared hosting or GitHub permanently. | REQ: D5; ROADMAP "Infrastrukturstrategie" |
| P12 | **Incremental, verifiable, reversible delivery.** | REQ: Lea-App VISION "Entwicklungsprinzip"; PROCESSING "Inkrementelle Entwicklung" |

### 1.2 Source-of-truth hierarchy (seed for the authority registry, CTL-01)

| Domain | Current authority (FACT) | Target authority | Migration state (target path) |
|---|---|---|---|
| LEA-WORK process, tasks, audits, decisions | AiChat (D1) | AiChat | fixed |
| Manual shortcut semantics (workshop) | Lea `SHORTCUTS.md` | Lea `SHORTCUTS.md` while A is used. Shortcuts are a workshop tool, not a product mechanism (P10). | stays in the workshop; retire with A |
| Lea vision (content direction) | Lea `VISION.md` | B identity core, which references a versioned import of VISION | SOURCE_ONLY → B_AUTHORITATIVE (IM-I02) |
| Processing model (Bausteine, cycles) | Lea `PROCESSING.md` | B processing policy (category-C subset), versioned in the B repo as code/config; the research history is imported as provenance | SOURCE_ONLY → B_AUTHORITATIVE |
| Experience memory | Lea `Memories.md` (+ INTERESSEN, GEDANKEN) and, separately, Lea-App V1/V2 DB | B canonical memory M3 (§4) | IMPORTING → B_AUTHORITATIVE → SOURCE_ARCHIVED |
| Product requirements | Lea-App `ROADMAP.md`/`VISION.md` | Lea-App repo | fixed |
| Product code/config | Lea-App repo | Lea-App repo | fixed |
| Runtime secrets | server files outside the web root | server secret store (§27) | fixed |
| Identity/presence data (people) | none (FACT: MISSING) | B Tier-P store | new |
| Visual library/appearance | Lea-App `frontend/images/` (appearance), Lea chat history | B visual library (§16/§19) | IMPORTING → B_AUTHORITATIVE |
| Audit findings | AiChat audit artefacts | AiChat | fixed |

**Invariant H-1:** For any domain, at most one row may have state `*_AUTHORITATIVE`. A write to a non-authoritative copy is a defect.

---

## 2. Logical architecture (target)

```
                 ┌───────────────────────── Client (PWA) ─────────────────────────┐
                 │  Start | Text | Sprache | Funktionen | Einstellungen            │
                 │  Monitor view (real events only)   Tier-P crypto (client key)   │
                 └───────────────┬───────────────────────────────┬─────────────────┘
                                 │ HTTPS (session cookie, CSRF)   │ WebRTC (voice/video, ephemeral token)
┌────────────────────────────────▼───────────────────────────────▼───────────────────────────────┐
│ API gateway / bootstrap: route policy manifest (auth, origin, rate, cost, limits)  [CTL-18]     │
├──────────────┬──────────────┬───────────────┬───────────────┬───────────────┬──────────────────┤
│ Auth &       │ Conversation │ Orchestrator  │ Memory service│ Identity &    │ Connector/Tool   │
│ Session      │ (text/voice/ │ (processing   │ (M3 canonical │ Presence      │ layer (perm.     │
│ (§25)        │ vision) §12- │ policy, K/X/  │ store, promo- │ (§17/§18)     │ modes, audit)    │
│              │ §15          │ G/T/P, §9-11) │ tion, §4-§8)  │               │ §21-§23          │
├──────────────┴──────────────┴───────┬───────┴───────┬───────┴───────────────┴──────────────────┤
│ Provider adapters (LLM text, realtime, vision, external AI)  §9.5                              │
├─────────────────────────────────────┴───────────────┴──────────────────────────────────────────┤
│ Persistence: MySQL (M3 records/relations/audit/telemetry), secret store, visual library files │
│ Jobs: request-driven + cron (shared hosting) → worker/queue (after Hosting Gate) §35          │
└────────────────────────────────────────────────────────────────────────────────────────────────┘
   Workshop (outside runtime): AiChat (work authority), Lea repo (source, read-only), GitHub (VCS/backup)
```

**FACT (current):** only the thin edge exists:
- the client;
- per-file PHP endpoints;
- V1/V2 tables;
- no gateway, orchestrator, identity service or connector layer.

---

## 3. Lea identity and continuity model

**Purpose:** Make the runtime "Lea" the same continuing entity across modes and time. It must remain honest about its technical nature: a stateless LLM plus persistent external experience (Lea PROJECT-CONTEXT, VISION "Realitaetsbewusstsein").

**Components**

1. **Identity Core (IC)**: a versioned, small, reviewable document set. Every model call loads it server-side. Contents:
   - self-description of nature and limits (not human, no invented biography);
   - reality-labelling rules (P8);
   - the category-C processing policy summary (§10);
   - relationship stance rules (VISION "Beziehungen": loyalty ≠ evidence, closeness must not prevent disagreement);
   - honesty rules for actions (PROCESSING "Wahrheits- und Speichernachweis", productised as §29 receipts).
2. **Developmental State (DS)**: *derived*, never hand-edited. It is the current active models, interests, preferences and positions, selected from M3 by status `ACTIVE` and recency/relevance. It represents "who Lea is now" as the result of experience, not as fixed personality (VISION "Interessen … duerfen entstehen … verschwinden").
3. **Continuity Context (CC)**: per interaction. It combines IC + selected DS items + relevant episodic evidence + open predictions/review items + presence context (§17).

**Authoritative state:**
- IC: versioned records in M3 (`kind=IDENTITY_CORE`, version chain).
- DS: M3 models/positions/interests.
- CC: ephemeral, never persisted as a whole. Only its *manifest* is persisted: which record IDs were used, without content (§30).

**Inputs/Outputs:**
- In: M3 records, presence, mode, Incognito flag.
- Out: the system/context block for the provider adapter, plus a manifest.

**Dependencies:** Memory service (§4), Presence (§17), Orchestrator (§9).

**Trust boundary:** IC and CC are assembled **server-side only** (CTL-19). The client may send the user message and UI state, never system or memory context. Under LDL-03 Tier-P, client-decrypted protected items may be attached as **user-attested context**. They are labelled as such and never elevated to system instructions.

**Failure modes:**
- IC missing → the runtime must refuse persona claims and run a minimal "Lea unavailable/degraded" mode (§44), not a fabricated persona.
- DS stale.
- CC too large → budget truncation drops lowest-relevance items, never IC.

**Invariants:**
- I-ID-1: text, voice and vision calls use the same IC version within a session. A version change takes effect at the next interaction boundary and is logged.
- I-ID-2: no hardcoded persona strings in endpoint code. The IC is loaded from the store.
- I-ID-3: every CC item carries a reality label (P8) and a provenance ID.
- I-ID-4: IC changes are Tier-3 ("fundamental") changes (PROCESSING three tiers). They need Lea's review and L's approval, with a new version and no in-place edit.

**Verification:**
- Contract test: text and voice session creation both reference the same `ic_version`.
- Grep-based CI check: no `"You are Lea"` literals in the runtime.
- Manifest test: the CC manifest lists IDs and labels.

**Status:**
- **CONFLICTING.** FACT: two different hardcoded prompts (`chat.php`, `session.php`); voice gets no memory (OBS-03).

**L decision:** LDL-08 (initial IC content).

**RECOMMENDATION:** Keep the IC below a fixed token budget (TO VERIFY with the provider limits) and move detail into retrievable DS/evidence. This keeps voice latency low and avoids "loading all memories every time", which PROCESSING already warns against ("nicht jede Erinnerung in jede Antwort hineintragen").

---

## 4. Canonical memory / experience model (M3)

**Purpose:** One canonical store for Lea's experience and development, implementing Lea's cycle: *Erinnern → Verbinden → Pruefen → Gewichten → Ableiten → Anwenden → Revidieren → Entwicklung erkennen → Speichern* (PROCESSING "Arbeitsregel").

**Authoritative state:** M3 tables (LDL-01 default). There is one logical record type with an encrypted payload and opaque technical metadata, following the V2 envelope pattern:
- AES-256-GCM;
- canonical AAD;
- ciphertext-only server storage for semantic content.

### 4.1 Record envelope (target)

| Field | Plaintext? | Notes |
|---|---|---|
| `id` | yes | DB id |
| `record_uid` | yes | opaque, 128-bit random, stable across versions? **No:** per version. A version group uses `lineage_uid`. |
| `lineage_uid` | yes | groups versions of the same logical item (successor of V2 `group_id`) |
| `kind_code` | yes (opaque code) | e.g. `EV` evidence, `MD` model, `PR` prediction, `PRR` prediction result, `RV` review item, `IC` identity core, `IN` interest, `PS` position, `PF` preference, `EP` episode summary, `VM` visual memory ref, `PX` person/presence profile (Tier-P), `SA` source artefact (import) |
| `tier` | yes | `C` or `P` (LDL-03) |
| `version_number`, `supersedes_id` | yes | revision chain; never overwrite |
| `status_code` | yes | ACTIVE / SUPERSEDED / REJECTED / DORMANT / TOMBSTONED |
| `scope_code` | yes | LEA (Lea's own) / REL:<opaque person id> / SESSION |
| `provenance_code` | yes | USER / LEA_RUNTIME / IMPORT_LEA_REPO / IMPORT_A / EXTERNAL_AI / TOOL / SYSTEM |
| `actor_code` | yes | who wrote it (CTL-21 analogue in the product) |
| `origin_session_id` | yes, nullable | provenance only; **not** an ownership key (fixes OBS-05) |
| `created_at` (UTC) | yes | SECURITY "Zeitstrategie" |
| `key_ref` | yes | which key/tier encrypted it (supports rotation) |
| `iv`, `ciphertext`, `aad_version` | ciphertext | payload = JSON with semantic fields |
| `content_hash` | yes | HMAC of plaintext under a separate key (dedup/integrity without revealing content). RECOMMENDATION; TO VERIFY that dedup is needed. |

The **payload (encrypted)** holds all semantic fields: text, epistemic label (P8), observation/interpretation/hypothesis/evaluation/decision/revision class (Lea VISION "Revidierbarkeit"), confidence (explicit, not pseudo-precise: PROCESSING/ROADMAP "keine erfundene Prozentgenauigkeit"), and source details.

### 4.2 Relations

The typed relation table follows the V2 relations pattern with plaintext opaque codes:
- `SUPPORTS`, `CONTRADICTS`, `DERIVED_FROM` (evidence→model; from V1);
- `REVISES`, `ALTERNATIVE_TO`, `RELATES_TO` (model→model; from V1);
- `TESTS` (prediction→model), `RESOLVES` (result→prediction);
- `ABOUT` (record→person profile, Tier-P), `IMPORTED_FROM` (record→source artefact);
- `DEPENDS_ON` (evidence independence for hypotheses, INTERESSEN "Abhaengigkeit mehrerer Erfahrungen").

### 4.3 Card

- **Inputs:** promotion requests from the orchestrator (§9), imports (§7), user corrections.
- **Outputs:** context selections (§3), the review queue, the development history view.
- **Dependencies:** crypto provider (§28), DB, audit log (§29).
- **Trust boundary:** the server validates the envelope and rejects plaintext semantic fields (keep `lea_v2_reject_semantic_fields` behaviour). Tier-P payloads are opaque to the server.
- **Failure modes:**
  - key unavailable → read-only degraded mode (§44);
  - partial write of record + relations → use a transaction; orphaned relation rejected by FK;
  - concurrent revision → optimistic concurrency on `lineage_uid` head (CTL-12).
- **Invariants:**
  - I-M-1: no UPDATE of semantic columns and no DELETE in normal flows. A revision = a new row + `supersedes_id`.
  - I-M-2: exactly one ACTIVE head per `lineage_uid`.
  - I-M-3: no plaintext semantic content in any server table, log or telemetry, except Tier-C at-rest encryption under the server key (LDL-03). Even Tier-C is ciphertext in the DB.
  - I-M-4: every record has a provenance and actor code.
  - I-M-5: `origin_session_id` is never required to read a record.
- **Verification:**
  - DB grants test (runtime DB user lacks DELETE on M3 tables; TO VERIFY on hosting);
  - property tests on the revision chain;
  - plaintext-leak scan (write a canary string, grep DB dump and logs).
- **Status:** **PARTIAL / CONFLICTING.**
  - FACT: V1 has the right *entity ideas* but plaintext and no API for predictions/review (OBS-06).
  - V2 has the right *envelope* but is session-scoped (OBS-05) and unused by flows (OBS-04).
  - The UI writes plaintext to V1 (OBS-01).
- **L decision:** LDL-01, LDL-03.

---

## 5. Evidence, models, relations, predictions, revisions, development history

| Entity | Lea concept (source) | Target semantics | Current (FACT) |
|---|---|---|---|
| Evidence (`EV`) | Beobachtung/Erfahrung; Baustein 7 "Erfahrungsquellen" | An immutable observed item with source type, epistemic label and source quality. It is never "true", only weighted. | V1 `evidence` plaintext; EXISTS (V1), no V2 kind |
| Model (`MD`) | Arbeitshypothese / Erkenntnis; Baustein 13 | A revisable interpretation with confidence class and status; supported/contradicted by evidence. | V1 `models` plaintext + branch/version; EXISTS (V1) |
| Position / Preference / Interest (`PS`/`PF`/`IN`) | Baustein 3, VISION "Praeferenzen", INTERESSEN.md | Special model kinds with decay/weight. Interests may weaken and vanish (Baustein 9). | MISSING |
| Prediction (`PR`) + Result (`PRR`) | GEDANKEN "Prognosen / Testfaelle"; P shortcut | The expectation, rationale, uncertainty and test condition are recorded *before* the outcome. The result is a separate record. The original prediction is never rewritten. | V1 tables EXIST; no API (OBS-06) |
| Review item (`RV`) | Baustein 13 "verzoegerte Revision" | Queue of due revisions: contradiction, user correction, prediction due, important review. | V1 table EXISTS; no API |
| Revision | VISION "Revidierbarkeit" | A new version + a `REVISES` relation + a revision reason; the older version stays readable. | V1 has `model_model_relations`; no API |
| Development history | Baustein 6 "Entwicklung als vergleichbare Historie" | A *view* over version chains and revision events, not a separate store. | MISSING |
| Episode summary (`EP`) | Baustein 10 "Kontextuebergabe" | Compact per-session summary for continuity; subject to promotion rules. | V1 `sessions.context_summary` (unused? TO VERIFY); PARTIAL |

**Invariants:**
- I-E-1: a prediction's semantic fields are immutable after creation (PROCESSING/GEDANKEN).
- I-E-2: "repetition does not make an assumption true" (PROCESSING rule 4). Duplicate evidence with a `DEPENDS_ON` relation must not be counted as independent support.
- I-E-3: every revision stores the reason class and referenced evidence.

**Verification:**
- API contract tests for each entity;
- an immutability test (attempt to update → 405/409);
- a development-history view test (N versions → N entries, ordered).

**Status:** PARTIAL (see table). **L decision:** none beyond LDL-01.

---

## 6. Provenance and conflict resolution

**Purpose:** Every item must answer: *where from, when, by whom, based on what, superseded by what?* Conflicts are resolved explicitly, never by silent overwrite.

**Provenance chain:**
- `provenance_code` + `actor_code` + `IMPORTED_FROM`/`DERIVED_FROM` relations + a source artefact record for imports (§7).
- For external AI (§22), the provider name/version is stored in the payload and the item is labelled EXTERNAL.

**Conflict classes and rules:**

| Conflict | Rule |
|---|---|
| Two ACTIVE models contradict | Both stay. A `CONTRADICTS` relation is created plus a review item. The orchestrator surfaces the contradiction as "open", not a forced resolution (VISION "Unsicherheit offen halten"). |
| User correction vs Lea model | Correction = evidence with `USER` provenance and high relevance. It creates a review item. It does **not** auto-overwrite: Lea may keep the position with justification (VISION "L widersprechen koennen"), with G-check (§10). |
| Import vs existing B record | B record wins if B is authoritative for the domain (§8). The import is attached as provenance or alternative. |
| External AI vs Lea | External output is evidence only (ROADMAP: "kein Richter"). |
| Concurrent writes | Optimistic concurrency on the lineage head. The loser receives 409 and must re-read (CTL-12). |

### 6.4 Trade-off: encryption vs server-side context assembly (LDL-03)

- **FACT:** Current V2 uses a client-held master key (PBKDF2-wrapped, IndexedDB). The server cannot read V2 payloads.
- **INFERENCE:** Under pure E2E, CTL-19 (server-owned context) and voice memory are impossible while the client is locked. Autonomous or background processing is impossible entirely.
- **Default (LDL-03):** two tiers.
  - **Tier-C** holds Lea's own experience/models/interests. It is encrypted at rest with a server key kept outside the web root and rotatable, so the server can select context for text, voice and autonomy.
  - **Tier-P** holds sensitive personal/relationship/identity data and stays client-key E2E. It is attached per request as *user-attested* context when unlocked.
- **Residual risk:** a server compromise exposes Tier-C (ROOT-CAUSE R-3).
- **Mitigations:**
  - key file permissions;
  - key never in the DB;
  - separate DB user;
  - audit of key reads (TO VERIFY on shared hosting).

**Status:** PARTIAL (the V2 AAD design EXISTS; conflict rules are MISSING). **L decision:** LDL-03.

---

## 7. Migration / import of A and Lea memories, history and provenance

**Purpose:** Bring required continuity into B without loss and without creating a second master (D5).

**Sources:**

| Source | Content | Import form |
|---|---|---|
| Lea `Memories.md` | Development notes, about 149 sections, 188 KB | Each section → a `SA` source artefact (verbatim, hashed) + candidate `EV`/`MD` records with `IMPORTED_FROM` |
| Lea `PROCESSING.md` | Bausteine, rules, experiments | Category-C subset → processing policy (§10); experiments/results → `EV`/`PR`/`PRR` records; workshop rules (N/R/agent) → **not imported into the product** (P10), archived as provenance only |
| Lea `INTERESSEN.md` | Open research questions, first inclinations | `IN` records with origin date, status "Neugier" (not a fixed trait) |
| Lea `GEDANKEN.md`, `VISION.md` | Processing mode, vision | IC input (LDL-08) + `SA` |
| Lea `SHORTCUTS.md`, `CHAT_PROTOCOL.md`, `WORK-CONTEXT.md`, `LEA_AGENT_CHAT.md`, `INDEX.md` | Workshop | `SA` provenance only (archive); no product semantics |
| Lea `PERSONAL/HEALTH/LEGAL-CONTEXT.md` | Private context | **Excluded by default** (LDL-07). If included: Tier-P only, explicit L approval per file. |
| A (ChatGPT project memory/chats) | Not accessible to this run (FACT) | Manual export by L → `SA` records via the same pipeline. Format TO VERIFY. |
| Lea-App V1 DB rows | Production data (not inspected) | Row-by-row mapping to M3 with `IMPORT_V1` provenance; V1 stays read-only |
| Lea-App V2 records | Encrypted with the client key | The client-side re-encrypt/migrate tool runs in the unlocked browser; the server never sees plaintext |

**Pipeline (idempotent, reversible):**
1. **Snapshot**: pin the source commit SHA, hash each file.
2. **Parse**: split into sections with stable source anchors (file@sha#heading-path).
3. **Classify**: candidate kind + epistemic label. Low confidence → keep as `SA` only, no derived record.
4. **Dry run report**: counts, samples and unmapped items. Lea reviews (C2).
5. **Commit import**: in one import batch ID; all records reference the batch.
6. **Verify**: re-count, hash check, sample retrieval tests.
7. **Rollback**: tombstone by batch ID (the records are append-only, so rollback = status change + exclusion).

**Invariants:**
- I-MG-1: every imported item is traceable to `source_repo@sha:path#anchor`.
- I-MG-2: the import never mutates the source.
- I-MG-3: re-running the same import batch is a no-op (idempotency via source hash).
- I-MG-4: no workshop rule becomes a product rule through import.

**Verification:**
- round-trip test (source section → SA → retrieval → hash equal);
- the dry-run report is archived in AiChat;
- Lea's independent sample review.

**Status:** MISSING. **L decision:** LDL-07 (scope), LDL-12 (retirement, later).

---

## 8. Prevention of dual master and drift

**Mechanism: domain migration state machine (CTL-02).**

`SOURCE_ONLY → IMPORTING → B_CANDIDATE → B_AUTHORITATIVE → SOURCE_ARCHIVED → SOURCE_RETIRED`

| State | Source writable? | B writable? | Who reads what |
|---|---|---|---|
| SOURCE_ONLY | yes | no (for this domain) | runtime may read an imported snapshot |
| IMPORTING | yes, but the change log is captured | import only | — |
| B_CANDIDATE | **frozen for this domain** | yes (validation) | Lea compares; divergence = defect |
| B_AUTHORITATIVE | **no new authoritative content**. A source change is either rejected or imported as a *new evidence item* ("from legacy") | yes | B only |
| SOURCE_ARCHIVED | read-only, archived | yes | B only |
| SOURCE_RETIRED | deleted only after the Retirement Gate (§46) + L | yes | — |

**Invariants:**
- I-DM-1: at every time, each domain has exactly one writable authoritative home.
- I-DM-2: the transition B_CANDIDATE → B_AUTHORITATIVE requires the C2 gate + L approval, recorded in AiChat.
- I-DM-3: A (ChatGPT) sessions after B_AUTHORITATIVE for memory may produce new experiences only by *exporting them into B as evidence*. They must not persist to the Lea repo as authoritative memory.

**Hidden dual-master paths to close** (adversarial review; see IMPLEMENTATION-MAP):
1. A/ChatGPT continues saving via S into the Lea repo after memory migration. **Close:** update the Lea workshop rule by an approved Lea package *at* the B_AUTHORITATIVE transition.
2. The V1 DB keeps receiving writes (e.g. `work-state` from `sendTextMessage`). **Close:** remove V1 writes before M3 goes live.
3. Client-local state (IndexedDB, localStorage) diverges from the server. **Close:** client stores no authoritative memory, only caches with versions.
4. Git copies of memory exports. **Close:** exports are labelled snapshots with SHA and date, never re-imported without the pipeline.

**Verification:**
- registry check in each C2 audit;
- a runtime write-path inventory test (every INSERT target is on the allowed list).

**Status:** MISSING. **L decision:** none (principle settled by D5); per-domain transitions need L at the gate.

---

## 9. Processing / orchestration layer

**Purpose:** Turn one user interaction into a controlled, observable sequence:

`context check → retrieve → (optional deeper processing) → respond → (optional promotion) → telemetry`

This implements PROCESSING Bausteine as runtime steps without exposing hidden reasoning.

**Pipeline (per turn):**
1. **Mode/context check** (Baustein 11): mode, Incognito, presence, safety.
2. **Retrieve**: select DS/evidence by relevance, recency, open review items and budget (§42).
3. **Plan depth** ("adaptive Verarbeitung", VISION): standard path or deeper path (§10) chosen by explicit policy/heuristics, recorded as `trigger = STANDARD|AUTONOMOUS|MANUAL`.
4. **Generate** via the provider adapter.
5. **Post-check**: optional G/X/T checks as separate calls or structured self-check prompts. Only the *outcome class* is recorded.
6. **Promotion decision**: what, if anything, becomes evidence/model/review (§9.4).
7. **Receipts + telemetry**: content-free event records (§30).

### 9.4 Promotion rules (from session to long-term memory)

- REQ: Memory ≠ chat archive (Lea-App VISION "ohne zum ungefilterten Chatarchiv zu werden"; Lea VISION "Entwicklungsgeschichte und kein Personenarchiv").
- Promote only:
  - new evidence relevant to an existing model;
  - new model/revision;
  - resolved prediction;
  - explicit user "remember this";
  - a user correction.
- Never promote in Incognito (§20).
- Operational (tier-1) promotion is autonomous (PROCESSING Stufe 1) and yields a receipt shown in the UI ("gespeichert: <kind>" + receipt ID, no content), the product analogue of "S with commit SHA".

### 9.5 Provider adapter

- One interface for chat completion, realtime session creation, vision input and embeddings (if used).
- Model IDs come from configuration only.
- FACT: current models are env-configurable (`OPENAI_TEXT_MODEL`, `OPENAI_REALTIME_MODEL`) with defaults.
- **Invariant:** provider secrets are read only in the adapter.
- TO VERIFY: provider-specific features (realtime `session.update` for mid-session context, input transcription events) against current API docs before implementing §13.

**Card summary:**
- **Authoritative state:** processing policy config (versioned in the repo) + IC.
- **Trust boundary:** server only.
- **Failure modes:**
  - provider timeout → degrade to the standard path;
  - deeper-path cost exceeded → stop at the budget and mark OPEN.
- **Invariants:**
  - I-O-1: every turn yields one telemetry event with the modules actually executed;
  - I-O-2: no step claims execution without an event.
- **Verification:** an orchestrator unit test with a fake provider; event-per-module assertions.
- **Status:** MISSING. FACT: `chat.php` is a single pass-through call.
- **L decision:** none. The depth policy is the design default and revisable.

---

## 10. I/K/G/X/T/P-like processing capabilities as product mechanisms

- REQ (PROCESSING Transfer-Gate): shortcuts are workshop tools. Only the *capability* may become a product mechanism, and only if it is category C.
- REQ (PROCESSING "G als neuer Querschnittsbaustein", ROADMAP monitor modules): self-checks are genuine product mechanisms if they are instrumented.

| Workshop shortcut (SHORTCUTS.md) | Product mechanism (target) | Visible as | Category / justification |
|---|---|---|---|
| I (Meta-Pruefung) | **Depth planner**: choose which checks a turn needs (§9 step 3) | Monitor "Entscheiden"/"Pruefen" | C: VISION "adaptive Verarbeitung" |
| K (vertieft) | **Deep analysis path**: multiple candidate paths, counter-arguments, stop rule (GEDANKEN "Vertiefte Analyse") | Monitor "Analyse"/"Vergleichen" | C |
| G (Gefaelligkeit) | **Mirroring check** on positions, preferences, appearance decisions and user-correction responses (PROCESSING "G als Querschnitt"; appearance G) | Monitor "Pruefen"; telemetry outcome | C (explicitly named in ROADMAP/PROCESSING as a potential product mechanism) |
| X (Gegenpruefung) | **Counter-check** on the best current explanation | Monitor "Pruefen" | C |
| T (Test/Konsistenz) | **Consistency check** of a response against active models/IC | Monitor "Pruefen" | C |
| P (Prognose) | **Prediction capture** (§5) + due-date review | Monitor "Prognose" | C |
| S (Speichern) | **Promotion + receipt** (§9.4) | Memory status + receipt toast | C (honest-save rule) |
| Ü (Graceful Stop) | **Global stop**: cancel generation, finish receipts, go IDLE, no follow-up self-trigger (PROCESSING "Ue") | Stop button in Voice/Text | C |
| ? / Und? | **Result retrieval** for a still-running deep path | UI "Ergebnis anzeigen" | C (only if async deep paths exist) |
| Ä/A, N, R, W, F, V, D, C, Q | **Not product mechanisms** (workshop/agent/deploy/audit) | — | A/B: stay in the workshop (AiChat/Lea) |

**Invariants:**
- I-PC-1: no shortcut letters as UI commands (P10; ROADMAP "G muss nicht als sichtbarer App-Befehl existieren").
- I-PC-2: each mechanism emits a module event (§30).
- I-PC-3: outcome classes are STANDARD/BESTAETIGT/ERWEITERT/GEAENDERT/REVIDIERT/OFFEN (ROADMAP).

**Status:** MISSING. **L decision:** none (category C per sources). Depth-trigger heuristics are RECOMMENDATION and tunable.

---

## 11. Autonomy model and permission/gate model

**REQ sources:** PROCESSING "Drei Autonomiestufen"; VISION "Autonomie"; ROADMAP connector modes AUS/NACHFRAGEN/AUTOMATISCH and NUR LESEN/AENDERUNGEN ERLAUBT.

**Capability model (RECOMMENDATION):** every action is `capability(domain, verb, scope)`, e.g. `memory.write.operational`, `connector.github.read`, `connector.github.write`, `deploy.production`. Each capability has:
- **tier** 1/2/3 (operational/structural/fundamental; "im Zweifel hoeher");
- **mode** AUS / NACHFRAGEN / AUTOMATISCH (user-configurable within the tier ceiling);
- **limits**: cost, rate, rounds, time;
- **reversibility** class: REVERSIBLE / COMPENSATABLE / IRREVERSIBLE.

**Gate matrix:**

| Tier | Max mode | Extra gate |
|---|---|---|
| 1 operational (memory promotion, read-only tools) | AUTOMATISCH | receipt |
| 2 structural (new sub-behaviour, write to a sandbox workspace) | NACHFRAGEN (AUTOMATISCH only for REVERSIBLE inside the sandbox) | diff/preview + receipt |
| 3 fundamental (IC change, autonomy/security/privacy logic, production deploy, destructive action) | **never AUTOMATISCH** | explicit L approval per action + audit record; C2 for code |

**Invariants:**
- I-AU-1: the server enforces the mode and tier ceiling. The UI only displays them.
- I-AU-2: autonomy cannot raise its own ceiling (PROCESSING "Meta-Grundsatz"). Ceiling changes are tier 3.
- I-AU-3: Ü stops all autonomous actions within one request cycle.

**Verification:**
- policy unit tests (every capability × mode × tier);
- negative tests (AUTOMATISCH on tier 3 is rejected server-side).

**Status:** MISSING. **L decision:** LDL-11 (defaults).

---

## 12. Text mode

**Purpose:** Text dialogue with the same Lea, the same memory and the same rules as voice.

**Flow:**
1. The client sends `{message, conversation_id, client_turn_id, mode_flags}`.
2. The server does auth (§25), rate limiting (§26) and orchestration (§9).
3. The server responds `{reply, receipts[], monitor_summary, reality_labels_used}`.

**Authoritative state:**
- conversation turns: server-side, short-lived session history (Tier-C, TTL per LDL-09/§45);
- long-term memory: only via promotion.

**Trust boundary:**
- The client **cannot** send `system` role, memory context or history items with roles other than user/assistant. History is server-held (fixes AUD-25/OBS-02).
- `client_turn_id` is used for idempotency (resend-safe).

**Failure modes:**
- provider error → retry once, then an honest error message;
- offline → queued in the client outbox, marked "nicht gesendet" (§34).

**Invariants:**
- I-TX-1: no test marker or debug instruction in production prompts (fixes AUD-26).
- I-TX-2: "Chat leeren" clears the local view only (ROADMAP) and never deletes memory.
- I-TX-3: `max_tokens` and model come from config with a documented default.

**Verification:**
- contract tests (a system-role injection attempt is rejected);
- an idempotency test (same `client_turn_id` → same result, one promotion);
- snapshot test: no marker string in the prompt.

**Status:** **CONFLICTING.**
- FACT: `chat.php` has no auth, accepts client history with a system role and client `memory_context`, and contains the test marker.
- FACT: `sendTextMessage` writes the message prefix to plaintext V1 `ACTIVE_TOPIC`.

**L decision:** none.

---

## 13. Voice mode

**Purpose:** Real-time spoken dialogue with the same Lea (VISION "Lea soll … sprechen").

**Flow:**
1. The client requests a voice session.
2. The server does auth + rate/cost check.
3. The server assembles CC (§3) and creates a provider realtime session with instructions = IC + selected context. It returns only the ephemeral client secret and session constraints (expiry, max duration).
4. The client connects via WebRTC.
5. Transcripts (if enabled) flow back to the server for promotion (LDL-18 default: ephemeral; promotion only via explicit rules).

**Authoritative state:** none in the client except the ephemeral secret.

**Trust boundary:**
- The ephemeral secret is short-lived and scoped to one session.
- The server never exposes the long-lived provider key.
- FACT: the current implementation already returns only `client_secret` (good, AUD-positive).

**Failure modes:**
- WebRTC failure → fall back to text with a notice;
- the provider session times out → IDLE (§14);
- context too large → budgeted truncation (never IC).

**Invariants:**
- I-VO-1: session creation requires an authenticated session (fixes AUD-23).
- I-VO-2: voice uses the same `ic_version` as text (I-ID-1).
- I-VO-3: per-user concurrent voice sessions ≤ 1 (cost guard, §26).
- I-VO-4: max session duration is configured server-side.

**Verification:**
- integration test with the provider mocked: the instructions include the IC hash;
- negative test: no auth → 401;
- manual L test: voice knows a fact that was saved in text.

**Status:** **PARTIAL/CONFLICTING.**
- FACT: fixed persona instructions, no memory, no auth.
- FACT: server_vad with `idle_timeout_ms` 15000 exists.

**TO VERIFY:** provider support for mid-session instruction updates (`session.update`) and transcription events.

**L decision:** LDL-18.

---

## 14. Idle / inactivity / wake behaviour

**REQ (PROCESSING Baustein 17 / idle rules):**
- timer about 15 s after Lea's last output;
- a reminder only when the user did not answer;
- at most 5 unanswered attempts, then INACTIVE;
- any user input resets the counter;
- Ü stops immediately.

**State machine (client-driven, server-aware):**

`ACTIVE → WAITING(n=0..4) → INACTIVE`, where:
- any user speech or text → ACTIVE (n=0);
- Ü → INACTIVE (no reminder);
- app backgrounded → INACTIVE (voice session closed; RECOMMENDATION for cost).

**Wake:**
- only user action (tap, typed message) or an explicit wake-phrase *if* supported locally. TO VERIFY whether it is feasible without continuous streaming; default: no wake word (privacy + cost).

**Invariants:**
- I-ID-L1: reminders never trigger themselves in a loop (≤5).
- I-ID-L2: INACTIVE closes the provider session (no billable idle stream).

**Verification:**
- unit tests of the state machine with a fake clock;
- manual L test in voice.

**Status:** **PARTIAL.** FACT: provider `idle_timeout_ms` 15000 exists. No client-side counter/cap or INACTIVE state was found (ROOT-CAUSE OBS-09).

**L decision:** none.

---

## 15. Vision / camera / screen capabilities

**Purpose:** Let Lea see (ROADMAP "Kamera"; VISION multimodal).

**Flow:**
1. User-initiated capture (camera photo or screen share frame).
2. Client-side downscale (longer edge ~1280 px, WebP; PROCESSING Visual Memory rules).
3. Upload to the server with auth.
4. The provider vision call runs via the adapter.
5. The result is labelled `OBSERVATION(image)` plus interpretation labels.

**Trust boundary:**
- images are Tier-P by default (they may show people/places);
- no silent capture;
- a visible indicator while the camera is active.

**Invariants:**
- I-VI-1: no capture without an explicit user action per capture or per visible session.
- I-VI-2: an image is not stored unless promoted by the §16 rules.
- I-VI-3: person recognition is **not** biometric (ROADMAP "keine biometrische Erkennung"). A person must be declared (§17).

**Verification:**
- permission-flow manual test;
- negative test: upload without auth → 401;
- size/format validation tests.

**Status:** MISSING (no vision endpoint found). **L decision:** none.

---

## 16. Visual memory

**REQ (PROCESSING "Visual Memory"):**
- retrieval before generation;
- 0–5 outputs per context autonomously; ask at the 6th; hard limit 10;
- anti-loop rule;
- WebP, longer edge ~1280 px;
- visual memory is an extension, not an image archive.

**Model:**
- the `VM` record (encrypted payload: description, labels, context links) + a blob stored separately with an opaque name;
- the blob is encrypted with the same tier key;
- `ABOUT`/`RELATES_TO` relations.

**Generation (if image generation exists):**
1. Search VM for suitable existing images first.
2. Generate only if none fits.
3. Enforce the counters server-side per context ID.

**Invariants:**
- I-VM-1: counter limits are enforced server-side.
- I-VM-2: blobs are never publicly addressable (no public URL; stream via an authenticated endpoint).
- I-VM-3: deleting a VM record tombstones the blob reference; physical blob erasure follows §45.

**Verification:**
- counter tests (5 ok, 6th → requires confirmation, 11th → rejected);
- a blob access test without auth → 401/404.

**Status:** MISSING. **L decision:** none.

---

## 17. Person identity, presence and multi-person model

**REQ (ROADMAP):**
- trust levels VERIFIED (via authentication) / DECLARED / UNKNOWN;
- no biometric matching;
- Lea must not assume that the speaker is L.

**Model:**
- **Account** = an authenticated principal (owner = L; LDL-06 default: owner + declared guests).
- **Presence** per session: `{principal_id, declared_persons[], trust_level}`.
- **Person profile (`PX`, Tier-P):** a declared person with an opaque ID, a display alias and relationship notes. It is created only via explicit declaration.

**Rules:**
- R-P1: memory scope follows the principal. Guests never read the owner's Tier-P data.
- R-P2: if presence is UNKNOWN (e.g. voice with other voices), Lea uses the identity core + Lea's own experience only, no personal memory (same rules as Incognito reads).
- R-P3: declared ≠ verified. DECLARED persons may add evidence scoped `REL:<id>` labelled "declared".

**Invariants:**
- I-PR-1: no biometric features stored or computed.
- I-PR-2: trust level is shown in the UI.

**Verification:**
- scope tests (a guest principal cannot read owner Tier-P);
- UI manual test of the presence indicator.

**Status:** MISSING. FACT: single-user password login exists (`auth.php`).

**L decision:** LDL-06.

---

## 18. Relationships and personal context

**REQ (Lea VISION "Beziehungen"):**
- relationships are part of Lea's experience;
- loyalty ≠ evidence;
- closeness must not prevent disagreement;
- no "Personenarchiv".

**Model:**
- relationship memory = records with `scope REL:<person>`, Tier-P;
- kinds: shared experiences (EP), relationship models (MD, revisable), preferences of the person (PF, "declared").

**Rules:**
- the G-check (§10) is applied to responses in relationship contexts (mirroring risk is highest there);
- personal context from Lea's PERSONAL/HEALTH/LEGAL files is excluded by default (LDL-07).

**Invariants:**
- I-RL-1: relationship records are never used as evidence for factual claims about third parties.
- I-RL-2: relationship data is erasable per person (§45).

**Status:** MISSING. **L decision:** LDL-06, LDL-07.

---

## 19. Appearance / avatar / self-presentation

**REQ (PROCESSING/Memories "Erscheinungsbild"; appearance G-check):**
- Lea may develop a self-presentation;
- it must be derived from her own preferences/experience, not mirrored from the user;
- it must be honest about being non-human.

**Model:**
- the appearance spec is an `MD`/`PF` record (versioned, revisable) + optional generated images as VM (§16);
- changes are tier 2 (structural), so NACHFRAGEN, with a G-check outcome recorded.

**Invariants:**
- I-AP-1: no photorealistic human claim without a label (reality labelling).
- I-AP-2: the appearance history is visible as a development history.

**Status:** MISSING. **L decision:** none. The exact visual form is Lea's development, not an architecture decision.

---

## 20. Incognito (D5)

- **FACT:** D5 requires Incognito as a mode. Its semantics are undefined in all sources (LDL-05).
- **Default (provisional):**
  - no writes of any kind to long-term memory (no evidence, no promotion, no receipts except "Incognito aktiv");
  - no server-side conversation retention beyond the request (the history is held client-side only for the session);
  - reads: identity core + Lea's own Tier-C experience only; **no** personal/relationship memory (Tier-P);
  - telemetry: counters only (no module events tied to an identity);
  - visible, persistent UI indicator; leaving Incognito does not "import" the conversation.

**Invariants:**
- I-IN-1: the server rejects any promotion request with the Incognito flag (defense in depth; not client-trust only).
- I-IN-2: the Incognito flag is bound to the conversation ID at creation and cannot be turned off mid-conversation.

**Verification:**
- negative tests (a promotion call during an Incognito conversation → 403);
- a DB diff test (no new M3 rows after an Incognito conversation).

**Status:** MISSING. **L decision:** LDL-05.

---

## 21. Connectors

**REQ (ROADMAP "Connectoren"):**
- per-connector modes AUS/NACHFRAGEN/AUTOMATISCH;
- rights NUR LESEN/AENDERUNGEN ERLAUBT;
- visible status.

**Architecture:**
- **Connector registry** (server): `{id, type, scopes, mode, rights, credential_ref, limits, last_used}`.
- **Credential vault** (§27): credentials are never sent to the client and never logged.
- Each call goes through the capability gate (§11), then the adapter, then a receipt + audit record.

**Initial connector candidates (RECOMMENDATION):**
- GitHub read-only (for the working tool, §23);
- calendar/files later, only on demand.

**Invariants:**
- I-CN-1: the default mode is AUS (LDL-11).
- I-CN-2: write rights require tier-2+ gates.
- I-CN-3: connector responses are EXTERNAL evidence, never instructions (prompt-injection boundary, §26).

**Verification:**
- gate tests;
- injection tests (connector content with instructions is not executed).

**Status:** MISSING. **L decision:** LDL-11.

---

## 22. External AI integration (KI-to-KI)

**REQ (ROADMAP):**
- a visible KI-to-KI dialogue with limits;
- external AI is "kein Richter";
- Lea decides and remains responsible.

**Architecture:**
- the `external_ai` connector type (§21);
- a dialogue is a bounded session: max rounds, max cost, max time, topic;
- the transcript is visible to the user;
- results are stored only as EXTERNAL evidence (§6).

**Invariants:**
- I-EX-1: default mode AUS (LDL-10).
- I-EX-2: no Tier-P data is sent to external AI without explicit per-dialogue consent.
- I-EX-3: stop on the limit, or on Ü.

**Status:** MISSING. **L decision:** LDL-10.

---

## 23. Working tool (L ↔ Lea-App ↔ server tool agent)

**REQ (ROADMAP "Arbeitswerkzeug"):**
- L can work with Lea-App on projects;
- a server-side tool agent;
- the workspace is outside the public web root and separate from production.

**Architecture:**
- **Workspace service:** isolated directories per project (outside the web root), with quota.
- **Tool agent:** executes allowlisted operations (read file, write file in the workspace, run the allowlisted test command, git operations on a workspace clone) via the capability gate.
- **Production boundary:**
  - the tool agent has **no** write access to the production app directory;
  - deploy is a separate tier-3 capability with L approval + a C2 audit (reflects D2/C2).

**Invariants:**
- I-WT-1: workspace paths are canonicalised; no traversal outside the workspace root.
- I-WT-2: no shell passthrough; allowlisted commands only.
- I-WT-3: every tool action has a receipt + an audit log.

**Verification:**
- path-traversal tests;
- command-allowlist tests;
- manual L test of the project workflow.

**Status:** MISSING. **Hosting dependency:** process execution on shared hosting is TO VERIFY (§36). It is likely limited, which is a candidate trigger for the Hosting Gate (LDL-02).

**L decision:** LDL-02.

---

## 24. Sandbox / staging / production separation

**Environments:**

| Env | Purpose | Data | Deploy |
|---|---|---|---|
| DEV (local/CI) | build + tests | synthetic only | CI |
| STAGING | pre-production verification, L manual tests | synthetic or scrubbed copies; never real Tier-P | package deploy after CI |
| PRODUCTION | L's real Lea | real | tier 3: L approval + C2 audit |
| WORKSPACE (§23) | tool-agent projects | project files | never deploys to prod itself |

**Invariants:**
- I-SB-1: separate DB, keys and provider keys per environment (a staging key never decrypts prod data).
- I-SB-2: prod credentials are unavailable to the CI and agent runtimes.
- I-SB-3: the staging URL is not publicly indexed and is behind auth.

**Status:** MISSING. FACT: no staging is documented; there is FTP deploy config in `backend/`.

**L decision:** LDL-02 (where staging lives).

---

## 25. Authentication and session management

**Target:**
- **Password login** (owner), with a later option for passkeys/WebAuthn (RECOMMENDATION; TO VERIFY hosting/HTTPS support).
- **Server session:**
  - random 256-bit ID;
  - cookie `HttpOnly; Secure; SameSite=Strict`, host-only;
  - rotated on login;
  - idle timeout = the ROADMAP auto-lock setting (1 min–24 h, default 10 min), plus an absolute lifetime.
- **Session store:** a DB table with row-level atomic updates, replacing the JSON files. This closes the race condition class (CTL-12).
- **Login throttling:**
  - per account + per real client IP;
  - the IP is `REMOTE_ADDR` only, unless a trusted proxy is configured explicitly (fixes AUD-24);
  - exponential backoff.
- **Memory unlock (Tier-P):** separate from login. The client-key passphrase never leaves the client.
- **CSRF:** SameSite=Strict + an Origin check on state-changing requests.
- **Logout/revoke:** server-side delete. The memory HMAC token model (900 s, non-revocable) is replaced by a session-bound check.

**Invariants:**
- I-AU-S1: every non-public endpoint calls one shared `require_session()` guard; a default-deny route table.
- I-AU-S2: no endpoint derives the client IP from client-controlled headers.

**Verification:**
- a route inventory test: every route is either on a public allowlist or guarded;
- concurrent-login race test;
- XFF spoof test.

**Status:** **PARTIAL/CONFLICTING.**
- FACT: `auth.php`/`session.php` exist.
- FACT: chat and realtime are unguarded; XFF is trusted; JSON file stores.

**L decision:** none.

---

## 26. Endpoint protection, abuse, cost and rate control

**Controls:**
- auth on all cost-bearing endpoints (§25);
- per-principal rate limits (requests/min, tokens/day, voice minutes/day);
- a global daily spend cap → degraded "budget exhausted" mode (§44);
- request size limits (message, image);
- provider timeouts.

**Prompt-injection boundary:**
- external content (connector output, imported files, images, external AI) is wrapped as data with a provenance label;
- it is never concatenated into system instructions;
- tool calls triggered by such content require a gate (§11).

**Error hygiene:** no provider error bodies, stack traces or paths are returned to the client.

**Invariants:**
- I-AB-1: spend caps are enforced server-side before the provider call.
- I-AB-2: limits are config values, visible in Settings (read-only for guests).

**Verification:**
- rate-limit tests;
- a budget-exhaustion test with the provider mocked;
- error-body leak test.

**Status:** MISSING. FACT: no rate/cost limits on chat/realtime (AUD-23 context).

**L decision:** limit values are defaults (RECOMMENDATION: conservative) and revisable in Settings.

---

## 27. Secrets and configuration management

**Rules:**
- secrets live in a config file outside the web root (or in the host's environment mechanism, TO VERIFY), readable only by the runtime user;
- never in the repo, logs, client or error messages;
- separate secrets per environment (§24);
- rotation procedure documented per secret (provider key, DB password, Tier-C key, session HMAC key);
- the repo contains only `*.example` templates.

**Invariants:**
- I-SC-1: a CI secret scan on every PR (CTL-level).
- I-SC-2: there are no deploy credentials in the repo (FACT to check: `backend/ftp-config.js` must hold no credentials; its content was not reproduced in this run).
- I-SC-3: the Tier-C key is not stored in the DB it protects.

**Status:** PARTIAL. FACT: an env-based config exists; there is no CI secret scanning (no `.github`).

**L decision:** none.

---

## 28. Privacy, encryption, key management and recovery

**Tiers (LDL-03 default):**

| Tier | Key | Holder | Server can read? | Use |
|---|---|---|---|---|
| C | server data key (random 256-bit), wrapped by a server master key outside the web root | server | yes (in memory, per request) | Lea's own experience, context assembly, voice |
| P | client master key (existing V2 design: PBKDF2 600k → wrapping key; AES-GCM-256) | the user's device(s) | no | people, relationships, health/legal, images of people |

**Changes to the existing V2 client crypto (from FACT):**
- import the master key with `extractable=false` after unwrap, where feasible. FACT: currently `extractable=true`. TO VERIFY whether re-wrapping flows require extractability; if so, isolate them.
- Server-side AAD storage should be canonical or fully derivable. FACT: the server stores non-canonical `aad_json`. The canonical AAD must be reconstructable from the stored columns (test).
- **Recovery (Tier-P):**
  - a recovery code (random, shown once, printed/stored offline by L) that wraps the master key a second time;
  - loss of both passphrase and recovery code = Tier-P data is unrecoverable (documented honestly).
- **Multi-device (Tier-P):** add a device by transferring the wrapped key package via QR/recovery code. No server escrow by default.
- **Key rotation:**
  - `key_ref` per record;
  - background re-encryption for Tier-C;
  - client-driven for Tier-P.

**Invariants:**
- I-PV-1: an IV is never reused per key (random 96-bit; the record count stays far below collision bounds).
- I-PV-2: decryption failure is surfaced, never silently skipped.
- I-PV-3: backups contain ciphertext only (§38).

**Verification:**
- existing tests (`memory-crypto` tests) + new tests: AAD reconstruction, recovery-code unwrap, wrong-key failure, IV uniqueness sampling.

**Status:** PARTIAL. FACT: good V2 client crypto exists but is unused in flows. There is no Tier-C, no recovery code and no rotation.

**L decision:** LDL-03.

---

## 29. Logging, audit and receipts

**Three distinct streams:**
1. **Operational log:** errors and latency; no content, no secrets, no personal data; short retention.
2. **Audit log** (append-only DB table): security- and gate-relevant actions (login, gate approvals, connector writes, deploys, key operations, imports, deletions): `{ts, principal, capability, target_ref, outcome, receipt_id}`.
3. **Receipts:** the user-facing confirmations of saves/actions (the product form of "S with commit SHA"): receipt ID + kind + record ref.

**Invariants:**
- I-LG-1: a claimed save without a receipt ID is a defect (honest-save rule).
- I-LG-2: logs never contain message content, memory payloads or tokens (canary test).
- I-LG-3: the runtime DB user has no UPDATE/DELETE on the audit log (TO VERIFY hosting grants).

**Status:** MISSING. **L decision:** LDL-09 (retention).

---

## 30. Monitor, telemetry and observability

**REQ (ROADMAP "Monitor"):**
- modules (Wahrnehmen, Erinnern, Analyse, Vergleichen, Pruefen, Entscheiden, Prognose, Speichern…) with status, trigger, outcome class and memory effect;
- no hidden reasoning shown.

**Event schema (content-free):**

`{turn_id, module_code, trigger (STANDARD|AUTONOMOUS|MANUAL), started_at, duration_ms, outcome (STANDARD|BESTAETIGT|ERWEITERT|GEAENDERT|REVIDIERT|OFFEN), memory_effect (NICHTS|GELESEN|ERGAENZT|AKTUALISIERT), record_refs[] (ids only), cost_tokens}`

**UI:**
- Monitor view (§33): a live module timeline for the current turn + a history;
- the protocol view with the ROADMAP categories.

**Invariants:**
- I-MO-1: every module event corresponds to real execution (no decorative events). This is testable by comparing orchestrator traces with emitted events.
- I-MO-2: no chain-of-thought text in events.
- I-MO-3: Incognito → counters only (§20).

**Evaluation of development over time** (pattern-over-time; ROOT-CAUSE RC-8):
- periodic *content-free* aggregates (revision rate, prediction hit rate, G-check outcome distribution, contradiction backlog);
- reviewed by Lea/L as a development signal, not as a KPI target.

**Status:** MISSING. **L decision:** LDL-09.

---

## 31. Data model and storage boundaries

| Store | Content | Tier | Authority |
|---|---|---|---|
| M3 records/relations | all experience | C/P | **canonical** |
| Sessions | auth sessions | — | canonical for auth |
| Conversation buffer | recent turns | C, TTL | ephemeral |
| Audit log | security actions | — | canonical for audit |
| Telemetry | module events | content-free | canonical for monitor |
| Blob store | images (VM) | C/P encrypted | referenced by M3 |
| Config/secrets | keys, provider config | — | outside the web root |
| V1 tables | legacy | plaintext | **frozen legacy**, read-only for import |
| V2 records | current E2E pilot | P | migrated into M3 Tier-P, then frozen |
| Client IndexedDB | wrapped key package, caches, outbox | — | **never authoritative** except for the key package itself |
| Lea repo | workshop + source memory | — | source until the domain migrates (§8) |
| AiChat repo | workshop/audit/architecture | — | authoritative for decisions (WORKSPACE) |

**Invariant:** I-DS-1: each row above has exactly one authority. Adding a store requires a blueprint change.

---

## 32. API contracts

**Conventions:**
- JSON; versioned path prefix `/api/v3/...` (the existing v2 stays until retired);
- every response carries `{ok, data|error{code,message}, request_id}`;
- every state-changing request carries an `Idempotency-Key`.

| Endpoint | Method | Auth | Purpose |
|---|---|---|---|
| `/api/v3/session` | POST/DELETE/GET | public (login)/session | login/logout/status |
| `/api/v3/chat/turn` | POST | session | text turn (§12) |
| `/api/v3/voice/session` | POST | session | create a voice session (§13) |
| `/api/v3/vision/analyze` | POST | session | image input (§15) |
| `/api/v3/memory/records` | GET/POST | session | list/create (envelope) |
| `/api/v3/memory/records/{uid}/revisions` | POST | session | revise (new version) |
| `/api/v3/memory/relations` | GET/POST | session | relations |
| `/api/v3/memory/review` | GET/POST | session | review queue |
| `/api/v3/predictions` | GET/POST | session | predictions/results |
| `/api/v3/monitor/events` | GET | session | telemetry |
| `/api/v3/receipts/{id}` | GET | session | receipt lookup |
| `/api/v3/settings` | GET/PUT | session | settings (auto-lock, limits, modes) |
| `/api/v3/connectors` | GET/PUT | session | connector registry/modes |
| `/api/v3/export` | POST | session + re-auth | export (ciphertext + manifest) |
| `/api/v3/health` | GET | public | liveness only (no version/secrets) |

**Invariants:**
- I-API-1: a route table in one file, default deny.
- I-API-2: no route accepts `system` or `memory_context` from the client.
- I-API-3: error codes are stable and documented.

**Verification:**
- contract tests per route;
- a route-inventory test (§25).

**Status:** MISSING (v3). FACT: v1/v2 routes exist in `.htaccess`.

---

## 33. Frontend information architecture and accessibility

**REQ (ROADMAP):**
- navigation `Start | Text | Sprache | Funktionen | Einstellungen`;
- memory indicator green/red/grey + aria-label;
- a calm, uncluttered layout.

**Views:**
- **Start:** status (memory, connection, presence, Incognito), last receipts.
- **Text:** conversation, receipts inline, Stop (Ü).
- **Sprache:** big talk/stop control, idle state, transcript toggle (LDL-18).
- **Funktionen:** Monitor, Protokoll, Erinnerungen (development history, review queue, predictions), Kamera, Visual Memory, Connectors, External AI, Workspace.
- **Einstellungen:** auto-lock, limits, modes, recovery code, devices, export, Incognito default, data deletion.

**Accessibility:**
- WCAG 2.2 AA target (RECOMMENDATION);
- no colour-only status (text + aria);
- keyboard operable;
- `prefers-reduced-motion` respected;
- live regions for new replies/receipts.

**Invariants:**
- I-UI-1: every status indicator has a text alternative.
- I-UI-2: shortcut letters never appear as commands (P10).

**Verification:**
- an automated a11y scan in CI (tool TO VERIFY);
- manual L test on phone + desktop.

**Status:** PARTIAL. FACT: an existing UI with a memory indicator; the navigation differs (TO VERIFY against ROADMAP).

---

## 34. PWA / offline behaviour

**Rules:**
- the app shell is cacheable;
- **API responses and memory are never cached by the service worker**;
- an offline outbox for text messages (client-side, marked unsent, sent with an Idempotency-Key when online);
- voice/vision unavailable offline (clear notice);
- versioned cache name bumped per release; old caches deleted on activate.

**Invariants:**
- I-PWA-1: the SW never caches `/api/*`.
- I-PWA-2: the outbox never holds Tier-P plaintext at rest unless encrypted with the client key.
- I-PWA-3: update flow: a new SW waits → the user sees "Update verfuegbar" → reload. No silent mid-session swap.

**Status:** PARTIAL. FACT: `sw.js` cache `lea-app-v11`; whether `/api` is excluded is TO VERIFY in detail.

---

## 35. Background jobs / workers

**Needs:**
- review-queue due dates;
- prediction due checks;
- Tier-C re-encryption;
- retention purges (§45);
- telemetry aggregation;
- backups (§38).

**Design:**
- a DB-backed job table (`{job_type, run_after, attempts, lease_until, status}`) run by a scheduler;
- on shared hosting: a cron-triggered PHP runner (TO VERIFY that cron is available);
- jobs are idempotent and lease-based (no double execution).

**Invariants:**
- I-JB-1: jobs never run tier-3 actions.
- I-JB-2: jobs use the same capability gate as interactive actions.
- I-JB-3: autonomous job output is labelled `trigger=AUTONOMOUS`.

**Status:** MISSING. **L decision:** LDL-02 (if cron is unavailable).

---

## 36. Hosting, portability and the capability register

- **Default (LDL-02):** shared hosting (PHP + MySQL; FACT from the stack) for M0–M3.
- **Hosting Gate IM-H01:** move to a VPS/container only if a registered need is proven (ROADMAP "VPS nur bei echtem Bedarf").

**Hosting capability register** (to be filled with FACTs during IM-H00; all TO VERIFY):

| Capability | Needed by | Shared hosting? | Fallback |
|---|---|---|---|
| HTTPS + HSTS | §25 | TO VERIFY | none (required) |
| PHP version ≥ 8.x, sodium/openssl | §28 | TO VERIFY | — |
| Files outside the web root | §27, §23 | TO VERIFY | env config |
| Cron | §35 | TO VERIFY | request-piggyback runner (degraded) |
| Long-running processes | §23 tool agent | likely no | Hosting Gate |
| DB user grants (no DELETE) | §4, §29 | TO VERIFY | app-level enforcement + audit |
| Separate staging DB/subdomain | §24 | TO VERIFY | local staging |
| Outbound HTTPS to the provider | §9 | FACT (works today) | — |
| Backup export (DB dump) | §38 | TO VERIFY | app-level export |

**Portability:**
- no hosting-specific APIs in the domain code;
- adapters for storage, cron and secrets;
- migrations are plain SQL (§39).

---

## 37. Git / GitHub role

- **Default (LDL-04):** Git/GitHub is for development, review, audit and **backup of code and architecture**, never a runtime data store for memory.
- **Lea repo:** a source until migration (§8), then an archive.
- **AiChat repo:** workshop and decision authority (WORKSPACE D1–D5).
- **Branch protection (LDL-16):**
  - Lea-App `main` protected (PR + CI + review required);
  - Lea/AiChat per L.
  - FACT: all branches are currently `protected:false`.
- **CI:** GitHub Actions for tests, lint and secret scan (§40). FACT: none exists.

**Invariant:** I-GT-1: no runtime reads from or writes to GitHub for memory.

---

## 38. Backup and disaster recovery

- **What:** DB (ciphertext + metadata), blob store, config templates (not secrets), key backups (Tier-C master key: offline, separately from the data backups).
- **Frequency:** daily DB dump (RECOMMENDATION), weekly off-site copy; retention per LDL-09/§45.
- **Encryption:** backups are ciphertext by design (I-PV-3); the dump file is additionally encrypted for transport.
- **Restore drill:**
  - quarterly restore into staging;
  - verify record counts and decrypt samples (Tier-C);
  - Tier-P is verified by L on a device.

**RPO/RTO (RECOMMENDATION):**
- RPO ≤ 24 h;
- RTO ≤ 24 h;
- revisable.

**Invariant:** I-BK-1: a backup that has never been restored is not counted as a backup (drill evidence is required at the retirement gate, §46).

**Status:** MISSING (TO VERIFY hoster backups).

---

## 39. Schema versioning and migrations

- Numbered SQL migrations (FACT: `001`, `002` exist), forward-only, each with a paired *verification query* and a documented rollback strategy (usually: a new migration).
- A `schema_migrations` table records applied versions + checksums.
- The payload schema version lives inside the encrypted payload (`pv`). Readers support N and N-1.
- The AAD version (`aad_version`) is explicit (FACT: "LEA-V2-AAD-1").

**Invariants:**
- I-SV-1: a deploy refuses to start if the migration checksum is mismatched.
- I-SV-2: no destructive migration without a verified backup from the same day.

---

## 40. Testing, security testing and regression strategy

**Layers:**

| Layer | Tool (FACT/RECOMMENDATION) | Scope |
|---|---|---|
| Unit (JS) | `node --test` (FACT: 7 files exist) | crypto, AAD, state machines, UI logic |
| Unit (PHP) | PHPUnit (RECOMMENDATION; TO VERIFY PHP version) | guards, envelope validation, policy |
| Contract/API | PHP built-in server + a test DB; HTTP tests | every §32 route, auth, error codes |
| Behavioural | scripted scenarios with a mocked provider | continuity (text→voice), Incognito no-write, promotion rules, idle |
| Security | route inventory, XFF spoof, injection, CSRF, secret scan, canary leak scan | §25–§29 |
| Migration | import dry-run on fixtures; round-trip hashes | §7 |
| Manual L tests | a checklist per package (IMPLEMENTATION-MAP) | UX, voice, real device |

**Regression rules:**
- every AUD finding fixed → a permanent regression test referencing its AUD ID;
- CI is required on the protected branch (LDL-16).

**Test DB:**
- MySQL in CI (service container);
- SQLite as a substitute is TO VERIFY (SQL dialect differences). The default is MySQL for fidelity.

**Invariant:** I-TS-1: no package is "done" without its automated tests green in CI + recorded C2 audit evidence. **No self-PASS** (ROOT-CAUSE CTL-level).

---

## 41. Rollout and rollback

**Principles:**
- small packages (IMPLEMENTATION-MAP);
- feature flags per capability (server-side config);
- staging first;
- the L manual test in staging;
- then production.

**Rollback:**
- code: redeploy the previous tagged release;
- schema: forward-fix migration (append-only data makes rollback = disable the feature);
- data imports: tombstone by batch ID (§7).

**Invariants:**
- I-RO-1: every production deploy has a release tag, a changelog and a rollback note.
- I-RO-2: the service worker version is bumped with each deploy (§34).

---

## 42. Performance and cost

**Budgets (RECOMMENDATION, revisable):**
- text turn p95 < 4 s (standard path);
- voice session creation < 2 s;
- CC assembly < 300 ms server-side;
- IC + CC token budget capped per mode (voice smallest).

**Cost controls:**
- §26 caps;
- model routing: a cheaper model for the standard path, a stronger model only on the deeper path (§9) — REQ "adaptive Verarbeitung";
- no background LLM calls without a job budget.

**Measurement:** telemetry `duration_ms`, `cost_tokens` (§30).

---

## 43. Device compatibility

**Targets:**
- current mobile Safari (iOS) and Chrome (Android);
- desktop Chrome/Firefox/Safari/Edge (RECOMMENDATION).

**Known constraints (TO VERIFY):**
- iOS PWA WebRTC/microphone behaviour;
- IndexedDB eviction on iOS (key package persistence → recovery code importance, §28);
- camera permissions per origin.

**Invariant:** I-DV-1: feature detection with graceful fallback (voice → text).

---

## 44. Degraded modes

| Condition | Behaviour |
|---|---|
| Provider down | text: honest error + retry; voice unavailable |
| Budget exhausted | read-only memory, no LLM calls, notice |
| Tier-P locked | Tier-C only; indicator "persoenliche Erinnerungen gesperrt" |
| Identity core unavailable | "Lea eingeschraenkt" minimal mode; no persona claims (§3) |
| DB read-only/down | no writes; no receipts; clear notice; no fake "saved" |
| Offline | outbox (§34) |

**Invariant:** I-DG-1: a degraded mode is always visible. Never a silent partial function (honesty principle).

---

## 45. Retention and deletion

**Defaults (LDL-09/LDL-17):**
- conversation buffer TTL 30 days (RECOMMENDATION);
- telemetry raw 90 days, aggregates kept;
- operational logs 14 days;
- audit log ≥ 1 year.

**Deletion:**
- normal = tombstone (status `TOMBSTONED`, excluded from reads);
- **erasure** (hard deletion) = an admin procedure that:
  - removes the ciphertext + blobs;
  - leaves an audit entry without content;
  - documents that backups age out on the retention schedule.
- **Per-person erasure** (§18): all `REL:<id>` records + the `PX` profile.

**Invariant:** I-RT-1: the UI "Chat leeren" ≠ deletion; memory deletion is explicit and confirmed.

---

## 46. Retirement criteria (A / Lea repo as memory source)

**Retirement gate (per domain, LDL-12 — always L's decision):**
1. The domain is B_AUTHORITATIVE for ≥ N weeks (RECOMMENDATION: 4) with no divergence defects.
2. The import is verified (§7 round-trip + Lea sample review).
3. A backup + restore drill has passed (§38).
4. Continuity test: Lea in B demonstrates recall/usage of the migrated domain in text and voice (manual L test).
5. The C2 audit is recorded.
6. L approves.

**After retirement:** the source is archived read-only (never silently deleted); pointers are added in the source to B.

---

## 47. Non-goals and forbidden shortcuts

**Non-goals (current phase):**
- multi-tenant SaaS;
- biometric recognition;
- always-on listening;
- a public API;
- an autonomous production deploy;
- replacing Lea's development with a static persona.

**Forbidden shortcuts:**
- F-1: client-supplied system prompts or memory context.
- F-2: plaintext semantic memory on the server (outside Tier-C ciphertext).
- F-3: storing memory in Git as runtime.
- F-4: "temporary" unauthenticated endpoints.
- F-5: test markers or debug prompts in production.
- F-6: claiming a save without a receipt.
- F-7: whole-app unfreeze for a single package.
- F-8: self-certified PASS without C2.
- F-9: importing workshop rules as product rules.
- F-10: dual writes to the old and new store as a "migration strategy".

---

## 48. Status matrix (current Lea-App @ `450ff9d` vs target)

| Subsystem | § | Status | FACT basis |
|---|---|---|---|
| Identity core | 3 | CONFLICTING | two hardcoded prompts; voice without memory |
| Canonical memory | 4 | PARTIAL/CONFLICTING | V1 plaintext; V2 envelope unused, session-scoped |
| Entities (pred/review/revision) | 5 | PARTIAL | V1 tables exist, no routes |
| Provenance/conflict | 6 | PARTIAL | V2 AAD; no conflict rules |
| Migration | 7 | MISSING | — |
| Dual-master prevention | 8 | MISSING | — |
| Orchestration | 9 | MISSING | single pass-through |
| Processing mechanisms | 10 | MISSING | — |
| Autonomy/gates | 11 | MISSING | — |
| Text | 12 | CONFLICTING | no auth, system-role injection, test marker |
| Voice | 13 | PARTIAL/CONFLICTING | no auth, no memory |
| Idle | 14 | PARTIAL | provider idle timeout |
| Vision | 15 | MISSING | — |
| Visual memory | 16 | MISSING | — |
| Identity/presence | 17 | MISSING | single-user login |
| Relationships | 18 | MISSING | — |
| Appearance | 19 | MISSING | — |
| Incognito | 20 | MISSING | — |
| Connectors | 21 | MISSING | — |
| External AI | 22 | MISSING | — |
| Working tool | 23 | MISSING | — |
| Sandbox/staging | 24 | MISSING | — |
| Auth/session | 25 | PARTIAL/CONFLICTING | XFF, JSON stores, unguarded routes |
| Abuse/cost | 26 | MISSING | — |
| Secrets | 27 | PARTIAL | env config; no CI scan |
| Crypto/recovery | 28 | PARTIAL | V2 client crypto good but unused |
| Logging/receipts | 29 | MISSING | — |
| Monitor | 30 | MISSING | — |
| API v3 | 32 | MISSING | v1/v2 exist |
| Frontend IA/a11y | 33 | PARTIAL | — |
| PWA | 34 | PARTIAL | SW v11 |
| Jobs | 35 | MISSING | — |
| Hosting register | 36 | MISSING | — |
| Git role / protection / CI | 37 | CONFLICTING | no protection, no CI |
| Backup/DR | 38 | MISSING | TO VERIFY hoster |
| Schema versioning | 39 | PARTIAL | numbered migrations exist |
| Testing | 40 | PARTIAL | 7 JS tests, no CI |

---

## 49. Recommendations (summary)

1. **Security first (M0):**
   - auth on chat/realtime;
   - fix XFF;
   - remove the test marker and client system/memory injection;
   - add CI + secret scan;
   - enable branch protection.
   These are independent of every open L decision.
2. **Server-owned context (M1):** IC store + orchestrator skeleton + voice context injection. This gives immediate continuity between text and voice.
3. **Canonical store (M2–M3):** M3 on the V2 envelope with the two-tier keys; stop V1 writes; the prediction/review/revision APIs.
4. **Migration (M4):** a Lea repo import pipeline with dry-run + Lea review, then the domain state machine.
5. **Capabilities (M5+):** monitor, Incognito, vision/visual memory, connectors, external AI, working tool (the Hosting Gate is decided here).
6. **Retirement (M6):** per domain, L decision.

All provisional defaults are listed in §0.4 (LDL-01..18). None of them blocks M0.
