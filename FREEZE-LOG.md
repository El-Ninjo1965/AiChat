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
