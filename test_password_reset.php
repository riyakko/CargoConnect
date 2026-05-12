<?php
/**
 * Test script for password reset functionality
 * This script helps verify the database setup and test the complete flow
 */

session_start();
require_once 'includes/db.php';
require_once 'includes/email_helper.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Test - CargoConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Password Reset Functionality Test</h3>
                    </div>
                    <div class="card-body">
                        <h4>Database Setup Check</h4>
                        <?php
                        if ($conn) {
                            echo '<div class="alert alert-success">✓ Database connection successful</div>';
                            
                            // Check if password_resets table exists
                            $table_check = $conn->query("SHOW TABLES LIKE 'password_resets'");
                            if ($table_check->num_rows > 0) {
                                echo '<div class="alert alert-success">✓ password_resets table exists</div>';
                                
                                // Show table structure
                                $structure = $conn->query("DESCRIBE password_resets");
                                echo '<h5>Table Structure:</h5>';
                                echo '<table class="table table-bordered">';
                                echo '<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr></thead>';
                                echo '<tbody>';
                                while ($row = $structure->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['Field']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['Type']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['Null']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['Key']) . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                            } else {
                                echo '<div class="alert alert-warning">⚠ password_resets table does not exist</div>';
                                echo '<p>Please run the SQL script: <code>database/password_resets.sql</code></p>';
                            }
                            
                            // Check existing users
                            $users = $conn->query("SELECT user_id, email, first_name, last_name FROM users LIMIT 5");
                            if ($users->num_rows > 0) {
                                echo '<h5>Sample Users (for testing):</h5>';
                                echo '<table class="table table-bordered">';
                                echo '<thead><tr><th>ID</th><th>Name</th><th>Email</th></tr></thead>';
                                echo '<tbody>';
                                while ($row = $users->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['user_id'] . '</td>';
                                    echo '<td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                            } else {
                                echo '<div class="alert alert-info">ℹ No users found in database</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger">✗ Database connection failed</div>';
                        }
                        ?>
                        
                        <hr>
                        
                        <h4>Test Functions</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Token Generation Test</h5>
                                <button onclick="testTokenGeneration()" class="btn btn-primary mb-3">Generate Test Token</button>
                                <div id="tokenResult"></div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Email Validation Test</h5>
                                <div class="input-group mb-3">
                                    <input type="email" id="testEmail" class="form-control" placeholder="Enter email to test">
                                    <button onclick="testEmailValidation()" class="btn btn-secondary">Test</button>
                                </div>
                                <div id="emailResult"></div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h4>Demo Session Data</h4>
                        <p>When you test the forgot password functionality, email data is stored in session for demo purposes:</p>
                        
                        <?php
                        if (isset($_SESSION['demo_email'])) {
                            echo '<div class="alert alert-info">';
                            echo '<h6>Last Demo Email:</h6>';
                            echo '<strong>To:</strong> ' . htmlspecialchars($_SESSION['demo_email']['to']) . '<br>';
                            echo '<strong>Subject:</strong> ' . htmlspecialchars($_SESSION['demo_email']['subject']) . '<br>';
                            echo '<strong>Reset Link:</strong> <a href="' . htmlspecialchars($_SESSION['demo_email']['reset_link']) . '" target="_blank">' . htmlspecialchars($_SESSION['demo_email']['reset_link']) . '</a><br>';
                            echo '<details><summary>View Email Content</summary>';
                            echo '<pre style="max-height: 200px; overflow-y: auto;">' . htmlspecialchars($_SESSION['demo_email']['message']) . '</pre>';
                            echo '</details>';
                            echo '</div>';
                            
                            // Clear session data after displaying
                            unset($_SESSION['demo_email']);
                        } else {
                            echo '<div class="alert alert-secondary">No demo email data available. Test the forgot password functionality first.</div>';
                        }
                        
                        if (isset($_SESSION['reset_link'])) {
                            echo '<div class="alert alert-warning">';
                            echo '<strong>Reset Link in Session:</strong> <a href="' . htmlspecialchars($_SESSION['reset_link']) . '" target="_blank">' . htmlspecialchars($_SESSION['reset_link']) . '</a>';
                            echo '</div>';
                        }
                        ?>
                        
                        <hr>
                        
                        <h4>Quick Links</h4>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <a href="auth.php" class="btn btn-primary">Go to Login Page</a>
                            <a href="database/password_resets.sql" class="btn btn-outline-secondary" target="_blank">View SQL Schema</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testTokenGeneration() {
            const tokenResult = document.getElementById('tokenResult');
            
            fetch('test_password_reset.php?action=generate_token')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        tokenResult.innerHTML = `
                            <div class="alert alert-success">
                                <strong>Token Generated:</strong> ${data.token}<br>
                                <strong>Length:</strong> ${data.token.length} characters<br>
                                <small>This is a secure 64-character hexadecimal token</small>
                            </div>
                        `;
                    } else {
                        tokenResult.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
                    }
                })
                .catch(error => {
                    tokenResult.innerHTML = `<div class="alert alert-danger">Network Error: ${error}</div>`;
                });
        }
        
        function testEmailValidation() {
            const email = document.getElementById('testEmail').value;
            const emailResult = document.getElementById('emailResult');
            
            if (!email) {
                emailResult.innerHTML = '<div class="alert alert-warning">Please enter an email address</div>';
                return;
            }
            
            fetch('test_password_reset.php?action=validate_email&email=' + encodeURIComponent(email))
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        emailResult.innerHTML = '<div class="alert alert-success">✓ Valid email address</div>';
                    } else {
                        emailResult.innerHTML = '<div class="alert alert-danger">✗ Invalid email address</div>';
                    }
                })
                .catch(error => {
                    emailResult.innerHTML = `<div class="alert alert-danger">Network Error: ${error}</div>`;
                });
        }
    </script>
</body>
</html>

<?php
// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if ($_GET['action'] === 'generate_token') {
        try {
            $token = generateSecureToken(32);
            echo json_encode(['success' => true, 'token' => $token]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } elseif ($_GET['action'] === 'validate_email') {
        $email = $_GET['email'] ?? '';
        $isValid = validateEmail($email);
        echo json_encode(['valid' => $isValid]);
    }
    exit;
}
?>
