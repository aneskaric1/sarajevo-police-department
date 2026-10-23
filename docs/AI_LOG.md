# AI log

## Milestone 1: setup, static frontend, ERD

### AI tools used
- Claude (Claude Code / Claude app): read the course documents, proposed the project idea (police department portal), generated the folder structure, the static SPA (index.html, router, views, CSS), the draft ERD (SVG/PNG) and draft SQL schema, README and AGENTS.md.
- Everything was reviewed by me before submitting. **TODO (student): read every file once and be able to explain it at the defense.**

### Most important prompts
1. "Research these course files and, based on them, make a simple web application for milestone 1: frontend and schemas only. Police department website with admin and user roles; after login a new section with generic features like tickets."
2. "Pages must be in frontend/views/, loaded into index.html with a hash router (no full page reloads), and the backend folders must exist."

### Cases where the AI was wrong or produced poor code
1. **Libraries could not be installed.** The AI planned to copy Bootstrap, jQuery and Chart.js into `frontend/assets/vendor/` using npm. The download was blocked in its environment, so it changed the plan to load them from the jsDelivr CDN, as the course lab does. Noticed: npm returned a 403 error. Consequence: the app needs internet access to look styled.
2. **Hard-coded user in the profile page.** The first version of `views/profile.html` always showed "Emir Kovac", even when the admin was logged in. Noticed while reviewing the router test (the profile view did not use the logged-in user). Fixed: an `onLoad('profile')` hook in `assets/js/app.js` fills the form from `AuthService.current()`.
3. **ERD labels hidden behind boxes.** The first drawing put the "1" and "N" cardinality labels on the box borders, so some were unreadable. Noticed when looking at the rendered PNG. Fixed by moving the labels further along the lines.
4. **TODO (student):** add your own case here if you find another mistake while testing (for example a Bootstrap class that does not exist or a wrong behaviour on mobile).
