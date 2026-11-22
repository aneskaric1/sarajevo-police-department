# AGENTS.md

## Project
Sarajevo Police Department Portal: a web application for a police department. A public website (home, login, register) and, after login, a section for officers to manage incidents, tickets, citizens and vehicles. Administrators also manage officers and violation types.

Roles: admin, user (a police officer). Entities: users, citizens, vehicles, violation_types, tickets, incidents, incident_citizens.

## Technology stack
- Frontend: HTML, CSS, JavaScript (no framework), Bootstrap 5 (CDN), Chart.js (CDN), single-page application (SPA) with a hash router
- Backend: PHP 8.2+, FlightPHP (REST API), PDO, MySQL (MariaDB in XAMPP), swagger-php 6 for OpenAPI
- External API: Open-Meteo (weather), called only from the backend
- Local server: XAMPP, or `php -S localhost:8000 server.php` in the main folder

## Folder structure
- frontend/: index.html (the only page), assets/ (css, js, img), views/ (one HTML file per view), services/ (code that calls the API; mock auth in Milestone 1), utils/ (router, UI helpers)
- backend/: index.php, config/ (config.example.php committed, config.php not), middleware/, rest/routes/, rest/services/, rest/dao/, logs/ (not committed)
- backend/rest/dao/: BaseDao.php (connection + generic CRUD), one DAO per table (UserDao, CitizenDao, VehicleDao, ViolationTypeDao, TicketDao, IncidentDao, IncidentCitizenDao), test.php and test_crud.php
- backend/utils/: exceptions.php (HttpException, ValidationException 400, NotFoundException 404, ExternalServiceException 503), Validator.php
- backend/rest/services/: BaseService.php and one service per entity, plus WeatherService.php (Open-Meteo)
- backend/rest/routes/: one routes file per entity (UserRoutes.php, TicketRoutes.php, ...), loaded in index.php
- backend/public/v1/docs/: index.html (Swagger UI), swagger.php (generates the JSON), doc_setup.php (info + schemas), paths/ (one Paths class per routes file)
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
- A route only reads the input (path, query, JSON body), calls ONE service method and answers with Flight::json(). No SQL, no rules, no try/catch in routes.
- Services hold all business rules. They never use $_GET/$_POST, echo or status codes; they throw ValidationException / NotFoundException / ExternalServiceException.
- BaseService::add() and update() always call validate(); child services put their rules in validate($entity, $id = null).
- Errors are handled once, in Flight::map('error') in index.php: HttpException -> its status, everything else -> 500 with a general message (details only in error_log).
- Every service is registered once with Flight::register() and used as Flight::ticket_service() etc.
- REST URLs: plural nouns, ids as @id:[0-9]+, filters in the query string, actions as sub-paths (PUT /tickets/{id}/pay). POST answers 201.
- Every route has a matching OpenAPI operation in public/v1/docs/paths/ with all its responses (200/201, 400, 404, 503). Change the route and its docs in the same commit.
- External APIs: called only from a service, with a timeout, status and JSON checks; failures become 503 and are logged. URLs and keys live in config.php.

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
