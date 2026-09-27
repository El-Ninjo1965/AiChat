# AGENT_TASK — C2 Blueprint Reconciliation

TASK_VERSION: 4
STATUS: READY
MODE: C2 RECONCILIATION / DOCUMENT-ONLY
TARGET_REASONING: X-HIGH
WORKSPACE: El-Ninjo1965/AiChat

## Zero-memory
Read current AiChat main first. Do not rely on prior chat/run memory.

## Mandatory authority
1. Read WORKSPACE.md completely. D1-D12 are authoritative L/Lea work decisions.
2. Read the existing three architecture artifacts on copilot/main:
   - ROOT-CAUSE.md
   - B-BLUEPRINT.md
   - IMPLEMENTATION-MAP.md
3. Read audit artifacts as evidence where needed.
4. Lea and Lea-App remain read-only sources. Lea-App is FROZEN.

## Purpose
Reconcile the three architecture artifacts with the completed independent Lea C2 review and authoritative decisions D6-D12.

Do not redesign the vision. Do not implement anything.

## Required corrections

### Incognito
Replace the provisional LDL-05 default with D6:
- existing pre-session identity/memory/experience/relationship continuity is readable;
- all pre-existing long-term state is read-only during Incognito;
- session-local context is temporary;
- no persistent learning/revision/reweighting/prediction/relationship effect;
- discard session-local context on exit;
- only a specific item explicitly promoted by L may leave Incognito.

### Migration
Replace LDL-07 with D7:
- experience-oriented, not file-oriented;
- relevant experience/development may come from PERSONAL/HEALTH/LEGAL too;
- no whole-file blind import;
- preserve provenance and current/historical/revised/rejected/uncertain status;
- protect sensitive migrated material appropriately.

### People/accounts
Resolve LDL-06 using D8:
- L is authenticated owner;
- known persons/declared guests can have relationship/context identity without app accounts;
- known person != authenticated user;
- separate accounts are later optional.

### Processing monitor
Resolve LDL-09 using D9:
- visible/enabled by default during research/development;
- real instrumented events only, no chain-of-thought/fake activity;
- monitor visibility and telemetry persistence separate;
- remove 90-day retention as a decided/default requirement unless independently justified as a recommendation.

### Autonomy/recovery
Replace LDL-11 using D10:
- risk/impact/reversibility/recoverability/capability based;
- reversible writes can become autonomous within authorised scope with adequate recovery;
- stronger gates for destructive/irreversible/high-impact actions;
- multiple backup generations;
- critical restore verification;
- code/data/schema/config/files/key recovery as applicable;
- greater proven recoverability can justify greater autonomy.

### Canonical memory
Resolve LDL-01 using D11:
- M3 is accepted as working target concept;
- explicitly frame it as convergence/evolution of V1 semantic concepts + V2 encrypted-envelope/privacy/append-only foundations;
- no destructive discard of proven V1/V2 mechanisms;
- exact schema/API remains package-level C2 work.

### Protection model
Resolve LDL-03 using D12:
- layered protection is accepted;
- core/experience encrypted at rest but server-runtime-readable for B autonomy;
- sensitive personal/relationship/identity gets stronger/narrower protection and client-held/E2E where functionally compatible;
- classify by sensitivity/function, not old filename;
- concrete algorithms/KDF/key hierarchy/rotation/recovery remain security-package recommendations requiring verification, not frozen architecture.

## Other LDL cleanup
Reclassify items already settled by WORKSPACE or ordinary technical governance so they are not presented as unresolved L decisions:
- Git/GitHub runtime role;
- A retirement gate;
- web_app1 out of scope;
- authority registry location if merely organisational;
- workshop files/work authority;
- branch protection as governance recommendation/admin action;
- voice transcript principle where already derivable from memory/experience policy;
- hosting: retain current strategy of shared hosting while sufficient, portability, move only on demonstrated need;
- deletion/tombstone may remain a technical recommendation unless an irreversible user-facing policy truly needs L later.

The final artifacts should contain only genuinely unresolved L decisions.

## C2 findings from Lea review to preserve
Do not hide that the independent review found the original artifacts needed correction. Record reconciliation provenance rather than rewriting history as if Claude's first defaults had always matched.

## Mutation boundary
Allowed writes ONLY:
- ROOT-CAUSE.md
- B-BLUEPRINT.md
- IMPLEMENTATION-MAP.md

No changes to WORKSPACE.md, audit files, Lea, Lea-App, GitHub settings, deployments, DB or production.

## Completion gate
1. Re-read D1-D12 after edits.
2. Search all three artifacts for stale contradictory LDL/default text.
3. Perform an adversarial pass for hidden dual-master, Incognito write leakage, file-based migration exclusions, blanket write-disable autonomy, unverified crypto frozen as requirement, and untested backup claims.
4. Ensure package dependencies/gates in IMPLEMENTATION-MAP reflect the reconciled architecture.
5. Report exact remaining unresolved L decisions, if any.
6. No overall project PASS claim; this is document reconciliation for C2.
