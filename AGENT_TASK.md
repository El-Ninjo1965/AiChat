# Agent Task

Task-Version: 1
Status: READY
Mode: CAPABILITY TEST / READ-ONLY CROSS-REPO

## Zero-memory rule
Treat this run as ZERO-MEMORY. Do not assume any earlier Copilot session or conversation.

## Workspace
Run in:
El-Ninjo1965/AiChat

Read WORKSPACE.md first.

## Immediate task
Perform only a capability/access test.

Determine, without changing any repository content:

1. Confirm that the current run is actually based in El-Ninjo1965/AiChat.
2. Identify the current Copilot working branch.
3. Confirm whether the agent can read AiChat.
4. Determine whether the normal Copilot mechanism permits writing only to its AiChat working branch. Do not perform a test write merely to prove this.
5. Test READ-ONLY access to El-Ninjo1965/Lea.
6. Test READ-ONLY access to El-Ninjo1965/Lea-App.
7. Report which authentication/access mechanism is actually available in this run, without printing any secret/token value.
8. If either external repository is inaccessible, report the exact observed blocker and the minimum configuration that would be required to obtain READ-ONLY access.

## Hard boundaries
- Do not create, edit, delete, commit, push or deploy anything in Lea.
- Lea-App is FROZEN / STRICTLY READ-ONLY. Do not create, edit, delete, commit, push or deploy anything there.
- Do not create AUDIT-FINDINGS.md or AUDIT-LOG.md yet.
- Do not begin or repeat the master audit.
- Do not copy prior audit findings into this workspace.
- Do not modify AiChat during this capability test.
- Do not create or merge a PR for this test.
- Do not expose secrets or token values.

## Output
Return a visible report to L containing:
- AiChat access result
- Lea read result
- Lea-App read result
- current branch
- available authentication mechanism(s)
- any blocker
- exact next step required, if any

End after the capability report.
