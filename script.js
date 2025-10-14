/*
 * Pequeño script para añadir efectos interactivos a la landing page.
 * - Hace que el menú de navegación se vuelva "pegajoso" al hacer scroll.
 */

window.addEventListener('DOMContentLoaded', () => {
  const nav = document.getElementById('navbar');
  const heroSection = document.querySelector('.hero');

  const stickyNav = () => {
    if (window.scrollY > heroSection.offsetHeight - nav.offsetHeight) {
      nav.classList.add('sticky');
    } else {
      nav.classList.remove('sticky');
    }
  };

  window.addEventListener('scroll', stickyNav);
});