/*
 * router.js: hash-based router for the single-page app.
 * URL "index.html#/tickets" -> loads views/tickets.html ONCE with fetch(),
 * puts it in the page, and afterwards only shows or hides it.
 * The page never reloads.
 */
const Router = (() => {
  // layout: where the view is shown. access: "public" | "auth" | "admin"
  const ROUTES = {
    home:       { title: 'Home',            layout: 'public', access: 'public' },
    login:      { title: 'Login',           layout: 'public', access: 'public' },
    register:   { title: 'Register',        layout: 'public', access: 'public' },
    dashboard:  { title: 'Dashboard',       layout: 'app',    access: 'auth' },
    incidents:  { title: 'Incidents',       layout: 'app',    access: 'auth' },
    tickets:    { title: 'Tickets',         layout: 'app',    access: 'auth' },
    citizens:   { title: 'Citizens',        layout: 'app',    access: 'auth' },
    vehicles:   { title: 'Vehicles',        layout: 'app',    access: 'auth' },
    profile:    { title: 'My profile',      layout: 'app',    access: 'auth' },
    violations: { title: 'Violation types', layout: 'app',    access: 'admin' },
    officers:   { title: 'Officers',        layout: 'app',    access: 'admin' },
    forbidden:  { title: 'No access',       layout: 'app',    access: 'auth' },
    notfound:   { title: 'Not found',       layout: 'public', access: 'public' }
  };

  const loaded = {};   // view name -> true after its HTML was fetched
  const hooks = {};    // view name -> function run once after the view is loaded

  function onLoad(name, fn) { hooks[name] = fn; }

  function currentName() {
    const name = location.hash.replace(/^#\/?/, '').split('?')[0] || 'home';
    return name;
  }

  async function loadView(name, container) {
    if (loaded[name]) return;
    const response = await fetch('views/' + name + '.html');
    if (!response.ok) throw new Error('View not found: ' + name);
    const section = document.createElement('section');
    section.dataset.view = name;
    section.innerHTML = await response.text();
    container.appendChild(section);
    loaded[name] = true;
    if (hooks[name]) hooks[name](section);
  }

  async function render() {
    let name = currentName();
    if (!ROUTES[name]) name = 'notfound';
    let route = ROUTES[name];
    const user = AuthService.current();

    // Guards (a real check happens on the backend in Milestone 4)
    if (route.access !== 'public' && !user) { location.hash = '#/login'; return; }
    if (route.access === 'admin' && user.role !== 'admin') { name = 'forbidden'; route = ROUTES[name]; }
    if (user && (name === 'login' || name === 'register')) { location.hash = '#/dashboard'; return; }

    UI.setRole(user ? user.role : 'guest');
    const isApp = route.layout === 'app';
    document.getElementById('public-layout').classList.toggle('d-none', isApp);
    document.getElementById('app-layout').classList.toggle('d-none', !isApp);
    if (isApp) {
      document.getElementById('role-badge').textContent = user.role;
      document.getElementById('user-name').textContent = user.name;
      document.getElementById('page-title').textContent = route.title;
    }

    const container = document.getElementById(isApp ? 'app-view' : 'public-view');
    try {
      await loadView(name, container);
    } catch (error) {
      container.innerHTML = '<div class="container py-5"><div class="alert alert-danger">Could not load this page. Open the app through a web server (XAMPP or php -S), not by double-clicking index.html.</div></div>';
      return;
    }
    container.querySelectorAll(':scope > section').forEach(s => { s.hidden = s.dataset.view !== name; });
    document.title = route.title + ' | Sarajevo Police Department';
    document.querySelectorAll('[data-route]').forEach(a => {
      a.classList.toggle('active', a.dataset.route === name);
    });
    window.scrollTo(0, 0);
  }

  function start() {
    window.addEventListener('hashchange', render);
    render();
  }

  return { start, onLoad };
})();
