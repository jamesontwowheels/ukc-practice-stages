function initImagePuzzle(containerElement, imagePath = 'your-image.jpg', buttonIdToActivate) {
  let tiles = [];
  let puzzleLocked = false;

  // Create UI elements
  const status = document.createElement('p');
  const grid = document.createElement('div');
  grid.className = 'puzzle-grid';

  // Set up the puzzle container
  containerElement.innerHTML = '';
  containerElement.appendChild(grid);
  containerElement.appendChild(status);

  function createSolvedTiles() {
    tiles = [];
    for (let i = 0; i < 15; i++) tiles.push(i);
    tiles.push(null); // empty
    puzzleLocked = false;
  }

  function drawTiles() {
    grid.innerHTML = '';
    tiles.forEach((tile, index) => {
      const div = document.createElement("div");
      div.className = "tile";
      if (tile === null) {
        div.classList.add("empty");
      } else {
        const row = Math.floor(tile / 4);
        const col = tile % 4;
        div.style.backgroundImage = `url('${imagePath}')`;
        div.style.backgroundSize = `400% 400%`;
        div.style.backgroundPosition = `${(col * 100) / 3}% ${(row * 100) / 3}%`;
        div.addEventListener("click", () => {
          if (!puzzleLocked) tryMove(index);
        });
      }
      grid.appendChild(div);
    });
  }

  function tryMove(index) {
    const emptyIndex = tiles.indexOf(null);
    const validMoves = getAdjacentIndices(emptyIndex);
    if (validMoves.includes(index)) {
      [tiles[emptyIndex], tiles[index]] = [tiles[index], tiles[emptyIndex]];
      drawTiles();
      checkWin();
    }
  }

  function getAdjacentIndices(index) {
    const row = Math.floor(index / 4);
    const col = index % 4;
    const adjacent = [];
    if (row > 0) adjacent.push(index - 4);
    if (row < 3) adjacent.push(index + 4);
    if (col > 0) adjacent.push(index - 1);
    if (col < 3) adjacent.push(index + 1);
    return adjacent;
  }

  function shuffleTiles(steps = 100) {
    createSolvedTiles();

    for (let i = 0; i < steps; i++) {
      const emptyIndex = tiles.indexOf(null);
      const moves = getAdjacentIndices(emptyIndex);
      const move = moves[Math.floor(Math.random() * moves.length)];
      [tiles[emptyIndex], tiles[move]] = [tiles[move], tiles[emptyIndex]];
    }

    drawTiles();
  }

  function checkWin() {
    for (let i = 0; i < 15; i++) {
      if (tiles[i] !== i) return;
    }
    if (tiles[15] === null) {
      status.textContent = "🎉 You solved the image!";
      puzzleLocked = true;

      if (buttonIdToActivate) {
        const button = document.getElementById(buttonIdToActivate);
        console.log("Activate buttone"+buttonIdToActivate);
        if (button) {
          button.classList.remove("inactive");
          button.classList.add("active");
          button.disabled = false;
        }
      }
    }
  }

  // Initialize puzzle and pre-shuffle
  shuffleTiles(500);
}
