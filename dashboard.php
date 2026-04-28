<?php
require_once 'includes/auth_check.php';
$page_title = 'Dashboard';
$active_page = 'dashboard';

// Fetch summary counts from shipments table
$total_active = 0; $total_pending = 0; $total_delivered = 0;
$recent = [];

if ($conn) {
    $r = $conn->query("SELECT COUNT(*) as c FROM shipments WHERE status='In Transit'");
    if ($r) $total_active = $r->fetch_assoc()['c'];

    $r = $conn->query("SELECT COUNT(*) as c FROM shipments WHERE status='Pending'");
    if ($r) $total_pending = $r->fetch_assoc()['c'];

    $r = $conn->query("SELECT COUNT(*) as c FROM shipments WHERE status IN ('Arrived','Completed')");
    if ($r) $total_delivered = $r->fetch_assoc()['c'];

    $r = $conn->query("SELECT * FROM shipments ORDER BY date_created DESC LIMIT 5");
    if ($r) { while ($row = $r->fetch_assoc()) $recent[] = $row; }
}

// Fetch notifications
$notifications = [];
if ($conn) {
    $r = $conn->query("SELECT * FROM notifications ORDER BY date_sent DESC LIMIT 5");
    if ($r) { while ($row = $r->fetch_assoc()) $notifications[] = $row; }
}
?>
<?php include 'includes/app_head.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="cc-main">
    <div class="cc-topbar">
        <div style="display:flex;align-items:center;">
            <button class="cc-menu-toggle" id="menuToggle" aria-label="Open sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <span class="cc-topbar-title"><i class="fas fa-gauge-high text-blue me-2"></i>Dashboard</span>
        </div>
        <div class="cc-topbar-actions cc-dashboard-topbar-actions">
            <a href="book.php" class="cc-btn cc-btn-primary cc-btn-sm cc-dashboard-topbar-btn"><i class="fas fa-plus"></i> <span class="cc-btn-text">New Shipment</span></a>
            <div class="cc-avatar"><?php echo $user_initials; ?></div>
        </div>
    </div>

    <div class="cc-page">
        <h2 class="cc-page-title">Hello, <?php echo htmlspecialchars($current_user['first_name']); ?> <span class="wave">👋</span></h2>
        <p class="cc-page-subtitle">Here's your logistics overview for today.</p>

        <div class="d-flex gap-4 cc-dashboard-layout">
            <div class="flex-grow-1 cc-dashboard-main-column">
                <!-- Stats -->
                <div class="cc-stats-grid mb-4">
                    <div class="cc-stat-card">
                        <div class="cc-stat-label">In Transit</div>
                        <div class="cc-stat-value"><?php echo $total_active; ?></div>
                        <div class="cc-stat-chart"><canvas id="chartActive"></canvas></div>
                    </div>
                    <div class="cc-stat-card">
                        <div class="cc-stat-label">Pending</div>
                        <div class="cc-stat-value"><?php echo $total_pending; ?></div>
                        <div class="cc-stat-chart"><canvas id="chartPending"></canvas></div>
                    </div>
                    <div class="cc-stat-card">
                        <div class="cc-stat-label">Completed</div>
                        <div class="cc-stat-value"><?php echo $total_delivered; ?></div>
                        <div class="cc-stat-chart"><canvas id="chartDelivered"></canvas></div>
                    </div>
                </div>

                <!-- Recent Shipments -->
                <div class="cc-card">
                    <div class="cc-card-header">
                        <h5><i class="fas fa-clock-rotate-left text-orange me-2"></i>Recent Shipment Activity</h5>
                    </div>
                    <div class="cc-card-body p-0">
                        <div class="cc-table-wrapper">
                        <table class="cc-table">
                            <thead>
                                <tr>
                                    <th>Tracking ID</th>
                                    <th>Origin</th>
                                    <th>Destination</th>
                                    <th>Cargo Type</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent)): ?>
                                    <?php foreach ($recent as $s): ?>
                                    <tr>
                                        <td><a href="track.php?id=<?php echo urlencode($s['tracking_id']); ?>" class="fw-semibold text-decoration-none"><?php echo htmlspecialchars($s['tracking_id']); ?></a></td>
                                        <td><?php echo htmlspecialchars($s['origin']); ?></td>
                                        <td><?php echo htmlspecialchars($s['destination']); ?></td>
                                        <td><?php echo htmlspecialchars($s['cargo_type']); ?></td>
                                        <td>
                                            <?php if ($s['shipping_method'] === 'container'): ?>
                                                <i class="fa-solid fa-ship text-muted me-1"></i>Container
                                            <?php else: ?>
                                                <i class="fa-solid fa-truck text-muted me-1"></i>Truck
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge = 'cc-badge-gray';
                                            if ($s['status'] === 'In Transit') $badge = 'cc-badge-blue';
                                            elseif (in_array($s['status'], ['Arrived','Completed'])) $badge = 'cc-badge-green';
                                            elseif ($s['status'] === 'Pending') $badge = 'cc-badge-orange';
                                            ?>
                                            <span class="cc-badge <?php echo $badge; ?>"><?php echo htmlspecialchars($s['status']); ?></span>
                                        </td>
                                        <td class="text-end"><a href="track.php?id=<?php echo urlencode($s['tracking_id']); ?>" class="cc-btn cc-btn-light cc-btn-sm">View</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center" style="padding:40px;color:var(--cc-text-muted);">
                                        <i class="fas fa-inbox me-2" style="font-size:1.5rem;"></i><br>
                                        No shipments yet. <a href="book.php" class="text-decoration-none fw-semibold">Create your first booking</a>.
                                    </td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        </div><!-- /.cc-table-wrapper -->
                    </div>
                </div>
            </div>

            <!-- Notifications Panel -->
            <div class="cc-notif-panel d-none d-xl-block">
                <div class="cc-card">
                    <div class="cc-card-header"><h5><i class="fas fa-bell text-orange me-2"></i>Notifications</h5></div>
                    <div class="cc-card-body">
                        <?php if (!empty($notifications)): ?>
                            <?php foreach ($notifications as $n): ?>
                            <div class="cc-notif-item">
                                <div class="cc-notif-icon"><i class="fas fa-<?php echo $n['status'] === 'unread' ? 'bell' : 'check-circle'; ?>"></i></div>
                                <div class="cc-notif-text">
                                    <h6><?php echo htmlspecialchars(mb_strimwidth($n['message'], 0, 40, '...')); ?></h6>
                                    <p><?php echo date('M d, h:i A', strtotime($n['date_sent'])); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color:var(--cc-text-muted);text-align:center;padding:20px 0;font-size:0.85rem;">No notifications yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ── Dashboard mobile fixes ─────────────────────────────── */
.cc-dashboard-layout,
.cc-dashboard-main-column,
.cc-stats-grid,
.cc-stat-card,
.cc-table-wrapper,
.cc-table,
.cc-dashboard-topbar-actions,
.cc-dashboard-topbar-btn {
    width: 100%;
    max-width: 100%;
}

.cc-dashboard-layout > * {
    min-width: 0;
}

/* Stat cards: 3 columns on ≥992px, 2 on tablet, 1 on phone */
.cc-stats-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}
.cc-stat-card {
    min-height: 140px;
    min-width: 0;
}

/* Table wrapper scroll */
.cc-table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 0 0 12px 12px;
}
.cc-table {
    min-width: 720px;
}
.cc-dashboard-topbar-actions {
    justify-content: flex-end;
    flex-wrap: wrap;
}
.cc-dashboard-topbar-btn {
    width: auto;
    flex: 0 1 auto;
}

@media (max-width: 992px) {
    .cc-dashboard-layout {
        flex-direction: column;
        gap: 1rem !important;
    }
    .cc-stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 768px) {
    /* Greeting */
    .cc-page-title { font-size: 1.25rem; }
    .cc-page-subtitle { font-size: 0.82rem; margin-bottom: 16px; }

    /* Stat cards: 2 columns on phone */
    .cc-stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px !important;
    }
    .cc-stat-card { min-height: 110px; padding: 16px; }
    .cc-stat-value { font-size: 1.75rem; }
    .cc-stat-chart { height: 44px; }

    /* Card header */
    .cc-card-header { padding: 14px 16px; }
    .cc-card-header h5 { font-size: 0.88rem; }

    /* Table cells compact */
    .cc-table thead th { padding: 10px 12px; font-size: 0.65rem; }
    .cc-table tbody td { padding: 10px 12px; font-size: 0.82rem; }

    .cc-topbar {
        gap: 12px;
    }
    .cc-dashboard-topbar-actions {
        width: auto;
        gap: 8px;
        flex: 0 1 auto;
    }
    .cc-dashboard-topbar-btn {
        padding-inline: 12px;
    }

    /* Hide button label on medium screens to save topbar space */
    .cc-btn-text { display: none; }
}

@media (max-width: 480px) {
    /* Stat cards: 1 column on very small phones */
    .cc-stats-grid { grid-template-columns: 1fr; }
    .cc-stat-card { min-height: 100px; }
    .cc-topbar {
        align-items: flex-start;
    }
    .cc-dashboard-topbar-actions {
        width: 100%;
        justify-content: space-between;
    }
    .cc-dashboard-topbar-btn {
        flex: 1 1 auto;
        justify-content: center;
        min-width: 0;
    }
    .cc-table {
        min-width: 640px;
    }
}
</style>

<script>
const sparkOpts = {
    maintainAspectRatio: false,
    scales: { x: { display: false }, y: { display: false } },
    plugins: { legend: { display: false }, tooltip: { enabled: false } },
    elements: { point: { radius: 0 } }
};
function spark(id, color) {
    new Chart(document.getElementById(id), {
        type: 'line',
        data: { labels: Array(12).fill(''), datasets: [{ data: [12,19,13,15,22,10,15,25,20,28,22,30], borderColor: color, borderWidth: 2, tension: 0.4, fill: false }] },
        options: sparkOpts
    });
}
spark('chartActive', '#2563eb');
spark('chartPending', '#f59e0b');
spark('chartDelivered', '#16a34a');
</script>

<?php include 'includes/app_foot.php'; ?>
