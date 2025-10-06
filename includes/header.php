<?php
// Start a session only if one isn't already active.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Include the database connection.
require_once 'config/db.php';

// Track Site Visit
$conn->query("INSERT INTO site_visits () VALUES ()");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shivarchanam</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="uploads/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>
<body>

  <!-- NEW HEADER STRUCTURE -->
  <header class="site-header" id="site-header">
    <div class="header-top-bar">
        <div class="header-container">
            <div class="social-media">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="Youtube"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="header-top-buttons">
                <a href="/temple/join_us.php"><button class="btn btn-join">Join Us</button></a>
                <button class="btn btn-donate">Donate</button>
            </div>
        </div>
    </div>
    <div class="main-header-wrapper">
        <div class="header-container">
            <div class="logo-container">
                <img class="hlogo" style="height: 70px;" src="uploads/images/logo.png" alt="">
                <!-- <a href="index.php" class="logo">Shivarchanam</a> -->
            </div>
        </div>
    </div>
    <nav class="main-nav" id="main-nav">
        <div class="header-container">
            <!-- This ul is for desktop -->
            <ul class="desktop-nav">
                <li><a href="#about">About</a></li>
                <li><a href="#panchang">Panchang</a></li>
                <li><a href="#community">Community</a></li>
                <li><a href="#events">Events</a></li>
                <li><a href="#gallery">Gallery</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <!-- This wrapper is for the mobile scrolling menu -->
            <div class="nav-scroll-wrapper">
                <ul class="mobile-nav-scroll">
                    <li><a href="#about">About</a></li>
                    <li><a href="#panchang">Panchang</a></li>
                    <li><a href="#community">Community</a></li>
                    <li><a href="#events">Events</a></li>
                    <li><a href="#gallery">Gallery</a></li>
                    <li><a href="#contact">Contact</a></li>
                     <!-- Duplicate for seamless scroll -->
                    <li><a href="#about">About</a></li>
                    <li><a href="#panchang">Panchang</a></li>
                    <li><a href="#community">Community</a></li>
                    <li><a href="#events">Events</a></li>
                    <li><a href="#gallery">Gallery</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
             <button class="hamburger" id="hamburger-button" aria-label="Menu">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </nav>
  </header>

  <!-- SIDE MENU (for Hamburger) -->
  <div class="overlay" id="overlay"></div>
  <div class="side-menu" id="sideMenu">
    <ul>
        <li><a href="#about"><span class="menu-number">01</span> About</a></li>
        <li><a href="#panchang"><span class="menu-number">02</span> Panchang</a></li>
        <li><a href="#community"><span class="menu-number">03</span> Community</a></li>
        <li><a href="#events"><span class="menu-number">04</span> Events</a></li>
        <li><a href="#gallery"><span class="menu-number">05</span> Gallery</a></li>
        <li><a href="#contact"><span class="menu-number">06</span> Contact</a></li>
    </ul>
    <div class="side-menu-buttons">
        <button class="btn btn-join">Join Us</button>
        <button class="btn btn-donate">Donate</button>
    </div>
  </div>

