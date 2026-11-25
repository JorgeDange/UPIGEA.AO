/* PIGEA Uploads — JS da aplicação (upload com progresso, modais, copiar URL) */
(function () {
  'use strict';

  var CSRF = document.querySelector('meta[name="csrf-token"]').content;

  /* ---------- Modais ---------- */
  document.addEventListener('click', function (e) {
    var open = e.target.closest('[data-modal-open]');
    if (open) {
      var modal = document.getElementById(open.getAttribute('data-modal-open'));
      if (modal) { modal.classList.add('open'); }
      return;
    }
    var close = e.target.closest('[data-modal-close]');
    if (close) {
      var m = close.closest('.dashboard-modal');
      if (m) { m.classList.remove('open'); }
      var box = m && m.querySelector('[data-upload-box]');
      if (box) {
        var clear = box.querySelector('[data-file-clear]');
        if (clear) { clear.click(); }
      }
      return;
    }
    if (e.target.classList && e.target.classList.contains('dashboard-modal')) {
      e.target.classList.remove('open');
      var mbox = e.target.querySelector('[data-upload-box]');
      if (mbox) {
        var mclear = mbox.querySelector('[data-file-clear]');
        if (mclear) { mclear.click(); }
      }
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.dashboard-modal.open').forEach(function (m) {
        m.classList.remove('open');
      });
    }
  });

  /* ---------- Copiar URL ---------- */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-copy]');
    if (!btn) return;
    e.preventDefault();
    var url = btn.getAttribute('data-copy');
    navigator.clipboard.writeText(url).then(function () {
      btn.classList.add('copy-done');
      var old = btn.title;
      btn.title = 'Copiado!';
      setTimeout(function () {
        btn.classList.remove('copy-done');
        btn.title = old;
      }, 1500);
    });
  });

  /* ---------- Confirmação de ação (modal própria, sem alert nativo) ---------- */
  var pendingForm = null;

  document.addEventListener('submit', function (e) {
    var form = e.target.closest('form[data-confirm]');
    if (!form || form.dataset.confirmed === '1') { return; }
    e.preventDefault();
    pendingForm = form;

    var modal = document.getElementById('confirm-modal');
    if (!modal) { form.submit(); return; }

    modal.querySelector('[data-confirm-message]').textContent = form.getAttribute('data-confirm');
    modal.querySelector('[data-confirm-title]').textContent = form.getAttribute('data-confirm-title') || 'Confirmar ação';
    modal.querySelector('[data-confirm-yes-label]').textContent = form.getAttribute('data-confirm-label') || 'Confirmar';
    modal.classList.add('open');
  });

  document.addEventListener('click', function (e) {
    var yes = e.target.closest('#confirm-modal [data-confirm-yes]');
    if (yes && pendingForm) {
      var modal = document.getElementById('confirm-modal');
      modal.classList.remove('open');
      var form = pendingForm;
      pendingForm = null;
      form.dataset.confirmed = '1';
      form.submit();
    }
  });

  /* ---------- Upload com barra de progresso ---------- */
  var uploadBoxes = document.querySelectorAll('[data-upload-box]');

  function humanSize(bytes) {
    if (bytes >= 1048576) { return (bytes / 1048576).toFixed(1) + ' MB'; }
    if (bytes >= 1024) { return Math.round(bytes / 1024) + ' KB'; }
    return bytes + ' B';
  }

  uploadBoxes.forEach(function (box) {
    var type = box.getAttribute('data-upload-type');
    var drop = box.querySelector('[data-file-drop]');
    var input = drop.querySelector('input[type="file"]');
    var selected = box.querySelector('[data-file-selected]');
    var nameOut = box.querySelector('[data-suggested-name]');
    var fileOut = box.querySelector('[data-file-name]');
    var sizeOut = box.querySelector('[data-file-size]');
    var clearBtn = box.querySelector('[data-file-clear]');
    var form = box.querySelector('form');
    var categorySelect = form.querySelector('select[name="category"]');
    var progress = box.querySelector('.progress-wrap');
    var fill = box.querySelector('.progress-fill');
    var pct = box.querySelector('[data-progress-pct]');
    var label = box.querySelector('[data-progress-label]');
    var msg = box.querySelector('[data-upload-msg]');

    function showFile(file) {
      fileOut.textContent = file.name;
      sizeOut.textContent = humanSize(file.size);
      selected.hidden = false;
      drop.classList.add('is-filled');
    }

    function clearFile() {
      input.value = '';
      selected.hidden = true;
      nameOut.textContent = '—';
      drop.classList.remove('is-filled');
      if (msg) { msg.style.display = 'none'; }
    }

    clearBtn.addEventListener('click', function () { clearFile(); });

    drop.addEventListener('dragover', function (e) {
      e.preventDefault();
      drop.classList.add('is-dragover');
    });
    drop.addEventListener('dragleave', function () {
      drop.classList.remove('is-dragover');
    });
    drop.addEventListener('drop', function (e) {
      e.preventDefault();
      drop.classList.remove('is-dragover');
      if (e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });

    input.addEventListener('change', function () {
      var file = input.files[0];
      if (!file) { clearFile(); return; }
      if (file.size > 10 * 1024 * 1024) {
        nameOut.textContent = '—';
        clearFile();
        if (msg) {
          msg.classList.add('dashboard-msg', 'is-error');
          msg.style.display = '';
          msg.textContent = 'O ficheiro excede o limite de 10 MB.';
        }
        return;
      }
      if (msg) { msg.style.display = 'none'; }
      showFile(file);

      var ext = file.name.split('.').pop().toLowerCase();
      nameOut.textContent = 'A gerar nome…';
      fetch('/uploads/nome?type=' + type + '&extension=' + encodeURIComponent(ext), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(function (r) { return r.json(); })
        .then(function (data) { nameOut.textContent = data.name; })
        .catch(function () { nameOut.textContent = file.name; });
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var file = input.files[0];
      if (!file) {
        if (msg) {
          msg.classList.add('dashboard-msg', 'is-error');
          msg.style.display = '';
          msg.textContent = 'Escolha um ficheiro para carregar.';
        }
        return;
      }

      var data = new FormData();
      data.append('type', type);
      if (categorySelect) { data.append('category', categorySelect.value); }
      data.append('file', file);
      data.append('_token', CSRF);

      var xhr = new XMLHttpRequest();
      xhr.open('POST', '/uploads');
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhr.setRequestHeader('X-CSRF-TOKEN', CSRF);

      xhr.upload.addEventListener('progress', function (ev) {
        if (ev.lengthComputable) {
          var p = Math.round((ev.loaded / ev.total) * 100);
          progress.classList.add('is-active');
          fill.classList.remove('is-error', 'is-done');
          fill.style.width = p + '%';
          pct.textContent = p + '%';
          label.textContent = 'A carregar…';
        }
      });

      xhr.addEventListener('load', function () {
        var res = {};
        try { res = JSON.parse(xhr.responseText); } catch (err) { /* noop */ }
        // Sucesso: qualquer 2xx (alguns servidores respondem 200 em vez de 201)
        var success = (res && res.upload) && xhr.status >= 200 && xhr.status < 300;
        if (success) {
          fill.style.width = '100%';
          fill.classList.add('is-done');
          pct.textContent = '100%';
          label.textContent = res.upload.name;
          var ok = document.getElementById('upload-success-modal');
          if (ok) {
            ok.querySelector('[data-success-name]').textContent = res.upload.name;
            ok.querySelector('[data-success-url]').textContent = res.upload.url;
            ok.classList.add('open');
          }
          setTimeout(function () { window.location.reload(); }, 1600);
        } else {
          var errorText = 'Falha no upload. Tente novamente.';
          var firstError = res.errors && (res.errors.file || res.errors.category);
          if (firstError) { errorText = firstError[0]; }
          fill.classList.add('is-error');
          pct.textContent = '—';
          label.textContent = 'Falhou';
          if (msg) {
            msg.classList.add('dashboard-msg', 'is-error');
            msg.style.display = '';
            msg.textContent = errorText;
          }
        }
      });

      xhr.addEventListener('error', function () {
        fill.classList.add('is-error');
        label.textContent = 'Erro de rede';
        if (msg) {
          msg.classList.add('dashboard-msg', 'is-error');
          msg.style.display = '';
          msg.textContent = 'Erro de rede. Verifique a ligação e tente novamente.';
        }
      });

      xhr.send(data);
    });
  });

  /* ---------- Copiar URL na modal de sucesso ---------- */
  var successCopy = document.getElementById('success-copy-btn');
  if (successCopy) {
    successCopy.addEventListener('click', function () {
      var urlEl = document.querySelector('[data-success-url]');
      if (!urlEl) return;
      navigator.clipboard.writeText(urlEl.textContent).then(function () {
        successCopy.classList.add('copy-done');
        setTimeout(function () { successCopy.classList.remove('copy-done'); }, 1500);
      });
    });
  }
})();
