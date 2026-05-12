<?php
require_once 'includes/auth_check.php';
/** @var int $user_id */  $user_id = $user_id ?? $_SESSION['user_id'];
/** @var string $user_role */ $user_role = $user_role ?? 'customer';
/** @var string $user_name */ $user_name = $user_name ?? '';
/** @var string $user_initials */ $user_initials = $user_initials ?? '';
$page_title  = 'Settings';
$active_page = 'settings';

// ── helpers ──────────────────────────────────────────────────────────────────
$success_msg = '';
$error_msg   = '';
$pw_success  = '';
$pw_error    = '';

// Load profile row
$profile = ['address' => '', 'preferences' => '', 'profile_picture' => ''];
if ($conn) {
    $ps = $conn->prepare("SELECT * FROM user_profiles WHERE user_id=?");
    $ps->bind_param('i', $user_id);
    $ps->execute();
    $pr = $ps->get_result();
    if ($pr->num_rows > 0) $profile = $pr->fetch_assoc();
    $ps->close();
}

// ── Handle: Profile Picture Upload ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_avatar') {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $max_size      = 2 * 1024 * 1024; // 2 MB
        $finfo         = finfo_open(FILEINFO_MIME_TYPE);
        $mime          = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed_types)) {
            $error_msg = 'Invalid file type. Only JPG, PNG, and WebP are allowed.';
        } elseif ($_FILES['avatar']['size'] > $max_size) {
            $error_msg = 'File too large. Maximum size is 2 MB.';
        } else {
            $ext      = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $user_id . '_' . time() . '.' . strtolower($ext);
            $dest     = __DIR__ . '/uploads/avatars/' . $filename;

            if (!$conn) {
                $error_msg = 'Database connection unavailable.';
            } elseif (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                // Delete old avatar file if it exists
                if (!empty($profile['profile_picture'])) {
                    $old = __DIR__ . '/' . $profile['profile_picture'];
                    if (file_exists($old)) @unlink($old);
                }

                $rel_path = 'uploads/avatars/' . $filename;
                $ups = $conn->prepare("INSERT INTO user_profiles (user_id, profile_picture) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE profile_picture = VALUES(profile_picture)");
                $ups->bind_param('is', $user_id, $rel_path);
                $ups->execute();
                $ups->close();

                $profile['profile_picture'] = $rel_path;
                $success_msg = 'Profile picture updated successfully!';
            } else {
                $error_msg = 'Failed to save the uploaded file. Check folder permissions.';
            }
        }
    } else {
        $error_msg = 'No file received or an upload error occurred.';
    }
}

// ── Handle: Remove Avatar ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_avatar') {
    if (!empty($profile['profile_picture'])) {
        $old = __DIR__ . '/' . $profile['profile_picture'];
        if (file_exists($old)) @unlink($old);
    }
    if ($conn) {
        $rms = $conn->prepare("UPDATE user_profiles SET profile_picture=NULL WHERE user_id=?");
        $rms->bind_param('i', $user_id);
        $rms->execute();
        $rms->close();
    }
    $profile['profile_picture'] = '';
    $success_msg = 'Profile picture removed.';
}

// ── Handle: Personal Info Update ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $fn  = trim($_POST['first_name'] ?? '');
    $ln  = trim($_POST['last_name']  ?? '');
    $cn  = trim($_POST['contact_number'] ?? '');
    $adr = trim($_POST['address'] ?? '');
    $prf = trim($_POST['preferences'] ?? '');

    if ($fn === '' || $ln === '') {
        $error_msg = 'First and last name are required.';
    } elseif (!$conn) {
        $error_msg = 'Database connection unavailable. Please try again later.';
    } else {
        $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, contact_number=? WHERE user_id=?");
        $stmt->bind_param('sssi', $fn, $ln, $cn, $user_id);
        $stmt->execute();
        $stmt->close();

        $pstmt = $conn->prepare("INSERT INTO user_profiles (user_id, address, preferences) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE address=VALUES(address), preferences=VALUES(preferences)");
        $pstmt->bind_param('iss', $user_id, $adr, $prf);
        $pstmt->execute();
        $pstmt->close();

        // Refresh
        $rs = $conn->prepare("SELECT * FROM users WHERE user_id=?");
        $rs->bind_param('i', $user_id);
        $rs->execute();
        $current_user = $rs->get_result()->fetch_assoc();
        $rs->close();
        $user_name     = $current_user['first_name'] . ' ' . $current_user['last_name'];
        $user_initials = strtoupper(substr($current_user['first_name'], 0, 1) . substr($current_user['last_name'], 0, 1));
        $success_msg   = 'Profile updated successfully!';
    }
}

// ── Handle: Change Password ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $cur_pw  = $_POST['current_password'] ?? '';
    $new_pw  = $_POST['new_password']     ?? '';
    $conf_pw = $_POST['confirm_password'] ?? '';

    if ($cur_pw === '' || $new_pw === '' || $conf_pw === '') {
        $pw_error = 'All password fields are required.';
    } elseif (!password_verify($cur_pw, $current_user['password'])) {
        $pw_error = 'Current password is incorrect.';
    } elseif (strlen($new_pw) < 8) {
        $pw_error = 'New password must be at least 8 characters.';
    } elseif ($new_pw !== $conf_pw) {
        $pw_error = 'New passwords do not match.';
    } elseif (!$conn) {
        $pw_error = 'Database connection unavailable. Please try again later.';
    } else {
        $hash   = password_hash($new_pw, PASSWORD_BCRYPT);
        $pwstmt = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
        $pwstmt->bind_param('si', $hash, $user_id);
        $pwstmt->execute();
        $pwstmt->close();
        $pw_success = 'Password changed successfully!';
    }
}

// ── Build avatar src ──────────────────────────────────────────────────────────
$avatar_src = !empty($profile['profile_picture']) && file_exists(__DIR__ . '/' . $profile['profile_picture'])
    ? htmlspecialchars($profile['profile_picture'])
    : '';
?>
<?php include 'includes/app_head.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="cc-main">
    <div class="cc-topbar">
        <div style="display:flex;align-items:center;">
            <button class="cc-menu-toggle" id="menuToggle" aria-label="Open sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <span class="cc-topbar-title"><i class="fas fa-gear text-blue me-2"></i>Settings</span>
        </div>
        <div class="cc-topbar-actions">
            <?php if ($avatar_src): ?>
                <img src="<?php echo $avatar_src; ?>" alt="avatar"
                     style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--cc-orange);">
            <?php else: ?>
                <div class="cc-avatar"><?php echo $user_initials; ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="cc-page">

        <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars($error_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <h2 class="cc-page-title">User Settings</h2>
        <p class="cc-page-subtitle">Manage your profile, picture, and security settings.</p>

        <div class="cc-settings-grid">

            <!-- ── Left: Profile Card ─────────────────────────────────── -->
            <div>
                <div class="cc-card cc-profile-card">
                    <div class="cc-profile-banner"></div>
                    <div class="cc-card-body" style="padding-top:0;text-align:center;">

                        <!-- Avatar display -->
                        <div class="cc-profile-avatar" id="avatarWrap" style="position:relative;display:inline-block;background:transparent;border:none;box-shadow:none;">
                            <?php if ($avatar_src): ?>
                                <img id="avatarPreview" src="<?php echo $avatar_src; ?>"
                                     alt="Profile Picture"
                                     style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--cc-orange);">
                            <?php else: ?>
                                <img id="avatarPreview" src=""
                                     alt="Profile Picture"
                                     style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--cc-orange);display:none;">
                                <div id="avatarFallback" style="width:80px;height:80px;border-radius:50%;background:var(--cc-orange);display:flex;align-items:center;justify-content:center;font-size:2rem;color:#fff;font-weight:700;">
                                    <?php echo $user_initials; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <h5 style="font-weight:800;margin:12px 0 4px;"><?php echo htmlspecialchars($user_name); ?></h5>
                        <p style="color:var(--cc-text-muted);font-size:0.85rem;margin:0 0 12px;">
                            <?php echo htmlspecialchars(ucfirst($current_user['role'])); ?><br>
                            <?php echo htmlspecialchars($current_user['email']); ?>
                        </p>
                        <span class="cc-badge <?php echo ($current_user['status'] ?? 'active') === 'active' ? 'cc-badge-green' : 'cc-badge-red'; ?>"
                              style="width:100%;justify-content:center;padding:8px;">
                            <i class="fas fa-circle-check"></i>
                            <?php echo ($current_user['status'] ?? 'active') === 'active' ? 'Active Account' : 'Inactive'; ?>
                        </span>

                        <!-- Upload avatar form -->
                        <form method="POST" action="profile.php" enctype="multipart/form-data" class="mt-3">
                            <input type="hidden" name="action" value="upload_avatar">
                            <label for="avatarInput" class="cc-btn cc-btn-light cc-btn-sm w-100 mb-2" style="cursor:pointer;">
                                <i class="fas fa-camera me-1"></i> Change Photo
                            </label>
                            <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp"
                                   style="display:none;" onchange="previewAvatar(this); this.form.submit();">
                        </form>

                        <?php if ($avatar_src): ?>
                        <form method="POST" action="profile.php" class="mt-1">
                            <input type="hidden" name="action" value="remove_avatar">
                            <button type="submit" class="cc-btn cc-btn-sm w-100"
                                    style="border:1px solid #fca5a5;color:#dc2626;background:transparent;"
                                    onclick="return confirm('Remove your profile picture?');">
                                <i class="fas fa-trash-can me-1"></i> Remove Photo
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <!-- Side nav links -->
                    <div style="border-top:1px solid var(--cc-border);">
                        <a href="#personal" class="d-flex align-items-center gap-3 p-3 text-decoration-none"
                           style="border-left:3px solid var(--cc-orange);background:#fafbfc;">
                            <i class="fas fa-id-card" style="color:var(--cc-orange);width:20px;text-align:center;"></i>
                            <span style="font-weight:600;font-size:0.88rem;color:var(--cc-orange);">Personal Info</span>
                        </a>
                        <a href="#security" class="d-flex align-items-center gap-3 p-3 text-decoration-none"
                           style="border-left:3px solid transparent;">
                            <i class="fas fa-shield-halved" style="color:var(--cc-text-muted);width:20px;text-align:center;"></i>
                            <span style="font-weight:600;font-size:0.88rem;color:var(--cc-text);">Security</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ── Right: Forms ───────────────────────────────────────── -->
            <div style="display:flex;flex-direction:column;gap:24px;">

                <!-- Personal Info -->
                <div class="cc-card" id="personal">
                    <div class="cc-card-body" style="padding:32px;">
                        <h4 style="font-weight:800;margin:0 0 24px;">
                            <i class="fas fa-id-card text-blue me-2"></i>Personal Information
                        </h4>
                        <form method="POST" action="profile.php">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="cc-form-label">First Name</label>
                                    <input type="text" name="first_name" class="cc-form-control"
                                           value="<?php echo htmlspecialchars($current_user['first_name']); ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="cc-form-label">Last Name</label>
                                    <input type="text" name="last_name" class="cc-form-control"
                                           value="<?php echo htmlspecialchars($current_user['last_name']); ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="cc-form-label">Email Address</label>
                                    <input type="email" class="cc-form-control"
                                           value="<?php echo htmlspecialchars($current_user['email']); ?>"
                                           readonly style="opacity:0.7;">
                                </div>
                                <div class="col-sm-6">
                                    <label class="cc-form-label">Contact Number</label>
                                    <input type="tel" name="contact_number" class="cc-form-control"
                                           value="<?php echo htmlspecialchars($current_user['contact_number'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="cc-form-label">Address</label>
                                    <textarea name="address" class="cc-form-control" rows="2"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="cc-form-label">Preferences / Notes</label>
                                    <textarea name="preferences" class="cc-form-control" rows="2"><?php echo htmlspecialchars($profile['preferences'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <hr style="border-color:#f3f4f6;margin:28px 0;">
                            <h4 style="font-weight:800;margin:0 0 20px;">Account Info</h4>
                            <div class="cc-switch-row">
                                <div>
                                    <div style="font-weight:600;">Role</div>
                                    <small style="color:var(--cc-text-muted);">Your account type.</small>
                                </div>
                                <span class="cc-badge cc-badge-blue"><?php echo ucfirst(htmlspecialchars($current_user['role'])); ?></span>
                            </div>
                            <div class="cc-switch-row">
                                <div>
                                    <div style="font-weight:600;">Account Status</div>
                                    <small style="color:var(--cc-text-muted);">Current account status.</small>
                                </div>
                                <span class="cc-badge <?php echo ($current_user['status'] ?? 'active') === 'active' ? 'cc-badge-green' : 'cc-badge-red'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($current_user['status'] ?? 'active')); ?>
                                </span>
                            </div>
                            <div class="cc-switch-row">
                                <div>
                                    <div style="font-weight:600;">Member Since</div>
                                    <small style="color:var(--cc-text-muted);">When your account was created.</small>
                                </div>
                                <span style="font-weight:600;font-size:0.88rem;">
                                    <?php echo isset($current_user['date_created']) ? date('M d, Y', strtotime($current_user['date_created'])) : '—'; ?>
                                </span>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4 pt-3">
                                <a href="profile.php" class="cc-btn cc-btn-light">Cancel</a>
                                <button type="submit" class="cc-btn cc-btn-primary">
                                    <i class="fas fa-check"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div><!-- /#personal -->

                <!-- Security / Change Password -->
                <div class="cc-card" id="security">
                    <div class="cc-card-body" style="padding:32px;">
                        <h4 style="font-weight:800;margin:0 0 8px;">
                            <i class="fas fa-shield-halved text-blue me-2"></i>Security Settings
                        </h4>
                        <p style="color:var(--cc-text-muted);font-size:0.875rem;margin-bottom:24px;">
                            Change your password. We recommend using at least 8 characters including letters and numbers.
                        </p>

                        <?php if ($pw_success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($pw_success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <?php if ($pw_error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars($pw_error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="profile.php#security">
                            <input type="hidden" name="action" value="change_password">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="cc-form-label">Current Password</label>
                                    <div style="position:relative;">
                                        <input type="password" name="current_password" id="curPw"
                                               class="cc-form-control" placeholder="Enter current password" required>
                                        <span onclick="togglePw('curPw','eyeCur')"
                                              style="position:absolute;right:14px;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--cc-text-muted);">
                                            <i id="eyeCur" class="fas fa-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="cc-form-label">New Password</label>
                                    <div style="position:relative;">
                                        <input type="password" name="new_password" id="newPw"
                                               class="cc-form-control" placeholder="Min. 8 characters" required minlength="8">
                                        <span onclick="togglePw('newPw','eyeNew')"
                                              style="position:absolute;right:14px;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--cc-text-muted);">
                                            <i id="eyeNew" class="fas fa-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="cc-form-label">Confirm New Password</label>
                                    <div style="position:relative;">
                                        <input type="password" name="confirm_password" id="confPw"
                                               class="cc-form-control" placeholder="Repeat new password" required>
                                        <span onclick="togglePw('confPw','eyeConf')"
                                              style="position:absolute;right:14px;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--cc-text-muted);">
                                            <i id="eyeConf" class="fas fa-eye-slash"></i>
                                        </span>
                                    </div>
                                    <div id="pwMatchHint" style="font-size:0.78rem;margin-top:4px;"></div>
                                </div>
                            </div>

                            <!-- Strength bar -->
                            <div style="margin-top:16px;">
                                <div style="font-size:0.78rem;color:var(--cc-text-muted);margin-bottom:4px;">Password strength</div>
                                <div style="height:6px;border-radius:99px;background:#e5e7eb;overflow:hidden;">
                                    <div id="strengthBar" style="height:100%;width:0;border-radius:99px;transition:width 0.3s,background 0.3s;"></div>
                                </div>
                                <div id="strengthLabel" style="font-size:0.75rem;margin-top:4px;color:var(--cc-text-muted);"></div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="cc-btn cc-btn-primary">
                                    <i class="fas fa-lock"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div><!-- /#security -->

            </div>
        </div><!-- /.cc-settings-grid -->
    </div>
</div>

<script>
// Live avatar preview (before form submits)
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('avatarPreview');
            const fb  = document.getElementById('avatarFallback');
            img.src   = e.target.result;
            img.style.display = 'block';
            if (fb) fb.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Show/hide password toggle
function togglePw(fieldId, iconId) {
    const f = document.getElementById(fieldId);
    const i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.classList.toggle('fa-eye-slash');
    i.classList.toggle('fa-eye');
}

// Password strength meter
document.getElementById('newPw').addEventListener('input', function () {
    const val  = this.value;
    const bar  = document.getElementById('strengthBar');
    const lbl  = document.getElementById('strengthLabel');
    let score  = 0;
    if (val.length >= 8)               score++;
    if (/[A-Z]/.test(val))            score++;
    if (/[0-9]/.test(val))            score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;

    const levels = [
        { w: '0%',   bg: 'transparent', txt: '' },
        { w: '25%',  bg: '#ef4444',     txt: 'Weak' },
        { w: '50%',  bg: '#f59e0b',     txt: 'Fair' },
        { w: '75%',  bg: '#3b82f6',     txt: 'Good' },
        { w: '100%', bg: '#22c55e',     txt: 'Strong' },
    ];
    bar.style.width      = levels[score].w;
    bar.style.background = levels[score].bg;
    lbl.textContent      = levels[score].txt;
    lbl.style.color      = levels[score].bg;
});

// Confirm password match hint
document.getElementById('confPw').addEventListener('input', function () {
    const hint = document.getElementById('pwMatchHint');
    if (this.value === '' ) { hint.textContent = ''; return; }
    if (this.value === document.getElementById('newPw').value) {
        hint.textContent = '✓ Passwords match';
        hint.style.color = '#22c55e';
    } else {
        hint.textContent = '✗ Passwords do not match';
        hint.style.color = '#ef4444';
    }
});
</script>

<?php include 'includes/app_foot.php'; ?>
