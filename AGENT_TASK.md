# AGENT_TASK — AiChat C2 Artifact Consolidation

TASK_VERSION: 5
STATUS: READY
MODE: MECHANICAL CONSOLIDATION
WORKSPACE: El-Ninjo1965/AiChat

## Goal
Put the independently reviewed C2 audit/architecture artifacts onto AiChat main without merging the historical copilot/main branch history.

## Authoritative source commit
Use exact file contents from commit/ref:
83be5a1

Expected source blob SHAs:
- AUDIT-FINDINGS.md = 1aeb9f5309dd28d9f52cf1c9d5422c07b6b1fedb
- AUDIT-LOG.md = d47fdaf58820336ad484bd42ea5b88b31a217b84
- ROOT-CAUSE.md = 51d2d4153f26b3ffde479600f293b6cd57813d41
- B-BLUEPRINT.md = 6ce7064178e72ff288c561da7f24de38379ade8a
- IMPLEMENTATION-MAP.md = bfa252eae1ff98e7bd6d1753d9dca907d0620cdf

## Required action
Create exactly those five files on a fresh working branch based on current AiChat main, byte-for-byte/content-identical to the source ref.

Do NOT merge copilot/main history.

Do NOT reinterpret, edit, clean up, reformat or update the artifact contents in this task.

## Preconditions
Before writing:
1. fetch current main;
2. verify WORKSPACE.md and this AGENT_TASK.md are current;
3. verify all five source files at 83be5a1 match the expected blob SHAs above;
4. verify none of the five target paths already exists on current main. If any exists, STOP and report rather than overwrite.

## Allowed writes
Only:
- AUDIT-FINDINGS.md
- AUDIT-LOG.md
- ROOT-CAUSE.md
- B-BLUEPRINT.md
- IMPLEMENTATION-MAP.md

No other file may change.

## Forbidden
- no Lea write;
- no Lea-App write;
- no deployment;
- no GitHub settings change;
- no branch protection change;
- no content edits;
- no merge of the historical branch.

## Verification
After writing:
1. verify the five resulting blob SHAs equal the expected SHAs;
2. verify diff against the starting main contains exactly five added files and no modifications/deletions;
3. report branch, commit SHA, exact files and verification result.

This is consolidation only, not implementation and not a project PASS.
