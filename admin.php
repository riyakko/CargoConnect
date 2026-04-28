<?php
require_once 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') { header('Location: auth.php'); exit; }

$current_user = null;
if ($conn) {
    $s = $conn->prepare("SELECT * FROM users WHERE user_id=?");
    $s->bind_param('i', $_SESSION['user_id']); $s->execute();
    $current_user = $s->get_result()->fetch_assoc(); $s->close();
}
if (!$current_user) { session_destroy(); header('Location: auth.php'); exit; }
$initials = strtoupper(substr($current_user['first_name'],0,1).substr($current_user['last_name'],0,1));

// ── Handle POST actions ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $conn) {

    // Edit user role
    if ($_POST['action']==='edit_role' && isset($_POST['uid'],$_POST['role'])) {
        $s=$conn->prepare("UPDATE users SET role=? WHERE user_id=?");
        $s->bind_param('si',$_POST['role'],$_POST['uid']); $s->execute(); $s->close();
    }
    // Toggle suspend
    if ($_POST['action']==='suspend' && isset($_POST['uid'])) {
        $s=$conn->prepare("UPDATE users SET status=IF(status='active','inactive','active') WHERE user_id=? AND user_id!=?");
        $s->bind_param('ii',$_POST['uid'],$_SESSION['user_id']); $s->execute(); $s->close();
    }
    // Add new user
    if ($_POST['action']==='add_user') {
        $hash=password_hash($_POST['password'],PASSWORD_DEFAULT);
        $s=$conn->prepare("INSERT INTO users(first_name,last_name,email,password,role,contact_number,status) VALUES(?,?,?,?,?,?,'active')");
        $fn=trim($_POST['first_name']); $ln=trim($_POST['last_name']);
        $em=trim($_POST['email']); $ro=$_POST['role']; $ph=trim($_POST['phone']??'');
        $s->bind_param('ssssss',$fn,$ln,$em,$hash,$ro,$ph); $s->execute(); $s->close();
    }
    // Update booking status
    if ($_POST['action']==='update_booking' && isset($_POST['bid'],$_POST['bstatus'])) {
        $s=$conn->prepare("UPDATE bookings SET booking_status=? WHERE booking_id=?");
        $s->bind_param('si',$_POST['bstatus'],$_POST['bid']); $s->execute(); $s->close();
        // Also update shipment status
        $map=['Approved'=>'In Transit','Rejected'=>'Pending','In Transit'=>'In Transit'];
        $shipStatus=$map[$_POST['bstatus']]??'Pending';
        $s2=$conn->prepare("UPDATE shipments s JOIN bookings b ON s.shipment_id=b.shipment_id SET s.status=? WHERE b.booking_id=?");
        if($s2){$s2->bind_param('si',$shipStatus,$_POST['bid']);$s2->execute();$s2->close();}
    }
    header('Location: admin.php?tab='.($_POST['tab']??'users')); exit;
}

$tab = $_GET['tab'] ?? 'users';

// ── Fetch data ────────────────────────────────────────────────
$users=[]; $bookings=[]; $chart_labels=[]; $chart_data=[];

if ($conn) {
    $search = $_GET['search'] ?? '';
    $filter = $_GET['filter'] ?? '';
    $q = "SELECT * FROM users WHERE 1";
    if ($search) $q .= " AND (first_name LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR last_name LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR email LIKE '%".mysqli_real_escape_string($conn,$search)."%')";
    if ($filter) $q .= " AND role='".mysqli_real_escape_string($conn,$filter)."'";
    $q .= " ORDER BY date_created DESC LIMIT 50";
    $r=$conn->query($q); if($r){while($row=$r->fetch_assoc())$users[]=$row;}

    $r=$conn->query("SELECT b.*, s.tracking_id, s.origin, s.destination, s.status as ship_status, u.first_name, u.last_name, u.user_id as uid FROM bookings b LEFT JOIN shipments s ON b.shipment_id=s.shipment_id LEFT JOIN users u ON b.user_id=u.user_id ORDER BY b.date_requested DESC LIMIT 50");
    if($r){while($row=$r->fetch_assoc())$bookings[]=$row;}

    // Chart: shipments per month (last 6 months)
    $r=$conn->query("SELECT DATE_FORMAT(date_created,'%b %Y') as mo, COUNT(*) as cnt FROM shipments WHERE date_created >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY YEAR(date_created),MONTH(date_created) ORDER BY YEAR(date_created),MONTH(date_created)");
    if($r){while($row=$r->fetch_assoc()){$chart_labels[]=$row['mo'];$chart_data[]=$row['cnt'];}}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Panel | CargoConnect</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root{
    --adm-blue:#1E3A8A;
    --adm-blue-dark:#0f2560;
    --adm-blue-light:#2563EB;
    --adm-orange:#F97316;
    --adm-orange-light:#FDBA74;
    --adm-bg:#f1f5f9;
    --adm-text:#111827;
    --adm-text-muted:#6b7280;
    --adm-border:#e5e7eb;
    --adm-sidebar-w:260px;
    --adm-radius:12px;
}
*{box-sizing:border-box;margin:0;padding:0}
body{
    font-family:'Inter',sans-serif;
    background:var(--adm-bg);
    display:flex;
    min-height:100vh;
    overflow-x:hidden;
}

/* Sidebar */
.adm-sidebar-overlay{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.45);
    backdrop-filter:blur(2px);
    -webkit-backdrop-filter:blur(2px);
    z-index:99;
}
.adm-sidebar-overlay.active{display:block;}
.adm-sidebar{
    width:var(--adm-sidebar-w);
    min-height:100vh;
    background:linear-gradient(180deg,var(--adm-blue) 0%, var(--adm-blue-dark) 100%);
    flex-shrink:0;
    display:flex;
    flex-direction:column;
    position:fixed;
    top:0;
    left:0;
    height:100vh;
    z-index:100;
    overflow-y:auto;
    scrollbar-width:none;
    transition:transform .3s cubic-bezier(.4,0,.2,1);
}
.adm-sidebar::-webkit-scrollbar{display:none;}
.adm-sidebar-close{
    display:none;
    position:absolute;
    top:16px;
    right:16px;
    width:32px;
    height:32px;
    border:none;
    border-radius:6px;
    background:rgba(255,255,255,0.1);
    color:rgba(255,255,255,0.7);
    cursor:pointer;
    align-items:center;
    justify-content:center;
    z-index:5;
}
.adm-sidebar-close:hover{background:rgba(255,255,255,0.2);color:#fff;}
.adm-logo{
    display:flex;
    align-items:center;
    gap:10px;
    padding:28px 24px 36px;
}
.adm-logo-text{font-size:1.35rem;font-weight:800;color:#fff;letter-spacing:-0.4px;line-height:1;}
.adm-logo-text span{color:#fff;}
.adm-logo-text b{color:var(--adm-orange);font-weight:300;}
.adm-logo-bars{display:flex;gap:3px;flex-shrink:0;align-items:center;margin-bottom:0;}
.adm-logo-bars div{background:var(--adm-orange);border-radius:2px;}
.adm-logo-bars div:last-child{background:var(--adm-orange-light);}
.adm-nav{
    flex:1;
    display:flex;
    flex-direction:column;
    gap:2px;
    padding:0 12px;
}
.adm-nav a,
.adm-sidebar-footer a{
    display:flex;
    align-items:center;
    gap:14px;
    padding:13px 16px;
    color:rgba(255,255,255,0.6);
    font-size:0.9rem;
    font-weight:500;
    text-decoration:none;
    border-radius:8px;
    transition:all .25s cubic-bezier(.4,0,.2,1);
}
.adm-nav a:hover,
.adm-sidebar-footer a:hover{
    color:#fff;
    background:rgba(255,255,255,0.08);
}
.adm-nav a.active,
.adm-sidebar-footer a.active{
    color:#fff;
    background:rgba(255,255,255,0.12);
    box-shadow:inset 3px 0 0 var(--adm-orange);
    font-weight:600;
}
.adm-nav a i,
.adm-sidebar-footer a i{
    width:20px;
    text-align:center;
    flex-shrink:0;
}
.adm-sidebar-footer{
    padding:12px;
    margin-top:auto;
    border-top:1px solid rgba(255,255,255,0.08);
    display:flex;
    flex-direction:column;
    gap:2px;
}
.adm-logout{color:rgba(255,255,255,0.65)!important;}
.adm-logout:hover{color:#fca5a5!important;background:rgba(239,68,68,0.12)!important;}

/* Main */
.adm-main{
    margin-left:var(--adm-sidebar-w);
    flex:1;
    display:flex;
    flex-direction:column;
    min-height:100vh;
    min-width:0;
}
.adm-topbar{
    min-height:64px;
    background:#fff;
    border-bottom:1px solid var(--adm-border);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    padding:12px 28px;
    position:sticky;
    top:0;
    z-index:50;
}
.adm-topbar h5{font-size:1.2rem;font-weight:800;color:var(--adm-text);margin:0;}
.adm-topbar-left,
.adm-topbar-right{
    display:flex;
    align-items:center;
    gap:12px;
    min-width:0;
}
.adm-menu-toggle{
    display:none;
    align-items:center;
    justify-content:center;
    width:40px;
    height:40px;
    border:none;
    border-radius:8px;
    background:transparent;
    color:var(--adm-text);
    font-size:1.2rem;
    cursor:pointer;
    flex-shrink:0;
}
.adm-menu-toggle:hover{background:#f3f4f6;}
.adm-avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--adm-blue-light),var(--adm-orange));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.82rem;border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
.adm-content{padding:28px;min-width:0;}

/* Cards */
.adm-card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 4px rgba(0,0,0,0.05);margin-bottom:24px;overflow:hidden;}
.adm-card-hdr{padding:18px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;}
.adm-card-hdr h5{font-size:1rem;font-weight:800;margin:0;color:#111827;}

/* Table */
.adm-table-wrap{
    width:100%;
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
}
.adm-table{width:100%;border-collapse:collapse;font-size:0.85rem;min-width:760px;}
.adm-table th{padding:12px 16px;text-align:left;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;font-weight:700;background:#f9fafb;border-bottom:1px solid #f3f4f6;}
.adm-table td{padding:13px 16px;border-bottom:1px solid #f9fafb;color:#374151;vertical-align:middle;text-align:center;}
.adm-table th{text-align:center;}
.adm-table tr:last-child td{border-bottom:none;}
.adm-table tr:hover td{background:#fafafa;}

/* Badges */
.bdg{display:inline-block;padding:3px 10px;border-radius:20px;font-size:0.75rem;font-weight:700;}
.bdg-green{color:#16a34a;} .bdg-red{color:#dc2626;} .bdg-orange{color:#d97706;} .bdg-blue{color:#2563eb;}

/* Buttons */
.btn-edit{background:#2563eb;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:0.78rem;font-weight:700;cursor:pointer;transition:background 0.2s;}
.btn-edit:hover{background:#1d4ed8;}
.btn-suspend{background:#dc2626;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:0.78rem;font-weight:700;cursor:pointer;transition:background 0.2s;}
.btn-suspend:hover{background:#b91c1c;}
.btn-approve{background:#2563eb;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:0.78rem;font-weight:700;cursor:pointer;}
.btn-approve:hover{background:#1d4ed8;}
.btn-reject{background:#dc2626;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:0.78rem;font-weight:700;cursor:pointer;}
.btn-reject:hover{background:#b91c1c;}
.btn-transit{background:#f97316;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:0.78rem;font-weight:700;cursor:pointer;}
.btn-add{background:#f97316;color:#fff;border:none;border-radius:8px;padding:9px 20px;font-size:0.82rem;font-weight:700;cursor:pointer;letter-spacing:0.04em;text-transform:uppercase;}
.btn-add:hover{background:#ea580c;}

/* Search bar */
.adm-toolbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.adm-search{display:flex;align-items:center;border:1.5px solid #e5e7eb;border-radius:8px;padding:7px 12px;gap:8px;background:#fff;}
.adm-search input{border:none;outline:none;font-size:0.85rem;font-family:'Inter',sans-serif;width:180px;max-width:100%;color:#111827;}
.adm-search i{color:#9ca3af;font-size:0.8rem;}
.adm-filter{border:1.5px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:0.85rem;font-family:'Inter',sans-serif;color:#374151;background:#fff;cursor:pointer;outline:none;}

/* Modal */
.modal-backdrop-custom{position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:500;display:none;align-items:center;justify-content:center;}
.modal-backdrop-custom.open{display:flex;}
.modal-box{background:#fff;border-radius:16px;padding:32px;width:400px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,0.2);}
.modal-box h5{font-weight:800;margin:0 0 20px;}
.modal-label{font-size:0.78rem;font-weight:600;color:#6b7280;display:block;margin-bottom:4px;}
.modal-input{width:100%;padding:9px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:0.85rem;font-family:'Inter',sans-serif;outline:none;margin-bottom:12px;}
.modal-input:focus{border-color:#2563eb;}
.modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:8px;}
.btn-cancel{background:#f3f4f6;color:#374151;border:none;border-radius:8px;padding:9px 18px;font-size:0.85rem;font-weight:600;cursor:pointer;}

/* Chart container */
.chart-wrap{padding:24px;position:relative;height:280px;}

/* Responsive */
@media (max-width: 991.98px){
    .adm-card-hdr{flex-wrap:wrap;gap:12px;}
}

@media (max-width: 768px){
    .adm-sidebar{
        transform:translateX(-100%);
        z-index:200;
    }
    .adm-sidebar.open{transform:translateX(0);}
    .adm-sidebar-close{display:flex;}
    .adm-logo{padding-right:52px;}
    .adm-main{margin-left:0;}
    .adm-menu-toggle{display:flex;}
    .adm-topbar{
        padding:12px 16px;
        flex-wrap:wrap;
    }
    .adm-content{padding:16px 12px 32px;}
    .adm-card-hdr{padding:16px;}
    .adm-card-hdr h5{font-size:0.95rem;}
    .adm-toolbar,
    .adm-toolbar form{
        width:100%;
    }
    .adm-toolbar form{
        display:flex;
        flex-wrap:wrap;
    }
    .adm-search{
        flex:1 1 220px;
        width:100%;
    }
    .adm-search input{
        width:100%;
    }
    .adm-filter,
    .btn-add{
        width:100%;
    }
}

@media (max-width: 480px){
    .adm-topbar h5{font-size:1rem;}
    .adm-topbar-right{
        width:100%;
        justify-content:space-between;
    }
    .modal-box{padding:20px;}
    .modal-footer{
        flex-direction:column;
    }
    .modal-footer button{
        width:100%;
    }
}
</style>
</head>
<body>

<div class="adm-sidebar-overlay" id="admSidebarOverlay"></div>

<!-- Sidebar -->
<aside class="adm-sidebar">
    <button class="adm-sidebar-close" id="admSidebarClose" aria-label="Close sidebar">
        <i class="fas fa-xmark"></i>
    </button>
    <div class="adm-logo">
        <div class="adm-logo-bars">
            <div style="width:8px;height:12px;"></div>
            <div style="width:10px;height:18px;"></div>
            <div style="width:6px;height:12px;"></div>
        </div>
        <div class="adm-logo-text"><span>Cargo</span><b>Connect.</b></div>
    </div>
    <nav class="adm-nav">
        <a href="admin.php?tab=users" class="<?php echo $tab==='users'?'active':''; ?>"><i class="fas fa-user"></i> User Management</a>
        <a href="admin.php?tab=bookings" class="<?php echo $tab==='bookings'?'active':''; ?>"><i class="fas fa-calendar-check"></i> Bookings</a>
    </nav>
    <div class="adm-sidebar-footer">
        <a href="dashboard.php"><i class="fas fa-gauge-high"></i> Dashboard</a>
        <a href="auth.php?action=logout" class="adm-logout"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>
</aside>

<!-- Main -->
<div class="adm-main">
    <div class="adm-topbar">
        <div class="adm-topbar-left">
            <button class="adm-menu-toggle" id="admMenuToggle" aria-label="Open sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <h5><?php echo $tab==='users'?'User Management':'Booking Management'; ?></h5>
        </div>
        <div class="adm-topbar-right">
            <span style="font-size:0.82rem;font-weight:700;color:#6b7280;">ADMIN</span>
            <div class="adm-avatar"><?php echo $initials; ?></div>
        </div>
    </div>

    <div class="adm-content">

    <?php if ($tab==='users'): ?>
    <!-- ── USER MANAGEMENT ── -->
    <div class="adm-card">
        <div class="adm-card-hdr">
            <h5></h5>
            <div class="adm-toolbar">
                <form method="GET" action="admin.php" style="display:flex;gap:8px;align-items:center;">
                    <input type="hidden" name="tab" value="users">
                    <div class="adm-search">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($_GET['search']??''); ?>">
                    </div>
                    <select name="filter" class="adm-filter" onchange="this.form.submit()">
                        <option value="">Filter</option>
                        <option value="admin" <?php echo ($_GET['filter']??'')==='admin'?'selected':'';?>>Admin</option>
                        <option value="customer" <?php echo ($_GET['filter']??'')==='customer'?'selected':'';?>>Customer</option>
                    </select>
                    <button type="submit" style="display:none;"></button>
                </form>
                <button class="btn-add" onclick="document.getElementById('addModal').classList.add('open')"><i class="fas fa-plus me-1"></i>ADD NEW USER</button>
            </div>
        </div>
        <div class="adm-table-wrap">
        <table class="adm-table">
            <thead><tr><th>User ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php if (!empty($users)): foreach($users as $u): ?>
                <tr>
                    <td>#<?php echo $u['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($u['first_name'].' '.$u['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo ucfirst(htmlspecialchars($u['role'])); ?></td>
                    <td>
                        <?php $st=$u['status']??'active';
                        $cls=$st==='active'?'bdg-green':($st==='inactive'?'bdg-red':'bdg-orange'); ?>
                        <span class="bdg <?php echo $cls; ?>"><?php echo ucfirst($st==='inactive'?'Suspended':$st); ?></span>
                    </td>
                    <td style="display:flex;gap:6px;justify-content:center;">
                        <button class="btn-edit" onclick="openEdit(<?php echo $u['user_id']; ?>,'<?php echo htmlspecialchars($u['first_name'].' '.$u['last_name']); ?>','<?php echo $u['role']; ?>')">Edit</button>
                        <?php if($u['user_id']!=$_SESSION['user_id']): ?>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="action" value="suspend">
                            <input type="hidden" name="uid" value="<?php echo $u['user_id']; ?>">
                            <input type="hidden" name="tab" value="users">
                            <button type="submit" class="btn-suspend"><?php echo $st==='inactive'?'Activate':'Suspend'; ?></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6" style="padding:40px;color:#9ca3af;">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

    <?php else: ?>
    <!-- ── BOOKINGS ── -->
    <!-- Chart -->
    <div class="adm-card">
        <div class="adm-card-hdr">
            <h5>System Volume <span style="font-weight:400;font-size:0.88rem;color:#9ca3af;">(Last 6 Months)</span></h5>
        </div>
        <div class="chart-wrap"><canvas id="volumeChart"></canvas></div>
    </div>

    <!-- Bookings Table -->
    <div class="adm-card">
        <div class="adm-card-hdr"><h5>Recent Shipment Activity</h5></div>
        <div class="adm-table-wrap">
        <table class="adm-table">
            <thead><tr><th>User ID</th><th>First Name</th><th>Last Name</th><th>Route</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php if(!empty($bookings)): foreach($bookings as $b): ?>
                <tr>
                    <td>#<?php echo $b['uid']??'—'; ?></td>
                    <td><?php echo htmlspecialchars($b['first_name']??'—'); ?></td>
                    <td><?php echo htmlspecialchars($b['last_name']??'—'); ?></td>
                    <td><?php echo htmlspecialchars(($b['origin']??'?').' → '.($b['destination']??'?')); ?></td>
                    <td>
                        <?php $bs=$b['booking_status']??'Pending';
                        $bc=$bs==='Approved'?'bdg-green':($bs==='Rejected'?'bdg-red':($bs==='In Transit'?'bdg-blue':'bdg-orange')); ?>
                        <span class="bdg <?php echo $bc; ?>"><?php echo htmlspecialchars($bs); ?></span>
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;justify-content:center;flex-wrap:wrap;">
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="update_booking">
                                <input type="hidden" name="bid" value="<?php echo $b['booking_id']; ?>">
                                <input type="hidden" name="bstatus" value="Approved">
                                <input type="hidden" name="tab" value="bookings">
                                <button type="submit" class="btn-approve">Approve</button>
                            </form>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="update_booking">
                                <input type="hidden" name="bid" value="<?php echo $b['booking_id']; ?>">
                                <input type="hidden" name="bstatus" value="Rejected">
                                <input type="hidden" name="tab" value="bookings">
                                <button type="submit" class="btn-reject">Reject</button>
                            </form>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="update_booking">
                                <input type="hidden" name="bid" value="<?php echo $b['booking_id']; ?>">
                                <input type="hidden" name="bstatus" value="In Transit">
                                <input type="hidden" name="tab" value="bookings">
                                <button type="submit" class="btn-transit">In Transit</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6" style="padding:40px;color:#9ca3af;">No bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endif; ?>

    </div><!-- /adm-content -->
</div><!-- /adm-main -->

<!-- Edit Role Modal -->
<div class="modal-backdrop-custom" id="editModal">
    <div class="modal-box">
        <h5><i class="fas fa-user-pen me-2 text-blue"></i>Edit User Role</h5>
        <form method="POST">
            <input type="hidden" name="action" value="edit_role">
            <input type="hidden" name="tab" value="users">
            <input type="hidden" name="uid" id="edit_uid">
            <label class="modal-label">User</label>
            <input class="modal-input" id="edit_uname" readonly style="background:#f9fafb;">
            <label class="modal-label">Role</label>
            <select name="role" id="edit_role" class="modal-input">
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
            </select>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="document.getElementById('editModal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn-edit" style="padding:9px 20px;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal-backdrop-custom" id="addModal">
    <div class="modal-box">
        <h5><i class="fas fa-user-plus me-2" style="color:#f97316;"></i>Add New User</h5>
        <form method="POST">
            <input type="hidden" name="action" value="add_user">
            <input type="hidden" name="tab" value="users">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div><label class="modal-label">First Name</label><input name="first_name" class="modal-input" required placeholder="First"></div>
                <div><label class="modal-label">Last Name</label><input name="last_name" class="modal-input" required placeholder="Last"></div>
            </div>
            <label class="modal-label">Email</label>
            <input name="email" type="email" class="modal-input" required placeholder="email@example.com">
            <label class="modal-label">Phone</label>
            <input name="phone" type="tel" class="modal-input" placeholder="Optional">
            <label class="modal-label">Role</label>
            <select name="role" class="modal-input"><option value="customer">Customer</option><option value="admin">Admin</option></select>
            <label class="modal-label">Password</label>
            <input name="password" type="password" class="modal-input" required placeholder="Min 6 characters" minlength="6">
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="document.getElementById('addModal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn-add">Create User</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(uid, name, role) {
    document.getElementById('edit_uid').value = uid;
    document.getElementById('edit_uname').value = name;
    document.getElementById('edit_role').value = role;
    document.getElementById('editModal').classList.add('open');
}
document.querySelectorAll('.modal-backdrop-custom').forEach(m => {
    m.addEventListener('click', e => { if(e.target===m) m.classList.remove('open'); });
});

(function () {
    const sidebar = document.querySelector('.adm-sidebar');
    const overlay = document.getElementById('admSidebarOverlay');
    const toggle = document.getElementById('admMenuToggle');
    const closeBtn = document.getElementById('admSidebarClose');

    if (!sidebar) return;

    function openSidebar() {
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (toggle) toggle.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidebar();
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });
})();

// Chart
<?php if($tab==='bookings'): ?>
const ctx = document.getElementById('volumeChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels ?: ['No data']); ?>,
        datasets: [{
            label: 'Shipments',
            data: <?php echo json_encode($chart_data ?: [0]); ?>,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#2563eb',
            pointRadius: 4,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: '#f3f4f6' }, ticks: { font: { family:'Inter', size:11 } } },
            y: { grid: { color: '#f3f4f6' }, ticks: { font: { family:'Inter', size:11 }, stepSize:1, beginAtZero:true } }
        }
    }
});
<?php endif; ?>
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
