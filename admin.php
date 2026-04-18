<?php include 'includes/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --cargo-blue: #1a3b8d;
        --cargo-orange: #ff7e21;
    }
    .admin-wrapper { min-height: 100vh; font-family: 'Segoe UI', sans-serif; background-color: #f4f7f9; }
    
    /* Sidebar */
    .admin-sidebar { width: 260px; background-color: var(--cargo-blue); color: white; position: fixed; height: 100%; }
    .brand-area { padding: 20px; display: flex; align-items: center; gap: 10px; }
    .logo-icon { width: 35px; height: 35px; background: var(--cargo-orange); border-radius: 4px; display: flex; align-items: center; justify-content: center; }
    
    .nav-pills .nav-link { color: rgba(255,255,255,0.8); margin: 5px 15px; padding: 12px 15px; border-radius: 8px; transition: 0.3s; }
    .nav-pills .nav-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
    .nav-pills .nav-link.active { background-color: rgba(255,255,255,0.2); border-left: 5px solid #fff; border-radius: 4px; color: #fff; }
    
    /* Main Content */
    .main-content { margin-left: 260px; width: calc(100% - 260px); }
    .content-header { background: white; padding: 15px 40px; border-bottom: 1px solid #e0e0e0; position: sticky; top: 0; z-index: 1000; }
    .admin-name { font-weight: 600; font-size: 0.9rem; color: #333; margin-right: 15px; text-transform: uppercase; }

    /* Cards & Tables */
    .custom-card { background: white; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-radius: 12px; margin-bottom: 30px; }
    .table-container { padding: 0; overflow: hidden; }
    .table thead { background-color: #f8f9fa; }
    .table th { padding: 18px; font-weight: 600; border: none; color: #444; }
    .table td { padding: 15px; vertical-align: middle; border-top: 1px solid #eee; }

    /* Buttons base sa screenshot */
    .btn-orange { background-color: var(--cargo-orange); color: white; font-weight: bold; border: none; padding: 10px 20px; border-radius: 6px; }
    .btn-blue { background-color: #2b63e1; color: white; border: none; font-size: 0.85rem; padding: 6px 15px; }
    .btn-red { background-color: #ff1a1a; color: white; border: none; font-size: 0.85rem; padding: 6px 15px; }
    
    /* Status Labels */
    .status-pending { color: var(--cargo-orange); font-weight: 500; }
    .status-approved { color: #28a745; font-weight: 500; }
    .status-suspended { color: #ff1a1a; font-weight: 500; }
</style>

<div class="d-flex admin-wrapper">
    <div class="admin-sidebar flex-shrink-0">
        <div class="brand-area mb-4">
            <div class="logo-icon">
                <i class="fa-solid fa-truck-fast text-white"></i>
            </div>
            <h4 class="mb-0 fw-bold">CargoConnect.</h4>
        </div>

        <ul class="nav nav-pills flex-column">
            <?php $view = isset($_GET['view']) ? $_GET['view'] : 'users'; ?>
            <li class="nav-item">
                <a href="admin.php?view=users" class="nav-link <?php echo ($view == 'users') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user me-3"></i>User Management
                </a>
            </li>
            <li class="nav-item">
                <a href="admin.php?view=bookings" class="nav-link <?php echo ($view == 'bookings') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-calendar-days me-3"></i>Bookings
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="content-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0 fw-bold"><?php echo ($view == 'bookings') ? 'Booking Management' : 'User Management'; ?></h2>
            <div class="d-flex align-items-center">
                <span class="admin-name">Admin</span>
                <img src="https://images.uifaces.co/our-content/donated/g-uHq86g_400x400.jpg" class="rounded-circle" width="45" height="45" style="border: 2px solid #eee;">
            </div>
        </div>

        <div class="p-4 mt-2">
            <?php if ($view == 'bookings'): ?>
                <div class="custom-card p-4">
                    <h4 class="fw-bold mb-4">System Volume <small class="text-muted fw-normal">(Last 30 Days)</small></h4>
                    <div style="height: 300px;"><canvas id="volumeChart"></canvas></div>
                </div>

                <div class="custom-card table-container">
                    <div class="p-4"><h4 class="fw-bold mb-0">Recent Shipment Activity</h4></div>
                    <div class="table-responsive">
                        <table class="table mb-0 text-center">
                            <thead>
                                <tr><th>User ID</th><th>First Name</th><th>Last Name</th><th>Route</th><th>Status</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php for($i=0; $i<6; $i++): ?>
                                <tr>
                                    <td class="text-muted">10325</td>
                                    <td>Jana</td>
                                    <td>Hamson</td>
                                    <td>Manila > Batangas</td>
                                    <td><span class="<?php echo ($i==0) ? 'status-pending':'status-approved'; ?>"><?php echo ($i==0) ? 'Pending':'Approved'; ?></span></td>
                                    <td>
                                        <button class="btn btn-blue rounded-1 me-1">Approve</button>
                                        <button class="btn btn-red rounded-1">Reject</button>
                                    </td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <div class="d-flex justify-content-end mb-4 gap-2">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control border-end-0" placeholder="Search">
                        <span class="input-group-text bg-white border-start-0"><i class="fa fa-search text-muted"></i></span>
                    </div>
                    <select class="form-select" style="width: 150px;"><option>Filter</option></select>
                    <button class="btn btn-orange text-uppercase">Add New User</button>
                </div>

                <div class="custom-card table-container">
                    <div class="table-responsive">
                        <table class="table mb-0 text-center">
                            <thead>
                                <tr><th>User ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-muted">10325</td>
                                    <td>Jana Hamson</td>
                                    <td>jana@mail.com</td>
                                    <td>Shipper</td>
                                    <td><span class="status-suspended">Suspended</span></td>
                                    <td>
                                        <button class="btn btn-blue rounded-1 me-1">Edit</button>
                                        <button class="btn btn-red rounded-1">Suspend</button>
                                    </td>
                                </tr>
                                <?php for($i=0; $i<8; $i++): ?>
                                <tr>
                                    <td class="text-muted">10325</td>
                                    <td>Jana</td>
                                    <td>jana@mail.com</td>
                                    <td><?php echo ($i==0) ? 'Provider' : 'Admin'; ?></td>
                                    <td><span class="status-approved"><?php echo ($i==0) ? 'Active' : 'Approved'; ?></span></td>
                                    <td>
                                        <button class="btn btn-blue rounded-1 me-1">Edit</button>
                                        <button class="btn btn-red rounded-1">Suspend</button>
                                    </td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    if (document.getElementById('volumeChart')) {
        const ctx = document.getElementById('volumeChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(74, 144, 226, 0.3)');
        gradient.addColorStop(1, 'rgba(74, 144, 226, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['1', '2', '4', '7', '8', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30'],
                datasets: [{
                    data: [10, 28, 29, 38, 55, 58, 65, 80, 85, 95],
                    borderColor: '#4A90E2',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#4A90E2'
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, max: 100, grid: { color: '#f0f0f0' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
</script>

<?php include 'includes/footer.php'; ?>