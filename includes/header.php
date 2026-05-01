<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoConnect - Streamline Your Shipping</title>
    <!-- Favicon -->
    <link rel="icon" href="favicon.png" type="image/png">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex flex-column min-vh-100" style="background-color: #fafbfc;">

    <!-- Header / Navigation -->
    <nav class="navbar navbar-expand-lg bg-white px-4 py-3 border-bottom shadow-sm">
        <div class="container container-fluid">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="index.php" style="font-family: 'Inter', sans-serif;">
                <div class="d-flex me-2">
                    <div style="background-color: #F97316; width: 14px; height: 18px; border-radius: 1px;"></div>
                    <div style="background-color: #FBD38D; width: 6px; height: 18px; margin-left: 2px; border-radius: 1px;"></div>
                </div>
                <span style="color: #1E3A8A; font-weight: 800; font-size: 1.4rem;">Cargo</span><span style="color: #F97316; font-weight: 300; font-size: 1.4rem;">Connect.</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav Links -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-4">
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-bold" style="font-size: 0.85rem;" href="index.php">HOME</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-bold" style="font-size: 0.85rem;" href="auth.php">LOGIN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-bold" style="font-size: 0.85rem;" href="auth.php?action=register">SIGN UP</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="book.php" class="btn text-white fw-bold rounded-1 px-4 py-2 shadow-sm" style="background-color: #2563EB; font-size: 0.85rem;">BOOK SHIPMENT</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
