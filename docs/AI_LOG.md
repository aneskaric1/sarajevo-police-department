# AI log

## Milestone 1: setup, static frontend, ERD

### AI tools used
- Claude (Claude Code / Claude app): proposed the project idea (police department portal), generated the folder structure, the static SPA (index.html, router, views, CSS), the draft ERD (SVG/PNG) and draft SQL schema, README and AGENTS.md.
- Everything was reviewed by me before submitting.

### Most important prompts
1. "Make a simple web application for milestone 1: frontend and schemas only. Police department website with admin and user roles; after login a new section with generic features like tickets."
2. "Pages must be in frontend/views/, loaded into index.html with a hash router (no full page reloads), and the backend folders must exist."

### Cases where the AI was wrong or produced poor code
1. **Libraries could not be installed.** The AI planned to copy Bootstrap, jQuery and Chart.js into `frontend/assets/vendor/` using npm. The download was blocked in its environment, so it changed the plan to load them from the jsDelivr CDN, as the course lab does. Noticed: npm returned a 403 error. Consequence: the app needs internet access to look styled.
2. **Hard-coded user in the profile page.** The first version of `views/profile.html` always showed "Emir Kovac", even when the admin was logged in. Noticed while reviewing the router test (the profile view did not use the logged-in user). Fixed: an `onLoad('profile')` hook in `assets/js/app.js` fills the form from `AuthService.current()`.
3. **ERD labels hidden behind boxes.** The first drawing put the "1" and "N" cardinality labels on the box borders, so some were unreadable. Noticed when looking at the rendered PNG. Fixed by moving the labels further along the lines.

## Milestone 2: database and DAO layer

### AI tools used
- Claude (Claude Code): turned the draft schema into `database/sarajevo_police.sql` with sample data, wrote `Config` (config.example.php), `BaseDao` and the seven child DAOs, and the two test scripts. It also updated the ERD, README and AGENTS.md.
- The AI could not run MySQL in its own environment, so it tested the DAOs on an SQLite copy of the schema. I then ran both tests myself on XAMPP with MySQL: `test.php` printed 6, 8, 8, 6, 10, 8 and 12 rows, and `test_crud.php` finished with `Passed: 59, failed: 0`.

### Most important prompts
1. "Next milestone. First plan what you will do and explain it before I give the signal."
2. "Yes, the reviewer merged M1. Yes, fix the naming: follow the lectures. Go!"

### Cases where the AI was wrong or produced poor code
1. **Reserved word as a column name.** The Milestone 1 draft used a column named `rank`. `RANK` is a reserved word in MySQL 8, and BaseDao builds `UPDATE users SET rank = :rank` from the array keys, so an update of that column could fail. Noticed while writing the generic `update()` in BaseDao. Fixed by renaming the column to `officer_rank` and adding a rule to AGENTS.md.
2. **Composite primary key did not fit BaseDao.** The draft gave `incident_citizens` a primary key of (incident_id, citizen_id) and no `id`. BaseDao's `get_by_id()`, `update()` and `delete()` all use `WHERE id = :id`, so they could not work for that table. Fixed by adding an `id` column and keeping (incident_id, citizen_id) as a UNIQUE key; the ERD was updated.
3. **A test that depended on the database.** One check in `test_crud.php` expected `officer_name` to be NULL for an incident without an officer. It failed on SQLite, because SQLite's `CONCAT()` turns NULL into empty text while MySQL returns NULL. Fixed by checking `officer_badge`, which is NULL in both databases. This also shows that the AI's SQLite test is not the same as testing on MySQL.
4. **`php` is not recognized.** The AI told me to run `php rest/dao/test.php`, but on my Windows machine PHP was not in the PATH, so PowerShell said the command was not found. I fixed it by calling `C:\xampp\php\php.exe` directly. The instructions should have mentioned this for XAMPP.

## Milestone 3: services, routes, OpenAPI and external API

### AI tools used
- Claude (Claude Code): wrote the business logic layer (`utils/exceptions.php`, `utils/Validator.php`, `BaseService` and one service per entity), the FlightPHP presentation layer (`index.php` with one error handler, one routes file per entity, `server.php`), the Open-Meteo `WeatherService` with the `GET /weather` endpoint, and the OpenAPI documentation with Swagger UI (`backend/public/v1/docs`). It also updated README and AGENTS.md.
- The AI could not use Composer, MySQL or the real Open-Meteo API in its environment. It tested with FlightPHP and swagger-php downloaded from GitHub, an SQLite copy of the database (81 API requests, all with the expected status) and a fake weather server (success, error page, HTTP 500, timeout). I still have to run `composer install` and check everything on XAMPP.

### Most important prompts
1. "Let's go to milestone 3. Tell me which files I have sent you so far."
2. "Research if needed, then we continue with milestone 3."

### Cases where the AI was wrong or produced poor code
1. **A comment that closed itself.** The generated `doc_setup.php` had a comment explaining that comments must be written as `/* */`. The `*/` inside the text ended the comment early and PHP stopped with a parse error. Noticed with `php -l`. Fixed by rewording the comment.
2. **Quotes in the OpenAPI examples.** The example `Field 'location' is required.` was written inside single quotes without escaping, which broke the PHP syntax of the schema. Noticed with `php -l`; fixed by escaping the quotes (`\'`).
3. **Testing without the real tools.** Because Composer and Open-Meteo were blocked for the AI, it could not prove that `composer install` and the live weather call work. It said so and tested with replacements, which is not the same as the real thing.
