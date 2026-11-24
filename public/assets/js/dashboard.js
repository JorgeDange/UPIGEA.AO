/* PIGEA Uploads — comportamentos do painel (sidebar móvel) */
(function () {
  'use strict';

  var wrap = document.querySelector('.dashboard-wrap');
  var toggle = document.querySelector('.sidebar-toggle');
  if (wrap && toggle) {
    toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      wrap.classList.toggle('sidebar-open');
    });
    document.addEventListener('click', function (e) {
      if (!e.target.closest('.dashboard-sidebar') && !e.target.closest('.sidebar-toggle')) {
        wrap.classList.remove('sidebar-open');
      }
    });
  }
})();
