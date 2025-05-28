function launchCelebration(particleCount = 150, duration = 15000) {
  const overlay = document.createElement("div");
  overlay.id = "celebration-overlay";
  document.body.appendChild(overlay);

  for (let i = 0; i < particleCount; i++) {
    const el = document.createElement("div");
    const rand = Math.random();

    if (rand < 0.2) {
      el.className = "streamer";
    } else if (rand < 0.4) {
      el.className = "sparkle";
    } else {
      el.className = "confetti";
    }

    const size = Math.random() * 12 + 12; // bigger base size
    if (el.classList.contains("streamer")) {
      el.style.width = `${size / 3}px`;
      el.style.height = `${size * 2}px`;
    } else {
      el.style.width = `${size}px`;
      el.style.height = `${size}px`;
    }

    if (el.classList.contains("confetti")) {
      el.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 50%)`;
    }

    el.style.left = Math.random() * 100 + "vw";
    el.style.animationDuration = 3 + Math.random() * 5 + "s";
    el.style.animationDelay = Math.random() * (duration / 1000 - 2) + "s";

    overlay.appendChild(el);
  }

  const fireworkInterval = setInterval(() => {
    createFireworkBurst(overlay);
  }, 1000);

  setTimeout(() => {
    clearInterval(fireworkInterval);
    overlay.remove();
  }, duration);
}
