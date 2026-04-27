<?php
/**
 * CargoConnect — Database Connection
 * 
 * Host from AwardSpace: fdb1033.awardspace.net
 * Database: 4746260_cargoconnecct
 *
 * Update credentials from your AwardSpace Control Panel → Database Manager.
 */

$db_host = 'fdb1033.awardspace.net';
$db_user = '4746260_cargoconnecct';
$db_pass = 'cargoconnect2026';                      
$db_name = '4746260_cargoconnecct';

$conn = null;
try {
    $conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    // Uncomment for debugging:
    // die("Database Error: " . $e->getMessage());
    $conn = null;
}
?>
