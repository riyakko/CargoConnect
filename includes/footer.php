    <!-- Spacer to prevent content overlap with the curve -->
    <div style="height: 150px;"></div>
    
    <!-- Dark Navy Footer Curve -->
    <div class="footer-curve w-100 mt-auto">
        <svg viewBox="0 0 1440 180" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="display: block; width: 100%; height: auto;">
            <path d="M0 100 C 400 0, 1040 0, 1440 100 L 1440 200 L 0 200 Z" fill="#0b1324"/>
        </svg>
        <div class="footer-bg text-center text-white pb-4" style="background-color: #0b1324; margin-top: -5px;">
            <div class="container">
                <div class="row pt-4 pb-3 text-start">
                    <div class="col-md-4 mb-4">
                        <h5 class="fw-bold mb-3"><span class="text-orange">Cargo</span><span class="text-blue">Connect.</span></h5>
                        <p class="text-white-50 small">Streamlining global logistics. Connecting the world securely, efficiently, and with total transparency.</p>
                        <div class="d-flex gap-3 mt-3">
                            <a href="#" class="text-white-50 text-decoration-none"><i class="fa-brands fa-twitter fs-5"></i></a>
                            <a href="#" class="text-white-50 text-decoration-none"><i class="fa-brands fa-linkedin fs-5"></i></a>
                            <a href="#" class="text-white-50 text-decoration-none"><i class="fa-brands fa-facebook fs-5"></i></a>
                        </div>
                    </div>
                    <div class="col-md-2 ms-auto mb-4">
                        <h6 class="fw-bold text-white mb-3">Company</h6>
                        <ul class="list-unstyled text-white-50 small mb-0">
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-white">About Us</a></li>
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-white">Careers</a></li>
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-white">Contact</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2 mb-4">
                        <h6 class="fw-bold text-white mb-3">Services</h6>
                        <ul class="list-unstyled text-white-50 small mb-0">
                            <li class="mb-2"><a href="book.php" class="text-white-50 text-decoration-none hover-white">Ocean Freight</a></li>
                            <li class="mb-2"><a href="book.php" class="text-white-50 text-decoration-none hover-white">Air Freight</a></li>
                            <li class="mb-2"><a href="calculator.php" class="text-white-50 text-decoration-none hover-white">Rate Calculator</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3 mb-4">
                        <h6 class="fw-bold text-white mb-3">Newsletter</h6>
                        <p class="text-white-50 small mb-2">Subscribe for logistics updates</p>
                        <div class="input-group input-group-sm">
                            <input type="email" class="form-control bg-dark border-secondary text-white" placeholder="Email address" aria-label="Email address">
                            <button class="btn btn-primary bg-blue border-blue fw-bold" type="button">Sub</button>
                        </div>
                    </div>
                </div>
                <hr class="border-secondary mb-3 mt-0">
                <p class="mb-0 text-white-50"><small>&copy; <?php echo date('Y'); ?> CargoConnect Logistics. All rights reserved.</small></p>
            </div>
        </div>
    </div>
    
    <!-- The following tags were part of the sam branch structure but are not needed for the curve design -->
    <!-- However, we keep them if they close any parent tags that weren't obvious -->
    <div class="d-none">
        </div>
    </footer>


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
    <script>
        // Custom interactive script
        document.addEventListener('DOMContentLoaded', () => {
            // Update glass card heights/hover effects dynamically if needed
            const cards = document.querySelectorAll('.glass-card, .card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => card.style.transform = 'translateY(-5px)');
                card.addEventListener('mouseleave', () => card.style.transform = 'translateY(0)');
                card.style.transition = 'transform 0.3s ease';
            });
        });
    </script>
</body>
</html>
