<?php include 'includes/header.php'; ?>

<div id="dashboard">
    <div class="sidebar">
        <div class="brand">
            <i class="fas fa-truck-fast"></i>
            CargoConnect.
        </div>
        <nav>
            <a href="#" class="nav-link active"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="book.php" class="nav-link"><i class="fas fa-calendar-check"></i> Bookings</a>
            <a href="calculator.php" class="nav-link"><i class="fas fa-calculator"></i> Calculator</a>
            <a href="track.php" class="nav-link"><i class="fas fa-location-arrow"></i> Tracking</a>
            <a href="manifest.php" class="nav-link"><i class="fas fa-file-invoice"></i> Manifests</a>
            <a href="#" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Dashboard</h1>
            <div class="profile-circle">
                <img src="https://i.pravatar.cc/150?u=a042581f4e29026704d" alt="Profile" style="width:100%; border-radius:50%;">
            </div>
        </div>

        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-header">Active Shipments</div>
                <div class="stat-value">12</div>
                <div class="stat-icon blue-icon"><i class="fas fa-truck"></i></div>
                <div class="stat-chart-container"><canvas id="activeShipmentsChart"></canvas></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">Pending</div>
                <div class="stat-value">4</div>
                <div class="stat-icon orange-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-chart-container"><canvas id="pendingChart"></canvas></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">Delivered</div>
                <div class="stat-value">12</div>
                <div class="stat-icon green-icon"><i class="fas fa-check"></i></div>
                <div class="stat-chart-container"><canvas id="deliveredChart"></canvas></div>
            </div>
        </div>

        <div class="activity-section">
            <h2 class="activity-header">Recent Shipment Activity</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tracking ID</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>00230001</td>
                        <td>Manila</td>
                        <td>Batangas</td>
                        <td>March 22, 2026</td>
                        <td><span class="status-badge status-in-transit">In Transit</span></td>
                    </tr>
                    <tr>
                        <td>00230002</td>
                        <td>Manila</td>
                        <td>Batangas</td>
                        <td>March 22, 2026</td>
                        <td><span class="status-badge status-pending">Pending</span></td>
                    </tr>
                    <tr>
                        <td>00230003</td>
                        <td>Manila</td>
                        <td>Batangas</td>
                        <td>March 22, 2026</td>
                        <td><span class="status-badge status-in-transit">In Transit</span></td>
                    </tr>
                    <tr>
                        <td>00230004</td>
                        <td>Manila</td>
                        <td>Batangas</td>
                        <td>March 22, 2026</td>
                        <td><span class="status-badge status-in-transit">In Transit</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="notifications-panel">
        <div class="notifications-header">Notifications & Updates</div>
        <div class="notification-item">
            <div class="notif-icon-box"><i class="fas fa-bell"></i></div>
            <div class="notif-content">
                <div class="notif-content-title">Shipment Updated</div>
                <div class="notif-content-text">Your shipment #00230001 has arrived at the Manila hub.</div>
            </div>
        </div>
        <div class="notification-item">
            <div class="notif-icon-box"><i class="fas fa-info-circle"></i></div>
            <div class="notif-content">
                <div class="notif-content-title">System Update</div>
                <div class="notif-content-text">New tracking features have been enabled for your account.</div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const getChartData = (color) => ({
        labels: Array(12).fill(''),
        datasets: [{
            data: [12, 19, 13, 15, 22, 10, 15, 25, 20, 28, 22, 30],
            borderColor: color,
            borderWidth: 2,
            tension: 0.4,
            pointRadius: 0,
            fill: false,
        }]
    });

    const options = {
        maintainAspectRatio: false,
        scales: { x: { display: false }, y: { display: false } },
        plugins: { legend: { display: false } }
    };

    new Chart(document.getElementById('activeShipmentsChart'), { type: 'line', data: getChartData('#3a76e1'), options });
    new Chart(document.getElementById('pendingChart'), { type: 'line', data: getChartData('#e1953a'), options });
    new Chart(document.getElementById('deliveredChart'), { type: 'line', data: getChartData('#22c55e'), options });
</script>

<?php include 'includes/footer.php'; ?>
