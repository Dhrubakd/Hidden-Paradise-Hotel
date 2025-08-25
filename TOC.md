# Hidden Paradise Hotel — Submission Table of Contents

This file is the complete Table of Contents (TOC) for your assignment deliverable. Each section is annotated with the grade level it satisfies: [P]=Pass, [M]=Merit, [D]=Distinction.

---

1. Title page & cover sheet [P]
2. Executive summary (project goals & outcomes) [P/M/D]
3. Mapping to learning outcomes (LO1–LO4) and marking criteria [P/M/D]

4. Stakeholders & scope
   - 4.1 Stakeholder list & responsibilities [P]
   - 4.2 Scope, assumptions & constraints [P/M]

5. User requirements (functional) — use-cases & acceptance criteria [P/M/D]
6. System & non-functional requirements (security, performance, maintainability) [M/D]

7. Conceptual design
   - 7.1 ER diagram (annotated) — ≥4 interrelated tables [P]
   - 7.2 High-level data model justification [M/D]

8. Logical design (relational schema)
   - 8.1 Table definitions, fields, types, PKs, FKs (DDL snippets) [P]
   - 8.2 Data dictionary (field-level descriptions & examples) [M/D]

9. Normalisation & data integrity
   - 9.1 Normalisation steps (1NF–3NF) with examples [M]
   - 9.2 Constraints, referential integrity, transactions [M/D]

10. Interface & output design
    - 10.1 Wireframes (front-desk check-in/out, admin) [P/M]
    - 10.2 High-fidelity UI screenshots (Tailwind) and sample pages (`checkin.php`, `checkout.php`) [M/D]

11. Implementation architecture & file map
    - 11.1 Technology choices, environment (XAMPP), deployment notes [P/M]
    - 11.2 File layout and brief purpose (e.g., `db.php`, `available_rooms.php`, `users.php`) [M/D]

12. Security & access control
    - 12.1 Authentication & role model (manager/staff) [P/M]
    - 12.2 Password handling (hashing), session policies, CSRF, prepared statements [M/D]
    - 12.3 Audit logging design (what to log, retention) [D]

13. Core functionality — implementation & code evidence
    - 13.1 Check-in flow: validation, reservation creation SQL + screenshots [P/M]
    - 13.2 Availability query & dynamic room dropdown (`available_rooms.php`, `js/script.js`) [P/M]
    - 13.3 Checkout flow: transactional payment, safe room release (no destructive FK deletes) [M/D]
    - 13.4 Room add/edit/delete with safe-delete checks (`edit_room.php`) [M]

14. Database maintenance & operations [M/D]
    - 14.1 Backup/restore strategy and example commands (XAMPP/MySQL) [M/D]
    - 14.2 Migrations & seeders (`migrations/`, `create_default_users.php`) [M/D]
    - 14.3 Reconcile tool (`reconcile_rooms.php`) and when to run it [D]

15. Management reporting & analytics [M/D]
    - 15.1 Weekly/monthly occupancy queries + sample outputs [M]
    - 15.2 Sales reports, SQL, charts and interpretation for manager actions [D]

16. Testing & QA evidence (LO3)
    - 16.1 Test plan (objectives, scope) [P/M/D]
    - 16.2 Test cases (normal, edge, erroneous) with test data sets [P/M]
    - 16.3 Test execution results, bug log, fixes applied [M/D]
    - 16.4 Assessment of test effectiveness and coverage (M4) [D]

17. Automation & maintainability [D]
    - 17.1 Suggested CI (GitHub Actions) — lint, run tests, migrations [D]
    - 17.2 Unit/Integration test examples (instructions + sample runs) [D]

18. Admin tooling & user management [M/D]
    - 18.1 `users.php`: create/list/reset/delete (manager-only) [M]
    - 18.2 Secure password-reset flow (token-based) and audit [D]

19. Security evaluation & risk assessment [D]
    - 19.1 Threat model, mitigations, remaining risks [D]
    - 19.2 Recommendations & priority roadmap [D]

20. User documentation (LO4)
    - 20.1 Quick start (front-desk check-in/out) with screenshots [P]
    - 20.2 Manager guide (reports, reconcile, user admin) [M/D]

21. Technical documentation (LO4)
    - 21.1 Deployment & setup guide (XAMPP commands, `setup.sql`) [P/M]
    - 21.2 DFDs, sequence diagrams, and flowcharts (annotated) [M/D]
    - 21.3 API/SQL reference and code snippets [M/D]

22. User videos & walkthroughs (links or embedded) [D]
    - 22.1 Short clip: create reservation (front-desk) [P/M/D]
    - 22.2 Short clip: checkout + report generation [M/D]
    - 22.3 Short clip: admin tasks (user mgmt, reconcile) [D]

23. Evaluation & recommendations
    - 23.1 Evaluate design effectiveness vs requirements (D1) [D]
    - 23.2 Evaluate database solution vs requirements and suggest improvements (D2/D3) [D]

24. Conclusion & deliverables checklist (what to hand in) [P/M/D]
25. Appendices
    - A: Full `setup.sql` and DDL [P/M]
    - B: Source listing of key files (`db.php`, `checkin.php`, `checkout.php`, `available_rooms.php`, `users.php`, `reconcile_rooms.php`) [M/D]
    - C: Test logs, screenshots, and raw CSV/JSON outputs [M/D]

---

# Separated tasks (Activity-based)

## Activity 1 — Designing the Database (LO1)
- Task A1.1: Produce requirements document (functional + non-functional) — deliverable: `requirements.md` [P]
- Task A1.2: Create ER diagram and conceptual model — deliverable: `diagrams/ERD.png` [P]
- Task A1.3: Produce logical schema and data dictionary — deliverable: `schema.md` + `setup.sql` [P/M]
- Task A1.4: Normalisation evidence and design evaluation — deliverable: `design_evaluation.md` [M/D]

Acceptance criteria: documents exist, ERD shows ≥4 related tables, DDL executes without errors.

## Activity 2 — Development & testing (LO2 & LO3)
- Task A2.1: Implement database + seed data — deliverable: `setup.sql`, `migrations/` [P]
- Task A2.2: Build UI (check-in, check-out, admin) — deliverable: `checkin.php`, `checkout.php`, `users.php` [P/M]
- Task A2.3: Implement availability & dynamic room selection — deliverable: `available_rooms.php`, `js/script.js` [P/M]
- Task A2.4: Implement transactional checkout & safe deletes — deliverable: `checkout.php` (transaction) & `reconcile_rooms.php` [M/D]
- Task A2.5: Implement security measures (CSRF, hashed passwords, prepared statements) — deliverable: `db.php` and verified forms [M/D]
- Task A2.6: Create and run test plan (functional + edge cases) — deliverable: `tests/`, `test_results.md` [M/D]

Acceptance criteria: core flows pass tests; no syntax errors; manager can generate reports.

## Activity 3 — Documentation, videos & evaluation (LO4)
- Task A3.1: Technical documentation — deliverable: `docs/technical_guide.md`, diagrams [P/M/D]
- Task A3.2: User documentation — deliverable: `docs/user_guide.md` with screenshots [P/M]
- Task A3.3: Record user videos — deliverable: 3 short clips (hosted or embedded links) [D]
- Task A3.4: Evaluation & improvement plan — deliverable: `evaluation.md` with D1/D2/D3 coverage [D]

Acceptance criteria: docs are clear, step-by-step setup works with `setup.sql`, videos show working flows.

---

# Quick next actions I can do now
- Create `TOC.md` (done).  
- Generate `diagrams/ERD.png` from current schema.  
- Implement secure token-based password reset (recommended next priority).  

If you want me to expand any TOC section into a ready-to-submit markdown section now, tell me which section and I will start filling it with content and evidence.
