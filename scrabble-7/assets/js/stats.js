function renderStatsCarousel() {
  // Try to get stats from sessionStorage first
  const storedStats = sessionStorage.getItem('userStats');
  const stats = storedStats ? JSON.parse(storedStats) : {
    words_played: [],
    puzzles: { attempts: [], solved: [] },
    letters_played: 0
  };

  const container = document.getElementById("carousel");
  container.innerHTML = ''; // Clear previous slides if any

  function createCard(title, content) {
    const slide = document.createElement("div");
    slide.className = "swiper-slide";
    slide.innerHTML = `
      <div class="card">
        <h2>${title}</h2>
        <div class="card-content">${content}</div>
      </div>
    `;
    container.appendChild(slide);
  }

  function renderCards() {
    const words = stats.words_played.slice().sort((a, b) => b.score - a.score);
    const total = words.reduce((sum, w) => sum + w.score, 0);

    // Word Table
    const wordHTML = `
      <table>
        ${words.map(w => `<tr><td>${w.word}</td><td>${w.score}</td></tr>`).join('')}
      </table>
    `;
    createCard("Words Played", wordHTML);

    // Pie Chart
    const pieHTML = `<canvas id="pie"></canvas>`;
    createCard("Score Contribution", pieHTML);

    // Puzzle Table
    const attempts = {};
    stats.puzzles.attempts.forEach(p => attempts[p] = (attempts[p] || 0) + 1);
    const all = Array.from(new Set([...stats.puzzles.attempts, ...stats.puzzles.solved]));
    const puzzleHTML = `
      <table>
        ${all.map(name => {
          const att = attempts[name] || 0;
          const solved = stats.puzzles.solved.includes(name) ? "✅" : "❌";
          return `<tr><td>${name}</td><td>${att} attempt(s)</td><td>${solved}</td></tr>`;
        }).join('')}
      </table>
    `;
    createCard("Puzzle Attempts", puzzleHTML);

    // Letters
    const lettersHTML = `<p style="font-size:4em; text-align:center;">${stats.letters_played}</p>`;
    createCard("Letters Played", lettersHTML);
  }

  renderCards();

  setTimeout(() => {
    const ctx = document.getElementById("pie")?.getContext("2d");
    if (ctx) {
      const data = stats.words_played.map(w => w.score);
      const labels = stats.words_played.map(w => w.word);
      const total = data.reduce((a, b) => a + b, 0);

      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels,
          datasets: [{
            label: 'Score Contribution',
            data,
            backgroundColor: ['#ff07d6', '#ff6384', '#36a2eb', '#ffcd56']
          }]
        },
        options: {
          cutout: '65%',
          plugins: {
            legend: {
              labels: {
                color: '#eee',
                font: { size: 32 }
              }
            },
            tooltip: { enabled: true }
          }
        },
        plugins: [{
          id: 'centerText',
          beforeDraw(chart) {
            const { ctx, width, height } = chart;
            ctx.save();
            ctx.font = 'bold 1.5em Arial';
            ctx.fillStyle = '#ff07d6';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(total, width / 2, height / 2);
          }
        }]
      });
    }
  }, 500);

  new Swiper('.swiper', {
    pagination: {
      el: '.swiper-pagination',
      clickable: true
    }
  });
}
