document.addEventListener('DOMContentLoaded', function () {
  var navLinks = document.getElementById('nav-links');
  var toggle   = document.getElementById('navbar-toggle');

  if (toggle && navLinks) {
    toggle.addEventListener('click', function () {
      var isOpen = navLinks.classList.toggle('open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  document.querySelectorAll('#nav-links a').forEach(function (link) {
    link.addEventListener('click', function () {
      if (navLinks) {
        navLinks.classList.remove('open');
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
      }
    });
  });
});
