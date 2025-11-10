document.addEventListener("DOMContentLoaded", () => {
  const loader = document.getElementById("loading-overlay");

  // Fade out the loader once everything is loaded
  window.addEventListener("load", () => {
    setTimeout(() => {
      loader.classList.add("fade-out");
    }, 1500); // short delay for smoothness
  });

  // Show loader again on navigation (for multi-page apps)
  document.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", e => {
      const href = link.getAttribute("href");
      if (href && !href.startsWith("#") && !link.target) {
        loader.classList.remove("fade-out");
        loader.style.opacity = "1";
      }
    });
  });
});