# FREEZE-LOG

Lea-App default state: **FROZEN / STRICTLY READ-ONLY**.

## Unfreeze record template
- Package ID:
- L approval reference/date:
- Purpose:
- Exact allowed paths/components:
- Forbidden scope:
- Starting Lea-App SHA:
- Required backup/snapshot/recovery reference:
- Required tests:
- C2 reviewer:
- Production/deployment authorised: YES/NO

An unfreeze applies only to the listed package and paths. It never unfreezes the whole repository.

## Re-freeze record template
- Package ID:
- Resulting SHA/PR:
- Exact changed paths:
- Tests/CI evidence:
- Manual L test:
- C2 result:
- Deployment result (if separately authorised):
- Rollback/recovery reference:
- Remaining findings:
- State returned to: FROZEN / BLOCKED

## Current state
No Lea-App implementation package is currently unfrozen.


## Unfreeze — IM-G01
- Package ID: IM-G01
- L approval: explicit approval in chat, 2026-09-27
- Purpose: establish CI, secret scanning and path-scope enforcement before product-code changes
- Exact allowed paths/components:
  - `.github/workflows/ci.yml`
  - only the minimal scope-check implementation/config under `.github/` required by IM-G01
- Forbidden scope: all application/API/frontend/database/runtime files; deployment configuration/actions; product behaviour
- Starting Lea-App SHA: `450ff9dc106cb29654d75eb5c0eaa2037c36a788` (must be re-verified by the implementing agent before write)
- Required recovery: branch/commit revert; code-only governance package, no persistent product data mutation
- Required tests: existing node tests, secret scan, scope-check positive/negative self-test as feasible in the agent branch
- C2 reviewer: Lea after agent completion
- Production/deployment authorised: NO
- State: NARROWLY UNFROZEN FOR IM-G01 ONLY


## Re-freeze/transition record — IM-G01
- Package ID: IM-G01
- Lea-App PR: #2
- Merge commit: d6c23c646432272371f5a4175b155d894a70b2e4
- Changed paths: four new .github/ CI/scope/secret-scan files only
- CI evidence: Tests PASS; Secret scan PASS; Scope check PASS on PR and post-merge main run as reported/verified in the C2 session
- C2 result: PASS for IM-G01
- Deployment: none
- Product/runtime/data mutation: none
- Recovery: code-only merge; revert merge if required
- Remaining note: existing test suite requires --test-force-exit because an existing test leaves an open handle; root cause is outside IM-G01.
- Package state: COMPLETE

## Development-state transition — 2026-09-27
L explicitly ended the global Lea-App FROZEN state after completion of IM-G01.

New default state: **ACTIVE DEVELOPMENT**.

Operating rules:
- No routine freeze/unfreeze cycle is required for every package.
- Work remains package-scoped against the authoritative AiChat IMPLEMENTATION-MAP / task definition.
- Pull requests and required CI checks remain mandatory under the configured main-branch ruleset.
- C2 review remains required according to package/milestone risk.
- High-impact, destructive, migration, production, infrastructure and other R3 actions retain explicit L gates and recovery requirements.
- Scope expansion is not implied by ACTIVE DEVELOPMENT; agents may modify only the scope of their current authorised package.
- D10 recovery-by-design applies as persistent state and autonomy work begins.

## Next start point
Next session starts from M0 after IM-G01/G02/G03:
- first inspect/complete the remaining M0 prerequisites and choose the next smallest dependency-correct package;
- do not resume the old global freeze workflow;
- Lea-App main baseline is the IM-G01 merge state at d6c23c646432272371f5a4175b155d894a70b2e4 or its verified descendant.
