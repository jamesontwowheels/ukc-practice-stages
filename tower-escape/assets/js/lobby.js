// assets/js/lobby.js
const steps       = document.querySelectorAll('.step');
const progressEls = document.querySelectorAll('.progress-step');

const requestBtn     = document.getElementById('request-location-btn');
const locStatus      = document.getElementById('location-status');
const teamList       = document.getElementById('team-list');
const createForm     = document.getElementById('create-team-form');
const newTeamInput   = document.getElementById('new-team-name');
const teamStatus     = document.getElementById('team-status');
const confirmBtn     = document.getElementById('confirm-team-btn');

const gameStatus     = document.getElementById('game-status');
const finalTeamName  = document.getElementById('final-team-name');
const countdownText  = document.getElementById('countdown-text');
const countdownCircle= document.getElementById('countdown-progress');
const changeBtn      = document.getElementById('change-team-btn');
const startBtn       = document.getElementById('go-to-start');

let teams = [], selected = null, pollInt, secs = 10;

// Show step `i` (0-based) and update progress indicator
function showStep(i) {
  steps.forEach((s, idx) => {
    s.classList.toggle('active', idx === i);
    progressEls[idx].classList.toggle('active', idx === i);
    progressEls[idx].classList.toggle('completed', idx < i);
  });
}

// --- Step 1: Location Permissions ---

function checkLoc() {
  if (!navigator.geolocation) {
    locStatus.textContent = 'Geolocation unsupported.';
    return;
  }
  navigator.permissions.query({ name: 'geolocation' })
    .then(res => {
      if (res.state === 'granted') grantLoc();
      else {
        locStatus.textContent = 'Permission needed.';
        requestBtn.style.display = 'inline-block';
      }
      res.onchange = checkLoc;
    });
}

function grantLoc() {
  locStatus.textContent = 'Permission granted ✓';
  requestBtn.style.display = 'none';
  showStep(1);
  fetchTeams();
  fetch('assets/php/get_my_team.php')
    .then(r => r.json())
    .then(team => {
      if (team && team.id) {
        selected = team;
        teamStatus.textContent = `Selected: ${team.name}`;
        confirmBtn.style.display = 'inline-block';
        showStep(2);
        finalTeamName.textContent = team.name;
        startPolling();
      }
    })
    .catch(() => console.log('No team membership or error loading team.'));
}

requestBtn.onclick = () =>
  navigator.geolocation.getCurrentPosition(grantLoc, () =>
    locStatus.textContent = 'Permission denied.'
  );

// --- Step 2: Fetch & Join/Create Teams ---

function fetchTeams() {
  fetch('assets/php/get_teams.php')
    .then(r => r.json())
    .then(data => {
      teams = data;
      renderTeams();
    })
    .catch(() => teamList.textContent = 'Error loading teams.');
}

function renderTeams() {
  teamList.innerHTML = '';
  teams.forEach(t => {
    const card = document.createElement('div');
    card.className = 'team-card' + (selected?.id === t.id ? ' selected' : '');
    card.textContent = t.name;
    card.onclick = () => joinTeam(t, card);
    teamList.appendChild(card);
  });
}

function joinTeam(team, cardEl) {
  fetch('assets/php/join_team.php', {
    method: 'POST',
    headers: { 'Content-Type':'application/json' },
    body: JSON.stringify({ team_id: team.id })
  })
  .then(r => r.json())
  .then(() => {
    selected = team;
    document.querySelectorAll('.team-card')
            .forEach(c=>c.classList.remove('selected'));
    cardEl.classList.add('selected');
    teamStatus.textContent = `Selected: ${team.name}`;
    confirmBtn.style.display = 'inline-block';
  })
  .catch(() => teamStatus.textContent = 'Error joining team.');
}

createForm.onsubmit = e => {
  e.preventDefault();
  const name = newTeamInput.value.trim();
  if (name.length < 2) {
    teamStatus.textContent = 'Name too short.';
    return;
  }
  teamStatus.textContent = 'Creating…';
  fetch('assets/php/create_team.php', {
    method: 'POST',
    headers: { 'Content-Type':'application/json' },
    body: JSON.stringify({ name })
  })
  .then(r => r.json())
  .then(newTeam => {
    teams.push(newTeam);
    renderTeams();
    const newCard = document.querySelector('.team-card:last-child');
    joinTeam(newTeam, newCard);
    newTeamInput.value = '';
  })
  .catch(() => teamStatus.textContent = 'Error creating team.');
};

confirmBtn.onclick = () => {
  if (!selected) return;
  finalTeamName.textContent = selected.name;
  showStep(2);
  startPolling();
};

// --- Step 3: Poll for Game Start ---

function startPolling() {
  gameStatus.textContent = 'Waiting…';
  startBtn.disabled = true;
  secs = 10; updateCountdown();
  pollInt = setInterval(() => {
    secs--; updateCountdown();
    if (secs <= 0) { secs = 10; pollGame(); }
  }, 1000);
  pollGame();
}

function updateCountdown() {
  countdownText.textContent = secs;
  const circ = 2 * Math.PI * 45;
  countdownCircle.style.strokeDashoffset = circ * (1 - secs / 10);
}

function pollGame() {
  fetch('assets/php/check_game_status.php')
    .then(r => r.json())
    .then(d => {
      if (d.started) {
        clearInterval(pollInt);
        gameStatus.textContent = 'Started! 🎉';
        startBtn.disabled = false;
        secs = 0; updateCountdown();
      }
    })
    .catch(() => gameStatus.textContent = 'Error, retrying…');
}

changeBtn.onclick = () => {
  clearInterval(pollInt);
  showStep(1);
  confirmBtn.style.display = 'inline-block';
  startBtn.disabled = true;
};

startBtn.onclick = () => {
  if (!selected) {
    showStep(1);
    return;
  }
  window.location.href = `game_start.php?team=${encodeURIComponent(selected.name)}`;
};

// Initialize
showStep(0);
checkLoc();
