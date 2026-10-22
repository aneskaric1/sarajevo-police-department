/*
 * authService.js: MILESTONE 1 MOCK.
 * No backend yet, so login is simulated with two demo accounts.
 * In Milestone 4 this file will call the REST API (POST /auth/login),
 * store the JWT and read the role from the server response.
 */
const AuthService = (() => {
  const KEY = 'spd_user';
  const DEMO_ACCOUNTS = [
    { email: 'admin@spd.test',   password: 'admin123', name: 'Anes Karic',  role: 'admin', badge: 'A-001', rank: 'Chief Inspector' },
    { email: 'officer@spd.test', password: 'user123',  name: 'Emir Kovac',    role: 'user',  badge: 'P-214', rank: 'Patrol Officer' }
  ];

  function login(email, password) {
    const match = DEMO_ACCOUNTS.find(a => a.email === email.trim().toLowerCase() && a.password === password);
    if (!match) return null;
    const { password: _hidden, ...user } = match; // never keep the password in storage
    try { sessionStorage.setItem(KEY, JSON.stringify(user)); } catch (e) { /* storage blocked: stay logged out */ }
    return user;
  }

  function current() {
    try { return JSON.parse(sessionStorage.getItem(KEY)); } catch (e) { return null; }
  }

  function logout() {
    try { sessionStorage.removeItem(KEY); } catch (e) { /* ignore */ }
  }

  return { login, current, logout, DEMO_ACCOUNTS };
})();
