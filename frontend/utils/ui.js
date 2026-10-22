/* ui.js: small helpers shared by all views. */
const UI = (() => {
  function toast(message) {
    const el = document.getElementById('app-toast');
    document.getElementById('app-toast-body').textContent = message;
    if (window.bootstrap) bootstrap.Toast.getOrCreateInstance(el, { delay: 3500 }).show();
  }

  // Role is stored on <body>; CSS hides .admin-only when it is not "admin".
  function setRole(role) {
    document.body.dataset.role = role;
  }

  // Demo forms: validate, show a toast, close the modal. Nothing is sent anywhere yet.
  function handleDemoForms() {
    document.addEventListener('submit', (event) => {
      const form = event.target.closest('form[data-demo-form]');
      if (!form) return;
      event.preventDefault();
      if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
      }
      const modalEl = form.closest('.modal');
      if (modalEl && window.bootstrap) bootstrap.Modal.getInstance(modalEl)?.hide();
      form.reset();
      form.classList.remove('was-validated');
      toast(form.dataset.demoForm + ' (demo only: not saved yet, the backend arrives in Milestone 4)');
    });
  }

  // Search box above a table: hides rows that do not contain the text.
  function handleTableSearch() {
    document.addEventListener('input', (event) => {
      const box = event.target.closest('[data-table-search]');
      if (!box) return;
      const rows = document.querySelectorAll(box.dataset.tableSearch + ' tbody tr');
      const text = box.value.toLowerCase();
      rows.forEach(row => { row.hidden = !row.textContent.toLowerCase().includes(text); });
    });
  }

  return { toast, setRole, handleDemoForms, handleTableSearch };
})();
