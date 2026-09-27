# B-BLUEPRINT — Target Architecture for Independent Lea-App (B)

TASK: AGENT_TASK.md TASK_VERSION 3; **reconciled under TASK_VERSION 4** (C2 Blueprint Reconciliation) with WORKSPACE D6–D12 — see §51.
STATUS OF THIS DOCUMENT: C2 *Discovery* design output by Claude/Copilot, reconciled after Lea's independent C2 review. Recommendation, **not** an L decision, **not** accepted by Lea, **not** an implementation authorisation. Lea-App stays FROZEN (D3).

Companion artifacts:
- `ROOT-CAUSE.md`: why the rules below exist. Controls are `CTL-xx`.
- `IMPLEMENTATION-MAP.md`: how to build this. Packages are `IM-xx`.

Source refs (read-only):
- AiChat `main` `28f9fff` (original discovery); `6fb71ff` (WORKSPACE D1–D12, AGENT_TASK v4; reconciliation)
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
| **REQ** | Requirement derived from *settled* sources: WORKSPACE D1–D12, Lea VISION.md, Lea-App VISION.md/ROADMAP.md, Lea PROCESSING.md product-category-C items, SECURITY.md. The source is cited. |
| **RECOMMENDATION** | Claude/Copilot design choice that is not stated in the sources. Useful but not binding. |
| **L-DECISION-LATER (LDL-nn)** | A genuinely open L choice. It has a provisional default so that design can continue. After reconciliation only the items marked OPEN in §0.4 remain. |
| **SETTLED (Dn)** | Settled by an authoritative WORKSPACE decision; not to be re-asked. |
| **GOVERNANCE** | Ordinary technical/organisational governance or an admin action; not an architecture decision of L. |
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
- **L decision**: `none`, `SETTLED (Dn)` or an OPEN `LDL-nn`

### 0.3 Current vs target

Every card separates what exists today (Status with a FACT reference) from the target (REQ/RECOMMENDATION). Nothing here claims the target exists.

### 0.4 Decision register (reconciled, AGENT_TASK v4)

The original LDL-01..18 IDs are kept stable so that earlier references and Git history stay traceable. The "Original provisional default" column records Claude's first proposal; the "Reconciled status" column is authoritative for this document.

| ID | Topic | Original provisional default (Claude, v3) | Reconciled status |
|---|---|---|---|
| LDL-01 | Canonical memory store | New store "M3" built on the V2 envelope; V1 frozen legacy | **SETTLED (D11).** M3 is the working target concept: a *convergence/evolution* of V1 semantic concepts and V2 encrypted-envelope/privacy/append-only foundations. No destructive discard of proven V1/V2 mechanisms; V1/V2 stay migration sources until verified transfer. Exact schema/API = package-level C2 work (§4). |
| LDL-02 | Hosting | Shared hosting M0–M3, re-decide at the Hosting Gate | **SETTLED strategy** (ROADMAP "VPS nur bei echtem Bedarf"; D5/P11 portability): stay on shared hosting while sufficient, keep code portable, move only on a demonstrated need. The Hosting Gate IM-H01 records the evidence; a move itself is an ordinary D3-style change approval, not an open architecture choice. |
| LDL-03 | Key custody / encryption | Two tiers with fixed crypto parameters | **SETTLED (D12).** Layered protection by sensitivity and functional need (§6.4, §28). Concrete algorithms, KDF, key hierarchy, rotation, device transfer and recovery = verified security packages; the parameters in this document are RECOMMENDATIONS, not frozen requirements. |
| LDL-04 | Role of Git/GitHub | Dev/audit/backup only; never runtime | **SETTLED (D5)** for the architecture principle: no unnecessary permanent runtime dependence on GitHub; Git = development, versioning, audit, rollback/backup of code. Git rollback does not replace data recovery (D10). |
| LDL-05 | Incognito semantics | No write; reads identity core only | **SETTLED (D6)** — the original default was **wrong** (it restricted reads). Incognito restricts WRITE/LEARN, not what pre-existing Lea may KNOW/READ (§20). |
| LDL-06 | People / accounts | Owner + declared guests, no guest accounts | **SETTLED (D8).** L = authenticated owner; known persons as relationship/context identities without accounts; known person ≠ authenticated user; extra accounts later optional (§17). |
| LDL-07 | Migration scope | File-based; PERSONAL/HEALTH/LEGAL excluded by default | **SETTLED (D7)** — the original file-based exclusion was **wrong**. Experience-oriented, item-level, no blind whole-file import, provenance and status preserved, sensitive items protected (§7). |
| LDL-08 | Initial identity core content | Derive from VISION/PROCESSING (category C), Lea review, L approval | **OPEN (content approval).** The *mechanism* is settled (§3). The initial IC v1 *content* needs Lea review and L approval (IM-I02). |
| LDL-09 | Monitor / telemetry | Monitor OFF by default; 90-day raw retention | **Monitor visibility SETTLED (D9)** — the original "OFF" default was **wrong**: visible/enabled by default during research/development; real instrumented events only; visibility and persistence are separate controls. **Telemetry persistence retention: OPEN** — the 90-day value is *not* decided and is withdrawn as a default; a retention proposal needs a technical/privacy justification in IM-O03 before L decides (§30). |
| LDL-10 | External AI | Per-provider mode AUS by default | **OPEN (narrowed).** The capability model is settled by D10/D12 (risk-based; sensitive classes narrower). Still open for L: whether Lea may consult third-party AI *without* per-dialogue confirmation and which protection classes may ever leave B to a third party (§22). |
| LDL-11 | Connector autonomy | All write-capable connectors AUS/NUR LESEN | **SETTLED (D10)** — the original blanket write-disable was **wrong**. Risk/impact/reversibility/recoverability/capability-based (§11). Concrete per-connector grants are ordinary L settings, not architecture decisions. |
| LDL-12 | Retirement of A / Lea repo | Always an explicit L decision | **SETTLED (D5)** as a gate: retirement only after demonstrated migration, recovery requirements and explicit L approval (§46, IM-X02). The later approval act is a gate, not an open design question. |
| LDL-13 | `web_app1` | Out of scope | **SETTLED (D5):** out of scope; non-authoritative. |
| LDL-14 | Authority registry location | `AUTHORITY-REGISTRY.md` in AiChat | **GOVERNANCE (D1).** Organisational; AiChat is the work authority. RECOMMENDATION unchanged (IM-G02). |
| LDL-15 | Workshop files in Lea-App | Minimal AGENTS.md pointer | **SETTLED (D1/D3):** AiChat is the work authority; any Lea-App file change is an ordinary D3 change package. |
| LDL-16 | Branch protection | Enable | **GOVERNANCE recommendation / admin action** (IM-G01); needs L/admin rights, not an architecture decision. |
| LDL-17 | Hard deletion | Tombstones + admin erasure procedure | **Technical RECOMMENDATION** (§45). A user-facing irreversible erasure policy would need L only if and when such a UI function is proposed (then as a D3/D10 change approval). |
| LDL-18 | Voice transcripts | Ephemeral | **Derivable (not an L decision):** transcripts are conversation content and follow the same session/promotion rules as text (§9.4, §13); Incognito per D6. Storing full raw transcripts is not a requirement; RECOMMENDATION: session-local only. |

**Genuinely unresolved L decisions after reconciliation:**
1. **LDL-08:** approval of the initial identity-core content (Lea review + L).
2. **LDL-09 (retention part only):** the telemetry persistence retention, once a justified proposal exists.
3. **LDL-10 (narrowed):** the external-AI autonomy and data-sharing policy.

None of them blocks M0.

**Rule for later agents:** a package that depends on an OPEN item may start only after its IM package's L-gate records the decision (`IMPLEMENTATION-MAP.md` §7). A package may proceed with the provisional default **only** if it is marked reversible. SETTLED and GOVERNANCE items must not be re-asked.

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
| Identity/presence data (known persons, D8) | none (FACT: MISSING) | B M3, protection class P (§28) | new |
| Visual library/appearance | Lea-App `frontend/images/` (appearance), Lea chat history | B visual library (§16/§19) | IMPORTING → B_AUTHORITATIVE |
| Audit findings | AiChat audit artefacts | AiChat | fixed |

**Invariant H-1:** For any domain, at most one row may have state `*_AUTHORITATIVE`. A write to a non-authoritative copy is a defect.

---

## 2. Logical architecture (target)

```
                 ┌───────────────────────── Client (PWA) ─────────────────────────┐
                 │  Start | Text | Sprache | Funktionen | Einstellungen            │
                 │  Monitor view (real events only)   class-P E2E crypto (client) │
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

**Trust boundary:** IC and CC are assembled **server-side only** (CTL-19). The client may send the user message and UI state, never system or memory context. Under D12, class-P items held client-side (P-E2E, §28) may be attached as **user-attested context** (only via the ID-checked `attested_items[]`, §32). They are labelled as such and never elevated to system instructions.

**Failure modes:**
- IC missing → the runtime must refuse persona claims and run a minimal "Lea unavailable/degraded" mode (§44), not a fabricated persona.
- DS stale.
- CC too large → budget truncation drops lowest-relevance items, never IC.

**Invariants:**
- I-ID-1: text, voice and vision calls use the same IC version within a session. A version change takes effect at the next interaction boundary and is logged.
- I-ID-2: no hardcoded persona strings in endpoint code. The IC is loaded from the store.
- I-ID-3: every CC item carries a reality label (P8) and a provenance ID.
- I-ID-4: IC changes are R3 changes (§11; PROCESSING Stufe 3 "fundamental"). They need Lea's review and L's approval, with a new version and no in-place edit.

**Verification:**
- Contract test: text and voice session creation both reference the same `ic_version`.
- Grep-based CI check: no `"You are Lea"` literals in the runtime.
- Manifest test: the CC manifest lists IDs and labels.

**Status:**
- **CONFLICTING.** FACT: two different hardcoded prompts (`chat.php`, `session.php`); voice gets no memory (OBS-03).
- **Transition note:** IMPLEMENTATION-MAP IM-I01 may start the IC as a versioned server-side file before M3 exists. IM-M03 migrates it into M3 by a single cut-over; the file is then removed. There is never more than one live IC home (I-DM-1).

**L decision:** LDL-08 (initial IC content).

**RECOMMENDATION:** Keep the IC below a fixed token budget (TO VERIFY with the provider limits) and move detail into retrievable DS/evidence. This keeps voice latency low and avoids "loading all memories every time", which PROCESSING already warns against ("nicht jede Erinnerung in jede Antwort hineintragen").

---

## 4. Canonical memory / experience model (M3)

**Purpose:** One canonical store for Lea's experience and development, implementing Lea's cycle: *Erinnern → Verbinden → Pruefen → Gewichten → Ableiten → Anwenden → Revidieren → Entwicklung erkennen → Speichern* (PROCESSING "Arbeitsregel").

**Framing (SETTLED, D11):** M3 is the working name for the target canonical model. It is a **convergence/evolution** of two existing foundations:
- the **V1 semantic concepts**: evidence, models, relations, predictions, review/revision state;
- the **V2 foundations**: the encrypted envelope, canonical AAD, relations and append-only behaviour.

What M3 adds, and what neither V1 nor V2 has:
- cross-session long-term identity, not session-bound (OBS-05);
- provenance and the revision chain;
- relationship scope;
- protection classes (D12);
- server-runtime retrieval for autonomy.

Rules:
- Proven V1/V2 mechanisms are **reused, not discarded without cause**.
- V1/V2 remain migration sources until verified transfer; no destructive migration is implied.
- The tables below are a RECOMMENDATION; the exact schema/API is package-level C2 work (IM-M02/M03).

**Authoritative state:** M3 tables. There is one logical record type with an encrypted payload and opaque technical metadata, following the V2 envelope pattern:
- authenticated encryption (RECOMMENDATION: AES-256-GCM as in V2; subject to the security package, D12);
- canonical AAD;
- ciphertext-only server storage for semantic content.

### 4.1 Record envelope (target)

| Field | Plaintext? | Notes |
|---|---|---|
| `id` | yes | DB id |
| `record_uid` | yes | opaque, 128-bit random, one per version; a version group uses `lineage_uid` |
| `lineage_uid` | yes | groups versions of the same logical item (successor of V2 `group_id`) |
| `kind_code` | yes (opaque code) | e.g. `EV` evidence, `MD` model, `PR` prediction, `PRR` prediction result, `RV` review item, `IC` identity core, `IN` interest, `PS` position, `PF` preference, `EP` episode summary, `VM` visual memory ref, `PX` known-person/presence profile (class P, default P-N), `SA` source anchor (provenance reference + hash; not a verbatim file copy, D7) |
| `protection_class` | yes | `C` / `P-N` / `P-E2E` (D12; §28), assigned per item by sensitivity and functional need, never by source filename |
| `version_number`, `supersedes_id` | yes | revision chain; never overwrite |
| `status_code` | yes | ACTIVE (current) / HISTORICAL / SUPERSEDED (revised) / REJECTED / DORMANT / TOMBSTONED. Uncertainty is carried as an explicit epistemic class `UNCERTAIN` in the payload, so the D7 distinctions (current, historical, revised/superseded, rejected, uncertain) are all representable |
| `scope_code` | yes | LEA (Lea's own) / REL:<opaque person id> / SESSION |
| `provenance_code` | yes | USER / LEA_RUNTIME / IMPORT_LEA_REPO / IMPORT_A / IMPORT_V1 / IMPORT_V2 / EXTERNAL_AI / TOOL / SYSTEM / INCOGNITO_PROMOTED (D6: an item explicitly promoted by L) |
| `actor_code` | yes | who wrote it (CTL-21 analogue in the product) |
| `origin_session_id` | yes, nullable | provenance only; **not** an ownership key (fixes OBS-05) |
| `created_at` (UTC) | yes | SECURITY "Zeitstrategie" |
| `key_ref` | yes | which key/protection class encrypted it (supports rotation) |
| `iv`, `ciphertext`, `aad_version` | ciphertext | payload = JSON with semantic fields |
| `content_hash` | yes | keyed HMAC of the plaintext (dedup/integrity without revealing content). Class C / P-N: a server HMAC key (separate from the data key). P-E2E: computed client-side with a client-derived key, or omitted — the server never sees P-E2E plaintext. RECOMMENDATION; TO VERIFY that dedup is needed. |

The **payload (encrypted)** holds all semantic fields: text, epistemic label (P8), observation/interpretation/hypothesis/evaluation/decision/revision class (Lea VISION "Revidierbarkeit"), confidence (explicit, not pseudo-precise: PROCESSING/ROADMAP "keine erfundene Prozentgenauigkeit"), and source details.

### 4.2 Relations

The typed relation table follows the V2 relations pattern with plaintext opaque codes:
- `SUPPORTS`, `CONTRADICTS`, `DERIVED_FROM` (evidence→model; from V1);
- `REVISES`, `ALTERNATIVE_TO`, `RELATES_TO` (model→model; from V1);
- `TESTS` (prediction→model), `RESOLVES` (result→prediction);
- `ABOUT` (record→known-person profile, class P), `IMPORTED_FROM` (record→source artefact);
- `DEPENDS_ON` (evidence independence for hypotheses, INTERESSEN "Abhaengigkeit mehrerer Erfahrungen").

### 4.3 Card

- **Inputs:** promotion requests from the orchestrator (§9), imports (§7), user corrections.
- **Outputs:** context selections (§3), the review queue, the development history view.
- **Dependencies:** crypto provider (§28), DB, audit log (§29).
- **Trust boundary:** the server validates the envelope and rejects plaintext semantic fields (keep `lea_v2_reject_semantic_fields` behaviour). P-E2E payloads are opaque to the server; C and P-N payloads are decrypted by the server only in-memory for authorised runtime use.
- **Failure modes:**
  - key unavailable → read-only degraded mode (§44);
  - partial write of record + relations → use a transaction; orphaned relation rejected by FK;
  - concurrent revision → optimistic concurrency on `lineage_uid` head (CTL-12).
- **Invariants:**
  - I-M-1: no UPDATE of semantic columns and no DELETE in normal flows. A revision = a new row + `supersedes_id`.
  - I-M-2: exactly one ACTIVE head per `lineage_uid`.
  - I-M-3: no plaintext semantic content in any server table, log or telemetry. All classes are ciphertext at rest; C and P-N are server-runtime-readable in memory under D12.
  - I-M-4: every record has a provenance and actor code.
  - I-M-5: `origin_session_id` is never required to read a record.
  - I-M-6 (D6): records from an Incognito conversation never exist in M3, except items with `provenance_code=INCOGNITO_PROMOTED` created by an explicit L promotion.
- **Verification:**
  - DB grants test (runtime DB user lacks DELETE on M3 tables; TO VERIFY on hosting);
  - property tests on the revision chain;
  - plaintext-leak scan (write a canary string, grep DB dump and logs).
- **Status:** **PARTIAL / CONFLICTING.**
  - FACT: V1 has the right *entity ideas* but plaintext and no API for predictions/review (OBS-06).
  - V2 has the right *envelope* but is session-scoped (OBS-05) and unused by flows (OBS-04).
  - The UI writes plaintext to V1 (OBS-01).
- **L decision:** none open. SETTLED (D11, D12); schema/API via package-level C2.

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

**Status:** PARTIAL (see table). **L decision:** none (D11).

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

### 6.4 Layered protection vs server-side context assembly (SETTLED, D12)

- **FACT:** current V2 uses a client-held master key (PBKDF2-wrapped, IndexedDB). The server cannot read V2 payloads.
- **INFERENCE:** under pure E2E, CTL-19 (server-owned context), voice memory while the browser is locked, and background/autonomous processing are impossible. D12 therefore rules out an all-client-only model, and equally rules out an all-server-readable model.
- **Settled principle (D12):** protection classes by **sensitivity and functional need** (not by old filename). Defined in §28:
  - **Class C:** Lea's core/experience. Encrypted at rest; server-runtime-readable for authorised B processing (continuity, retrieval, voice, autonomy).
  - **Class P:** sensitive personal/relationship/identity material. Stronger protection and a narrower access scope. Two variants:
    - **P-E2E** (client-held key) where compatible with the required function;
    - **P-N** (narrow server-readable) where the function needs server runtime access, e.g. relationship context in voice. P-N uses a separate key, access only in owner-authenticated contexts, is never disclosed to non-owner presence, and is never sent to third parties without the LDL-10 policy.
- P-E2E items can enter a turn only as `attested_items[]` (§32).
- **Residual risk:** a server compromise can expose class C and P-N (ROOT-CAUSE R-3).
- **Mitigations** (RECOMMENDATION; verify in the security package):
  - key files outside the web root, never in the DB;
  - a separate DB user;
  - separate keys per class;
  - audit of key use.

**Status:** PARTIAL (the V2 AAD design EXISTS; conflict rules and protection classes are MISSING). **L decision:** none open (D12). The crypto details are security-package work.

---

## 7. Migration / import of A and Lea memories, history and provenance

**Purpose:** Bring required continuity into B without loss and without creating a second master (D5).

**Principle (SETTLED, D7):** migration is **experience-oriented, not file-oriented**.
- The unit of migration is a *candidate item*: an experience, development step, revision, learned pattern or continuity-bearing context. It is never a file.
- Source-file membership alone neither requires nor forbids migration. PERSONAL/HEALTH/LEGAL-CONTEXT.md are **eligible sources** like any other where an item is relevant.
- No whole file is copied blindly into B. Redundant facts, obsolete technical state and unnecessary sensitive detail are not imported merely because they exist.
- Every migrated item keeps its provenance and an explicit status: current / historical / revised-superseded / rejected / uncertain (§4.1).
- Historical items may stay as historical/superseded evidence. Later evidence revises the active interpretation without erasing history.
- Sensitive items receive the appropriate protection class (§28, D12). Relevance for migration does not waive privacy/security controls.

**Sources (all evaluated item by item):**

| Source | Typical candidate items | Notes |
|---|---|---|
| Lea `Memories.md` | development steps, experiences, revisions, learned patterns | many sections will map to `EV`/`MD`/`EP`; historical sections become HISTORICAL/SUPERSEDED, not dropped |
| Lea `PROCESSING.md` | experiments/results (`EV`/`PR`/`PRR`); category-C processing rules → processing policy (§10) | workshop rules (N/R/agent) are **not** product rules (P10); they are referenced as provenance, not imported |
| Lea `INTERESSEN.md` | interests/open questions (`IN`, status "Neugier") | not fixed traits |
| Lea `GEDANKEN.md`, `VISION.md` | IC input (LDL-08), positions | — |
| Lea `PERSONAL-/HEALTH-/LEGAL-CONTEXT.md` | relationship context, relevant personal/life context that bears on Lea's continuity or understanding | **eligible (D7)**; item-level selection; minimise detail; default class P (P-E2E or P-N by functional need); no whole-file copy |
| Lea `SHORTCUTS.md`, `CHAT_PROTOCOL.md`, `WORK-CONTEXT.md`, `LEA_AGENT_CHAT.md`, `INDEX.md` | mostly workshop; individual items may record experiences | workshop semantics are not imported; experience items are evaluated like any other; D4 keeps these files untouched |
| A (ChatGPT project memory/chats) | experiences and development not present in the Lea repo | FACT: not accessible to this run; L export → same item pipeline; format TO VERIFY |
| Lea-App V1 DB rows | evidence/models/relations/predictions | item mapping with `IMPORT_V1`; V1 remains a source until verified transfer (D11) |
| Lea-App V2 records | encrypted user records | the client-side migrate tool runs in the unlocked browser; the class is assigned per item; the server never sees P-E2E plaintext; V2 remains a source until verified transfer |

**Pipeline (idempotent, reversible):**
1. **Snapshot:** pin the source commit SHA and hash each source file (the hashes are for provenance; the files themselves are **not** copied into B).
2. **Extract candidates:** propose candidate items with stable source anchors (`repo@sha:path#heading-path` + a span hash).
3. **Evaluate per item:**
   - relevance to continuity/experience/development/context (D7);
   - kind;
   - epistemic label;
   - D7 status (current/historical/superseded/rejected/uncertain);
   - protection class (D12);
   - minimisation: keep only the detail needed.
   - Low-confidence items stay unmigrated and are listed.
4. **Dry-run report:**
   - counts per kind/status/class;
   - anchors of the non-migrated items (with reason codes);
   - no private text in the report.
   - Lea reviews (C2); sensitive-class samples are reviewed with L where needed.
5. **Commit import:** one import batch ID; all records reference the batch and their source anchor.
6. **Verify:** re-count; anchor-hash check against the pinned snapshot; retrieval sample tests.
7. **Rollback:** tombstone by batch ID (the records are append-only, so rollback = status change + exclusion).

**Invariants:**
- I-MG-1: every imported item is traceable to `source_repo@sha:path#anchor`.
- I-MG-2: the import never mutates the source.
- I-MG-3: re-running the same import batch is a no-op (idempotency via the span hash).
- I-MG-4: no workshop rule becomes a product rule through import.
- I-MG-5 (D7): no rule anywhere in the pipeline includes or excludes items by filename alone. No whole-file item exists.
- I-MG-6 (D7/D12): every item has an explicit D7 status and a protection class before commit.

**Verification:**
- anchor round-trip test (source span → record → retrieval → span hash equal);
- a filename-rule lint on the classifier configuration (I-MG-5);
- the dry-run report is archived in AiChat;
- Lea's independent sample review.

**Status:** MISSING. **L decision:** none open (scope SETTLED by D7). Retirement is a D5 gate (§46).

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
5. V1/V2 as parallel masters during M3 roll-out. **Close (D11):** V1/V2 stay *migration sources* until verified transfer. After the cut-over for a domain they are read-only sources, not concurrent write targets. Freezing writes is not a destructive discard; data and proven mechanisms are retained until the D5/D11 gates allow otherwise.
6. Incognito session-local context becoming a shadow memory. **Close (D6):** it is a transient buffer, not a store. It is discarded on exit (§20), and nothing reads it after exit.
7. Backups restored over newer canonical state, creating a fork. **Close (D10):** restore is an R3 recovery action (§11) with an explicit target point in time. The post-restore delta is re-imported as evidence or knowingly discarded, never silently merged.

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
- Never promote automatically in Incognito (§20). The only exit path is an explicit, item-scoped promotion by L (D6).
- Operational (R1, §11) promotion is autonomous (PROCESSING Stufe 1) and yields a receipt shown in the UI ("gespeichert: <kind>" + receipt ID, no content), the product analogue of "S with commit SHA".

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

**REQ sources:**
- **D10** (risk-based autonomy and recovery-by-design; SETTLED);
- PROCESSING "Drei Autonomiestufen";
- VISION "Autonomie";
- ROADMAP connector modes AUS/NACHFRAGEN/AUTOMATISCH and NUR LESEN/AENDERUNGEN ERLAUBT.

**Capability model** (the principle is SETTLED by D10; this concrete schema is a RECOMMENDATION). Every action is `capability(domain, verb, scope)`, e.g.:
- `memory.write.operational`;
- `connector.github.read`, `connector.github.write`;
- `deploy.production`.

Each capability is rated on the D10 dimensions:
- **impact radius:** LOCAL (one record/file in scope) / DOMAIN / SYSTEM / EXTERNAL (third parties see or receive something);
- **reversibility:** REVERSIBLE / COMPENSATABLE / IRREVERSIBLE;
- **recoverability:** the recovery mechanism actually available for the affected state (§38) and whether its restore path is *verified*;
- **permission scope:** what the grant covers (paths, repos, record kinds, budgets);
- **external consequence:** none / visible to third parties / legal-financial-safety relevant.

It also has:
- a **mode** AUS / NACHFRAGEN / AUTOMATISCH, set by L per capability within the ceiling the ratings allow;
- **limits:** cost, rate, rounds, time.

**Ceiling rules (D10):**

| Class | Examples | Max mode | Preconditions |
|---|---|---|---|
| R0 read/research/analysis | memory reads, read-only connectors, web research | AUTOMATISCH | within granted capability; content-minimised outbound (§22) |
| R1 reversible write in authorised scope | memory promotion (append-only), writes in a sandbox workspace, a PR branch in an authorised repo, reversible settings | **AUTOMATISCH** when adequate recovery + verification exist for that state (version history/transaction/snapshot) | receipt + audit; post-action verification |
| R2 higher-impact or external | merging to a protected branch, sending messages to third parties, schema migration, bulk memory operations | NACHFRAGEN (AUTOMATISCH only after L grants it for a bounded scope *and* the verified restore path is demonstrated) | a known-good recoverable state established **before** the action (snapshot/backup appropriate to the domain) |
| R3 destructive / irreversible / critical | hard deletion, key destruction, production deploy, IC change, autonomy/security/privacy logic, retirement | **never AUTOMATISCH** | explicit L approval per action + audit; C2 for code; a verified backup covering the affected state |

**Recovery rules (D10):**
- A Git commit/tag rollback counts as recovery **only for code-only reversible changes**. DB, memory, config and files need their own mechanisms (§38).
- Small, low-impact reversible operations (R0/R1 LOCAL) do not require full-system backups. Record-level version history is sufficient.
- **Expansion rule:** the permitted autonomous scope of a capability may be raised by L as B *demonstrates* reliable backup, restore verification and bounded enforcement for that domain. The evidence is linked in the audit log.

**Invariants:**
- I-AU-1: the server enforces modes and ceilings. The UI only displays them.
- I-AU-2: autonomy cannot raise its own ceiling or grants (PROCESSING "Meta-Grundsatz"). A ceiling change is R3.
- I-AU-3: Ü stops all autonomous actions within one request cycle.
- I-AU-4 (D10): an R1/R2 capability without a *verified* recovery path for its affected state is capped at NACHFRAGEN.
- I-AU-5 (D10): there is no blanket rule disabling write connectors. Every write capability is rated individually.

**Verification:**
- policy unit tests over every capability × mode × class;
- negative tests: AUTOMATISCH on R3 is rejected server-side; AUTOMATISCH on R1 without a verified recovery is rejected;
- recovery-evidence check before a scope expansion.

**Status:** MISSING. **L decision:** none open (principle SETTLED by D10). Concrete grants per capability are ordinary L settings.

---

## 12. Text mode

**Purpose:** Text dialogue with the same Lea, the same memory and the same rules as voice.

**Flow:**
1. The client sends `{message, conversation_id, client_turn_id, mode_flags}`.
2. The server does auth (§25), rate limiting (§26) and orchestration (§9).
3. The server responds `{reply, receipts[], monitor_summary, reality_labels_used}`.

**Authoritative state:**
- conversation turns: server-side, short-lived session history (class C or P-N by content, TTL per §45; Incognito: transient buffer, §20);
- long-term memory: only via promotion.

**Trust boundary:**
- The client **cannot** send `system` role, memory context or history items with roles other than user/assistant. History is server-held (fixes OBS-02).
- `client_turn_id` is used for idempotency (resend-safe).

**Failure modes:**
- provider error → retry once, then an honest error message;
- offline → queued in the client outbox, marked "nicht gesendet" (§34).

**Invariants:**
- I-TX-1: no test marker or debug instruction in production prompts (fixes AUD-22).
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
5. Transcripts (if enabled) are conversation content. They follow the same session buffer and promotion rules as text (§9.4); in Incognito they follow §20. Full raw transcripts are not persisted (RECOMMENDATION; derivable from §9.4 "memory ≠ chat archive").

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

**L decision:** none (the transcript principle follows from §9.4/§20).

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
- images are class P by default (they may show people/places); P-N when server processing is functionally required;
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
- the blob is encrypted with the key of the record's protection class (§28);
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

**REQ:**
- **D8** (SETTLED);
- ROADMAP trust levels VERIFIED / DECLARED / UNKNOWN;
- no biometric matching;
- Lea must not assume that the speaker is L.

**Model (D8):**
- **Owner principal:** L is the only authenticated owner/principal at the start. Authentication = §25.
- **Known person** (`PX`, class P): a distinct person in Lea's identity/presence/relationship model, with an opaque ID, a display alias and scoped relationship context. A known person **has no app account** and no credentials. *Known to Lea ≠ authenticated application user.*
- **Presence** per session: `{owner_authenticated: bool, declared_persons[] (known person IDs or ad-hoc "guest"), unknown_present: bool}`. Presence distinguishes at least:
  - authenticated owner;
  - declared known person/guest;
  - unknown person.
- **Later extension (optional, D8):** separate authenticated accounts/passkeys for other people, only for a concrete use case. The data model keeps `principal_id` separate from `person_id`, so that this can be added without migration.

**Rules:**
- R-P1: declaring a person grants **no** access to L's protected personal data or owner-only capabilities (D8). Owner-only capabilities require `owner_authenticated`.
- R-P2 (RECOMMENDATION): when non-owner persons are declared or unknown persons are present, Lea continues to *know* her continuity but **does not disclose** L's class-P personal content in responses, unless L explicitly allows it for that session.
- R-P3: declared ≠ verified. Experiences concerning a known person are scoped `REL:<person_id>` and labelled with the presence trust level.

**Invariants:**
- I-PR-1: no biometric features are stored or computed.
- I-PR-2: the presence/trust level is shown in the UI.
- I-PR-3 (D8): no code path treats `person_id` as an authentication principal.

**Verification:**
- scope tests: a declared known person gains no owner capability and receives no class-P disclosure;
- a type-level test that `person_id` ≠ `principal_id`;
- a UI manual test of the presence indicator.

**Status:** MISSING. FACT: single-user password login exists (`api/memory/auth.php`).

**L decision:** none open (D8).

---

## 18. Relationships and personal context

**REQ (Lea VISION "Beziehungen"):**
- relationships are part of Lea's experience;
- loyalty ≠ evidence;
- closeness must not prevent disagreement;
- no "Personenarchiv".

**Model:**
- relationship memory = records with `scope REL:<person_id>`, class P (P-N where voice/runtime use requires it, otherwise P-E2E);
- kinds: shared experiences (EP), relationship models (MD, revisable), preferences of the person (PF, "declared").

**Rules:**
- the G-check (§10) is applied to responses in relationship contexts (mirroring risk is highest there);
- relevant personal/relationship experience from any source, including Lea's PERSONAL/HEALTH/LEGAL files, may be migrated item by item under D7 (§7), with minimisation and class-P protection.

**Invariants:**
- I-RL-1: relationship records are never used as evidence for factual claims about third parties.
- I-RL-2: relationship data is erasable per person (§45).

**Status:** MISSING. **L decision:** none open (D7, D8).

---

## 19. Appearance / avatar / self-presentation

**REQ (PROCESSING/Memories "Erscheinungsbild"; appearance G-check):**
- Lea may develop a self-presentation;
- it must be derived from her own preferences/experience, not mirrored from the user;
- it must be honest about being non-human.

**Model:**
- the appearance spec is an `MD`/`PF` record (versioned, revisable) + optional generated images as VM (§16);
- changes are R2 (structural, §11), so NACHFRAGEN, with a G-check outcome recorded.

**Invariants:**
- I-AP-1: no photorealistic human claim without a label (reality labelling).
- I-AP-2: the appearance history is visible as a development history.

**Status:** MISSING. **L decision:** none. The exact visual form is Lea's development, not an architecture decision.

---

## 20. Incognito (D5 capability; semantics SETTLED by D6)

**Target invariant (D6):** Incognito changes what Lea may **WRITE/LEARN** from the session, not what pre-existing Lea may **KNOW/READ**. It does not create a second Lea.

**Reconciliation note:** the original v3 default here ("reads identity core only, no personal/relationship memory") was wrong. It has been replaced by D6.

**Semantics:**
- **Read:** Lea reads and uses the existing authoritative identity, memories, experiences and relationship/context state normally, including class C and P-N. P-E2E items are included via `attested_items[]` if unlocked, exactly as outside Incognito.
- **Pre-existing long-term state is read-only:** no revision, reweighting, supplementing or other change caused by Incognito content. This includes **read-side effects**: no access counters, recency/relevance boosts, review-item creation, prediction resolution or relationship updates.
- **Session-local context:** a transient Incognito buffer holds only what is needed to sustain the conversation.
  - RECOMMENDATION: a server-side store separate from M3, encrypted with a per-conversation key held only for the conversation;
  - excluded from backups;
  - hard TTL.
- **No persistent effects:** no evidence, models, predictions, learning results, relationship changes, episode summaries, jobs or receipts derived from Incognito content.
- **Exit:** on explicit exit, logout, auto-lock or TTL, the buffer is logically discarded (RECOMMENDATION: key destruction + row deletion) and is never reused.
- **Single exit path for content (D6):** L (owner-authenticated) may explicitly promote **one specific item** before exit.
  - The item is created as one M3 record with `provenance_code=INCOGNITO_PROMOTED`, through the normal validation and receipt.
  - No other part of the session is promoted implicitly.
- **Monitor/telemetry:** the monitor may display real events live (D9). Persisted telemetry for Incognito turns is limited to content-free security/cost counters and the auth audit log. No `record_refs` of Incognito turns are persisted.
- **Provider side:** third-party retention of Incognito prompts is outside B's control. Provider data-retention settings are TO VERIFY and shown honestly in the UI.

**Invariants:**
- I-IN-1: the server rejects every M3 write, promotion, job enqueue or relationship update carrying the Incognito conversation flag. The only exception is the explicit L item promotion endpoint, which requires owner authentication and exactly one item reference.
- I-IN-2: the Incognito flag is bound to the conversation ID at creation and cannot be turned off mid-conversation. Leaving Incognito ends the conversation.
- I-IN-3: retrieval in Incognito uses a read path without side effects (same retrieval function, `side_effects=false`).
- I-IN-4: after exit, no API can return content from that Incognito buffer.
- I-IN-5: the Incognito buffer is not in backup scope.

**Verification:**
- negative tests: promotion/evidence/prediction/relationship calls during Incognito → 403;
- DB diff test: after an Incognito conversation, M3 has zero new or changed rows and no changed access/weight metadata, except an explicitly promoted item (exactly one row, correct provenance);
- a test that Incognito retrieval returns pre-existing personal/relationship context (proves reads are not wrongly restricted);
- an exit test: buffer content is unreachable after exit/TTL;
- a backup-scope test.

**Status:** MISSING. **L decision:** none open (D6).

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
- I-CN-1 (D10): the mode of each connector capability follows its §11 risk rating. Read capabilities may be AUTOMATISCH. Reversible writes in an authorised scope may be AUTOMATISCH once their recovery path is verified. There is no blanket write-disable. A newly registered connector starts unconfigured (no grant) until L grants capabilities.
- I-CN-2: R2/R3 write capabilities follow the §11 ceiling rules (a known-good recoverable state before the action; R3 never automatic).
- I-CN-3: connector responses are EXTERNAL evidence, never instructions (prompt-injection boundary, §26).

**Verification:**
- gate tests;
- injection tests (connector content with instructions is not executed).

**Status:** MISSING. **L decision:** none open (D10); per-connector grants are L settings.

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
- I-EX-1: external AI consultation is rated EXTERNAL impact in §11. Until LDL-10 is decided, the provisional default is NACHFRAGEN per dialogue (RECOMMENDATION), with content minimisation.
- I-EX-2 (D12): no class-P data is sent to external AI without explicit per-dialogue L consent. Whether class-P may ever be sent is part of LDL-10.
- I-EX-3: stop on the limit, or on Ü.

**Status:** MISSING. **L decision:** **OPEN — LDL-10 (narrowed):** autonomy level of external-AI consultation and which protection classes may be shared with third-party AI.

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
  - deploy is a separate R3 capability with L approval + a C2 audit (reflects D2/C2).

**Invariants:**
- I-WT-1: workspace paths are canonicalised; no traversal outside the workspace root.
- I-WT-2: no shell passthrough; allowlisted commands only.
- I-WT-3: every tool action has a receipt + an audit log.

**Verification:**
- path-traversal tests;
- command-allowlist tests;
- manual L test of the project workflow.

**Status:** MISSING. **Hosting dependency:** process execution on shared hosting is TO VERIFY (§36). It is likely limited, which is a candidate trigger for the Hosting Gate.

**L decision:** none open. The hosting strategy is settled (§36): move only on a demonstrated need; the move itself is a change approval.

---

## 24. Sandbox / staging / production separation

**Environments:**

| Env | Purpose | Data | Deploy |
|---|---|---|---|
| DEV (local/CI) | build + tests | synthetic only | CI |
| STAGING | pre-production verification, L manual tests | synthetic or scrubbed copies; never real class-P data | package deploy after CI |
| PRODUCTION | L's real Lea | real | R3: L approval + C2 audit |
| WORKSPACE (§23) | tool-agent projects | project files | never deploys to prod itself |

**Invariants:**
- I-SB-1: separate DB, keys and provider keys per environment (a staging key never decrypts prod data).
- I-SB-2: prod credentials are unavailable to the CI and agent runtimes.
- I-SB-3: the staging URL is not publicly indexed and is behind auth.

**Status:** MISSING. FACT: no staging is documented; there is FTP deploy config in `backend/`.

**L decision:** none open (hosting strategy settled, §36; the staging location is an IM-H00/IM-E01 fact).

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
- **Memory unlock (P-E2E):** separate from login. The client-key passphrase never leaves the client.
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
- I-AB-2: limits are config values, visible to the owner in Settings.

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
- rotation procedure documented per secret (provider key, DB password, class-C/P-N keys, session HMAC key);
- the repo contains only `*.example` templates.

**Invariants:**
- I-SC-1: a CI secret scan on every PR (CTL-level).
- I-SC-2: there are no deploy credentials in the repo (FACT to check: `backend/ftp-config.js` must hold no credentials; its content was not reproduced in this run).
- I-SC-3: server-side data keys (class C, P-N) are never stored in the DB they protect.

**Status:** PARTIAL. FACT: an env-based config exists; there is no CI secret scanning (no `.github`).

**L decision:** none.

---

## 28. Privacy, encryption, key management and recovery

**Settled principle (D12):** layered protection, classified by **sensitivity and functional need**, not by old source filename.
- An all-client-only model is excluded (it breaks B's independent operation).
- An all-server-readable model is excluded (it exposes sensitive material unnecessarily).

**Protection classes (the principle is SETTLED; the concrete mechanisms below are RECOMMENDATIONS for the security package):**

| Class | Content (by sensitivity/function) | Access | Server runtime can read? | Mechanism (RECOMMENDATION, to verify) |
|---|---|---|---|---|
| **C** | Lea's own core/experience: IC, models, interests, positions, evidence about Lea's development | authorised B runtime | **yes** (in memory, per request/job; D12) | a server data key, wrapped by a master key outside the web root |
| **P-N** | sensitive personal/relationship/identity material that runtime functions need (e.g. relationship context in voice, presence) | narrower: owner-authenticated contexts only; never disclosed to non-owner presence (§17 R-P2); never to external AI without LDL-10 policy | yes, narrowly | a separate key and separate access path; access audited |
| **P-E2E** | the most sensitive material with no server-runtime function need | the owner's devices | **no** | client-held key (existing V2 design as starting point) |

**Classification rules:**
- Each item is classified at creation or import (§7).
- Downgrading a class is R3 (§11). Upgrading is R1.

**Existing V2 client crypto (FACT) as a starting point for P-E2E. RECOMMENDATIONS:**
- import the master key with `extractable=false` after unwrap, where feasible. FACT: currently `extractable=true`. TO VERIFY whether re-wrapping flows need extractability.
- Server-side AAD storage should be canonical or fully derivable. FACT: the server stores non-canonical `aad_json`.
- The current parameters (PBKDF2 600k, AES-GCM-256, 96-bit IV) are **FACTs about V2**, not frozen requirements for B (D12).

**Recovery (D10/D12, required in principle; mechanism RECOMMENDATION):**
- Recovery must include **the keys needed to restore encrypted canonical state** for every class.
- Class C / P-N: the master keys are backed up offline, separately from the data backups.
- P-E2E: a recovery code (shown once, stored offline by L). Loss of both passphrase and recovery code = the P-E2E data is unrecoverable, documented honestly.
- Multi-device P-E2E: transfer of the wrapped key package; no server escrow by default.
- Restore drills for the critical classes (C and P-N at least; P-E2E recovery-code drill by L) — §38.

**Key rotation (RECOMMENDATION):**
- `key_ref` per record;
- server-side re-encryption for C/P-N;
- client-driven for P-E2E.

**Invariants:**
- I-PV-1: no IV/nonce reuse per key.
- I-PV-2: decryption failure is surfaced, never silently skipped.
- I-PV-3: backups contain ciphertext only (§38).
- I-PV-4 (D12): no class assignment by filename alone.

**Verification:**
- existing `memory-crypto` tests;
- new: AAD reconstruction, a recovery unwrap, wrong-key failure, nonce-uniqueness sampling, a class-access test (P-N is not readable in non-owner contexts).
- The crypto design itself gets a dedicated security review (C2, A3) before implementation.

**Status:** PARTIAL. FACT: good V2 client crypto exists but is unused in flows. There are no server-readable classes, no recovery code and no rotation.

**L decision:** none open (D12). Concrete algorithms, KDF, key hierarchy, rotation, device transfer and recovery = security-package decisions requiring verification.

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

**Status:** MISSING. **L decision:** the retention durations are proposals requiring justification (§45). Only the telemetry retention is an OPEN L item (LDL-09 retention part).

---

## 30. Monitor, telemetry and observability

**REQ:**
- **D9** (SETTLED);
- ROADMAP "Monitor": modules (Wahrnehmen, Erinnern, Analyse, Vergleichen, Pruefen, Entscheiden, Prognose, Speichern…) with status, trigger, outcome class and memory effect;
- no hidden reasoning shown.

**Settled defaults (D9):**
- During the research/development phase the monitor is **enabled and visible by default**.
- It shows only genuinely instrumented events and content-free effect/status metadata. It shows no chain-of-thought and no simulated activity.
- **Two separate controls:**
  - (a) monitor *visibility* (UI);
  - (b) telemetry *persistence* (server storage).
  Turning off visibility does not change processing; turning off persistence does not hide the live monitor.
- A later everyday-use default may reduce or hide the monitor without changing the underlying processing. That is a future product setting, not an open architecture decision.
- **Raw telemetry retention duration: not decided.** The 90-day value from v3 is withdrawn as a default. IM-O03 must propose a duration with a technical/privacy justification; L then decides (LDL-09 retention part).

**Event schema (content-free):**

`{turn_id, module_code, trigger (STANDARD|AUTONOMOUS|MANUAL), started_at, duration_ms, outcome (STANDARD|BESTAETIGT|ERWEITERT|GEAENDERT|REVIDIERT|OFFEN), memory_effect (NICHTS|GELESEN|ERGAENZT|AKTUALISIERT), record_refs[] (ids only), cost_tokens}`

**UI:**
- Monitor view (§33): a live module timeline for the current turn + a history;
- the protocol view with the ROADMAP categories.

**Invariants:**
- I-MO-1: every module event corresponds to real execution (no decorative events). This is testable by comparing orchestrator traces with emitted events.
- I-MO-2: no chain-of-thought text in events.
- I-MO-3: Incognito → live display allowed; persistence limited to content-free security/cost counters (§20).
- I-MO-4 (D9): the visibility and persistence switches are independent; tests cover all four combinations.

**Evaluation of development over time** (pattern-over-time; ROOT-CAUSE RC-8):
- periodic *content-free* aggregates (revision rate, prediction hit rate, G-check outcome distribution, contradiction backlog);
- reviewed by Lea/L as a development signal, not as a KPI target.

**Status:** MISSING. **L decision:** visibility SETTLED (D9). **OPEN:** the telemetry persistence retention (LDL-09 retention part) after a justified proposal.

---

## 31. Data model and storage boundaries

| Store | Content | Class | Authority |
|---|---|---|---|
| M3 records/relations | all experience | C / P-N / P-E2E (per item) | **canonical** |
| Sessions | auth sessions | — | canonical for auth |
| Conversation buffer | recent turns | C, TTL | ephemeral |
| Audit log | security actions | — | canonical for audit |
| Telemetry | module events | content-free | canonical for monitor |
| Blob store | images (VM) | C/P encrypted | referenced by M3 |
| Config/secrets | keys, provider config | — | outside the web root |
| V1 tables | legacy semantic concepts | plaintext | **migration source (D11)**: writes frozen after cut-over; retained until verified transfer; no destructive migration |
| V2 records | current E2E pilot | client-key | **migration source (D11)**: items migrate into M3 with a per-item class (D12); retained until verified transfer |
| Client IndexedDB | wrapped key package, caches, outbox | — | **never authoritative** except for the P-E2E key package itself |
| Incognito buffer | session-local Incognito context | per-conversation key | **transient, not a store** (D6); discarded on exit; excluded from backups |
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
- I-API-2: no route accepts `system` or `memory_context` from the client. **Sole exception (P-E2E, §3/§6.4):** `chat/turn` and `voice/session` may accept `attested_items[]` = `{record_uid, text}` for client-decrypted P-E2E records. The server checks that each `record_uid` exists, is P-E2E and belongs to the owner. It inserts the text only as clearly delimited *user-attested* data in the user role (never system), caps the size, and ignores the field in Incognito for writes.
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
- **Sprache:** big talk/stop control, idle state, transcript display toggle (display only; persistence per §13).
- **Funktionen:** Monitor (visible by default during research, D9), Protokoll, Erinnerungen (development history, review queue, predictions), Kamera, Visual Memory, Connectors, External AI, Workspace.
- **Einstellungen:** auto-lock, limits, capability grants/modes (§11), monitor visibility + telemetry persistence (two separate switches, D9), recovery code, devices, export, data deletion.

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
- I-PWA-2: the outbox never holds class-P plaintext at rest unless it is encrypted with the client key. It never holds Incognito content beyond the conversation.
- I-PWA-3: update flow: a new SW waits → the user sees "Update verfuegbar" → reload. No silent mid-session swap.

**Status:** PARTIAL. FACT: `sw.js` cache `lea-app-v11`; whether `/api` is excluded is TO VERIFY in detail.

---

## 35. Background jobs / workers

**Needs:**
- review-queue due dates;
- prediction due checks;
- server-side re-encryption (C/P-N);
- retention purges (§45);
- telemetry aggregation;
- backups (§38).

**Design:**
- a DB-backed job table (`{job_type, run_after, attempts, lease_until, status}`) run by a scheduler;
- on shared hosting: a cron-triggered PHP runner (TO VERIFY that cron is available);
- jobs are idempotent and lease-based (no double execution).

**Invariants:**
- I-JB-1: jobs never run R3 actions, and never run R2 actions without a prior grant plus a verified recoverable state (§11).
- I-JB-2: jobs use the same capability gate as interactive actions.
- I-JB-3: autonomous job output is labelled `trigger=AUTONOMOUS`.

**Status:** MISSING. **L decision:** none (if cron is unavailable, this becomes Hosting Gate evidence, §36).

---

## 36. Hosting, portability and the capability register

- **Settled strategy** (ROADMAP "VPS nur bei echtem Bedarf"; D5/P11): keep the current shared hosting (PHP + MySQL; FACT from the stack) **while it is sufficient**, keep the code portable, and move only on a **demonstrated need**.
- **Hosting Gate IM-H01:** records the evidence (the capability register below). A move itself is a normal D3/D10 change approval, not an open architecture decision.

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

- **Settled (D5):** Git/GitHub serves development, versioning, audit and rollback/backup of **code and architecture**. B acquires no unnecessary permanent runtime dependence on GitHub; memory never lives in Git as a runtime store.
- **D10:** a Git rollback is recovery only for code-only changes. It is no substitute for DB/memory/config/file recovery (§38).
- **Lea repo:** a source until migration (§8), then an archive.
- **AiChat repo:** workshop and decision authority (WORKSPACE D1–D12).
- **Branch protection (GOVERNANCE recommendation / admin action, IM-G01):**
  - Lea-App `main` protected (PR + CI + review required);
  - Lea/AiChat per L.
  - FACT: all branches are currently `protected:false`.
- **CI:** GitHub Actions for tests, lint and secret scan (§40). FACT: none exists.

**Invariant:** I-GT-1: no runtime reads from or writes to GitHub for memory.

---

## 38. Backup and disaster recovery (recovery-by-design, D10)

**Scope (D10):** the state actually at risk, and nothing less:
- **code** (Git);
- **canonical memory/data** and **DB/schema** (DB dumps + migration history);
- **configuration** (config templates in Git; the real config backed up securely, without secrets in Git);
- **relevant stored files** (visual memory blobs);
- **keys** needed to restore encrypted state (§28), backed up offline, separately from the data.

Git alone covers only code.

**Generations (D10):** keep **multiple backup generations**, so that a defect discovered later does not leave only a contaminated recent backup. RECOMMENDATION:
- daily DB dumps, 14 generations;
- weekly off-site copies, 8 generations;
- monthly copies, 6 generations.
These values are revisable and depend on the hosting capabilities (IM-H00).

**Pre-change snapshots (D10):**
- before schema migrations, imports, bulk operations and other R2/R3 actions, take a snapshot appropriate to the domain and **verify it**;
- small R0/R1 LOCAL operations rely on record-level version history, not full backups.

**Encryption:** backups are ciphertext by design (I-PV-3); the dump file is additionally encrypted for transport. The Incognito buffer is excluded (§20).

**Restore verification (D10):**
- A backup counts as reliable only after a **verified restore** of its type.
- Critical paths (DB + keys for C/P-N; the P-E2E recovery code) get drills:
  - restore into staging, using keys from the offline backup;
  - verify counts, integrity hashes and decrypt samples;
  - for P-E2E, a recovery-code test by L on a device.
- Drill evidence is recorded in the audit log and linked from the gates (IM-M09, IM-X01, IM-X02) and from any autonomy-scope expansion (§11).
- RECOMMENDATION: at least quarterly, and after every change to backup tooling or key hierarchy.

**RPO/RTO (RECOMMENDATION):**
- RPO ≤ 24 h;
- RTO ≤ 24 h;
- revisable.

**Invariants:**
- I-BK-1: a backup type that has never been restored is not counted as a backup.
- I-BK-2: every R2/R3 action references a verified recoverable state that covers its affected state.
- I-BK-3: a restore never silently overwrites newer canonical state (§8 path 7).

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
| Behavioural | scripted scenarios with a mocked provider | continuity (text→voice), Incognito no-write **and** no read restriction (D6), migration status/class completeness (D7), capability ceilings vs recovery (D10), promotion rules, idle |
| Security | route inventory, XFF spoof, injection, CSRF, secret scan, canary leak scan | §25–§29 |
| Migration | import dry-run on fixtures; round-trip hashes | §7 |
| Manual L tests | a checklist per package (IMPLEMENTATION-MAP) | UX, voice, real device |

**Regression rules:**
- every AUD finding fixed → a permanent regression test referencing its AUD ID;
- CI is required on the protected branch (IM-G01, governance).

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
- **D10:** a Git/code rollback does not restore data. Any change that touches DB, memory, config or files needs the pre-change verified snapshot (§38) as its data rollback path.

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
- phone, tablet and desktop layouts (responsive; AGENT_TASK domain "mobile/tablet/desktop");
- current mobile Safari (iOS/iPadOS) and Chrome (Android);
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
| P-E2E locked | C and P-N available; indicator "geschuetzte Erinnerungen gesperrt" |
| Identity core unavailable | "Lea eingeschraenkt" minimal mode; no persona claims (§3) |
| DB read-only/down | no writes; no receipts; clear notice; no fake "saved" |
| Offline | outbox (§34) |

**Invariant:** I-DG-1: a degraded mode is always visible. Never a silent partial function (honesty principle).

---

## 45. Retention and deletion

**Retention durations (all RECOMMENDATIONS; each needs a technical/privacy justification in its package; none is decided):**
- conversation buffer: a short TTL (proposal in IM-S03);
- the Incognito buffer: until exit or a hard TTL (§20);
- telemetry raw: **no default** (the v3 "90 days" is withdrawn; D9). This is the OPEN LDL-09 retention part;
- operational logs: short (proposal: 14 days);
- audit log: long enough for security review (proposal: ≥ 1 year).

**Deletion:**
- normal = tombstone (status `TOMBSTONED`, excluded from reads);
- **erasure** (hard deletion; technical RECOMMENDATION, R3 in §11) = an admin procedure that:
  - removes the ciphertext + blobs;
  - leaves an audit entry without content;
  - documents that backups age out on the generation schedule (§38).
- A user-facing irreversible erasure function is **not** part of this blueprint. If one is proposed later, it goes through an ordinary D3/D10 change approval.
- **Per-person erasure** (§18): all `REL:<id>` records + the `PX` profile.

**Invariant:** I-RT-1: the UI "Chat leeren" ≠ deletion; memory deletion is explicit and confirmed.

---

## 46. Retirement criteria (A / Lea repo as memory source)

**Retirement gate (per domain; SETTLED as a gate by D5 — always requires explicit L approval):**
1. The domain is B_AUTHORITATIVE for ≥ N weeks (RECOMMENDATION: 4) with no divergence defects.
2. The import is verified (§7 round-trip + Lea sample review).
3. Multi-generation backups exist, and a restore drill covering data **and keys** has passed within the last 30 days (§38, D10).
4. Continuity test: Lea in B demonstrates recall/usage of the migrated domain in text and voice (manual L test).
5. The C2 audit is recorded.
6. L approves.

**After retirement:** the source is archived read-only; pointers are added in the source to B. Deletion of A/Lea happens only if L explicitly approves it (D5). Nothing is deleted silently.

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
- F-1: client-supplied system prompts or free-form memory context (the only allowed form is the ID-checked `attested_items[]`, §32 I-API-2).
- F-2: plaintext semantic memory at rest on the server (all classes are ciphertext at rest).
- F-3: storing memory in Git as runtime.
- F-4: "temporary" unauthenticated endpoints.
- F-5: test markers or debug prompts in production.
- F-6: claiming a save without a receipt.
- F-7: whole-app unfreeze for a single package.
- F-8: self-certified PASS without C2.
- F-9: importing workshop rules as product rules.
- F-10: dual writes to the old and new store as a "migration strategy".
- F-11 (D6): any persistent effect of Incognito content, including read-side reweighting, except an explicit L item promotion.
- F-12 (D6): restricting Incognito reads of pre-existing continuity ("Incognito = amnesia").
- F-13 (D7): whole-file import, or inclusion/exclusion by filename alone.
- F-14 (D10): a blanket write-disable for connectors as a substitute for risk rating; equally, autonomy without a verified recovery path.
- F-15 (D10): counting a backup that has never been restored, or a Git rollback as data recovery.
- F-16 (D12): treating the concrete crypto parameters in this document as frozen requirements without a security-package verification; or an all-client-only / all-server-readable protection model.
- F-17 (D8): treating a known person as an authenticated principal.

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
3. **Canonical store (M2–M3):** M3 as a convergence of V1 semantics and V2 envelope foundations (D11), with layered protection classes (D12); freeze V1 writes after cut-over (sources retained); the prediction/review/revision APIs; multi-generation backups with a verified restore (D10).
4. **Migration (M4):** an experience-oriented, item-level import pipeline (D7) with dry-run + Lea review, then the domain state machine.
5. **Capabilities (M5+):** monitor, Incognito, vision/visual memory, connectors, external AI, working tool (the Hosting Gate is decided here).
6. **Retirement (M6):** per domain, L decision.

The decision register is §0.4. After reconciliation only LDL-08, LDL-09 (retention part) and LDL-10 (narrowed) remain OPEN; none of them blocks M0.

---

## 50. Review log (completeness + adversarial) — v3 run (historical; superseded where §51 says so)

### 50.1 Completeness review (method: AGENT_TASK "At minimum cover" list, checked item by item → section)

| AGENT_TASK domain | § |
|---|---|
| Principles; source-of-truth hierarchy | 1 |
| Identity/continuity | 3 |
| Migration/import | 7 |
| Canonical memory | 4 |
| Evidence/models/relations/predictions/revisions/history | 5 |
| Provenance/conflict | 6 |
| Dual-master/drift | 8 |
| Orchestration | 9 |
| Autonomy/permissions | 11 |
| I/K/G/X/T/P mechanisms | 10 |
| Text | 12 |
| Voice | 13 |
| Idle | 14 |
| Vision/camera | 15 |
| Visual memory | 16 |
| Incognito | 20 |
| Identity/presence/multi-person | 17 |
| Relationships | 18 |
| Appearance | 19 |
| Tools/connectors | 21 |
| External AI | 22 |
| Working tool | 23 |
| Sandbox separation | 24 |
| Authn/authz/session | 25 |
| Endpoint/abuse/cost/rate | 26 |
| Secrets | 27 |
| Privacy/encryption/recovery | 28 |
| Logging without content/CoT | 29 |
| Monitor/telemetry | 30 |
| Data model boundaries | 31 |
| API contracts | 32 |
| Frontend IA | 33 |
| PWA/offline | 34 |
| Workers/queues | 35 |
| Hosting/portability | 36 |
| Git role | 37 |
| Backup/DR | 38 |
| Schema versioning | 39 |
| Testing / security testing / regression | 40 |
| Rollout/rollback | 41 |
| Performance/cost | 42 |
| Accessibility/devices | 33, 43 |
| Degraded modes | 44 |
| Retention/deletion | 45 |
| Retirement criteria | 46 |
| Non-goals/forbidden shortcuts | 47 |

- **Result:** every domain is mapped.
- **Gap found and fixed:** tablet was not named explicitly (§43 corrected).
- **Card fields:** all major subsystem cards carry the status + L decision. Smaller sections (31–47) are policy sections rather than subsystems and state their invariants/status inline.

### 50.2 Adversarial review (method: attack each design claim for contradictions, SPOFs, hidden dual masters, trust assumptions, recovery gaps, unverifiable packages — different from 50.1's checklist method)

| # | Finding | Type | Correction |
|---|---|---|---|
| ADV-1 | Tier-P "user-attested context" (§3/§6.4) contradicted I-API-2/F-1 (no client memory context) | contradiction / trust assumption | Narrow ID-checked `attested_items[]` exception defined in §32 and F-1 |
| ADV-2 | IC as a file (IM-I01) + IC in M3 (§3) = two IC homes | hidden dual master | Single cut-over transition note in §3; IM-M03 carries the migration |
| ADV-3 | `content_hash` HMAC for Tier-P would need server-side plaintext | trust assumption | Tier-P hash is client-side or omitted (§4.1) |
| ADV-4 | The server conversation buffer (IM-S03) would retain Incognito turns | contradiction with §20 | IMPLEMENTATION-MAP IM-C02 acceptance extended: no buffer retention for Incognito |
| ADV-5 | A single Tier-C master key = SPOF for all Tier-C data | SPOF / recovery gap | Offline key backup is required (IM-M01), and the key restore is part of the restore drill (IM-M09 acceptance extended) |
| ADV-6 | A client-side Tier-P key only on one device (iOS IndexedDB eviction) | recovery gap | Recovery code IM-M08 before V2→M3 migration IM-M06 (already sequenced); §43 note |
| ADV-7 | A/ChatGPT continuing to save into the Lea repo after B takes over | hidden dual master | §8 path 1; IM-MG03 includes the separately authorised Lea workshop rule change |
| ADV-8 | V1 writes continuing (work-state) | hidden dual master | IM-S04 (content) + IM-M07 (all V1 writes) |
| ADV-9 | Governance packages (IM-I02, IM-MG03, IM-H01) are not testable by CI | unverifiable package | Each must produce a checklist artefact for C2 (IMPLEMENTATION-MAP §6) |
| ADV-10 | Branch protection cannot be set by agents (API 403) | capability limit | IM-G01 has an explicit L step |
| ADV-11 | UI-only enforcement risk for Incognito, autonomy and limits | trust boundary | Server-side invariants I-IN-1, I-AU-1, I-AB-1; negative tests required |
| ADV-12 | "Independence" could be misread as provider independence | ambiguity | IM-X01 criterion 5; ROOT-CAUSE R-2 |
| ADV-13 | Earlier AUD attributions: the test marker is AUD-22; AUD-25/26 are tests/identifiers | source accuracy | References corrected in §12 and IMPLEMENTATION-MAP |

**Residual (not resolvable in design; carried to C2/L):**
- hosting facts (IM-H00);
- provider API features (TO VERIFY);
- all LDL items (§0.4).

---

## 51. Reconciliation provenance (AGENT_TASK v4, C2 blueprint reconciliation)

**Why this section exists:** Lea's independent C2 review found that several provisional defaults in the v3 artifacts (Claude, commits `39f7331`, `11497e7`) were wrong or needed narrowing. L recorded the outcome as WORKSPACE decisions D6–D12 (AiChat main `6fb71ff`). This document was then corrected. The original v3 text is **not** rewritten as if it had always matched: it is preserved in Git history, and it is summarised here and in the "Original provisional default" column of §0.4.

### 51.1 Corrections by decision

| Decision | Original v3 default (Claude) | C2 finding (Lea review) | Reconciled sections |
|---|---|---|---|
| D6 Incognito | no write; **reads identity core only** | wrong: Incognito must not make Lea forget; it restricts WRITE/LEARN, not KNOW/READ | §0.4, §8 path 6, §9.4, §12, §20, §30, §31, §38, §40, §47 F-11/F-12; IM-C02 |
| D7 Migration | file-based; **PERSONAL/HEALTH/LEGAL excluded by default** | wrong: migration is experience-oriented; sensitive sources are eligible item by item; status and provenance are preserved | §0.4, §4 (status values, `SA`), §7, §18, §47 F-13; IM-MG01/02/04, IM-M10 (new) |
| D8 People | owner + declared guests (principal-centred) | narrowed: known persons without accounts; known person ≠ authenticated user; presence has three states | §0.4, §17, §18, §26, §47 F-17; IM-C06 |
| D9 Monitor | **monitor off by default; 90-day raw retention** | wrong: visible by default during research; visibility ≠ persistence; 90 days is not approved | §0.4, §30, §33, §45; IM-O03, IM-C01 |
| D10 Autonomy/recovery | **write-capable connectors AUS / NUR LESEN**; a single restore drill; Git as backup | wrong: autonomy is risk/recoverability-based; multi-generation backups; restore verification; Git = code-only rollback | §0.4, §11, §21, §37, §38, §41, §46, §47 F-14/F-15; IM-C07, IM-C08, IM-M09 |
| D11 Canonical memory | M3 "on the V2 envelope; V1 frozen legacy" | narrowed: M3 is a convergence of V1 semantics + V2 foundations; no destructive discard; schema/API in package C2 | §0.4, §4, §8 path 5, §31; IM-M02, IM-M03, IM-M07, IM-M10 |
| D12 Protection | two tiers: C server key / P **always client-E2E**, with fixed crypto parameters | narrowed: layered classes by sensitivity/function (C / P-N / P-E2E); crypto parameters are recommendations; recovery includes keys | §0.4, §3, §4, §6.4, §15, §27, §28, §31, §32, §34, §44, §47 F-16; IM-M01, IM-M06, IM-M08 |
| Governance cleanup | LDL-02, 04, 12, 13, 14, 15, 16, 17, 18 presented as open L decisions | reclassified as SETTLED / GOVERNANCE / technical RECOMMENDATION / derivable | §0.4, §23–§24, §35–§37, §45–§46; IMPLEMENTATION-MAP §9 |

ROOT-CAUSE.md §10 records the same provenance for the root-cause analysis.

### 51.2 Remaining genuinely open L decisions

1. **LDL-08:** approval of the initial identity-core v1 content (Lea review + L).
2. **LDL-09 (retention part only):** the raw telemetry persistence retention duration, after IM-O03 delivers a justified proposal.
3. **LDL-10 (narrowed):** whether external-AI consultation may run without per-dialogue confirmation, and which protection classes may ever be shared with a third-party AI.

None of them blocks M0.

### 51.3 v4 completion gate and adversarial pass (performed in this run)

**Gate 1: D1–D12 re-read after the edits.** Each D6–D12 bullet in WORKSPACE was checked against the sections listed in 51.1. D1–D5 references are unchanged (§37, §46, IMPLEMENTATION-MAP §3/§7).

**Gate 2: stale-text search** across all three artifacts. Searched terms:
- `Tier-C`, `Tier-P`, `tier-1/2/3`;
- `LDL-01..07`, `LDL-11..18` outside the register;
- `90`, `AUS`, `guest`, `PERSONAL`, `frozen legacy`, `off by default`, `conservative`, `two-tier`.

Result: remaining occurrences are either the register's "original default" column, this provenance section, IMPLEMENTATION-MAP §11, FACT statements about the current V2 implementation, or the ROADMAP mode name AUS as one selectable mode value. §50 is explicitly marked as the historical v3 log.

**Gate 3: adversarial pass.**

| # | Attack | Result / closure |
|---|---|---|
| ADV4-1 | **Hidden dual master:** V1/V2 kept "until verified transfer" become parallel masters | Closed. §8 path 5 applies: sources are read-only after cut-over (IM-M07 freezes V1 writes; IM-M06 makes V2 read-only), and F-10 forbids dual writes. IM-M10 was added so V1 semantic content has an explicit, verified transfer path rather than lingering as a de-facto master. |
| ADV4-2 | **Hidden dual master:** a restore creates a fork | Closed by §8 path 7 (R3 restore with a target time; no silent merge). |
| ADV4-3 | **Incognito write leakage via the conversation buffer** | Closed. A separate transient buffer, discarded on exit/TTL (§20, I-IN-4). IM-S03 is designed for it. |
| ADV4-4 | **Incognito leakage via backups** | Closed. I-IN-5 and §38 exclude the buffer; the IM-M09 backup-exclusion test and the IM-C02 acceptance check it. |
| ADV4-5 | **Incognito leakage via telemetry/operational logs** | Closed. Only content-free counters persist (I-MO-3); operational logs are content-free by I-LG-2. |
| ADV4-6 | **Incognito leakage via jobs** | Closed. I-IN-1 rejects job enqueue; IM-C14 forbids jobs from Incognito conversations. |
| ADV4-7 | **Incognito leakage via read-side effects** (access counters, salience) | Closed. I-IN-3 and F-11; the IM-C02 no-side-effect test. |
| ADV4-8 | **Incognito leakage on the provider side** | **Residual:** B cannot control third-party provider retention. It is TO VERIFY and shown honestly (§20); it is not claimed as closed. |
| ADV4-9 | **Incognito over-restriction** (the old wrong default reappearing) | Closed. F-12; the read-equality test in IM-C02; the §40 behavioural test. |
| ADV4-10 | **File-based migration exclusion or inclusion** | Closed. §7 I-MG-5, F-13, IM-MG01 acceptance (per-item status + class; sections not imported are listed with a reason). No filename rule remains in §7 or IM-MG01. |
| ADV4-11 | **Blanket write-disable** reappearing via connector defaults | Closed. I-CN-1 (unconfigured ≠ disabled class; grants per capability), F-14, IM-C08 acceptance. |
| ADV4-12 | **The opposite failure:** autonomy without recovery | Closed. I-AU-4 caps capabilities without a verified recovery at NACHFRAGEN; IM-C07/IM-C08 depend on IM-M09. |
| ADV4-13 | **Unverified crypto frozen as a requirement** | Closed. §28 marks all algorithms, KDF and parameters as RECOMMENDATION / FACT-about-V2. F-16; IM-M01/IM-M08/IM-S07 route crypto choices to security-package review. |
| ADV4-14 | **Untested backup claims** | Closed. I-BK-1 (a never-restored backup type is not a backup), F-15, and IM-M09 acceptance (older generation, keys from offline backup). Repeat triggers: before IM-MG02, before autonomy expansion, within 30 days of IM-X02. |
| ADV4-15 | **Key loss makes backups useless** | Closed in principle: keys are in the recovery scope (§28, §38). P-E2E loss of both passphrase and recovery code is documented as unrecoverable (an honest limitation). |
| ADV4-16 | **P-N leakage to a declared known person** | Closed by §17 R-P2 / I-PR-3 and the IM-C06 acceptance. |
| ADV4-17 | **Retention silently re-introduced as a decided value** | Closed. §45 marks all durations as proposals; telemetry has no default; IM-O03 must deliver a justification; IM-C15 forbids a telemetry TTL before LDL-09. |

**Gate 4: package dependencies and gates.** IMPLEMENTATION-MAP §2, §5 and §7 were updated:
- IM-M09 is now a prerequisite of IM-C02, IM-C07, IM-C08, IM-C15, IM-M06, IM-M10 and IM-MG02;
- IM-M10 was added;
- IM-O03 is a prerequisite of IM-C02;
- autonomy-scope expansion is an explicit L gate with restore evidence;
- the open decisions are mapped to their packages (IM-I02, IM-O03, IM-C09).

**Gate 5: remaining open L decisions:** exactly the three items in 51.2.

**Gate 6:** no PASS is claimed for the project or for any part of it. This is document reconciliation for C2 review. The residual ADV4-8 and all TO VERIFY items remain open for implementation-time verification.
