<?php include 'includes/header.php'; ?>

<style>
    .sidebar { width: 260px; background-color: #1e3a8a; color: #fff; display: flex; flex-direction: column; flex-shrink: 0; padding: 25px 0; height: calc(100vh - 76px); position: sticky; top: 76px; }
    .sidebar .nav-link { display: flex; align-items: center; padding: 14px 25px; color: rgba(255,255,255,0.7); text-decoration: none; transition: 0.3s; font-size: 15px; }
    .sidebar .nav-link i { width: 25px; font-size: 18px; margin-right: 15px; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255,255,255,0.1); color: #fff; }
    .sidebar .nav-link.active { border-left: 4px solid #fff; }
    .notifications-panel { width: 300px; background-color: #fff; border-left: 1px solid #e5e7eb; padding: 30px 20px; flex-shrink: 0; overflow-y: auto; height: calc(100vh - 76px); position: sticky; top: 76px; }
    .stat-card { background-color: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); position: relative; display: flex; flex-direction: column; min-height: 180px; }
    .stat-chart-container { height: 80px; margin-top: auto; }
    .wave { animation-name: wave-animation; animation-duration: 2.5s; animation-iteration-count: infinite; transform-origin: 70% 70%; display: inline-block; } @keyframes wave-animation { 0% { transform: rotate( 0.0deg) } 10% { transform: rotate(14.0deg) }  20% { transform: rotate(-8.0deg) } 30% { transform: rotate(14.0deg) } 40% { transform: rotate(-4.0deg) } 50% { transform: rotate(10.0deg) } 60% { transform: rotate( 0.0deg) } 100% { transform: rotate( 0.0deg) } }
</style>

<div class="d-flex overflow-hidden">
    <!-- Sidebar from Leader's changes -->
    <nav class="sidebar d-none d-lg-flex">
        <a href="dashboard.php" class="nav-link active"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="book.php" class="nav-link"><i class="fas fa-calendar-check"></i> Bookings</a>
        <a href="calculator.php" class="nav-link"><i class="fas fa-calculator"></i> Calculator</a>
        <a href="track.php" class="nav-link"><i class="fas fa-location-arrow"></i> Tracking</a>
        <a href="manifest.php" class="nav-link"><i class="fas fa-file-invoice"></i> Manifests</a>
    </nav>

    <main class="flex-grow-1 bg-light py-4 px-4 overflow-auto" style="height: calc(100vh - 76px);">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0 text-dark">Hello, John <span class="wave fs-3">👋</span></h2>
                <div class="d-flex gap-2">
                    <a href="book.php" class="btn btn-primary rounded-pill px-4 fw-semibold border-0 text-white shadow-sm" style="background: linear-gradient(90deg, #0d47a1, #1976d2);"><i class="fa-solid fa-plus me-2"></i>New Shipment</a>
                </div>
            </div>

            <!-- Stats Section (Mixed) -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card shadow-sm border-0">
                        <div class="stat-header text-muted small fw-bold text-uppercase">Active Shipments</div>
                        <div class="stat-value fs-1 fw-bold">12</div>
                        <div class="stat-chart-container"><canvas id="activeShipmentsChart"></canvas></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card shadow-sm border-0">
                        <div class="stat-header text-muted small fw-bold text-uppercase">Pending</div>
                        <div class="stat-value fs-1 fw-bold">4</div>
                        <div class="stat-chart-container"><canvas id="pendingChart"></canvas></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card shadow-sm border-0">
                        <div class="stat-header text-muted small fw-bold text-uppercase">Delivered</div>
                        <div class="stat-value fs-1 fw-bold">48</div>
                        <div class="stat-chart-container"><canvas id="deliveredChart"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Recent Shipments Table -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                    <h5 class="fw-bold mb-0 text-dark">Recent Shipment Activity</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-3 border-0">Tracking No.</th>
                                    <th class="border-0">Destination</th>
                                    <th class="border-0">Type</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-3"><a href="track.php" class="fw-semibold text-decoration-none">CRG-99382</a></td>
                                    <td>Rotterdam, NL</td>
                                    <td><i class="fa-solid fa-ship text-muted me-2"></i>Ocean</td>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 border border-primary border-opacity-25" style="color:#0d47a1!important;">In Transit</span></td>
                                    <td class="text-end pe-3"><button class="btn btn-sm btn-light rounded-circle"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                                </tr>
                                <tr>
                                    <td class="ps-3"><a href="track.php" class="fw-semibold text-decoration-none">CRG-10254</a></td>
                                    <td>New York, USA</td>
                                    <td><i class="fa-solid fa-plane text-muted me-2"></i>Air</td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">Delivered</span></td>
                                    <td class="text-end pe-3"><button class="btn btn-sm btn-light rounded-circle"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Notifications Panel from Leader's changes -->
    <aside class="notifications-panel d-none d-xl-flex">
        <div class="w-100">
            <h6 class="fw-bold text-dark mb-4">Notifications & Updates</h6>
            <div class="d-flex gap-3 mb-4 pb-3 border-bottom">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 40px; height: 40px;"><i class="fas fa-bell text-muted"></i></div>
                <div>
                    <h6 class="fw-bold small mb-1">Shipment Updated</h6>
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">Your shipment #CRG-99382 has arrived at the hub.</p>
                </div>
            </div>
            <div class="d-flex gap-3 mb-4 pb-3 border-bottom">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 40px; height: 40px;"><i class="fas fa-info-circle text-muted"></i></div>
                <div>
                    <h6 class="fw-bold small mb-1">System Update</h6>
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">New tracking features enabled.</p>
                </div>
            </div>
        </div>
    </aside>
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
    const options = { maintainAspectRatio: false, scales: { x: { display: false }, y: { display: false } }, plugins: { legend: { display: false } } };
    new Chart(document.getElementById('activeShipmentsChart'), { type: 'line', data: getChartData('#3a76e1'), options });
    new Chart(document.getElementById('pendingChart'), { type: 'line', data: getChartData('#e1953a'), options });
    new Chart(document.getElementById('deliveredChart'), { type: 'line', data: getChartData('#22c55e'), options });
</script>
/main

<?php include 'includes/footer.php'; ?>
