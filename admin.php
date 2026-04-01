<?php include 'includes/header.php'; ?>

<style>
    .admin-wrapper { min-height: calc(100vh - 74px); }
    .admin-sidebar { width: 260px; background-color: #0b1324; box-shadow: 4px 0 10px rgba(0,0,0,0.05); position: sticky; top: 74px; height: calc(100vh - 74px); overflow-y: auto;}
    .nav-pills .nav-link { color: rgba(255,255,255,0.7); }
    .nav-pills .nav-link.active { background-color: rgba(255,255,255,0.1); color: #fff; font-weight: 600; border-left: 4px solid #f57c00; border-radius: 0; }
    .nav-pills .nav-link:hover:not(.active) { color: #fff; background-color: rgba(255,255,255,0.05); }
</style>

<div class="d-flex flex-grow-1 admin-wrapper bg-light">
    
    <!-- Left Sidebar -->
    <div class="admin-sidebar flex-shrink-0 d-none d-lg-block pt-3">
        <div class="px-4 pb-3 mb-2 border-bottom border-secondary border-opacity-25">
            <h6 class="text-uppercase text-white-50 small fw-bold mb-0">System Controls</h6>
        </div>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="#" class="nav-link active px-4 py-3" aria-current="page">
                    <i class="fa-solid fa-chart-pie me-3 opacity-75"></i>Overview
                </a>
            </li>
            <li>
                <a href="#" class="nav-link px-4 py-3">
                    <i class="fa-solid fa-users me-3 opacity-75"></i>User Management
                </a>
            </li>
            <li>
                <a href="#" class="nav-link px-4 py-3">
                    <i class="fa-solid fa-truck-fast me-3 opacity-75"></i>Active Routes
                </a>
            </li>
            <li>
                <a href="manifest.php" class="nav-link px-4 py-3">
                    <i class="fa-solid fa-file-invoice me-3 opacity-75"></i>Manifests
                </a>
            </li>
            <li>
                <a href="#" class="nav-link px-4 py-3">
                    <i class="fa-solid fa-bullhorn me-3 opacity-75"></i>Announcements
                </a>
            </li>
            <li>
                <a href="#" class="nav-link px-4 py-3">
                    <i class="fa-solid fa-gear me-3 opacity-75"></i>Platform Settings
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bolder text-dark mb-0">Admin Dashboard</h3>
            <button class="btn btn-outline-primary border-primary border-2 fw-semibold px-4 rounded-pill" style="color: #0d47a1; border-color: #0d47a1!important;"><i class="fa-solid fa-download me-2"></i>Export Report</button>
        </div>

        <!-- Global Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <p class="text-muted small fw-bold text-uppercase mb-2"><i class="fa-solid fa-chart-line text-success me-2"></i>Total Revenue</p>
                    <h2 class="fw-bolder text-dark mb-0">$2.4M</h2>
                    <p class="text-success small mb-0"><i class="fa-solid fa-arrow-trend-up me-1"></i>+14.5% this month</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <p class="text-muted small fw-bold text-uppercase mb-2"><i class="fa-solid fa-ship text-blue me-2"></i>Global Volume</p>
                    <h2 class="fw-bolder text-dark mb-0">8.2k TEUs</h2>
                    <p class="text-success small mb-0"><i class="fa-solid fa-arrow-trend-up me-1"></i>+2.1% this month</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <p class="text-muted small fw-bold text-uppercase mb-2"><i class="fa-solid fa-users text-orange me-2"></i>New Users</p>
                    <h2 class="fw-bolder text-dark mb-0">1,492</h2>
                    <p class="text-danger small mb-0"><i class="fa-solid fa-arrow-trend-down me-1"></i>-0.4% this month</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <p class="text-muted small fw-bold text-uppercase mb-2"><i class="fa-solid fa-server text-secondary me-2"></i>System Health</p>
                    <h2 class="fw-bolder text-dark mb-0 text-success">99.9%</h2>
                    <p class="text-success small mb-0"><i class="fa-solid fa-circle-check me-1"></i>All APIs Operational</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Alert Log -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                        <h6 class="fw-bold text-dark mb-0">System Alerts & Logs</h6>
                    </div>
                    <div class="card-body px-4 pt-2">
                        <div class="list-group list-group-flush border-top border-bottom">
                            <div class="list-group-item px-0 py-3 border-light d-flex align-items-center">
                                <span class="badge bg-danger rounded-circle p-2 me-3"><i class="fa-solid fa-triangle-exclamation"></i></span>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-semibold">Port Congestion Alert (Los Angeles)</h6>
                                    <small class="text-muted">High wait times reported. Impacts 34 active shipments.</small>
                                </div>
                                <small class="text-muted">10m ago</small>
                            </div>
                            <div class="list-group-item px-0 py-3 border-light d-flex align-items-center">
                                <span class="badge bg-warning text-dark rounded-circle p-2 me-3"><i class="fa-solid fa-clock"></i></span>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-semibold">API Rate Limit Approaching</h6>
                                    <small class="text-muted">Customs Clearance API is at 92% capacity.</small>
                                </div>
                                <small class="text-muted">2h ago</small>
                            </div>
                            <div class="list-group-item px-0 py-3 border-light d-flex align-items-center">
                                <span class="badge bg-info rounded-circle p-2 me-3"><i class="fa-solid fa-user-plus"></i></span>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-semibold">New Corporate Account Approved</h6>
                                    <small class="text-muted">TechGlobal Corp was granted B2B rates.</small>
                                </div>
                                <small class="text-muted">5h ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mini Map/Visual -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column justify-content-center align-items-center bg-blue text-white" style="background:#0d47a1;">
                    <div class="card-body text-center p-5">
                       <i class="fa-solid fa-earth-americas display-1 mb-4 opacity-50"></i>
                       <h4 class="fw-bolder">Global Traffic</h4>
                       <p class="text-white-50 small mb-4">View real-time movement of all fleet vessels globally.</p>
                       <button class="btn btn-light text-blue fw-bold rounded-pill px-4">Open Map</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php include 'includes/footer.php'; ?>
