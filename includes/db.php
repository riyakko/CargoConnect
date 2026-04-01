<?php
/**
 * AwardSpace Database Connection
 * 
 * Find these details in your AwardSpace Control Panel under "Database Manager".
 */

// 1. Your Database Host (AwardSpace will specify this, e.g., "fdb27.awardspace.net")
$db_host = 'YOUR_AWARDSPACE_DB_HOST'; 

// 2. Your Database User (Often similar to the DB name, e.g., "4746260_user")
$db_user = 'YOUR_AWARDSPACE_USER'; 

// 3. The password you set for this database
$db_pass = 'YOUR_DB_PASSWORD'; 

// 4. Your Database Name (Provided by you)
$db_name = '4746260_cargoconnecct'; 

// Try to connect using MySQLi
try {
    // Suppress warnings temporarily during connection attempts using @
    $conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Set charset to ensure modern characters/emojis are saved properly
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    // For debugging, you can uncomment the line below to see exact errors.
    // die("Database Error: " . $e->getMessage());
    
    // We set $conn to null if it fails so the UI doesn't completely crash while you're offline.
    $conn = null; 
}
?>
