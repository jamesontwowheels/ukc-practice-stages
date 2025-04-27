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
    <img src="images/mindgames_faq_1.png" alt="Screenshot 1">
    <p>Step 1: Sign up and create your profile in just a few seconds!</p>
  </div>
  <div class="slide">
    <img src="screenshot2.png" alt="Screenshot 2">
    <p>Step 2: Organize your projects and invite your team members.</p>
  </div>
  <div class="slide">
    <img src="screenshot3.png" alt="Screenshot 3">
    <p>Step 3: Track progress with real-time updates and notifications.</p>
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