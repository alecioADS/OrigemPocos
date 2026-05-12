document.addEventListener('DOMContentLoaded', function () {
  // Fecha o menu mobile ao clicar em um link
  document.querySelectorAll('#nav-links a').forEach(function (link) {
    link.addEventListener('click', function () {
      document.getElementById('nav-links').classList.remove('open');
    });
  });
});
