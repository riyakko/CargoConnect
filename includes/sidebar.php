<?php
$nav_items = [
    ['id' => 'dashboard',   'label' => 'Dashboard',   'icon' => 'fa-gauge-high',      'href' => 'dashboard.php'],
    ['id' => 'bookings',    'label' => 'Bookings',    'icon' => 'fa-calendar-check',  'href' => 'book.php'],
    ['id' => 'calculator',  'label' => 'Calculator',  'icon' => 'fa-calculator',      'href' => 'calculator.php'],
    ['id' => 'tracking',    'label' => 'Tracking',    'icon' => 'fa-location-dot',    'href' => 'track.php'],
    ['id' => 'manifests',   'label' => 'Manifests',   'icon' => 'fa-file-invoice',    'href' => 'manifest.php'],
    ['id' => 'settings',    'label' => 'Settings',    'icon' => 'fa-gear',            'href' => 'profile.php'],
];
?>
<aside class="cc-sidebar">
    <!-- Logo -->
    <div class="cc-sidebar-logo">
        <div class="cc-logo-icon">
            <div class="cc-logo-bar1"></div>
            <div class="cc-logo-bar2"></div>
        </div>
        <span class="cc-logo-text">
            <span class="cc-logo-cargo">Cargo</span><span class="cc-logo-connect">Connect.</span>
        </span>
    </div>

    <!-- Navigation -->
    <nav class="cc-sidebar-nav">
        <?php foreach ($nav_items as $item): ?>
        <a href="<?php echo $item['href']; ?>"
           id="nav-<?php echo $item['id']; ?>"
           class="cc-nav-link<?php echo (isset($active_page) && $active_page === $item['id']) ? ' active' : ''; ?>">
            <i class="fas <?php echo $item['icon']; ?>"></i>
            <span><?php echo $item['label']; ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- Footer -->
    <div class="cc-sidebar-footer">
        <?php if (isset($user_role) && $user_role === 'admin'): ?>
        <a href="admin.php" class="cc-nav-link <?php echo (basename($_SERVER['PHP_SELF']) === 'admin.php') ? 'active' : ''; ?>">
            <i class="fas fa-user-shield"></i>
            <span>Admin Panel</span>
        </a>
        <?php endif; ?>
        <a href="index.php" class="cc-nav-link">
            <i class="fas fa-house"></i>
            <span>Back to Home</span>
        </a>
        <a href="auth.php?action=logout" class="cc-nav-link cc-nav-logout">
            <i class="fas fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
