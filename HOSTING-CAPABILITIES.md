# Hosting capabilities — IM-H00

Status: FACT register updated 2026-09-28.

This file records capabilities only. It intentionally does not store credentials, secret values, hostnames, account identifiers, or unnecessary infrastructure paths.

| Capability | Status | Evidence / method | Consequence |
|---|---|---|---|
| PHP runtime | FACT: PHP 8.5 | L confirmed from hosting environment | PHP packages may target PHP 8.5 compatibility. |
| Cron | FACT: available; minimum interval 1 minute | cPanel Cron Jobs UI shown by L | Scheduled/job-runner designs may use one-minute cron cadence where appropriate. |
| Files outside public web root | FACT: available and already used by Lea-App configuration | Existing Lea-App protected config/secret storage outside public web root confirmed by L and current application design | Server-side keys/secrets/backups may be kept outside the public web root, subject to package-specific permissions/tests. |
| Database engine | FACT: MySQL family in current Lea-App configuration; exact production server version remains UNKNOWN | Current application configuration + cPanel database UI | Do not assume a production MySQL minor/version-specific feature until verified. |
| Application DB grants | FACT: all privileges for the Lea-App database | cPanel MySQL database privilege UI shown by L | Schema migrations including CREATE/ALTER/DROP and normal DML are available to the application DB user; migration safety/recovery gates still apply. |
| Backup service | FACT: JetBackup 5 available with multiple retained backup points; full, home-directory and database restore/download capabilities visible | cPanel JetBackup 5 UI shown by L | Hoster backup/restore is available as one recovery layer. |
| Additional encrypted backups | DECIDED TARGET: multiple encrypted backup archives outside the public web root plus downloadable/off-site copy | L decision | IM-M09 must integrate/verify this as an additional recovery layer; hoster backup alone is not the only copy. |
| Staging/subdomain capability | UNKNOWN | Not yet directly verified from hosting UI | IM-E01 must verify/create a separate staging instance before relying on it. |
| Reverse proxy/CDN in actual hosting path | UNKNOWN | No direct technical evidence yet | IM-S02 must not trust X-Forwarded-For based on assumption. |
| Desired network architecture | DECIDED TARGET: end device -> Internet/HTTPS -> cPanel-hosted Lea-App, without a Lea-managed CDN/reverse-proxy dependency | L decision | Prefer REMOTE_ADDR unless a trusted proxy is later technically proven and explicitly configured. |
| CLI availability | PARTIAL FACT: cPanel exposes PHP CLI use for cron | cPanel Cron Jobs UI | Sufficient for PHP cron execution; broader shell/CLI capabilities remain package-specific TO VERIFY. |
| Outbound provider HTTPS | FACT: current Lea-App provider path already operates | Existing application behaviour / blueprint baseline | Provider integration remains available; exact provider limits are package-specific TO VERIFY. |

## IM-H00 conclusion

IM-H00's core hosting-fact questionnaire is substantially complete. The remaining unknowns are deliberately narrow and do not justify guessing:

1. exact production MySQL version;
2. whether the hoster transparently places a reverse proxy/CDN in front of the origin;
3. staging/subdomain capability until directly verified;
4. broader CLI/filesystem capabilities beyond the confirmed PHP/cPanel use cases.

These are implementation-time verification items. They do not block M0 security implementation except where a package explicitly depends on the fact:
- IM-S02 must treat proxy/CDN as UNKNOWN and default fail-closed: do not trust client-supplied forwarding headers unless a trusted proxy is proven/configured.
- IM-E01 must verify staging capability before declaring staging acceptance.
- version-specific DB features must not be used until the production version is verified.

No credential or secret value is recorded here.
