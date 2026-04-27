<?php
require_once 'includes/auth_check.php';
$page_title = 'Settings';
$active_page = 'settings';

$success_msg = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn) {
    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, contact_number=? WHERE user_id=?");
    if ($stmt) {
        $stmt->bind_param('sssi', $_POST['first_name'], $_POST['last_name'], $_POST['contact_number'], $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Upsert user_profiles
    $pstmt = $conn->prepare("INSERT INTO user_profiles (user_id, address, preferences) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE address=VALUES(address), preferences=VALUES(preferences)");
    if ($pstmt) {
        $pstmt->bind_param('iss', $user_id, $_POST['address'], $_POST['preferences']);
        $pstmt->execute();
        $pstmt->close();
    }

    $success_msg = "Settings saved successfully!";

    // Refresh user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r->num_rows > 0) $current_user = $r->fetch_assoc();
    $stmt->close();
    $user_name = $current_user['first_name'] . ' ' . $current_user['last_name'];
    $user_initials = strtoupper(substr($current_user['first_name'], 0, 1) . substr($current_user['last_name'], 0, 1));
}

// Load profile data
$profile = ['address'=>'','preferences'=>'','profile_picture'=>''];
if ($conn) {
    $r = $conn->prepare("SELECT * FROM user_profiles WHERE user_id=?");
    $r->bind_param('i', $user_id);
    $r->execute();
    $pr = $r->get_result();
    if ($pr->num_rows > 0) $profile = $pr->fetch_assoc();
    $r->close();
}
?>
<?php include 'includes/app_head.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="cc-main">
    <div class="cc-topbar">
        <span class="cc-topbar-title"><i class="fas fa-gear text-blue me-2"></i>Settings</span>
        <div class="cc-topbar-actions"><div class="cc-avatar"><?php echo $user_initials; ?></div></div>
    </div>

    <div class="cc-page">
        <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <h2 class="cc-page-title">User Settings</h2>
        <p class="cc-page-subtitle">Manage your profile and preferences.</p>

        <div class="cc-settings-grid">
            <!-- Profile Card -->
            <div>
                <div class="cc-card cc-profile-card">
                    <div class="cc-profile-banner"></div>
                    <div class="cc-card-body" style="padding-top:0;">
                        <div class="cc-profile-avatar"><i class="fas fa-user-tie"></i></div>
                        <h5 style="font-weight:800;margin:0 0 4px;"><?php echo htmlspecialchars($user_name); ?></h5>
                        <p style="color:var(--cc-text-muted);font-size:0.85rem;margin:0 0 16px;">
                            <?php echo htmlspecialchars(ucfirst($current_user['role'])); ?><br>
                            <?php echo htmlspecialchars($current_user['email']); ?>
                        </p>
                        <span class="cc-badge <?php echo ($current_user['status'] ?? 'active') === 'active' ? 'cc-badge-green' : 'cc-badge-red'; ?>" style="width:100%;justify-content:center;padding:8px;">
                            <i class="fas fa-circle-check"></i> <?php echo ($current_user['status'] ?? 'active') === 'active' ? 'Active Account' : 'Inactive'; ?>
                        </span>
                    </div>
                    <div style="border-top:1px solid var(--cc-border);padding:0;">
                        <a href="#personal" class="d-flex align-items-center gap-3 p-3 text-decoration-none" style="border-left:3px solid var(--cc-orange);background:#fafbfc;">
                            <i class="fas fa-id-card" style="color:var(--cc-orange);width:20px;text-align:center;"></i>
                            <span style="font-weight:600;font-size:0.88rem;color:var(--cc-orange);">Personal Info</span>
                        </a>
                        <a href="#" class="d-flex align-items-center gap-3 p-3 text-decoration-none" style="border-left:3px solid transparent;">
                            <i class="fas fa-shield-halved" style="color:var(--cc-text-muted);width:20px;text-align:center;"></i>
                            <span style="font-weight:600;font-size:0.88rem;color:var(--cc-text);">Security</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div>
                <div class="cc-card">
                    <div class="cc-card-body" style="padding:32px;">
                        <h4 style="font-weight:800;margin:0 0 24px;">Personal Information</h4>
                        <form method="POST" action="profile.php">
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="cc-form-label">First Name</label>
                                    <input type="text" name="first_name" class="cc-form-control" value="<?php echo htmlspecialchars($current_user['first_name']); ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="cc-form-label">Last Name</label>
                                    <input type="text" name="last_name" class="cc-form-control" value="<?php echo htmlspecialchars($current_user['last_name']); ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label class="cc-form-label">Email Address</label>
                                    <input type="email" class="cc-form-control" value="<?php echo htmlspecialchars($current_user['email']); ?>" readonly style="opacity:0.7;">
                                </div>
                                <div class="col-sm-6">
                                    <label class="cc-form-label">Contact Number</label>
                                    <input type="tel" name="contact_number" class="cc-form-control" value="<?php echo htmlspecialchars($current_user['contact_number'] ?? ''); ?>">
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
                                <span style="font-weight:600;font-size:0.88rem;"><?php echo isset($current_user['date_created']) ? date('M d, Y', strtotime($current_user['date_created'])) : '—'; ?></span>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4 pt-3">
                                <a href="profile.php" class="cc-btn cc-btn-light">Cancel</a>
                                <button type="submit" class="cc-btn cc-btn-primary"><i class="fas fa-check"></i> Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/app_foot.php'; ?>
