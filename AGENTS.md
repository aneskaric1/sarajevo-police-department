# AGENTS.md

## Project
Sarajevo Police Department Portal: a web application for a police department. A public website (home, login, register) and, after login, a section for officers to manage incidents, tickets, citizens and vehicles. Administrators also manage officers and violation types.

Roles: admin, user (a police officer). Entities: users, citizens, vehicles, violation_types, tickets, incidents, incident_citizens.

## Technology stack
- Frontend: HTML, CSS, JavaScript (no framework), Bootstrap 5 (CDN), Chart.js (CDN), single-page application (SPA) with a hash router
- Backend: PHP, PDO, MySQL (MariaDB in XAMPP); FlightPHP for the REST API from Milestone 3
- Local server: XAMPP (or `php -S localhost:8000 -t frontend`)

## Folder structure
- frontend/: index.html (the only page), assets/ (css, js, img), views/ (one HTML file per view), services/ (code that calls the API; mock auth in Milestone 1), utils/ (router, UI helpers)
- backend/: index.php, config/ (config.example.php committed, config.php not), middleware/, rest/routes/, rest/services/, rest/dao/, logs/ (not committed)
- backend/rest/dao/: BaseDao.php (connection + generic CRUD), one DAO per table (UserDao, CitizenDao, VehicleDao, ViolationTypeDao, TicketDao, IncidentDao, IncidentCitizenDao), test.php and test_crud.php
- database/: sarajevo_police.sql (schema + sample data; the single source of truth for the database)
- docs/: AI_LOG.md, erd.svg / erd.png

## Frontend rules
- Each view is in its own file in frontend/views/ and is loaded once by utils/router.js with fetch().
- Navigation uses hash links (#/tickets). Never use links that reload the page.
- Use Bootstrap 5 classes; avoid inline styles. Admin-only elements get the class `admin-only`.
- Only files in frontend/services/ talk to the backend (from Milestone 4 on); views must not contain AJAX calls.

## Backend rules
- Requests go: route -> service -> DAO -> database.
- Only services call DAO classes. Routes never call DAO classes directly.
- All SQL uses PDO prepared statements. Values never go into the SQL text; column names only through the BaseDao whitelist.
- Every DAO extends BaseDao and passes its table name and writable columns to the parent constructor.
- One DAO per table. A DAO only reads and writes data: no validation, no HTTP, no JSON.
- UserDao never returns password_hash, except get_by_email() (used by login).
- Change a counter or several rows that belong together in one statement or one transaction.
- Routes return JSON with correct HTTP status codes.

## Conventions
- PHP classes: PascalCase (TicketService, TicketDao). Files are named after the class.
- PHP methods and variables: snake_case, as in the course lectures (get_by_id, $table_name).
- JavaScript functions and variables: camelCase.
- Database tables and columns: snake_case, tables in plural (tickets, violation_types).
- Avoid SQL reserved words as column names (e.g. officer_rank instead of rank).

## Rules for AI assistants
- Do not add new libraries or frameworks without asking me first.
- Do not change the database schema without asking me first.
- Never put passwords, keys or secrets in code; they belong in backend/config/config.php, which is not committed. Update config.example.php instead.
- Do not edit files in backend/logs/.
- Make small changes and explain what you changed and why.
- Update docs/AI_LOG.md in every milestone.
