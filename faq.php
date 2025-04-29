<?php
// dashboard.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit;
}?>
<head>

<link rel="stylesheet" href="main.css">
<link rel="stylesheet" href="assets/css/app-buttons.css">
<link rel="stylesheet" href="assets/css/faq.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<header>
  <h1>In-game screens</h1>
</header>

<section class="carousel">
  <div class="slide active">
    <img src="images/faq/FAQ 1.jpeg" alt="Screenshot 1">
    <br><br>
    <p>A player's personal inventory is shown at the top of the screen</p>
  </div>
  <div class="slide">
    <img src="images/faq/FAQ 1.jpeg" alt="Screenshot 1">
    <br><br>
    <p>Available checkpoints and the distance away are shown in the middle of the screen</p>
  </div>
  <div class="slide">
    <img src="images/faq/FAQ 1.jpeg" alt="Screenshot 1">
    <br><br>
    <p>Current score and time remaining are shown at the bottom</p>
  </div>
  <div class="slide">
    <img src="images/faq/FAQ 1.jpeg" alt="Screenshot 1">
    <br><br>
    <p>The Scores tab in the footer shows the current leaderboard, the History tab shows all the checkpoint actions taken by your team</p>
  </div>
  <div class="slide">
    <img src="images/faq/FAQ 1.jpeg" alt="Screenshot 1">
    <br><br><p>Checkpoints are inactive when you are not close</p>
  </div>
  <div class="slide">
    <img src="images/faq/FAQ 3.jpeg" alt="Screenshot 2">
    <br><br><p>Checkpoints activate when you are in range</p>
  </div>
  <div class="slide">
    <img src="images/faq/FAQ 4.jpeg" alt="Screenshot 3">
    <br><br><p>Tapping into a checkpoint shows the details and available actions</p>
  </div>
  <div class="slide">
    <img src="images/faq/FAQ 2.jpeg" alt="Screenshot 3">
    <br><br><p>Tapping an action button within a checkpoint performs the action</p>
  </div>

  <div class="controls">
    <button onclick="prevSlide()">Prev</button>
    <button onclick="nextSlide()">Next</button>
  </div>
</section>

<script>
  let currentSlide = 0;
  const slides = document.querySelectorAll('.slide');

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }

  function prevSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(currentSlide);
  }
</script>

<div id="footer-back"></div>
<div id="footer">
<div class="app-buttons">
        <a href="index.php" class="app-button" id="app1"><i class="fas fa-house"></i><br></a>
        <a href="profile.php" class="app-button" id="app2"><i class="fas fa-address-card"></i><br></a>
        <a href="faq.php" class="app-button" id="app3"><i class="fas fa-circle-question"></i><br></a>
</div>
</div>
</body>