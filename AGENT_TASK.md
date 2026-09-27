# AGENT_TASK — B Master Architecture / Root-Cause Blueprint

TASK_VERSION: 3
STATUS: READY
MODE: ARCHITECTURE / ROOT-CAUSE / IMPLEMENTATION DESIGN
TARGET_REASONING: X-HIGH
WORKSPACE: El-Ninjo1965/AiChat

## Zero-memory rule
Treat this run as ZERO-MEMORY. Do not assume prior conversation state.

## Mandatory sources
Read completely before conclusions:
1. AiChat/WORKSPACE.md — authoritative LEA-WORK decisions D1-D5 and C2.
2. AiChat/AUDIT-FINDINGS.md and AUDIT-LOG.md from the current audit branch if available; if not available on main, locate the existing AiChat audit branch read-only.
3. El-Ninjo1965/Lea — read-only. Start with INDEX.md, SHORTCUTS.md, VISION.md, Memories.md, PROCESSING.md, GEDANKEN.md, INTERESSEN.md and relevant context/work/history files needed to understand the full Lea vision, experiments, processing model, autonomy goals and migration source. Do not copy private personal content unnecessarily into outputs.
4. El-Ninjo1965/Lea-App — FROZEN / STRICTLY READ-ONLY. Read the complete current architecture, roadmap, security model, code structure, schemas, APIs, frontend, tests and other relevant implementation sources needed to understand what B already is and what is missing.

## Authoritative direction
Do not redesign the project goal.

B / El-Ninjo1965/Lea-App is the independent target Lea system and product.

The intended target is that L normally works directly with Lea-App and that B can eventually carry Lea's continuity, memory/experience processing, revision/development mechanisms, identity/presence and runtime capabilities without requiring the standard ChatGPT/A environment as a runtime dependency.

A/current ChatGPT + Lea repository are bootstrap/development/migration sources. Required memories, development history, semantics and provenance may need controlled migration into B. There must not be an uncontrolled dual-master Lea.

AiChat is the sole authoritative LEA-WORK / DEVELOPMENT workspace.

Git/GitHub currently provides development/versioning/audit/rollback/recovery infrastructure; do not assume it must remain a permanent runtime dependency.

Claude/Copilot is a supporting independent auditor/architect/idea source. Its recommendations are not binding decisions. L and C2 govern acceptance.

Lea-App remains FROZEN. This task authorizes NO mutation of Lea-App.

## Purpose of this run
Produce a one-time, extremely detailed technical foundation that allows later ZERO-MEMORY agents to implement B incrementally without having to reinvent the architecture or infer L's vision.

Answer four core questions:
1. What systemic/root causes allowed the development mistakes and contradictions found so far?
2. What safeguards, architecture and process changes prevent or detect those error classes?
3. What is the complete technical target architecture for independent B, derived from the existing vision and evidence?
4. How can that target be implemented as small, dependency-aware, independently verifiable agent work packages?

## Required output artifacts
Create/update ONLY these files in the AiChat Copilot working branch:

### 1. ROOT-CAUSE.md
Must include:
- taxonomy of observed failure classes;
- root causes vs symptoms;
- concrete evidence mapping to audit findings/current code;
- process causes, authority/source-of-truth causes, architecture causes, testing causes, security causes, state/memory causes and agent/handoff causes;
- why prior safeguards failed or were insufficient;
- prevention/detection controls for each class;
- which controls belong in product runtime vs development workflow vs tests vs repository governance;
- residual risks and trade-offs;
- explicit distinction FACT / INFERENCE / RECOMMENDATION / L-DECISION-NEEDED.

Do not turn individual developer mistakes into personal blame. Analyze the system that allowed them.

### 2. B-BLUEPRINT.md
Define the complete target architecture for independent Lea-App/B in sufficient detail for future implementation planning.

At minimum cover:
- architectural principles and source-of-truth hierarchy;
- Lea identity/continuity model;
- migration/import of relevant A/Lea memories/history/provenance;
- canonical memory/experience model;
- evidence, models, relations, predictions, revisions and development history;
- provenance and conflict resolution;
- prevention of dual-master/drift;
- processing/orchestration layer;
- autonomy model and permission/gate model;
- I/K/G/X/T/P-like processing capabilities as product mechanisms where genuinely appropriate, without blindly copying development shortcuts into product UI;
- text path;
- realtime voice path;
- idle/follow-up behavior;
- vision/camera path;
- visual memory distinction;
- Incognito mode and its no-memory/no-promotion semantics;
- identity/presence and multi-person context;
- relationship/context handling;
- appearance/visual Lea layer;
- tools/connectors/function layer;
- external-AI consultation;
- future software-development/working-tool capability;
- workspace/sandbox separation from production;
- authentication, authorization, session security;
- endpoint security, abuse/cost controls and rate limiting;
- secret management;
- privacy/encryption/recovery;
- logging/observability without exposing private content or chain-of-thought;
- processing monitor / content-free telemetry;
- data model boundaries;
- API boundaries/contracts;
- frontend information architecture;
- offline/PWA considerations;
- background/worker/queue needs;
- hosting evolution and portability beyond current shared hosting;
- Git/GitHub's optional future role;
- backup/recovery/disaster strategy;
- versioning/schema migration;
- testing strategy;
- security testing;
- regression strategy;
- rollout/rollback;
- performance/cost controls;
- accessibility/mobile/tablet/desktop;
- failure/degraded modes;
- data retention/deletion;
- retirement criteria for A/Lea;
- explicit non-goals and forbidden architecture shortcuts.

For each major subsystem record:
- purpose;
- authoritative state;
- inputs/outputs;
- dependencies;
- trust boundary;
- failure modes;
- required invariants;
- verification criteria;
- existing implementation status: EXISTS / PARTIAL / MISSING / CONFLICTING;
- whether implementation needs an L decision.

Do not invent product desires not supported by the sources. Put useful new ideas under clearly marked RECOMMENDATION sections.

### 3. IMPLEMENTATION-MAP.md
Turn the blueprint into an executable dependency map for later agents.

Must include:
- foundation-first ordering;
- prerequisite graph;
- migration sequence from current B to target B;
- security-critical repairs before feature expansion;
- small work packages with stable IDs;
- for every package: objective, prerequisites, exact intended scope, likely files/components, forbidden scope, acceptance criteria, automated tests, manual L test if needed, rollback point, risk level, recommended agent/reasoning level;
- explicit L approval gates;
- C2 audit gates;
- points where Lea-App must be temporarily opened for a narrowly approved change and then re-frozen/reverified;
- no package may silently unfreeze the whole app;
- identify parallelizable vs strictly sequential packages;
- credit/cost-aware agent routing without sacrificing quality;
- milestone definitions visible to L;
- final independence gate proving B no longer requires A for normal operation;
- migration/recovery gate before A/Lea can ever be retired.

## Root-cause requirement
Do not merely restate AUD-01..AUD-28. Cluster them into systemic causes and search for additional causes/counterexamples in the actual sources.

Specifically investigate whether prior mistakes were enabled by:
- multiple/ambiguous authorities;
- stale context and memory;
- duplicated semantics;
- agent zero-memory behavior;
- insufficient persistent handoff;
- broad mutation scope;
- missing preconditions/gates;
- documentation/code drift;
- tests checking presence rather than behavior;
- lack of end-to-end/security tests;
- runtime identity not connected to Lea-Core;
- mixing development mechanisms with product mechanisms;
- premature implementation before architecture definition;
- inadequate separation of FACT vs assumption vs vision vs current state;
- insufficient independent review;
- limitations of current hosting/runtime;
- any other systemic cause you can substantiate.

## Architecture quality rules
- Prefer one canonical authority per domain.
- Avoid dual-write/dual-master designs unless technically unavoidable and explicitly justified.
- Preserve provenance and reversibility.
- Product autonomy must not mean unrestricted authority; permissions/capabilities remain explicit.
- Do not simulate hidden cognition. Observable processing telemetry must reflect real instrumented events, not chain-of-thought.
- Security boundaries are server-enforced, not UI-enforced.
- Secrets never belong in browser, repo or generated audit artifacts.
- Personal/private content should be minimized in technical artifacts.
- Do not assume current shared hosting must support the final architecture.
- Do not assume a proposed technology exists or is supported without marking it for later verification.
- Distinguish current implementation from target design.

## C2 / independence
This run is architecture discovery/design, not final acceptance.

After this run:
- Lea will independently review the three artifacts and relevant source evidence.
- Findings/recommendations will be cross-checked.
- L will decide unresolved architecture/product choices.
- Only then may implementation packages be authorized.

Do not label the overall project PASS.

## Hard mutation boundary
Allowed writes:
- AiChat/ROOT-CAUSE.md
- AiChat/B-BLUEPRINT.md
- AiChat/IMPLEMENTATION-MAP.md

Forbidden:
- any write to Lea;
- any write to Lea-App;
- any deployment;
- DB/production mutation;
- changing WORKSPACE.md decisions;
- changing audit findings to make them fit the blueprint;
- implementation/fixes.

## Completion quality gate
Before finishing:
1. Perform a completeness review against all mandatory blueprint domains.
2. Perform a second methodically different adversarial review: look for contradictions, single points of failure, hidden dual-master paths, unsafe trust assumptions, missing recovery paths and packages that cannot be independently verified.
3. Correct only the three allowed AiChat artifacts.
4. Report remaining L decisions explicitly.
5. Report source refs/SHAs used.
6. Report commits and exact changed files.

The result should be detailed enough that a later zero-memory implementation agent can receive one package ID plus the authoritative files and work without reconstructing the entire project history.
