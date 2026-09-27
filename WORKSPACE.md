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
