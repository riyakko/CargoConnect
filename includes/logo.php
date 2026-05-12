<?php
/**
 * CargoConnect — Logo Partial
 *
 * Place your logo images in the project root:
 *   content/logo.png         — main logo (dark text, for light backgrounds: navbar/header/login)
 *   content/logo_sidebar.png — sidebar logo (light/white text, for dark backgrounds: sidebar/admin)
 *
 * Supported extensions: .png, .jpg, .jpeg, .svg, .webp
 *
 * Usage:
 *   <?php include __DIR__ . '/logo.php'; ?>                          — main logo (default)
 *   <?php $logo_variant = 'sidebar'; include __DIR__ . '/logo.php'; ?> — sidebar logo
 *
 * Optional params (set before including):
 *   $logo_variant — 'main' (default) or 'sidebar'
 *   $logo_height  — max-height in px (default: 44)
 *   $logo_class   — extra CSS class on wrapper
 */

$logo_height  = $logo_height  ?? 44;
$logo_class   = $logo_class   ?? '';
$logo_variant = $logo_variant ?? 'main';

// Resolve project root (works from both root pages and includes/ subdirectory)
$project_root = rtrim(dirname(dirname(__FILE__)), '/\\'); // goes up from includes/ to root

// Determine which file to look for
$logo_exts    = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
$prefix       = ($logo_variant === 'sidebar') ? 'logo_sidebar' : 'logo';

$logo_file = null;
foreach ($logo_exts as $ext) {
    if (file_exists($project_root . '/content/' . $prefix . '.' . $ext)) {
        $logo_file = 'content/' . $prefix . '.' . $ext;
        break;
    }
}

// Fallback: if sidebar logo not found, try main logo
if (!$logo_file && $logo_variant === 'sidebar') {
    foreach ($logo_exts as $ext) {
        if (file_exists($project_root . '/content/logo.' . $ext)) {
            $logo_file = 'content/logo.' . $ext;
            break;
        }
    }
}

// Reset variants for next call
$logo_variant = 'main';
?>

<?php if ($logo_file): ?>
    <div class="cc-logo-wrapper <?php echo htmlspecialchars($logo_class); ?>" style="display:flex;align-items:center;">
        <img src="<?php echo htmlspecialchars($logo_file); ?>"
             alt="CargoConnect"
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
<?php
// Reset for next call
$logo_height = 44;
$logo_class  = '';
?>
