# AGENT_TASK — IM-T01 Behavioural/API Test Harness

TASK_VERSION: 6
STATUS: READY
MODE: IMPLEMENTATION PACKAGE DESIGN/EXECUTION
TARGET_REPO: El-Ninjo1965/Lea-App
PACKAGE: IM-T01

## Authority
Read current AiChat main first:
- WORKSPACE.md (D1-D16)
- IMPLEMENTATION-MAP.md, package IM-T01
- B-BLUEPRINT.md relevant testing/security/API sections
- AUDIT-FINDINGS.md relevant AUD-22/23/24/25 and OBS references

Then work directly in Lea-App from current main. Expected baseline at task preparation:
d6c23c646432272371f5a4175b155d894a70b2e4
If main has moved, re-read the diff and stop if it changes IM-T01 assumptions.

Lea repository is read-only context only if needed.

## Goal
Implement IM-T01: a behavioural/API test harness that exercises the existing PHP endpoints against an isolated test database/provider mock and documents current behaviour before security fixes.

This package must make later IM-S01..S04 changes safely testable. It must not fix those defects now.

## Required preflight
1. Read Lea-App AGENTS.md and applicable repo rules.
2. Inventory current routes from .htaccess and current tests.
3. Determine PHP version/requirements available in CI without changing production.
4. Inspect migrations 001/002 and endpoint configuration dependencies.
5. Confirm how to provide an isolated MySQL/MariaDB CI service and a local provider mock.
6. Confirm current CI from IM-G01 is green.
7. Identify exact path scope needed before writing; keep it minimal.

## Scope
Expected/allowed categories:
- tests/php/** (new)
- tests/fixtures/** (new if needed)
- test-only provider mock/stub files under tests/**
- .github/workflows/ci.yml only as required to run the harness
- minimal testability/config hook in api/text/chat.php and/or api/realtime/session.php or a shared helper ONLY if the current code has no safe provider-base override for tests

Any production-code hook must:
- change no normal production behaviour;
- default to the current production provider URL/behaviour;
- be server-side;
- not expose secrets;
- be justified in the result.

Do not broaden into frontend/UI work.

## Forbidden
- no fix for AUD-22/23/24/25 in this package;
- no authentication behaviour change;
- no prompt/persona change;
- no memory schema redesign;
- no M3;
- no D13 UI implementation;
- no Voice reconnect D14 implementation;
- no deployment/production mutation;
- no production secrets/data.

## Required behaviour coverage
At minimum:
- route inventory derived from .htaccess, with an explicit classification of tested/public/non-public where determinable;
- at least one HTTP behavioural test for each existing application/API route that can be safely exercised in the isolated harness;
- chat provider mock records whether/what provider call occurred;
- realtime provider mock records whether/what provider call occurred;
- isolated DB is initialized from migrations 001 and 002 where applicable;
- current known defects are represented as explicit expected-current-behaviour / expected-fail evidence linked to the relevant AUD/OBS IDs rather than silently fixed;
- tests fail clearly if a route is added but omitted from the harness inventory.

## CI
Extend existing IM-G01 CI rather than replacing its protections.
Preserve:
- existing Node tests;
- Secret scan;
- Scope check;
- no deployment.

The PHP/API harness must run in CI using isolated test resources only.

## Existing test open-handle issue
IM-G01 documented that tests/memory-v2-client.test.js leaves an open handle and CI currently uses --test-force-exit. Do not silently hide or expand this issue. IM-T01 may leave the existing workaround in place; fixing the root cause requires a separate package unless the cause is inside the exact new harness scope and the fix is trivial/non-behavioural.

## D13-D16
These decisions were recorded after IM-G01. They are NOT implementation scope for IM-T01. Avoid touching frontend layout, shortcut UI, learning modes, multimodal perception or Voice continuity.

## Completion
Before finishing:
1. run existing Node tests;
2. run new harness locally in the agent environment;
3. run route-inventory test;
4. run secret scan if available;
5. inspect git diff/status and prove scope;
6. commit/push only to the assigned Lea-App agent branch;
7. do not merge and do not deploy.

Report:
- branch and commit SHA;
- exact changed files;
- route inventory and coverage;
- test results;
- any expected-fail/current-defect markers;
- any production-code test hook and why it is behaviour-neutral;
- blockers/unknowns;
- whether the package is ready for independent C2.

No project PASS claim.
