<?php include 'includes/header.php'; ?>

    <!-- Main Hero Section -->
    <main class="hero-section text-center position-relative overflow-hidden flex-grow-1">
        
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
                <a href="book.php" class="btn glass-btn d-flex align-items-center gap-2 px-4 py-3 shadow-sm rounded-4">
                    <i class="fa-solid fa-mobile-screen-button fs-4"></i>
                    <span class="fw-semibold">Book Shipment</span>
                </a>
                <a href="track.php" class="btn glass-btn d-flex align-items-center gap-2 px-4 py-3 shadow-sm rounded-4">
                    <i class="fa-solid fa-map-location-dot fs-4"></i>
                    <span class="fw-semibold">Track Cargo</span>
                </a>
            </div>
            
            <!-- 3D Model Container -->
            <div class="model-container-wrapper mx-auto mt-4 mb-5 position-relative">
                <div class="glow-effect left-glow"></div>
                <div class="glow-effect right-glow"></div>
                <div id="canvas-container" class="w-100 h-100 position-relative z-1">
                    <!-- Three.js Canvas goes here -->
                </div>
            </div>
            
        </div>

    </main>

<?php include 'includes/footer.php'; ?>
