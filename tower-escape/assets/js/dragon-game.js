function startDragonGame(containerId,buttonIdToActivate) {
  const container = document.getElementById(containerId);
  if (!container) return;

  // Add styles
  const style = document.createElement("style");
  style.textContent = `
    .dragon-container {
      text-align: center;
      margin: 20px auto;
    }
    .dragon-img {
      width: 100%;
      image-rendering: pixelated;
    }
    .status-message {
      font-size: 1.2em;
      margin-top: 10px;
    }
    .dragon-button {
      padding: 10px 20px;
      margin-top: 15px;
      font-size: 1rem;
      cursor: pointer;
    }
    .countdown-timer {
      font-size: 1.5em;
      margin-top: 10px;
      color: #555;
    }
  `;
  document.head.appendChild(style);

  // Create elements
  const game = document.createElement("div");
  game.className = "dragon-container";

  const dragonImg = document.createElement("img");
  dragonImg.className = "dragon-img";
  dragonImg.src = "assets/img/dragon_awake.png";

  const statusMsg = document.createElement("div");
  statusMsg.className = "status-message";
  statusMsg.textContent = "Click when the dragon blinks!";

  const countdown = document.createElement("div");
  countdown.className = "countdown-timer";
  countdown.textContent = "Next blink in: 10.0s";

  const stopBtn = document.createElement("button");
  stopBtn.className = "dragon-button";
  stopBtn.textContent = "STOP!";

  // Game logic
  let startTime = null;
  let blinkInterval = null;
  let blinkDuration = 200; // ms (updated to 2 seconds)
  let isBlinking = false;
  let gameEnded = false;
  let blinkCountdown = 10000; // ms
  let nextBlinkTime = null;

  function blinkDragon() {
    isBlinking = true;
    dragonImg.src = "assets/img/dragon_asleep.png";
    setTimeout(() => {
      dragonImg.src = "assets/img/dragon_awake.png";
      isBlinking = false;
      nextBlinkTime = Date.now() + blinkCountdown;
    }, blinkDuration);
  }

  function updateCountdown() {
    if (gameEnded) return;
    const remaining = Math.max(0, nextBlinkTime - Date.now());
    countdown.textContent = `Next blink in: ${(remaining / 1000).toFixed(1)}s`;
    requestAnimationFrame(updateCountdown);
  }

  function startGame() {
    gameEnded = false;
    stopBtn.disabled = false;
    statusMsg.textContent = "Click when the dragon blinks!";
    startTime = Date.now();
    nextBlinkTime = startTime + blinkCountdown;
    blinkInterval = setInterval(blinkDragon, blinkCountdown);
    requestAnimationFrame(updateCountdown);
  }

  function resetGameAfterDelay() {
    setTimeout(() => {
      clearInterval(blinkInterval);
      startGame();
    }, 3000); // reset after 3 seconds
  }

  stopBtn.addEventListener("click", () => {
    if (gameEnded) return;

    const elapsed = Date.now() - startTime;
    const secondsElapsed = elapsed / 1000;
    const withinBlinkWindow = Math.abs(secondsElapsed % 10) <= 0.1 || Math.abs((secondsElapsed % 10) - 10) <= 0.1;

    gameEnded = true;
    clearInterval(blinkInterval);

    if (withinBlinkWindow) {
      statusMsg.textContent = `Success! You stopped it at ${secondsElapsed.toFixed(2)}s!`;
      if (buttonIdToActivate) {
        const button = document.getElementById(buttonIdToActivate);
        console.log("Activate buttone"+buttonIdToActivate);
        if (button) {
          button.classList.remove("inactive");
          button.classList.add("active");
          button.disabled = false;
        }
      }

    } else {
      statusMsg.textContent = `Missed! You stopped at ${secondsElapsed.toFixed(2)}s. Restarting...`;
      resetGameAfterDelay();
    }

    stopBtn.disabled = true;
  });

  // Append elements
  game.appendChild(dragonImg);
  game.appendChild(countdown);
  game.appendChild(statusMsg);
  game.appendChild(stopBtn);
  container.appendChild(game);

  // Start
  startGame();
}

// Example usage:
// startDragonGame("blink-space");
