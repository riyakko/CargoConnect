<?php
/**
 * CargoConnect — Sidebar Partial
 *
 * Variables injected by auth_check.php (guaranteed non-empty on protected pages):
 * @var string $user_avatar   Relative path to uploaded avatar, or '' if none.
 * @var string $user_initials Two-letter initials (e.g. "JD").
 * @var string $user_name     Full display name.
 * @var string $user_role     'admin' or 'customer'.
 */

/** @var string $user_avatar */
/** @var string $user_initials */
/** @var string $user_name */
/** @var string $user_role */

$nav_items = [
    ['id' => 'dashboard',   'label' => 'Dashboard',   'icon' => 'fa-gauge-high',      'href' => 'dashboard.php'],
    ['id' => 'bookings',    'label' => 'Bookings',    'icon' => 'fa-calendar-check',  'href' => 'book.php'],
    ['id' => 'calculator',  'label' => 'Calculator',  'icon' => 'fa-calculator',      'href' => 'calculator.php'],
    ['id' => 'tracking',    'label' => 'Tracking',    'icon' => 'fa-location-dot',    'href' => 'track.php'],
    ['id' => 'manifests',   'label' => 'Manifests',   'icon' => 'fa-file-invoice',    'href' => 'manifest.php'],
    ['id' => 'settings',    'label' => 'Settings',    'icon' => 'fa-gear',            'href' => 'profile.php'],
];
?>
<!-- Sidebar overlay (mobile) -->
<div class="cc-sidebar-overlay" id="sidebarOverlay"></div>

<aside class="cc-sidebar" id="ccSidebar">
    <!-- Mobile close button -->
    <button class="cc-sidebar-close" id="sidebarClose" aria-label="Close sidebar">
        <i class="fas fa-xmark"></i>
    </button>

    <!-- Logo -->
    <div class="cc-sidebar-logo">
        <?php $logo_height = 36; $logo_variant = 'sidebar'; include __DIR__ . '/logo.php'; ?>
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
        <!-- User identity block -->
        <a href="profile.php" class="d-flex align-items-center gap-2 text-decoration-none mb-2"
           style="padding:8px 12px;border-radius:10px;transition:background 0.2s;"
           onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='transparent'">
            <?php if (!empty($user_avatar)): ?>
                <img src="<?php echo htmlspecialchars($user_avatar); ?>"
                     alt="avatar"
                     style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid var(--cc-orange);flex-shrink:0;">
            <?php else: ?>
                <div style="width:34px;height:34px;border-radius:50%;background:var(--cc-orange);
                            display:flex;align-items:center;justify-content:center;
                            font-size:0.85rem;color:#fff;font-weight:700;flex-shrink:0;">
                    <?php echo htmlspecialchars($user_initials); ?>
                </div>
            <?php endif; ?>
            <div style="overflow:hidden;">
                <div style="font-weight:600;font-size:0.82rem;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <?php echo htmlspecialchars($user_name); ?>
                </div>
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.5);">
                    <?php echo htmlspecialchars(ucfirst($user_role)); ?>
                </div>
            </div>
        </a>
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

