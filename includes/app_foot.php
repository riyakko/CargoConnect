    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ── Global Sidebar Toggle (mobile) ─────────────────────── -->
    <script>
    (function () {
        const sidebar  = document.getElementById('ccSidebar');
        const overlay  = document.getElementById('sidebarOverlay');
        const toggle   = document.getElementById('menuToggle');
        const closeBtn = document.getElementById('sidebarClose');

        if (!sidebar) return; // not an app page

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // prevent background scroll
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (toggle)   toggle.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (overlay)  overlay.addEventListener('click', closeSidebar);

        // Close sidebar when a nav link is clicked on mobile
        sidebar.querySelectorAll('.cc-nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 768) closeSidebar();
            });
        });

        // Close on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSidebar();
        });

        // If window resized above mobile breakpoint, reset state
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    })();
    </script>
</body>
</html>
