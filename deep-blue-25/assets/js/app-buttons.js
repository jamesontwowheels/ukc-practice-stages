document.addEventListener("DOMContentLoaded", function() {
  // Get the current URL path
  const currentPath = window.location.pathname;
  const currentPage = currentPath.substring(currentPath.lastIndexOf('/') + 1);

  // Get all buttons
  const buttons = document.querySelectorAll('.app-button');

  // Loop through buttons and set the active class
  buttons.forEach(button => {
      if (button.getAttribute('href') === currentPage) {
          button.classList.add('active'); // Add active class to the current button
      }
  });
});