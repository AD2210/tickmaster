/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import Chart from 'chart.js/auto';
// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';


// Initialise tous les offcanvas déclarés par data-bs-*
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-bs-toggle="offcanvas"]').forEach(trigger => {
    const offcanvasEl = document.querySelector(trigger.getAttribute('data-bs-target'));
    // crée ou récupère l’instance
    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
    trigger.addEventListener('click', () => offcanvas.toggle());
  });
});

