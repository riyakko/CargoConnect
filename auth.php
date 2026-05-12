<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/email_helper.php';

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

// Handle FORGOT PASSWORD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'forgot_password') {
    $email = trim($_POST['forgot_email'] ?? '');
    
    // Rate limiting: check if user has requested reset recently
    $rate_limit_key = 'forgot_password_' . md5($email);
    if (isset($_SESSION[$rate_limit_key]) && (time() - $_SESSION[$rate_limit_key]) < 300) { // 5 minutes
        $forgot_error = 'Please wait 5 minutes before requesting another password reset.';
    } elseif (!$email) {
        $forgot_error = 'Please enter your email address.';
    } elseif (!validateEmail($email)) {
        $forgot_error = 'Please enter a valid email address.';
    } elseif ($conn) {
        // Clean expired tokens first
        cleanExpiredTokens($conn);
        
        // Check if email exists in database
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND status = 'active'");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Generate secure token
            $token = generateSecureToken(32);
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            
            // Delete any existing tokens for this email
            $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $delete_stmt->bind_param('s', $email);
            $delete_stmt->execute();
            $delete_stmt->close();
            
            // Insert new token
            $insert_stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $insert_stmt->bind_param('sss', $email, $token, $expires_at);
            
            if ($insert_stmt->execute()) {
                // Generate reset link
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
                
                // Send email (demo version stores in session)
                if (sendPasswordResetEmail($email, $reset_link)) {
                    // Set rate limiting
                    $_SESSION[$rate_limit_key] = time();
                    $forgot_success = "Reset link sent to your email. (Demo: Check session for email content)";
                } else {
                    $forgot_error = 'Failed to send reset email. Please try again.';
                }
            } else {
                $forgot_error = 'Failed to generate reset link. Please try again.';
            }
            $insert_stmt->close();
        } else {
            // Don't reveal if email exists or not for security
            $forgot_success = "If an account with this email exists, a reset link has been sent.";
        }
        $stmt->close();
    } else {
        $forgot_error = 'Database connection error. Please try again later.';
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
                <?php $logo_height = 48;
                include 'includes/logo.php'; ?>
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
                            <a href="#" class="text-decoration-none fw-medium" style="color: #2563eb; font-size: 0.9rem;" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
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

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Enter your email address and we'll send you a link to reset your password.</p>
                    
                    <form id="forgotPasswordForm" action="auth.php" method="POST">
                        <input type="hidden" name="form_type" value="forgot_password">
                        <div class="mb-3">
                            <label for="forgotEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="forgotEmail" name="forgot_email" placeholder="Enter your registered email" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        
                        <div id="forgotPasswordMessage" class="alert d-none" role="alert"></div>
                        
                        <button type="submit" class="btn btn-primary w-100" id="forgotPasswordSubmitBtn">
                            <span class="btn-text">Send Reset Link</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
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
            linkCreateAccount.addEventListener('click', (e) => {
                e.preventDefault();
                showRegister();
            });
            linkLogin.addEventListener('click', (e) => {
                e.preventDefault();
                showLogin();
            });
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

        // Handle forgot password form submission
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('forgotPasswordSubmitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const spinner = submitBtn.querySelector('.spinner-border');
            const messageDiv = document.getElementById('forgotPasswordMessage');
            const emailInput = document.getElementById('forgotEmail');
            
            // Clear previous messages
            messageDiv.classList.add('d-none');
            emailInput.classList.remove('is-invalid');
            
            // Validate email
            const email = emailInput.value.trim();
            if (!email || !email.includes('@')) {
                emailInput.classList.add('is-invalid');
                return;
            }
            
            // Show loading state
            btnText.textContent = 'Sending...';
            spinner.classList.remove('d-none');
            submitBtn.disabled = true;
            
            // Submit form
            this.submit();
        });

        // Handle forgot password modal events
        const forgotPasswordModal = document.getElementById('forgotPasswordModal');
        forgotPasswordModal.addEventListener('show.bs.modal', function () {
            // Reset form when modal opens
            document.getElementById('forgotPasswordForm').reset();
            document.getElementById('forgotPasswordMessage').classList.add('d-none');
            document.getElementById('forgotEmail').classList.remove('is-invalid');
            
            // Reset button state
            const submitBtn = document.getElementById('forgotPasswordSubmitBtn');
            submitBtn.querySelector('.btn-text').textContent = 'Send Reset Link';
            submitBtn.querySelector('.spinner-border').classList.add('d-none');
            submitBtn.disabled = false;
        });

        <?php if (isset($forgot_success)): ?>
            // Show success message
            const modal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
            modal.show();
            
            const messageDiv = document.getElementById('forgotPasswordMessage');
            messageDiv.className = 'alert alert-success';
            messageDiv.textContent = '<?php echo htmlspecialchars($forgot_success); ?>';
            messageDiv.classList.remove('d-none');
            
            // Hide submit button after success
            document.getElementById('forgotPasswordSubmitBtn').style.display = 'none';
            
            <?php if (isset($_SESSION['reset_link'])): ?>
            console.log('Demo: Reset link = <?php echo htmlspecialchars($_SESSION['reset_link']); ?>');
            <?php unset($_SESSION['reset_link'], $_SESSION['reset_email']); ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($forgot_error)): ?>
            // Show error message
            const modal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
            modal.show();
            
            const messageDiv = document.getElementById('forgotPasswordMessage');
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = '<?php echo htmlspecialchars($forgot_error); ?>';
            messageDiv.classList.remove('d-none');
        <?php endif; ?>
    </script>
</body>

</html>