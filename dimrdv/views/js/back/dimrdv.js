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
  document.body.classList.add('dimrdv-back-module');

  // Example:  Add a confirmation dialog to a button
  const generateItineraryButton = document.querySelector('button[name="generate_itinerary"]');
  if (generateItineraryButton) {
    generateItineraryButton.addEventListener('click', function(event) {
      const checkedCheckboxes = document.querySelectorAll('input[name="selected[]"]:checked');
      if (checkedCheckboxes.length === 0) {
        alert('Please select at least one appointment to generate the itinerary.');
        event.preventDefault(); // Prevent form submission
      } else {
          const confirmation = confirm('Are you sure you want to generate the itinerary for the selected appointments?');
          if (!confirmation) {
              event.preventDefault(); // Prevent form submission if not confirmed
          }
      }
    });
  }

  // Add more back-end JavaScript functionality here

});
