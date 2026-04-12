    <!-- Footer -->
    <footer class="text-white text-center py-4 mt-auto" style="background-color: #294084; font-size: 0.8rem; width: 100%;">
        <div class="container">
            <p class="mb-0"><i class="fa-regular fa-copyright me-1"></i> Copyright <?php echo date('Y'); ?>. All Right Reserved by CargoConnect</p>
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
