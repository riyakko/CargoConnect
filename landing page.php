<?php

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoConnect - Streamline Your Shipping</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header / Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top header-nav px-4 py-3">
        <div class="container-fluid align-items-center">
            <!-- Logo -->
            <a class="navbar-brand brand-logo d-flex align-items-center" href="#">
                <i class="fa-solid fa-truck-fast brand-icon me-2"></i>
                <span class="brand-text-orange">Cargo</span><span class="brand-text-blue">Connect.</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav Links & Login -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center mb-2 mb-lg-0 gap-4">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#">Track</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#">Book</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a href="#" class="btn btn-login px-4 py-2 rounded-pill fw-bold">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Hero Section -->
    <main class="hero-section text-center position-relative overflow-hidden">
        
        <!-- Background Swoosh Graphics (Decorative) -->
        <div class="decorative-swoosh left-swoosh"></div>
        <div class="decorative-swoosh right-swoosh"></div>

        <div class="container position-relative z-2">
            <!-- Headline -->
            <h1 class="display-4 fw-bolder mt-5 mb-4 hero-headline">
                Streamline Your <span class="text-blue">Shipping</span>,<br>
                <span class="text-orange">Track</span> with Confidence
            </h1>

            <!-- CTA Buttons (Glassmorphism) -->
            <div class="d-flex justify-content-center gap-4 mt-5 cta-container flex-wrap flex-md-nowrap">
                <a href="#" class="btn glass-btn d-flex align-items-center gap-2 px-4 py-3 shadow-sm rounded-4">
                    <i class="fa-solid fa-mobile-screen-button fs-4"></i>
                    <span class="fw-semibold">Book Shipment</span>
                </a>
                <a href="#" class="btn glass-btn d-flex align-items-center gap-2 px-4 py-3 shadow-sm rounded-4">
                    <i class="fa-solid fa-map-location-dot fs-4"></i>
                    <span class="fw-semibold">Track Cargo</span>
                </a>
            </div>
            
            <!-- 3D Model Container -->
            <div class="model-container-wrapper mx-auto mt-4 position-relative">
                <div class="glow-effect left-glow"></div>
                <div class="glow-effect right-glow"></div>
                <div id="canvas-container" class="w-100 h-100 position-relative z-1">
                    <!-- Three.js Canvas goes here -->
                </div>
            </div>
            
        </div>

        <!-- Dark Navy Footer Curve -->
        <div class="footer-curve w-100">
            <svg viewBox="0 0 1440 250" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M0 150 C 400 50, 1040 50, 1440 150 L 1440 300 L 0 300 Z" fill="#0b1324"/>
            </svg>
            <div class="footer-bg"></div>
        </div>

    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.css"></script>
    
    <!-- Three.js Library and GLTFLoader (Boilerplate) via CDN -->
    <script type="importmap">
        {
          "imports": {
            "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
          }
        }
    </script>
    
    <!-- Main Application Logic for 3D -->
    <script type="module" src="main.js"></script>
</body>
</html>

<?php
?>