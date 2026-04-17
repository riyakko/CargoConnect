<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoConnect - Streamline Your Shipping</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Header / Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top header-nav px-4 py-3">
        <div class="container-fluid align-items-center">
            <!-- Logo -->
            <a class="navbar-brand brand-logo d-flex align-items-center" href="index.php">
                <i class="fa-solid fa-truck-fast brand-icon me-2"></i>
                <span class="brand-text-orange">Cargo</span><span class="brand-text-blue">Connect.</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav Links & Login -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center mb-2 mb-lg-0 gap-4">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-link-custom dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['dashboard.php', 'admin.php', 'manifest.php']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Portals
                        </a>
                        <ul class="dropdown-menu shadow border-0 rounded-3 mt-2">
                            <li><a class="dropdown-item py-2" href="dashboard.php"><i class="fa-solid fa-table-columns me-2 text-blue"></i>Dashboard</a></li>
                            <li><a class="dropdown-item py-2" href="admin.php"><i class="fa-solid fa-shield-halved me-2 text-orange"></i>Admin Panel</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2" href="manifest.php"><i class="fa-solid fa-file-invoice me-2 text-muted"></i>Manifest Generator</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'track.php' ? 'active' : ''; ?>" href="track.php">Track</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'calculator.php' ? 'active' : ''; ?>" href="calculator.php">Rates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'book.php' ? 'active' : ''; ?>" href="book.php">Book</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0 gap-2 d-flex align-items-center">
                        <?php if (basename($_SERVER['PHP_SELF']) == 'profile.php'): ?>
                             <a class="nav-link nav-link-custom active" href="profile.php"><i class="fa-solid fa-circle-user fs-4"></i></a>
                        <?php else: ?>
                             <a href="auth.php" class="btn btn-login px-4 py-2 rounded-pill fw-bold">Login</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
