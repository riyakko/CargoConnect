<?php
session_start();
require_once 'includes/db.php';

$page_title = "Login | CargoConnect";
$error = '';
$success = '';
$show_register = isset($_GET['action']) && $_GET['action'] === 'register';

// Handle LOGOUT
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: auth.php');
    exit;
}

// Already logged in? Redirect to dashboard
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
    header('Location: dashboard.php');
    exit;
}

// Handle LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password && $conn) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $r = $stmt->get_result();

        if ($r->num_rows > 0) {
            $user = $r->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect admin to admin page, customer to dashboard
                if ($user['role'] === 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid password. Please try again.';
            }
        } else {
            $error = 'No account found with that email.';
        }
        $stmt->close();
    } else {
        $error = $conn ? 'Please fill in all fields.' : 'Database connection error.';
    }
}

// Handle REGISTER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'register') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $show_register = true;

    if (!$full_name || !$email || !$password) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($conn) {
        // Check if email already exists
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'An account with this email already exists.';
        } else {
            // Split full name
            $parts = explode(' ', $full_name, 2);
            $first_name = $parts[0];
            $last_name = $parts[1] ?? '';
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, contact_number, status) VALUES (?, ?, ?, ?, 'customer', ?, 'active')");
            $stmt->bind_param('sssss', $first_name, $last_name, $email, $hashed, $phone);

            if ($stmt->execute()) {
                // Auto-login after registration
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_role'] = 'customer';
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $error = 'Database connection error.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body id="auth-page">
    <div class="split-container flex-column flex-md-row">
        <!-- Left Panel -->
        <div class="left-panel d-none d-md-flex">
            <div class="logo-container">
                <div class="d-flex align-items-end me-2" style="gap: 3px;">
                    <div style="width: 10px; height: 14px; background-color: #F97316; border-radius: 2px;"></div>
                    <div style="width: 12px; height: 20px; background-color: #F97316; border-radius: 2px;"></div>
                    <div style="width: 8px; height: 14px; background-color: #F97316; border-radius: 2px;"></div>
                </div>
                <h3 class="mb-0 fw-bolder" style="letter-spacing: -1px; font-size: 1.8rem;">
                    <span style="color: #1e3a8a;">Cargo</span><span class="text-orange">Connect.</span>
                </h3>
            </div>
            
            <div class="content-container text-center">
                <h1 class="hero-title text-start">
                    Streamline Your <span class="text-blue">Shipping</span>,<br>
                    <span class="text-orange">Track</span> <span style="font-weight: 800; color: #111827;">with Confidence</span>
                </h1>
            </div>
            
            <div id="canvas-container"></div>

            <div class="decorative-path">
                <svg width="100%" height="100%" viewBox="0 0 1000 800" preserveAspectRatio="none">
                    <path d="M 100 650 Q 200 700 450 550" fill="none" stroke="#3b82f6" stroke-width="2.5" />
                    <polygon points="95,645 105,650 90,655" fill="none" stroke="#3b82f6" stroke-width="2.5" transform="rotate(-30, 100, 650)" />
                    <path d="M 550 550 C 700 700 800 500 850 550" fill="none" stroke="#3b82f6" stroke-width="2.5" />
                    <rect x="846" y="546" width="8" height="8" fill="none" stroke="#3b82f6" stroke-width="2.5" transform="rotate(20, 850, 550)" />
                </svg>
            </div>

            <svg class="curve-svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
                <path d="M0,80 C320,0 1120,0 1440,80 L1440,100 L0,100 Z" fill="#0f172a" />
            </svg>
            <div class="bottom-dark"></div>
            
            <div class="glow-orange"></div>
            <div class="glow-orange-right"></div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="login-card">
                <div class="toggle-switch">
                    <div class="toggle-btn <?php echo !$show_register ? 'active' : ''; ?>" id="btn-login">LOGIN</div>
                    <div class="toggle-btn <?php echo $show_register ? 'active' : ''; ?>" id="btn-register">REGISTER</div>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-danger py-2 mb-3" style="font-size:0.85rem;border-radius:6px;">
                    <i class="fas fa-exclamation-circle me-1"></i><?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="alert alert-success py-2 mb-3" style="font-size:0.85rem;border-radius:6px;">
                    <i class="fas fa-check-circle me-1"></i><?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form id="form-login" action="auth.php" method="POST" class="<?php echo $show_register ? 'd-none' : ''; ?>">
                    <input type="hidden" name="form_type" value="login">
                    <div class="mb-4">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                    </div>
                    <div class="mb-4 position-relative">
                         <div class="d-flex justify-content-between align-items-center mb-1">
                             <label class="form-label mb-0">Password</label>
                             <a href="#" class="text-decoration-none fw-medium" style="color: #2563eb; font-size: 0.9rem;">Forgot Password?</a>
                         </div>
                        <div class="position-relative">
                            <input type="password" name="password" id="loginPasswordField" class="form-control pe-5" placeholder="Password" required>
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer; color: #64748b;" onclick="togglePassword('loginPasswordField', this.children[0])">
                                <i class="fa-solid fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit mt-4 shadow-sm">LOGIN</button>

                    <div class="text-center mt-4" style="font-size: 0.9rem; color: #1e293b;">
                        New to CargoConnect? <a href="#" id="link-create-account" class="text-decoration-none fw-medium" style="color: #3b82f6;">Create a free account</a>
                    </div>
                </form>

                <!-- Register Form -->
                <form id="form-register" action="auth.php" method="POST" class="<?php echo !$show_register ? 'd-none' : ''; ?>">
                    <input type="hidden" name="form_type" value="register">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="First Last" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" placeholder="Phone Number">
                    </div>
                    <div class="mb-3 position-relative">
                        <label class="form-label mb-1">Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="regPasswordField" class="form-control pe-5" placeholder="Password (min 6 chars)" required minlength="6">
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer; color: #64748b;" onclick="togglePassword('regPasswordField', this.children[0])">
                                <i class="fa-solid fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4 position-relative">
                        <label class="form-label mb-1">Confirm Password</label>
                        <div class="position-relative">
                            <input type="password" name="confirm_password" id="regConfirmPasswordField" class="form-control pe-5" placeholder="Confirm Password" required>
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer; color: #64748b;" onclick="togglePassword('regConfirmPasswordField', this.children[0])">
                                <i class="fa-solid fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit mt-4 shadow-sm">REGISTER</button>
                    <div class="text-center mt-4" style="font-size: 0.9rem; color: #1e293b;">
                        Already have an account? <a href="#" id="link-login" class="text-decoration-none fw-medium" style="color: #3b82f6;">Log in</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="importmap">
        {
          "imports": {
            "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
          }
        }
    </script>
    <script type="module" src="main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnLogin = document.getElementById('btn-login');
            const btnRegister = document.getElementById('btn-register');
            const formLogin = document.getElementById('form-login');
            const formRegister = document.getElementById('form-register');
            const linkCreateAccount = document.getElementById('link-create-account');
            const linkLogin = document.getElementById('link-login');

            function showLogin() {
                btnLogin.classList.add('active');
                btnRegister.classList.remove('active');
                formLogin.classList.remove('d-none');
                formRegister.classList.add('d-none');
            }

            function showRegister() {
                btnRegister.classList.add('active');
                btnLogin.classList.remove('active');
                formRegister.classList.remove('d-none');
                formLogin.classList.add('d-none');
            }

            btnLogin.addEventListener('click', showLogin);
            btnRegister.addEventListener('click', showRegister);
            linkCreateAccount.addEventListener('click', (e) => { e.preventDefault(); showRegister(); });
            linkLogin.addEventListener('click', (e) => { e.preventDefault(); showLogin(); });
        });

        function togglePassword(fieldId, iconElement) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                iconElement.classList.remove('fa-eye-slash');
                iconElement.classList.add('fa-eye');
            } else {
                field.type = 'password';
                iconElement.classList.remove('fa-eye');
                iconElement.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
