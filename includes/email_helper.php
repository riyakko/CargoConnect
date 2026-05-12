<?php
/**
 * Email Helper Functions for CargoConnect
 * 
 * This file contains functions for sending emails related to password resets.
 * In a production environment, you would configure proper SMTP settings.
 */

/**
 * Send password reset email
 * 
 * @param string $to Recipient email address
 * @param string $reset_link Password reset link
 * @return bool True if email sent successfully, false otherwise
 */
function sendPasswordResetEmail($to, $reset_link) {
    // For demo purposes, we'll simulate email sending
    // In production, you would use PHPMailer, SwiftMailer, or similar
    
    $subject = "CargoConnect - Password Reset Request";
    $message = "
    <html>
    <head>
        <title>Password Reset</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #F97316; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 5px; }
            .button { display: inline-block; background: #F97316; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
            .security-note { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>CargoConnect</h1>
                <p>Password Reset Request</p>
            </div>
            <div class='content'>
                <h2>Hello,</h2>
                <p>We received a request to reset your password for your CargoConnect account.</p>
                
                <div class='security-note'>
                    <strong>Security Notice:</strong> If you didn't request this password reset, please ignore this email. Your password will remain unchanged.
                </div>
                
                <p>Click the button below to reset your password:</p>
                
                <div style='text-align: center;'>
                    <a href='$reset_link' class='button'>Reset My Password</a>
                </div>
                
                <p>Or copy and paste this link into your browser:</p>
                <p style='word-break: break-all; background: #f0f0f0; padding: 10px; border-radius: 3px;'>$reset_link</p>
                
                <p><strong>This link will expire in 30 minutes.</strong></p>
            </div>
            <div class='footer'>
                <p>&copy; 2024 CargoConnect. All rights reserved.</p>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@cargoconnect.com" . "\r\n";
    
    // For demo purposes, we'll store the email in session instead of actually sending
    $_SESSION['demo_email'] = [
        'to' => $to,
        'subject' => $subject,
        'message' => $message,
        'headers' => $headers,
        'reset_link' => $reset_link
    ];
    
    // In production, you would use:
    // return mail($to, $subject, $message, $headers);
    
    // For demo, return true
    return true;
}

/**
 * Validate email format
 * 
 * @param string $email Email address to validate
 * @return bool True if email is valid, false otherwise
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate secure random token
 * 
 * @param int $length Token length in bytes
 * @return string Hexadecimal token
 */
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Clean expired tokens from database
 * 
 * @param mysqli $conn Database connection
 * @return int Number of tokens cleaned
 */
function cleanExpiredTokens($conn) {
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
    $stmt->execute();
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    return $affected_rows;
}
?>
