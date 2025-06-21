const dragonGameInstances = {};

function startDragonGame(containerId, buttonIdToActivate) {
  const container = document.getElementById(containerId);
  if (!container) return;

  if (dragonGameInstances[containerId]) {
    dragonGameInstances[containerId].cleanup();
  }

  container.innerHTML = `
    <div class="dragon-container">
      <img class="dragon-img" src="assets/img/dragon_awake.png" />
      <div class="countdown-timer">Next blink in: --</div>
      <div class="status-message">Click START to begin.</div>
      <button class="dragon-button start-btn">START</button>
    </div>
  `;

  const img = container.querySelector(".dragon-img");
  const countdown = container.querySelector(".countdown-timer");
  const status = container.querySelector(".status-message");
  const startBtn = container.querySelector(".start-btn");
  const button = document.getElementById(buttonIdToActivate);

  let startTime = null;
  let gameLoop = null;
  let blinkActive = false;
  let blinkInterval = 10000;
  let blinkDuration = 200;
  let nextBlinkTime = null;
  let resetTimeout = null;
  let blinkTimeout = null;

  const awakeSrc = "assets/img/dragon_awake.png";
  const asleepSrc = "assets/img/dragon_asleep.png";

  function activateButton(active) {
    if (!button) return;
    button.disabled = !active;
    button.classList.toggle("active", active);
    button.classList.toggle("inactive", !active);
  }

  function blinkDragon() {
    blinkActive = true;

    const blinkImage = new Image();
    blinkImage.onload = () => {
      img.src = asleepSrc + `?t=${Date.now()}`;
    };
    img.src = asleepSrc + `?t=${Date.now()}`;
    console.log(img.src);
    activateButton(true);

    blinkTimeout = setTimeout(() => {
      const wakeImage = new Image();
      wakeImage.onload = () => {
        img.src = awakeSrc + `?t=${Date.now()}`;
      };
      wakeImage.src = awakeSrc + `?t=${Date.now()}`;

      activateButton(false);
      blinkActive = false;
      nextBlinkTime = Date.now() + blinkInterval;
    }, blinkDuration);
  }

  function loop() {
    const now = Date.now();

    if (!blinkActive && now >= nextBlinkTime) {
      blinkDragon();
    }

    const timeLeft = Math.max(0, nextBlinkTime - now);
    countdown.textContent = `Next blink in: ${(timeLeft / 1000).toFixed(1)}s`;

    gameLoop = requestAnimationFrame(loop);
  }

  function startGame() {
    startBtn.remove();
    status.textContent = "Click your main button during a blink!";
    startTime = Date.now();
    nextBlinkTime = startTime + blinkInterval;
    loop();
  }

  startBtn.addEventListener("click", startGame);

  dragonGameInstances[containerId] = {
    cleanup: () => {
      cancelAnimationFrame(gameLoop);
      clearTimeout(resetTimeout);
      clearTimeout(blinkTimeout);
      activateButton(false);
    }
  };
}

// Styles only added once
if (!document.getElementById("dragon-style")) {
  const style = document.createElement("style");
  style.id = "dragon-style";
  style.textContent = `
    .dragon-container {
      text-align: center;
      margin: 20px auto;
    }
    .dragon-img {
      width: 100%;
      max-width: 80%;
      image-rendering: pixelated;
    }
    .countdown-timer {
      font-size: 1.2em;
      margin-top: 10px;
    }
    .status-message {
      margin: 10px 0;
    }
    .dragon-button {
      padding: 8px 16px;
      margin: 5px;
    }
    button.active {
      background-color: limegreen;
    }
    button.inactive {
      background-color: gray;
    }
  `;
  document.head.appendChild(style);
}
