<?php
/**
 * CargoConnect — Logo Partial
 *
 * Drop your custom logo image into the project root as:
 *   logo.png  (or logo.jpg / logo.svg)
 *
 * This partial automatically detects it and uses it.
 * If no image is found, falls back to the default text logo.
 *
 * Usage:
 *   <?php include 'includes/logo.php'; ?>   (from root pages)
 *   <?php include __DIR__ . '/logo.php'; ?> (from includes/ itself)
 *
 * Params (optional, set before including):
 *   $logo_class  — extra CSS class on the wrapper  (default: '')
 *   $logo_height — img max-height in px            (default: 44)
 */

$logo_height  = $logo_height ?? 44;
$logo_class   = $logo_class  ?? '';

// Detect image from root — works whether called from root or includes/
$root = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/' . trim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$root = rtrim($root, '/\\');

// Check for the closest project root by looking for auth.php as a landmark
$search_root = dirname(__FILE__); // starts at includes/
// Walk up one level to project root
$project_root = dirname($search_root);

$logo_file   = null;
$logo_exts   = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
foreach ($logo_exts as $ext) {
    if (file_exists($project_root . '/logo.' . $ext)) {
        $logo_file = 'logo.' . $ext;
        break;
    }
}
?>

<?php if ($logo_file): ?>
    <div class="cc-logo-wrapper <?php echo htmlspecialchars($logo_class); ?>" style="display:flex;align-items:center;">
        <img src="<?php echo htmlspecialchars($logo_file); ?>"
             alt="CargoConnect Logo"
             style="max-height:<?php echo (int)$logo_height; ?>px; width:auto; object-fit:contain; display:block;">
    </div>
<?php else: ?>
    <div class="cc-logo-wrapper <?php echo htmlspecialchars($logo_class); ?>" style="display:flex;align-items:center;gap:8px;">
        <div class="cc-logo-icon">
            <div class="cc-logo-bar1"></div>
            <div class="cc-logo-bar2"></div>
        </div>
        <span class="cc-logo-text">
            <span class="cc-logo-cargo">Cargo</span><span class="cc-logo-connect">Connect.</span>
        </span>
    </div>
<?php endif; ?>
