<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/email_helper.php';

$page_title = "Reset Password | CargoConnect";
$error = '';
$success = '';
$token_valid = false;
$user_email = '';

// Check if token is provided
$token = $_GET['token'] ?? '';
if (!$token) {
    $error = 'Invalid reset link. Please request a new password reset.';
} elseif (strlen($token) !== 64) { // Tokens should be 64 characters (32 bytes * 2 hex chars)
    $error = 'Invalid reset link. Please request a new password reset.';
} elseif ($conn) {
    // Clean expired tokens first
    cleanExpiredTokens($conn);
    
    // Validate token
    $stmt = $conn->prepare("SELECT email, expires_at, used FROM password_resets WHERE token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $error = 'Invalid reset link. Please request a new password reset.';
    } else {
        $reset_data = $result->fetch_assoc();
        
        // Check if token is expired
        if (strtotime($reset_data['expires_at']) < time()) {
            $error = 'Reset link has expired. Please request a new password reset.';
        } elseif ($reset_data['used']) {
            $error = 'Reset link has already been used. Please request a new password reset.';
        } else {
            $token_valid = true;
            $user_email = $reset_data['email'];
            
            // Additional security: verify user still exists and is active
            $user_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND status = 'active'");
            $user_stmt->bind_param('s', $user_email);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            
            if ($user_result->num_rows === 0) {
                $error = 'Account not found or inactive. Please contact support.';
                $token_valid = false;
            }
            $user_stmt->close();
        }
    }
    $stmt->close();
} else {
    $error = 'Database connection error. Please try again later.';
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valid && isset($_POST['form_type']) && $_POST['form_type'] === 'reset_password') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Rate limiting: prevent multiple rapid attempts
    $reset_attempts_key = 'reset_attempts_' . md5($token);
    if (!isset($_SESSION[$reset_attempts_key])) {
        $_SESSION[$reset_attempts_key] = 0;
    }
    
    if ($_SESSION[$reset_attempts_key] >= 5) {
        $error = 'Too many reset attempts. Please request a new reset link.';
    } elseif (!$password || !$confirm_password) {
        $error = 'Please fill in all fields.';
        $_SESSION[$reset_attempts_key]++;
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
        $_SESSION[$reset_attempts_key]++;
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
        $_SESSION[$reset_attempts_key]++;
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $password)) {
        $error = 'Password must contain at least one uppercase letter, one lowercase letter, and one number.';
        $_SESSION[$reset_attempts_key]++;
    } elseif ($conn) {
        // Start transaction for atomic operations
        $conn->begin_transaction();
        
        try {
            // Hash the new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Update user password
            $update_stmt = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?");
            $update_stmt->bind_param('ss', $hashed_password, $user_email);
            
            if ($update_stmt->execute()) {
                // Mark token as used
                $mark_used_stmt = $conn->prepare("UPDATE password_resets SET used = TRUE WHERE token = ?");
                $mark_used_stmt->bind_param('s', $token);
                $mark_used_stmt->execute();
                $mark_used_stmt->close();
                
                // Clean up any other tokens for this email
                $cleanup_stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ? AND token != ?");
                $cleanup_stmt->bind_param('ss', $user_email, $token);
                $cleanup_stmt->execute();
                $cleanup_stmt->close();
                
                // Commit transaction
                $conn->commit();
                
                $success = 'Password reset successfully! You can now login with your new password.';
                
                // Clear rate limiting
                unset($_SESSION[$reset_attempts_key]);
                
                // Redirect to login page after 3 seconds
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "auth.php";
                    }, 3000);
                </script>';
            } else {
                throw new Exception('Failed to update password');
            }
            $update_stmt->close();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $error = 'Failed to reset password. Please try again.';
            $_SESSION[$reset_attempts_key]++;
        }
    } else {
        $error = 'Database connection error. Please try again later.';
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

<body id="reset-page">
    <div class="reset-container">
        <div class="reset-card">
            <!-- Logo Section -->
            <div class="text-center mb-4">
                <?php $logo_height = 48;
                include 'includes/logo.php'; ?>
            </div>

            <h2 class="text-center mb-4">Reset Password</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger py-2 mb-3" style="font-size:0.9rem;border-radius:6px;">
                    <i class="fas fa-exclamation-circle me-1"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success py-2 mb-3" style="font-size:0.9rem;border-radius:6px;">
                    <i class="fas fa-check-circle me-1"></i><?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($token_valid && !$success): ?>
                <form id="resetPasswordForm" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                    <input type="hidden" name="form_type" value="reset_password">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
                        <small class="text-muted">Email cannot be changed</small>
                    </div>

                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">New Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="newPasswordField" class="form-control pe-5" placeholder="Enter new password (min 6 chars)" required minlength="6">
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer; color: #64748b;" onclick="togglePassword('newPasswordField', this.children[0])">
                                <i class="fa-solid fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4 position-relative">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <div class="position-relative">
                            <input type="password" name="confirm_password" id="confirmNewPasswordField" class="form-control pe-5" placeholder="Confirm new password" required>
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer; color: #64748b;" onclick="togglePassword('confirmNewPasswordField', this.children[0])">
                                <i class="fa-solid fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit w-100 shadow-sm">Reset Password</button>
                </form>

                <div class="text-center mt-4">
                    <a href="auth.php" class="text-decoration-none" style="color: #3b82f6;">
                        <i class="fas fa-arrow-left me-1"></i> Back to Login
                    </a>
                </div>
            <?php elseif (!$success): ?>
                <div class="text-center">
                    <a href="auth.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Login
                    </a>
                    <div class="mt-3">
                        <a href="auth.php" class="text-decoration-none" style="color: #3b82f6;">
                            Request new password reset
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

        // Password confirmation validation
        document.getElementById('resetPasswordForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('newPasswordField').value;
            const confirmPassword = document.getElementById('confirmNewPasswordField').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match. Please make sure both passwords are the same.');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
                return false;
            }
        });

        // Real-time password validation
        const confirmPasswordField = document.getElementById('confirmNewPasswordField');
        const newPasswordField = document.getElementById('newPasswordField');
        
        if (confirmPasswordField && newPasswordField) {
            confirmPasswordField.addEventListener('input', function() {
                if (this.value !== newPasswordField.value) {
                    this.style.borderColor = '#ef4444';
                } else {
                    this.style.borderColor = '#22c55e';
                }
            });
        }
    </script>
</body>

</html>
