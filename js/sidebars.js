/* global bootstrap: false */
(function () {
  'use strict';
  var tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });
})();

// Toggle sidebar
var sidebar = document.getElementById('sidebar');
var toggleButton = document.getElementById('sidebar-toggle');

toggleButton.addEventListener('click', function() {
  sidebar.classList.toggle('active');
});