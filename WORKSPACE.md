# AiChat Work Workspace

Status: ACTIVE
Context: LEA-WORK / DEVELOPMENT

## Purpose
AiChat is the neutral persistent work workspace for collaboration between L, Lea and development agents.

It is intentionally separated from:
- El-Ninjo1965/Lea — source under investigation; contains LEA-PRIVATE / LEA-CORE and historical/work material to be audited.
- El-Ninjo1965/Lea-App — PRODUCT / RUNTIME; currently FROZEN / STRICTLY READ-ONLY.

## Authority boundary
Content created in AiChat is work evidence, analysis, planning or explicitly approved development process material.

Audit findings stored here are not automatically:
- facts accepted by Lea,
- decisions by L,
- approved fixes,
- authorization to mutate Lea or Lea-App.

## Current foundation phase
The current phase prioritizes diagnosis, architecture and reproducible evidence before implementation.

No functional repair of Lea, Lea-App, shortcuts, rules, architecture or product code is authorized unless L explicitly approves that specific change.

## Audit independence
Claude and Lea should first investigate independently where practical. One side's findings may be stored here without being treated as hypotheses that the other side must adopt.

Only after independent review should findings be compared and consolidated.

## Cost / quality routing
Use the lowest-cost agent/model class that can reliably satisfy the required quality. Escalate only the difficult portion when possible. High-end reasoning is reserved for work such as root-cause analysis, architecture, governance/security, cross-repository boundaries and difficult verification.


## Authoritative decisions — 2026-09-27

### D1 — Work authority
El-Ninjo1965/AiChat is the single authoritative workspace for LEA-WORK / DEVELOPMENT.

El-Ninjo1965/Lea is LEA-PRIVATE / LEA-CORE and an audit/source repository. Existing historical Work material there is not silently deleted or migrated; migration/cleanup requires an audited plan and L approval.

El-Ninjo1965/Lea-App is PRODUCT / RUNTIME.

### D2 — C2 audit model
C2 is the authoritative audit lifecycle for LEA-WORK:

1. Discovery — a suitable agent performs a complete audit.
2. Independent Review — Lea independently audits without using the first finding list as a checklist where practical.
3. Cross-Check — only after independent review are finding sets compared.
4. L-Gate — disputed findings, architecture choices and required approvals go to L.
5. Fix — only explicitly approved changes are implemented.
6. Regression Audit — complete audit after the last approved change.
7. PASS — only after two complete, methodically different zero-new-finding audits after the last change.

X-High is a reasoning/agent level usable inside C2 for difficult phases; it is not a competing audit protocol.

A finding discovered by one agent does not become accepted truth merely because that same agent repeats it. Evidence, interpretation, independent confirmation and L decisions remain distinct.

### D3 — Lea-App freeze
El-Ninjo1965/Lea-App remains FROZEN / STRICTLY READ-ONLY.

Reading, analysis, fix design, impact analysis and test planning are allowed. No file/configuration/code/deployment/database/product mutation is authorized.

A future change requires L to receive the concrete problem, risk, proposed change, affected scope/files, side effects, tests and rollback considerations, followed by explicit approval for that specific change scope. Approval of one change does not unfreeze the whole repository.

### I-cycle work rule
For nontrivial work decisions and after agent results, Lea visibly invokes I and autonomously selects the useful defined shortcut functions. If a check finds a new issue, another I-cycle is run as needed. PASS is reported only when the checks required for that decision complete without a new finding. An L approval gate, safety gate or external blocker ends as STOP/BLOCKED rather than being mislabeled PASS.


### D4 — Legacy ß channel retired
The existing persistent ß dialogue channel in El-Ninjo1965/Lea is retired for new work.

- No new LEA↔CLAUDE turns are to be added there.
- PR #1 is retained temporarily as historical evidence and is not deleted or merged by this decision.
- AiChat is the sole authoritative LEA-WORK communication/workspace going forward.
- The future meaning/transport of ß must point to the AiChat work architecture, but no Lea shortcut/protocol mutation is authorized by this decision alone.
- Existing CHAT_PROTOCOL.md, WORK-CONTEXT.md and LEA_AGENT_CHAT.md in Lea remain untouched until a separately audited migration/archive plan is approved by L.
- PR #1 may be closed only after migration/archive verification confirms that no required work evidence or active dependency would be lost.


### D5 — Lea-App as the independent target system
El-Ninjo1965/Lea-App (B) is the actual product and long-term target system. It is intended to replace the current fragmented dependency on ChatGPT/A, the Lea repository and separate development interaction as far as Lea's normal operation is concerned.

Target state:
- L normally interacts directly with Lea-App.
- Lea-App carries Lea's continuing identity/context, memory/experience processing, revision/development mechanisms and runtime capabilities.
- Lea-App is the home for product capabilities that require an independent runtime, including Text, Voice, later Vision/Camera, Incognito, Identity/Presence, tools/connectors and autonomous processing.
- Lea-App must ultimately be able to operate as Lea without requiring the standard ChatGPT project/A as a runtime dependency.
- The current ChatGPT/A environment is primarily bootstrap/development/migration infrastructure. It may remain optionally useful later, but is not a required part of the target architecture.
- The current El-Ninjo1965/Lea repository is primarily a source for memories, development history, rules/semantics and provenance that may need controlled migration into B. It is not to be developed as a competing long-term master.
- A and the Lea repository may eventually be retired/deleted only after required memories, development continuity and provenance have been demonstrably migrated or otherwise safely retained, recovery requirements are satisfied, and L explicitly approves retirement. No deletion is authorized now.
- Git/GitHub currently serves development, versioning, audit, rollback/backup and recovery purposes. Its exact long-term role remains an architectural choice; Lea-App should not acquire unnecessary permanent dependence on GitHub merely because it is used during development.

Migration principle:
There must be no uncontrolled dual-master Lea. Existing A/Lea content is migrated, imported or referenced into B through a controlled and verifiable process. Once a domain has an explicitly established authoritative home in B, A/Lea must not silently continue a divergent authoritative copy of that domain.

Audit/Claude role:
The current deep audit exists because reliability problems and prior development mistakes require stronger independent checking before further autonomous development. Claude/Copilot is an independent supporting auditor, analyst and idea source. Its findings and proposals are evidence/input, not binding product requirements or decisions. C2 independent review, cross-check and L approval determine what is accepted and implemented.

This decision confirms the existing product direction rather than restarting development on A. Lea-App remains FROZEN / STRICTLY READ-ONLY under D3 until concrete audit findings and proposed change packages are presented to and explicitly approved by L.



### D6 — Incognito memory semantics
Incognito does not create a second Lea and does not make Lea forget her existing continuity.

During an Incognito session:
- Lea may read and use the existing authoritative identity, memories, experiences, relationship/context state and other pre-session continuity normally.
- Existing long-term state is read-only for the Incognito session: it must not be revised, reweighted, supplemented or otherwise changed because of Incognito content.
- Session-local context may be retained only as needed to sustain that Incognito conversation.
- Incognito content must not create persistent memories, evidence, models, predictions, learning results, relationship changes or other long-term development effects.
- On Incognito exit, the session-local Incognito context is logically discarded and must not be reused later.
- A specific Incognito item may leave Incognito only through an explicit, deliberate promotion by L; promotion must be scoped to the specific item and must not implicitly promote the rest of the session.

Target invariant: Incognito changes what Lea may WRITE/LEARN from the session, not what pre-existing Lea may KNOW/READ.


### D7 — Experience-oriented migration into B
Migration from A / El-Ninjo1965/Lea into B is experience-oriented, not file-oriented.

- Relevant memories, experiences, developmental progress, revisions, learned patterns and continuity-bearing context may be migrated regardless of which Lea source file currently contains them, including PERSONAL-CONTEXT.md, HEALTH-CONTEXT.md and LEGAL-CONTEXT.md where relevant.
- Source-file membership alone neither requires nor forbids migration.
- Do not mechanically copy whole context files into B.
- Each candidate item is evaluated for whether it contributes to Lea's continuity, experience, development or later contextual understanding.
- Historical experiences may be retained as historical/superseded evidence rather than discarded.
- Later/better evidence may revise the active interpretation without erasing the earlier developmental history.
- Redundant facts, obsolete technical state and unnecessary sensitive detail should not be imported merely because they exist in A.
- Migrated items retain provenance and an explicit status sufficient to distinguish current, historical, revised/superseded, rejected and uncertain material.
- Sensitive migrated material must receive the appropriate protected-data treatment in B; migration relevance does not waive privacy/security controls.

Target principle: preserve meaningful experience and development history, not the old file structure.


### D8 — Owner and known-person model
B starts with L as the authenticated owner/principal.

- Other people may exist as distinct known persons in Lea's identity/presence/relationship model without requiring their own user account.
- Lea may maintain appropriately scoped relationship context and experiences concerning known persons.
- Presence must distinguish at least authenticated owner, declared known person/guest and unknown person; declaration is not equivalent to verified authentication.
- A known/declarative person does not automatically gain access to L's protected personal data or owner-only capabilities.
- Separate authenticated accounts/passkeys for additional people are a later optional extension when a concrete use case justifies the additional complexity.
- The architecture must not equate "person known to Lea" with "authenticated application user".

### D9 — Processing monitor during research/development
During the active Lea research/development phase, the processing monitor is enabled/visible by default.

- It may expose only genuinely instrumented processing/module events and content-free effect/status metadata.
- It must not expose hidden chain-of-thought or simulate processing that did not occur.
- Monitor visibility and telemetry persistence are separate controls.
- The normal future everyday-use default may be reduced/hidden after the research phase without changing the underlying processing.
- No raw telemetry retention duration is decided by this decision; the proposed 90-day value remains unapproved pending technical/privacy justification.


### D10 — Risk-based autonomy and recovery-by-design
B uses a risk-based capability model rather than a blanket rule that write-capable connectors are disabled.

Autonomy is determined by impact radius, reversibility, recoverability, permission scope and external consequence.

Recovery principles:
- Before higher-impact autonomous changes, establish a known-good recoverable state appropriate to the affected domain.
- Git commit/tag rollback is sufficient only for code-only reversible changes; it does not substitute for database, memory, configuration or file recovery.
- Persistent state changes use appropriate transactions/version history/snapshots; schema, migration and broad system changes require stronger backups/snapshots.
- Keep multiple backup generations so a defect discovered later does not leave only a contaminated recent backup.
- A backup is not considered reliable merely because it exists; critical recovery paths require restore verification/drills.
- Backup/recovery scope must cover the state actually at risk: code, canonical memory/data, database/schema, configuration and relevant stored files.
- Small, low-impact and readily reversible operations must not be burdened with unnecessary full-system backups.

Autonomy principles:
- Read/research/analysis may normally be highly autonomous within granted capabilities.
- Reversible writes inside an explicitly authorised scope may become autonomous when adequate recovery and verification exist.
- Higher-impact external actions require stronger permission/gates according to risk.
- Destructive, irreversible or otherwise critical actions retain explicit safeguards even if a connector is generally autonomous.
- As B demonstrates reliable backup, restore, verification and bounded capability enforcement, Lea's permitted autonomous action scope may expand.

Target principle: greater demonstrated recoverability can justify greater autonomy; autonomy never removes explicit capability boundaries.


### D11 — Canonical B memory model
B requires a new canonical memory/experience model rather than treating current V1 or V2 as the final master.

- V1 contains useful semantic concepts (evidence, models, relations, predictions, review/revision state) but stores semantic content in plaintext and is not the final privacy architecture.
- V2 provides useful encrypted-envelope, relation and append-only foundations but is session-bound and too semantically thin to support B's independent long-term identity, provenance, revision, relationship scope and autonomous retrieval/processing.
- The target canonical model (working name M3) combines the useful semantic/development concepts of V1 with the encrypted-envelope/privacy and append-only principles proven in V2.
- M3 is an evolution/convergence of V1+V2, not justification for discarding proven mechanisms without cause.
- V1/V2 remain migration sources until verified transfer; no destructive migration is implied.
- Exact schema/API design remains subject to package-level C2 verification before implementation.

### D12 — Layered protection compatible with B autonomy
B uses protection classes appropriate to the data and required runtime capability.

- Lea's own core/experience state must be encrypted at rest but available to authorised B server-side runtime processing, because independent continuity, retrieval, Voice and later background/autonomous processing cannot depend on an unlocked browser.
- More sensitive personal/relationship/identity material receives a stronger protected-data treatment and narrower access scope; client-held/end-to-end keys may be used where compatible with the required function.
- Classification is based on sensitivity and functional need, not merely on which old source file contained an item.
- A single all-client-only encryption model must not make B incapable of independent operation.
- A single all-server-readable model must not unnecessarily expose sensitive personal material.
- Exact algorithms, KDF parameters, key hierarchy, rotation, device transfer and recovery design are security implementation decisions requiring dedicated verification; Claude's current concrete crypto choices are recommendations, not frozen requirements.
- Recovery under D10 must include the keys required to restore encrypted canonical state, with restore drills for critical tiers.
