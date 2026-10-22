/* app.js: starts the app and holds the small scripts of individual views. */

// Logout: clear the mock session; the link itself goes to #/home.
document.getElementById('logout-link').addEventListener('click', () => AuthService.logout());

UI.handleDemoForms();
UI.handleTableSearch();

// ----- View scripts (run once, right after the view's HTML is loaded) -----

Router.onLoad('login', (view) => {
  const form = view.querySelector('#login-form');
  const error = view.querySelector('#login-error');
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
    const user = AuthService.login(form.email.value, form.password.value);
    if (!user) { error.classList.remove('d-none'); return; }
    error.classList.add('d-none');
    form.reset();
    location.hash = '#/dashboard';
  });
  // Demo buttons fill in the form so you can test both roles quickly.
  view.querySelectorAll('[data-fill]').forEach(btn => {
    btn.addEventListener('click', () => {
      const account = AuthService.DEMO_ACCOUNTS[Number(btn.dataset.fill)];
      form.email.value = account.email;
      form.password.value = account.password;
    });
  });
});

Router.onLoad('register', (view) => {
  const form = view.querySelector('#register-form');
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    const mismatch = form.password.value !== form.confirm.value;
    form.confirm.setCustomValidity(mismatch ? 'Passwords do not match' : '');
    if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
    form.reset();
    form.classList.remove('was-validated');
    UI.toast('Account request sent (demo only). Use a demo account to log in.');
    location.hash = '#/login';
  });
});

Router.onLoad('dashboard', (view) => {
  if (!window.Chart) return; // CDN not reachable: the rest of the page still works
  new Chart(view.querySelector('#chart-tickets'), {
    type: 'bar',
    data: {
      labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
      datasets: [{ label: 'Tickets issued', data: [112, 98, 134, 121, 87, 76], backgroundColor: '#14213d', borderRadius: 3 }]
    },
    options: { plugins: { legend: { display: false } }, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
  });
  new Chart(view.querySelector('#chart-incidents'), {
    type: 'doughnut',
    data: {
      labels: ['Traffic', 'Theft', 'Disturbance', 'Assault', 'Other'],
      datasets: [{ data: [38, 24, 18, 9, 11], backgroundColor: ['#14213d', '#f2b705', '#5b7aa6', '#b3392f', '#9aa5b5'] }]
    },
    options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
  });
});

Router.onLoad('profile', (view) => {
  const user = AuthService.current();
  const [first, ...rest] = user.name.split(' ');
  view.querySelector('#pf-first').value = first;
  view.querySelector('#pf-last').value = rest.join(' ');
  view.querySelector('#pf-badge').value = user.badge;
  view.querySelector('#pf-email').value = user.email;
});

Router.start();
