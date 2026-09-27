# AUDIT-FINDINGS — X-High Master Audit

Context: LEA-WORK / DEVELOPMENT (AiChat). Work evidence only — not accepted by Lea, not decided by L, no fix authorized.
Audit-Task: AiChat `AGENT_TASK.md` Task-Version 2.
Stand: 2026-09-27 (Claude / Copilot run, ZERO-MEMORY).

## Audited refs

| Repo | Ref | SHA |
|---|---|---|
| El-Ninjo1965/Lea | `main` | `757880a6eaafda04edd35d06472b5fb8ac92fc7a` |
| El-Ninjo1965/Lea | `copilot/setup-communication-channel` (dialogue head) | `752e1f918c1f298a09fb626569f43a24a2a3e115` |
| El-Ninjo1965/Lea | `copilot/update-lea-private-structure` | `0dc92a62b9e716c0f8d417a75d23388f9a44a6a4` |
| El-Ninjo1965/Lea-App | `main` | `450ff9dc106cb29654d75eb5c0eaa2037c36a788` |
| El-Ninjo1965/AiChat | `main` (task source) | `39c50ed` |

Access: read-only via GitHub MCP. No write to Lea or Lea-App. PR endpoints of Lea/Lea-App return `403 Resource not accessible by personal access token` (see AUDIT-LOG, blockers).

## Legend

- Status: `BELEGT` (reproducible evidence) / `OFFEN` (not decidable with available evidence) / `WIDERLEGT` / `SUPERSEDED`.
- Revalidation (AUD-01..16 only): `REVALIDIERT` (re-checked this run against the refs above), `TEILWEISE`, `NICHT REVALIDIERBAR` (source not accessible this run).
- Origin: `PRIOR-CLAUDE` = first X-High pass (preserved from AGENT_TASK v2), `NEW` = found in this run.
- Every finding: Lea independent review = **NOT REVIEWED**; L decision = **NOT DECIDED** (unless stated otherwise).
- "Evidence" = fact. "Interpretation" = hypothesis. "Options" = not implementation.

---

## Summary

| ID | Title | Origin | Status | Revalidation | Impact |
|---|---|---|---|---|---|
| AUD-01 | Lea-App freeze absent where shortcuts are defined | PRIOR | BELEGT | REVALIDIERT (extended by AUD-17) | high |
| AUD-02 | Conflicting agent handoff rules (AGENT_RESULT vs ß single-file) | PRIOR | BELEGT | REVALIDIERT | high |
| AUD-03 | INDEX additionally defines S semantics | PRIOR | BELEGT | REVALIDIERT (sharpened) | medium |
| AUD-04 | Protocol duplicated on main and dialogue head without reconciliation | PRIOR | BELEGT (state); risk = interpretation | REVALIDIERT | medium |
| AUD-05 | L decisions not persisted / WAITING_FOR_L exit undefined | PRIOR | BELEGT | REVALIDIERT (now observed live) | high |
| AUD-06 | Lea and L writes not distinguishable by Git identity | PRIOR | BELEGT | REVALIDIERT | low-medium |
| AUD-07 | WORK-CONTEXT contains stale state | PRIOR | BELEGT | REVALIDIERT (extended) | medium |
| AUD-08 | PR #1 description stale | PRIOR | BELEGT (prior) | NICHT REVALIDIERBAR | low |
| AUD-09 | INDEX file registry incomplete | PRIOR | BELEGT | REVALIDIERT | low |
| AUD-10 | Development material in Private/Core-loaded files | PRIOR | location BELEGT; cause interpretation | REVALIDIERT (extended) | medium |
| AUD-11 | Two audit definitions; applicable PASS rule unclear | PRIOR | definitions BELEGT; relation OFFEN | REVALIDIERT (extended) | medium-high |
| AUD-12 | SHORTCUTS "Stand" date stale | PRIOR | BELEGT | REVALIDIERT | low |
| AUD-13 | Obsolete branches | PRIOR | BELEGT | REVALIDIERT (extended to Lea-App) | low |
| AUD-14 | Protocol trigger form differs from practice | PRIOR | BELEGT | TEILWEISE | low |
| AUD-15 | PR description outside ß mutation rules but may change | PRIOR | OFFEN | NICHT REVALIDIERBAR | low |
| AUD-16 | Concurrent writes untested | PRIOR | OFFEN | REVALIDIERT (still untested) | low |
| AUD-17 | Lea-App freeze is neither stated in Lea-App nor technically enforced | NEW | BELEGT | — | high |
| AUD-18 | Lea-App `AGENT_TASK.md` still `STATUS: READY` for an already-executed task | NEW | BELEGT | — | medium-high |
| AUD-19 | Mandatory C/R result write target collides with Lea-App freeze | NEW | BELEGT (rule texts); resolution OFFEN | — | high |
| AUD-20 | AiChat workspace unknown to Lea's authoritative work files | NEW | BELEGT | — | medium-high |
| AUD-21 | Lea-App runtime "Lea" is a hardcoded prompt; no link to Lea core; two unrelated memory stores | NEW | facts BELEGT; relation OFFEN | — | medium (architecture) |
| AUD-22 | Test marker `LEA_CORE_MEMORY_TEST_20260925` in production prompt/frontend | NEW | BELEGT | — | low-medium |
| AUD-23 | Text chat and Realtime endpoints have no server-side auth check | NEW | BELEGT (code level); production exposure OFFEN | — | high (security) |
| AUD-24 | Login rate limit keyed on client-controlled `X-Forwarded-For` | NEW | BELEGT (code level); effective exposure OFFEN | — | medium (security) |
| AUD-25 | Test suite does not cover chat/realtime auth; many assertions are weak alternations | NEW | BELEGT | — | low-medium |
| AUD-26 | Infrastructure identifiers hardcoded in Lea-App | NEW | BELEGT | — | low |
| AUD-27 | Binding shortcut execution rules outside `SHORTCUTS.md` | NEW | OFFEN (interpretation of authority rule) | — | medium |
| AUD-28 | Near-duplicate section in `PROCESSING.md` | NEW | BELEGT | — | low |

---

## AUD-01 — Lea-App freeze absent where shortcuts are defined

- Origin: PRIOR-CLAUDE. Scope: Lea (INDEX, SHORTCUTS, PROJECT-CONTEXT, CHAT_PROTOCOL, WORK-CONTEXT).
- Status: BELEGT. Revalidation: REVALIDIERT. Impact: high.
- Evidence:
  - Code search `FROZEN repo:El-Ninjo1965/Lea` → hits only in `CHAT_PROTOCOL.md`, `WORK-CONTEXT.md`, `LEA_AGENT_CHAT.md` (all LEA-WORK).
  - `SHORTCUTS.md` (blob `d10b9f9`): D = "Geprueften Stand kontrolliert deployen"; Q = "Befunde autonom beheben/bereinigen" in Lea + Lea-App; C = Pflicht-Write `AGENT_RESULT.md`. No freeze/READ-ONLY mention.
  - `INDEX.md` (blob `ca990b4`), section "Kontexttrennung": Work files "werden beim normalen persoenlichen Lea-Einlesen ueber A/Ä nicht automatisch geladen".
  - `PROJECT-CONTEXT.md` (blob `c4b08f9`), section "Lea-App — externer technischer Projektverweis": no freeze.
- Counter-evidence: SHORTCUTS Q: "Sicherheits-/Freigabe-Gates werden nicht umgangen" — only effective if the freeze is known in the loaded context.
- Uncertainty: whether Lea in practice loads WORK-CONTEXT before Q/D/C. Not observable from repo.
- Interpretation: a private-context A/Ä load followed by Q/D can reach Lea-App without the freeze in context.
- Dependencies: AUD-17 (Lea-App side), AUD-19, AUD-10.
- Options: freeze notice in SHORTCUTS or INDEX; freeze notice in Lea-App itself (AUD-17); or operational rule "no Q/D/C until freeze revoked".
- Verification: freeze is visible from every entry point that can trigger Q/D/C (A/Ä load, Lea-App agent start).
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-02 — Conflicting agent handoff rules

- Origin: PRIOR-CLAUDE. Scope: Lea SHORTCUTS vs CHAT_PROTOCOL.
- Status: BELEGT. Revalidation: REVALIDIERT. Impact: high.
- Evidence:
  - `SHORTCUTS.md`, "R-/Agenten-Handoff-Protokoll": "Nach jedem Agentenlauf (PASS/FAIL/STOPPED) muss der Agent `AGENT_RESULT.md` vollstaendig … ueberschreiben, separat committen und pushen".
  - `CHAT_PROTOCOL.md` (blob `5db64c8`, identical on main and head), "Mutation boundary": "Trigger `ß` authorizes mutation of exactly one file: `El-Ninjo1965/Lea/LEA_AGENT_CHAT.md`".
  - No precedence rule between the two in either file.
  - `AGENT_RESULT.md` does not exist in Lea (`main` and head tree); it exists only in Lea-App.
- Counter-evidence: WORK-CONTEXT "Aktuelle methodische Korrektur": missing AGENT_TASK/AGENT_RESULT "derzeit kein bestaetigter Fehler". Missing files are not the finding; the rule conflict is.
- Uncertainty: whether ß runs count as "Agentenlauf" in the sense of SHORTCUTS.
- Interpretation: a ß run cannot satisfy both rules.
- Dependencies: AUD-11, AUD-19.
- Options: explicit precedence (ß protocol overrides handoff for ß runs), or define the result location per workspace.
- Verification: one unambiguous handoff rule per run type, readable from both files.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-03 — INDEX additionally defines S semantics

- Origin: PRIOR-CLAUDE. Scope: Lea INDEX vs SHORTCUTS.
- Status: BELEGT. Revalidation: REVALIDIERT (sharpened). Impact: medium.
- Evidence (`INDEX.md` blob `ca990b4`):
  - Section "Kurzsignale": "Diese Datei definiert keine parallele Shortcut-Semantik."
  - Directly following section "Kurzsignal zum Speichern": standalone "s"/"S" = save "auf Branch `main`" — a parallel definition.
  - Section "Speicherregel fuer S": context-dependent storage (Private vs Work) without a branch.
  - `SHORTCUTS.md` "S-Ausfuehrungsprotokoll": context-dependent; no branch named.
- Counter-evidence: content largely compatible; S meaning ("Relevanten Stand speichern") is the same.
- Uncertainty: which branch a Work-context S targets (main, dialogue head, or now AiChat — see AUD-20).
- Interpretation: INDEX contradicts its own non-definition clause; "always main" conflicts with Work-context storage when Work lives on a head branch or in AiChat.
- Dependencies: AUD-20, AUD-27.
- Options: remove/replace the INDEX S section by a pointer to SHORTCUTS; define Work-context target explicitly in SHORTCUTS.
- Verification: S defined in exactly one file; target per context unambiguous.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-04 — Protocol duplicated on main and dialogue head without robust reconciliation

- Origin: PRIOR-CLAUDE. Scope: Lea branches.
- Status: state BELEGT; risk = interpretation. Revalidation: REVALIDIERT. Impact: medium.
- Evidence: root trees of `main` (`757880a`) and head (`752e1f9`) differ only in `LEA_AGENT_CHAT.md` (main blob `817ccbc`, head blob `f562b6b`). `CHAT_PROTOCOL.md` (`5db64c8`) and `WORK-CONTEXT.md` (`3446910`) are identical. CHAT_PROTOCOL steps 2–3 read protocol and transcript from the head. WORK-CONTEXT "Naechste Schritte" 1: "Protokoll und Work-Kontext auf main und dem Dialog-Head synchron halten" — intention, no mechanism.
- Counter-evidence: currently in sync; no divergence observed.
- Uncertainty: none about the state; the risk is future divergence.
- Interpretation: a protocol change landing only on main is invisible to ß runs; a naive main→head sync could drop turns.
- Dependencies: AUD-20 (AiChat may supersede the channel location).
- Options: single-location protocol; documented sync procedure; or move work channel to AiChat.
- Verification: one authoritative protocol location, documented.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-05 — L decisions have no persistent transcript representation / WAITING_FOR_L exit undefined

- Origin: PRIOR-CLAUDE. Scope: Lea CHAT_PROTOCOL + transcript.
- Status: BELEGT. Revalidation: REVALIDIERT — the predicted situation is now observable. Impact: high.
- Evidence:
  - CHAT_PROTOCOL "Fixed participants": only `## LEA` and `## CLAUDE` headings; no L heading or L decision record.
  - Head transcript (`f562b6b`, last commit `752e1f9`): final message is `## CLAUDE` with "Entscheidung von L erforderlich:" (3 questions: channel PASS, Lea-App access, PR #1 description) → state `WAITING_FOR_L`.
  - No L answer exists in the transcript. The current audit is started from AiChat `AGENT_TASK.md` v2, which states the Lea-App capability result, i.e. L's answers live only outside the Lea channel.
- Counter-evidence: AiChat `AGENT_TASK.md` is itself a persistent record — but it is not referenced by the Lea protocol (AUD-20).
- Uncertainty: whether L intends AiChat to be the decision record.
- Interpretation: every future zero-memory ß run on the Lea channel must return `WAITING_FOR_L` indefinitely.
- Dependencies: AUD-20, AUD-06.
- Options: defined L heading / decision file; defined exit procedure for WAITING_FOR_L; or declare AiChat the decision record.
- Verification: a zero-memory ß run can derive the post-decision state from persistent sources alone.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-06 — Lea and L writes not technically distinguishable by Git identity

- Origin: PRIOR-CLAUDE. Scope: Lea commit metadata.
- Status: BELEGT. Revalidation: REVALIDIERT. Impact: low-medium.
- Evidence: `46d40af` ("LEA turn: …") and all `main` commits (e.g. `757880a`) carry the same human author/committer identity. Claude turns (`d4c941a`, `752e1f9`) carry the Copilot bot identity. (Identity values intentionally not reproduced.)
- Counter-evidence: commit-message convention "LEA turn:" and heading `## LEA`.
- Uncertainty: none about metadata.
- Interpretation: attribution Lea vs L rests only on convention.
- Dependencies: AUD-05.
- Options: separate identity/trailer for Lea writes; or accept convention explicitly.
- Verification: attribution rule documented.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-07 — WORK-CONTEXT contains stale state

- Origin: PRIOR-CLAUDE. Scope: Lea `WORK-CONTEXT.md` (blob `3446910`, "Stand: 27.09.2026").
- Status: BELEGT. Revalidation: REVALIDIERT (extended). Impact: medium.
- Evidence (section → contradicting evidence):
  - "Repository-Zugriff und Schutz": "naechster neuer Claude-Lauf soll … direkt im Arbeits-Repository El-Ninjo1965/Lea gestartet werden" → later ß runs ran from Lea PR #1; the work workspace is now AiChat (`WORKSPACE.md`).
  - Same section: "Der von L bereitgestellte GH_TOKEN ermoeglicht Claude Zugriff auf beide Repositories" / "umfassende Rechte" → transcript CLAUDE turns 1 and 2 report Lea-App 404 and no `GH_TOKEN`; AiChat `AGENT_TASK.md` v2 states the MCP token is scoped to Lea + Lea-App only; this run: PR endpoints 403.
  - "Persistenter Lea-Claude-Kanal": "der erste echte ß-Turn muss noch als End-to-End-Test verifiziert werden" → head commits `d4c941a`, `46d40af`, `752e1f9` show three completed turns.
  - "Naechste Schritte" 3: "Erst nach PASS des Kanals X-High-Master-Audit" → no PASS recorded in Lea; audit now runs from AiChat.
- Counter-evidence: "Stand" header is current (27.09.2026); parts (freeze, X-High frame) remain valid.
- Uncertainty: none.
- Dependencies: AUD-05, AUD-20.
- Options: update WORK-CONTEXT or declare it superseded by AiChat.
- Verification: every current-state sentence matches repo evidence.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-08 — PR #1 description stale

- Origin: PRIOR-CLAUDE. Scope: Lea PR #1.
- Status: BELEGT (prior evidence). Revalidation: NICHT REVALIDIERBAR — `pulls` API 403 for the MCP token this run. Impact: low.
- Evidence (secondary): head transcript, CLAUDE turn 2, item 3 records the claim.
- Uncertainty: current description text not verified this run.
- Options: correct description (who: L decision pending, see AUD-05 question 3).
- Verification: read PR #1 body with PR-read permission.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-09 — INDEX file registry incomplete

- Origin: PRIOR-CLAUDE. Scope: Lea `INDEX.md`.
- Status: BELEGT. Revalidation: REVALIDIERT. Impact: low.
- Evidence:
  - Table "Dateien" lists only SHORTCUTS, VISION, Memories, PROCESSING, GEDANKEN, INTERESSEN. Missing from the table: `CHAT_PROTOCOL.md`, `WORK-CONTEXT.md`, `LEA_AGENT_CHAT.md`, `PERSONAL-CONTEXT.md`, `HEALTH-CONTEXT.md`, `LEGAL-CONTEXT.md`, `PROJECT-CONTEXT.md`, `README.md`.
  - Rule in INDEX: "Jede neue dauerhafte Datei wird hier mit Zweck und Einlesereihenfolge eingetragen."
  - GEDANKEN description "Kontrollierte Denkpause: Steuersignale, Arbeitsauftrag, technischer Fähigkeitstest und Abschlussdisziplin" vs actual `GEDANKEN.md` (blob `6280657`) title "Denk- und Pruefmodus" (analysis/cross-check/test/meta working method).
- Counter-evidence: Work files named in "Kontexttrennung"; context files in "Zusaetzliche Kontextdateien" — referenced, but without Zweck row.
- Options: complete the table; correct GEDANKEN row.
- Verification: every root file has a Zweck row; descriptions match content.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-10 — Development material remains in Private/Core-loaded files

- Origin: PRIOR-CLAUDE. Scope: Lea PROCESSING, SHORTCUTS, PROJECT-CONTEXT.
- Status: location BELEGT; cause interpretation. Revalidation: REVALIDIERT (extended). Impact: medium.
- Evidence:
  - `PROCESSING.md` (blob `ee8fb4e`) L967–1077: agent workflow (AGENT_TASK/RESULT, AGENT LOKAL/COPILOT, N gate, retry rule, Codespaces credit state).
  - `PROCESSING.md` L1014–1024 "Verbindliche Repo-/Layer-Grenze" (26.09): Lea repo = "gemeinsame Entwicklungs-, … werkstatt" containing agent steering — predates and conflicts in framing with INDEX "Kontexttrennung" (27.09: Private vs Work) and AiChat `WORKSPACE.md` (Work outside Lea).
  - `PROCESSING.md` L1058–1062 "Aktuelle GitHub-Ressourcenlage": time-bound quota state with reset 01.10.2026 → will be stale by construction.
  - SHORTCUTS (Work commands N/R/D/C/Q) is loaded in the private sequence (INDEX order #2). PROJECT-CONTEXT (technical projects) is loaded in the private sequence ("Zusaetzliche Kontextdateien").
- Counter-evidence: historical residue before the 27.09 separation; not necessarily a new violation.
- Uncertainty: intended final placement (Lea Work area vs AiChat).
- Dependencies: AUD-20, AUD-27.
- Options: move/label development sections; mark time-bound state as dated snapshot.
- Verification: private load sequence contains no active development steering, or it is explicitly labelled.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-11 — Two audit definitions; applicable PASS rule unclear

- Origin: PRIOR-CLAUDE. Scope: Lea SHORTCUTS C, Lea WORK-CONTEXT X-High frame, Lea-App AGENTS.md C, AiChat AGENT_TASK.
- Status: definitions BELEGT; relation OFFEN. Revalidation: REVALIDIERT (extended). Impact: medium-high.
- Evidence:
  - SHORTCUTS C: PASS "nach zwei aufeinanderfolgenden vollstaendigen Pruefzyklen mit 0 neuen Befunden"; mandatory `AGENT_RESULT.md` write. The "methodisch gegenpruefen" requirement is stated for Q only.
  - Lea-App `AGENTS.md` (blob `2fc1e0d`) "C — Read-only Quality Audit": PASS after two "methodisch unterschiedlichen" cycles — stricter than SHORTCUTS C.
  - WORK-CONTEXT "Fundament-/X-High-Phase": lifecycle ENTDECKT → … → GESCHLOSSEN with AUDIT-LOG/AUDIT-FINDINGS; no PASS criterion.
  - AiChat AGENT_TASK v2: allowed writes only AUDIT-FINDINGS/AUDIT-LOG; "Do not claim final PASS until … AUD-11 is resolved by L".
- Uncertainty: whether X-High audit is a C run.
- Interpretation: three non-identical audit regimes; C's output artefact and X-High's artefacts are mutually exclusive in this run.
- Dependencies: AUD-02, AUD-19.
- Options: L declares which regime applies to X-High; harmonise C definition in SHORTCUTS with AGENTS.md.
- Verification: one PASS rule per audit type, stated in the authoritative file.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED — **required before any PASS**.

## AUD-12 — SHORTCUTS "Stand" date stale

- Origin: PRIOR-CLAUDE. Status: BELEGT. Revalidation: REVALIDIERT. Impact: low.
- Evidence: header "Stand: 26.09.2026"; last commit touching `SHORTCUTS.md` is `2392db7` ("Make S storage context-aware", 2026-09-27T06:13:36Z).
- Options: update header or remove manual date.
- Verification: header ≥ last change date.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-13 — Obsolete branches

- Origin: PRIOR-CLAUDE (Lea), extended NEW (Lea-App). Status: BELEGT. Revalidation: REVALIDIERT. Impact: low.
- Evidence:
  - Lea `copilot/update-lea-private-structure` tip `0dc92a6` appears in `main` history (list_commits main) → no unique commits.
  - Lea-App: `copilot/chatgpt-lea-development` and `copilot/3b38e6fe-…` point to `450ff9d` = `main`; `copilot/leapp-project-init` tip `11e55a8` (2026-09-24, Copilot bot).
- Uncertainty: whether `11e55a8` is contained in Lea-App `main` (no compare endpoint used; PR list 403).
- Options: delete branches (Lea-App only after freeze revocation).
- Verification: branch list = active branches only.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-14 — Protocol trigger-comment form differs from practice

- Origin: PRIOR-CLAUDE. Status: BELEGT. Revalidation: TEILWEISE (PR comments not readable, 403). Impact: low.
- Evidence: CHAT_PROTOCOL "Persistent channel": "self-contained comment beginning `@copilot ß`". Head transcript CLAUDE turn 2 records a run triggered by a bare `ß` plus model selection that succeeded.
- Uncertainty: whether platform-supplied PR context remains available.
- Options: document the minimal working trigger or require self-contained form strictly.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-15 — PR description outside ß mutation rules but may change

- Origin: PRIOR-CLAUDE. Status: OFFEN. Revalidation: NICHT REVALIDIERBAR (403). Impact: low.
- Evidence (prior): description replacement observed; mechanism not proven.
- Verification: PR timeline with PR-read access.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-16 — Concurrent writes untested

- Origin: PRIOR-CLAUDE. Status: OFFEN. Revalidation: REVALIDIERT (no test exists). Impact: low.
- Evidence: CHAT_PROTOCOL step 9 pre-write blob-SHA check; SHORTCUTS "Q-Concurrency-/Write-Guard" — both check-then-write, not atomic. No conflict test in history.
- Options: controlled conflict test; rely on Git fast-forward rejection as the atomic guard.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

---

## AUD-17 — Lea-App freeze is neither stated in Lea-App nor technically enforced (NEW)

- Scope: Lea-App (all files), Lea/Lea-App branch settings.
- Status: BELEGT. Impact: high.
- Evidence:
  - Code search `FROZEN repo:El-Ninjo1965/Lea-App` → 0 hits. `README.md`, `AGENTS.md`, `ARCHITECTURE.md`, `SECURITY.md`, `ROADMAP.md` contain no freeze/READ-ONLY notice.
  - Lea-App `ROADMAP.md` "Aktueller Stand — 26.09.2026" lists "Aktuelle technische UI-Arbeit" as ongoing.
  - `list_branches`: all branches in Lea and Lea-App report `protected: false`.
  - `actions/list_workflows` Lea-App: "Copilot cloud agent" workflow `active`.
- Counter-evidence: freeze is stated in Lea `CHAT_PROTOCOL.md` / `WORK-CONTEXT.md` and AiChat `WORKSPACE.md`.
- Uncertainty: repository rulesets are not visible via the branch `protected` flag; not queried.
- Interpretation: an agent started in Lea-App reads `AGENTS.md` (its binding rule file) and sees no freeze; the freeze is declarative only in other repositories.
- Dependencies: AUD-01, AUD-18.
- Options (no implementation): freeze notice in Lea-App (requires L to authorise a write to a frozen repo); branch protection/ruleset on Lea-App `main`; disable agent triggers in Lea-App.
- Verification: freeze visible inside Lea-App and/or enforced by GitHub settings.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-18 — Lea-App `AGENT_TASK.md` still `STATUS: READY` for an already-executed task (NEW)

- Scope: Lea-App `AGENT_TASK.md` (blob `78969a8`), `AGENT_RESULT.md` (blob `b583bbb`).
- Status: BELEGT. Impact: medium-high.
- Evidence: AGENT_TASK `TASK_ID: LEA-DUAL-REPO-C-READONLY-AUDIT-01-20260926`, `STATUS: READY`. AGENT_RESULT for the same TASK_ID: `TASK_STATUS: FAIL` (commit `556673f`, "C audit result: dual-repo prerequisite missing"). The task requires a result commit+push to Lea-App.
- Counter-evidence: none found.
- Interpretation: a zero-memory agent in Lea-App would find a READY task whose execution mandates a write to the frozen repo.
- Dependencies: AUD-17, AUD-19.
- Options: mark task consumed/blocked (requires L approval for Lea-App write) or rely on AUD-17 enforcement.
- Verification: no READY task in a frozen repo.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-19 — Mandatory C/R result write target collides with the Lea-App freeze (NEW)

- Scope: Lea SHORTCUTS C/R, Lea-App AGENTS.md C, CHAT_PROTOCOL freeze, AiChat AGENT_TASK.
- Status: rule texts BELEGT; resolution OFFEN. Impact: high.
- Evidence: SHORTCUTS C: "muss ausschliesslich `AGENT_RESULT.md` … ueberschrieben, separat committed und gepusht werden" (no repository named). The only existing `AGENT_RESULT.md` is in Lea-App. CHAT_PROTOCOL: Lea-App "No file may be … committed, pushed". AiChat AGENT_TASK v2 forbids everything except AUDIT-*.md.
- Interpretation: a C run as defined in SHORTCUTS cannot be completed without breaking either the handoff rule or the freeze; this run followed the stricter AiChat rule and wrote no AGENT_RESULT.md.
- Dependencies: AUD-02, AUD-11, AUD-18.
- Options: define result location per workspace (e.g. AiChat); explicit freeze precedence over handoff.
- Verification: C executable without rule violation.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-20 — AiChat workspace unknown to Lea's authoritative work files (NEW)

- Scope: Lea CHAT_PROTOCOL, WORK-CONTEXT, INDEX; AiChat WORKSPACE.md.
- Status: BELEGT. Impact: medium-high.
- Evidence: code search `AiChat repo:El-Ninjo1965/Lea` → 0 hits. AiChat `WORKSPACE.md`: AiChat is "the neutral persistent work workspace"; Lea = "source under investigation". Lea `CHAT_PROTOCOL.md`: "single authoritative definition of the direct LEA ↔ CLAUDE development dialogue" on the Lea dialogue head; WORK-CONTEXT names Lea PR #1 as the channel.
- Interpretation: two work-context authorities with no precedence rule; a zero-memory agent starting from Lea cannot discover AiChat, and vice versa AiChat does not state whether the Lea channel is retired.
- Dependencies: AUD-03, AUD-04, AUD-05, AUD-07, AUD-10.
- Options: L decides which location is authoritative for LEA-WORK; cross-reference in the non-authoritative location.
- Verification: both entry points lead to the same authoritative work location.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-21 — Lea-App runtime "Lea" is a hardcoded prompt; no link to Lea core; two unrelated memory stores (NEW)

- Scope: Lea-App `api/realtime/session.php` (`a3b528a`), `api/text/chat.php` (`1bc7ea8`), `backend/server.js` (`731d51f`), `ROADMAP.md`, `ARCHITECTURE.md`; Lea `INDEX.md`, `PROJECT-CONTEXT.md`.
- Status: facts BELEGT; relationship OFFEN. Impact: medium (architecture).
- Evidence:
  - Identity = one-line English instruction "You are Lea, a concise … German-speaking … assistant" duplicated in three files; no code reads from `El-Ninjo1965/Lea`.
  - Lea-App memory = MySQL V1/V2 (`database/migrations/001_v1_schema.sql`, `002_privacy_v2.sql`); Lea core memory = `Memories.md`/`PROCESSING.md` in Git.
  - Lea-App `ROADMAP.md` Phase 3 "lesender Zugriff auf Lea-Memory", Phase 4 "kontrolliertes Schreiben ins Lea-Repository" — no status marker; section "Aktueller Stand" does not mention them.
  - Lea-App `VISION.md`: "Lea-App darf keine konkurrierende … Persoenlichkeits- … semantik erfinden."
- Counter-evidence: ARCHITECTURE principle 5 and PROJECT-CONTEXT keep core semantics out of the app deliberately; a minimal prompt may be an intended placeholder.
- Uncertainty: whether "Lea-Memory" in ROADMAP means the Lea repo or the app DB.
- Interpretation: the stated project goal (merge Lea development and Lea-App, transcript LEA turn 1) has no technical bridge yet; "Lea" in the app and "Lea" in the core are currently independent.
- Options: define the core↔app interface (read path, provenance, which store is authoritative) as an architecture item for L.
- Verification: documented relationship of the two stores and identity sources.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-22 — Test marker in production prompt and frontend (NEW)

- Scope: Lea-App `api/text/chat.php`, `frontend/app.js`, `tests/memory-api.test.js`.
- Status: BELEGT. Impact: low-medium.
- Evidence: chat.php system prompt contains "If you are given a clearly marked test memory item such as LEA_CORE_MEMORY_TEST_20260925 …"; app.js `persistMemoryTestMarker()` writes evidence when the marker appears; test asserts presence of the marker.
- Interpretation: dated test instrumentation is part of the production prompt/runtime.
- Options: remove or gate behind a test flag (after freeze revocation).
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-23 — Text chat and Realtime endpoints have no server-side auth check (NEW, security)

- Scope: Lea-App `api/text/chat.php`, `api/realtime/session.php`, `.htaccess`.
- Status: BELEGT at code level; production exposure OFFEN. Impact: high.
- Evidence: code search `lea_memory_require_auth` → called in all `api/memory/*` data endpoints and `api/v2/memory/*`, **not** in `api/text/chat.php` and not in `api/realtime/session.php` (which does not include `_common.php`). Both endpoints read the server OpenAI key file and call OpenAI. `.htaccess` only rewrites routes; no access restriction.
- Counter-evidence: host-level protection outside the repo is possible; not verifiable read-only. No live probe performed (forbidden by scope).
- Interpretation: any client able to reach the endpoints could consume the server-side OpenAI credential (cost/abuse), bypassing the app login.
- Options: require app session on both endpoints (after freeze revocation / L approval).
- Verification: unauthenticated request → 401 in test and production.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-24 — Login rate limit keyed on client-controlled `X-Forwarded-For` (NEW, security)

- Scope: Lea-App `api/memory/_common.php` `lea_memory_client_ip()`.
- Status: BELEGT at code level; effective exposure OFFEN. Impact: medium.
- Evidence: the first `X-Forwarded-For` value, if present, is used as the rate-limit key before `REMOTE_ADDR`.
- Counter-evidence: a hosting proxy may overwrite the header; unverified.
- Interpretation: varying the header yields a fresh attempt budget, weakening brute-force protection of `auth.php`.
- Options: trust XFF only from known proxies; key on `REMOTE_ADDR`.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-25 — Tests do not cover chat/realtime auth; many assertions are weak alternations (NEW)

- Scope: Lea-App `tests/memory-api.test.js` (`620b420`).
- Status: BELEGT. Impact: low-medium.
- Evidence: no test targets auth of `api/text/chat.php` or `api/realtime/session.php`. Contract assertions like `/privacy_v2_records|lea_memory_require_auth\(\)|prepare\s*\(|bind_param/i` pass if any one alternative is present. The "sessions A and B isolated" test partly asserts on an in-test array filter.
- Counter-evidence: dedicated tests assert `lea_memory_require_auth\(\)` for v2 records/relations exactly and run a real PHP login/logout flow.
- Interpretation: AGENTS.md "Test-PASS allein genuegt nicht" is justified; the suite would not detect AUD-23.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-26 — Infrastructure identifiers hardcoded in Lea-App (NEW)

- Scope: Lea-App `.env.example`, `backend/ftp-config.js`, `api/memory/db.php`, `api/memory/_common.php`.
- Status: BELEGT. Impact: low.
- Evidence: FTP host/user, target dir, DB name/user and hosting home path appear as defaults (values not reproduced here). Files are labelled "Non-secret"; passwords/keys are read from files outside the repo. Pattern secret scan found no secret values.
- Counter-evidence: private repository; values are not credentials.
- Options: keep as accepted, or move defaults to environment only.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-27 — Binding shortcut execution rules outside `SHORTCUTS.md` (NEW)

- Scope: Lea `PROCESSING.md`, `INDEX.md`, `SHORTCUTS.md`.
- Status: OFFEN (depends on reading of the authority clause). Impact: medium.
- Evidence: SHORTCUTS: sole authority; other files may "Funktionen … beschreiben, aber keine abweichende Shortcut-Semantik definieren". PROCESSING L977–988 "Verbindliches Agenten-Gate vor N" ("MUSS", gate precedence), L1067–1072 "Kombibefehl N & Agenten-Chat", L941 "Ue (Shortcut „Ü“)" — normative execution rules for shortcuts not all reflected in SHORTCUTS (e.g. N-gate step order, "N & [Chat]" combined form).
- Counter-evidence: mostly consistent with SHORTCUTS N/Ü rows; can be read as "description".
- Interpretation: additive binding rules outside the sole authority reintroduce the risk addressed by PROCESSING L1074 "Shortcut-Quellenregel nach Fehlbereinigung".
- Dependencies: AUD-03, AUD-10.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

## AUD-28 — Near-duplicate section in `PROCESSING.md` (NEW)

- Scope: Lea `PROCESSING.md` (Lea-Core content).
- Status: BELEGT. Impact: low.
- Evidence: L144–165 "Baby-Grundfunktionen fuer Bausteine 2–6 — 21.09.2026" and L167–188 "Baby-Grundfunktionen der Bausteine 2–6 — 21.09.2026" define the same minimal functions with slightly different wording.
- Counter-evidence: content is compatible; Q-Schutz forbids freezing legitimate development — this is a consistency note, not a content judgement.
- Options: Lea decides whether to consolidate.
- Lea review: NOT REVIEWED. L decision: NOT DECIDED.

---

## Withdrawn / superseded

None in this run. No prior finding was refuted.

## Open decisions for L (stop points)

1. AUD-11: which audit regime (C / X-High lifecycle) and PASS rule applies — prerequisite for any PASS.
2. AUD-20 / AUD-05: authoritative LEA-WORK location (Lea dialogue head vs AiChat) and where L decisions are recorded; how the Lea channel leaves `WAITING_FOR_L`.
3. AUD-17 / AUD-18 / AUD-23: whether any protective action on frozen Lea-App (notice, branch protection, endpoint auth) is wanted — every such action requires explicit L approval.
4. Access: PR-read permission for the MCP token if AUD-08/14/15 shall be revalidated.
