# Sarajevo Police Department Portal

Web application for a police department (course project, IT 2001 Web Programming).
A public website with login and registration, and after login a section for officers to manage incidents, tickets, citizens and vehicles. Administrators also manage officers and violation types.

**Status: Milestone 1** (project structure, static single-page frontend, draft ERD). The backend and database are added in later milestones.

## Technology
- Frontend: HTML, CSS, JavaScript, Bootstrap 5, Chart.js, SPA with hash router
- Backend (later): PHP, FlightPHP, PDO, MySQL
- API documentation (later): OpenAPI / Swagger

## Entities and relationships
Draft ERD: [docs/erd.png](docs/erd.png) (source [docs/erd.svg](docs/erd.svg), draft SQL in [docs/schema.sql](docs/schema.sql)).

| Entity | Purpose |
|---|---|
| users | Officers and administrators who log in (role: admin or user) |
| citizens | People known to the department |
| vehicles | Registered vehicles, each owned by one citizen |
| violation_types | Catalogue of violations with a standard fine |
| tickets | A fine issued by an officer to a citizen for a violation, optionally for a vehicle |
| incidents | Cases reported and handled by an officer |
| incident_citizens | Links citizens to incidents as victim, witness or suspect (many-to-many) |

![ERD](docs/erd.png)

## Roles and test accounts
Milestone 1 uses mock accounts in the frontend only (no backend yet).

| Role | Email | Password | Can do |
|---|---|---|---|
| admin | admin@spd.test | admin123 | Everything, including Officers and Violation types, edit and delete |
| user (officer) | officer@spd.test | user123 | View records, add incidents, tickets, citizens, vehicles |

## External API (planned, Milestone 3)
Weather for patrols (OpenWeatherMap), shown in the "Patrol weather" card on the dashboard. The card already exists with sample data.

## Run locally
The pages are loaded with `fetch()`, so open the app through a web server, not by double-clicking `index.html`.
- XAMPP: place the project in `htdocs` and open `http://localhost/<project-folder>/frontend/`
- or, with PHP installed: `php -S localhost:8000 -t frontend` and open `http://localhost:8000`

Internet access is needed for Bootstrap, Chart.js and fonts (loaded from CDNs).

Later milestones: import `docs/*.sql` (Milestone 2), copy `backend/config/config.example.php` to `config.php` and fill it in, run `composer install` in `backend/`.

## Folder structure
```
frontend/   index.html, assets/ (css, js, img), views/, services/, utils/
backend/    index.php, config/, middleware/, rest/ (routes, services, dao), logs/
docs/       ERD, draft schema, AI_LOG.md
AGENTS.md   rules for AI assistants
```

## Links
- Swagger documentation: coming in Milestone 3
- Deployed application: coming in Milestone 5
