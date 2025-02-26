/**
 * @author Roberto Minini <r.minini@solution61.fr>
 * @copyright 2025 Roberto Minini
 * @license MIT
 *
 * This file is part of the dimrdv project.
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

document.addEventListener('DOMContentLoaded', function() {
  // This code runs when the DOM is fully loaded

  // Example: Add a class to the body
  document.body.classList.add('dimrdv-front-module');

  // Example:  Simple form validation (you can enhance this)
  const form = document.querySelector('.dimrdv-form');
  if (form) {
    form.addEventListener('submit', function(event) {
      const lastname = document.querySelector('#lastname').value;
      if (!lastname) {
        alert('Please enter your last name.');
        event.preventDefault(); // Prevent form submission
      }
       // Add more validation checks here as needed
    });
  }
    // Add more front-end JavaScript functionality here
});
