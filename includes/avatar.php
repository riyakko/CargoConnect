<?php
/**
 * CargoConnect — Global Avatar Partial
 *
 * Renders the topbar avatar circle (image if uploaded, initials fallback).
 * Variables injected by auth_check.php:
 *
 * @var string $user_avatar   Relative path to uploaded avatar, or '' if none.
 * @var string $user_initials Two-letter initials fallback (e.g. "JD").
 *
 * Optional vars set before including:
 * @var int    $avatar_size   Max-height in px (default: 38).
 * @var string $avatar_border CSS border string (default: '2px solid var(--cc-orange)').
 */

/** @var string $user_avatar */
/** @var string $user_initials */
$_av_size   = isset($avatar_size)   ? (int)$avatar_size                          : 38;
$_av_border = isset($avatar_border) ? (string)$avatar_border                     : '2px solid var(--cc-orange)';
?>
<?php if (!empty($user_avatar)): ?>
    <img src="<?php echo htmlspecialchars($user_avatar); ?>"
         alt="<?php echo htmlspecialchars($user_initials); ?>"
         style="width:<?php echo $_av_size; ?>px;height:<?php echo $_av_size; ?>px;
                border-radius:50%;object-fit:cover;border:<?php echo $_av_border; ?>;
                display:block;">
<?php else: ?>
    <div class="cc-avatar"><?php echo htmlspecialchars($user_initials); ?></div>
<?php endif;
// Reset optional vars
unset($avatar_size, $avatar_border);
?>
