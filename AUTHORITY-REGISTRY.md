# AUTHORITY-REGISTRY

Status: ACTIVE
Authority: AiChat WORKSPACE.md + approved C2 architecture

| Domain | Current authority | State | Target |
|---|---|---|---|
| LEA-WORK decisions/tasks/audits | AiChat | AUTHORITATIVE | AiChat during development |
| Product requirements/code | Lea-App | AUTHORITATIVE | Lea-App |
| Lea vision/processing source | Lea repo | SOURCE_ONLY | migrate relevant semantics/experience into B |
| Experience memory in Lea repo | Lea repo | SOURCE_ONLY | B canonical M3 after verified migration |
| Lea-App V1 memory | Lea-App DB | MIGRATION_SOURCE | B canonical M3 |
| Lea-App V2 memory | Lea-App DB | MIGRATION_SOURCE | B canonical M3 |
| B canonical identity/memory | not yet established | MISSING | M3 in B |
| Runtime secrets | protected server configuration | AUTHORITATIVE | protected server secret store |
| Product identity/presence people data | not yet established | MISSING | protected B store |
| Incognito session buffer | none | NON_STORE | ephemeral only; never authoritative |
| Git/GitHub | repositories | DEVELOPMENT_INFRA | version/audit/recovery, not B runtime memory master |

## Invariant
At most one writable AUTHORITATIVE source exists for any domain. SOURCE_ONLY and MIGRATION_SOURCE copies must not silently become competing masters.

## Migration
Authority moves only through an explicitly verified migration/cutover package under C2 and the applicable L gate.
