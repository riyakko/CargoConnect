<?php
require_once 'includes/auth_check.php';
$page_title = 'Rate Calculator';
$active_page = 'calculator';

// Load shipping rates from DB
$rates_data = [];
if ($conn) {
    $r = $conn->query("SELECT * FROM shipping_rates ORDER BY cargo_type, weight");
    if ($r) { while ($row = $r->fetch_assoc()) $rates_data[] = $row; }
}

// Calculate on submit
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cargo_type = $_POST['cargo_type'] ?? 'General';
    $weight = floatval($_POST['weight'] ?? 0);
    $distance = floatval($_POST['distance'] ?? 0);
    $method = $_POST['shipping_method'] ?? 'container';

    // Try to find a matching rate from DB
    $base_rate = 2.50; // fallback
    if ($conn) {
        $stmt = $conn->prepare("SELECT base_rate, calculated_cost FROM shipping_rates WHERE cargo_type = ? ORDER BY ABS(weight - ?) LIMIT 1");
        $stmt->bind_param('sd', $cargo_type, $weight);
        $stmt->execute();
        $rr = $stmt->get_result();
        if ($rr->num_rows > 0) {
            $rate_row = $rr->fetch_assoc();
            $base_rate = floatval($rate_row['base_rate']);
        }
        $stmt->close();
    }

    // Rate multiplier by method
    $method_mult = ($method === 'truck') ? 1.5 : 1.0;

    // Calculate
    $base_cost = $weight * $base_rate * $method_mult;
    $distance_surcharge = $distance * 0.05;
    $total = $base_cost + $distance_surcharge;

    $result = [
        'total' => $total,
        'base_cost' => $base_cost,
        'distance_surcharge' => $distance_surcharge,
        'base_rate' => $base_rate,
        'weight' => $weight,
        'method' => $method,
        'cargo_type' => $cargo_type,
    ];
}
?>
<?php include 'includes/app_head.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="cc-main">
    <div class="cc-topbar">
        <div style="display:flex;align-items:center;">
            <button class="cc-menu-toggle" id="menuToggle" aria-label="Open sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <span class="cc-topbar-title"><i class="fas fa-calculator text-blue me-2"></i>Rate Calculator</span>
        </div>
        <div class="cc-topbar-actions"><div class="cc-avatar"><?php echo $user_initials; ?></div></div>
    </div>

    <div class="cc-page">
        <h2 class="cc-page-title">Instant <span class="text-blue">Rate Calculator</span></h2>
        <p class="cc-page-subtitle">Get a transparent, estimated shipping cost in seconds.</p>

        <div class="cc-calc-card">
            <div class="cc-card">
                <div class="cc-card-body" style="padding: 32px;">
                    <form method="POST" action="calculator.php">

                        <!-- Shipping Method -->
                        <div class="mb-4">
                            <label class="cc-form-label">Shipping Method</label>
                            <div class="cc-mode-group">
                                <input type="radio" name="shipping_method" id="modeContainer" value="container" <?php echo (!$result || $result['method']==='container') ? 'checked' : ''; ?>>
                                <label class="cc-mode-label" for="modeContainer"><i class="fas fa-ship text-blue me-2"></i>Container</label>

                                <input type="radio" name="shipping_method" id="modeTruck" value="truck" <?php echo ($result && $result['method']==='truck') ? 'checked' : ''; ?>>
                                <label class="cc-mode-label" for="modeTruck"><i class="fas fa-truck me-2" style="color:#6b7280"></i>Truck</label>
                            </div>
                        </div>

                        <hr style="border-color:#f3f4f6; margin: 24px 0;">

                        <!-- Cargo Type -->
                        <div class="mb-3">
                            <label class="cc-form-label">Cargo Type</label>
                            <select name="cargo_type" class="cc-form-control cc-form-select">
                                <option value="General" <?php echo ($result && $result['cargo_type']==='General') ? 'selected' : ''; ?>>General</option>
                                <option value="Fragile" <?php echo ($result && $result['cargo_type']==='Fragile') ? 'selected' : ''; ?>>Fragile</option>
                                <option value="Perishable" <?php echo ($result && $result['cargo_type']==='Perishable') ? 'selected' : ''; ?>>Perishable</option>
                                <option value="Hazardous" <?php echo ($result && $result['cargo_type']==='Hazardous') ? 'selected' : ''; ?>>Hazardous</option>
                                <option value="Electronics" <?php echo ($result && $result['cargo_type']==='Electronics') ? 'selected' : ''; ?>>Electronics</option>
                            </select>
                        </div>

                        <!-- Weight & Distance -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="cc-form-label">Weight (KG)</label>
                                <input type="number" name="weight" class="cc-form-control" value="<?php echo $_POST['weight'] ?? '1000'; ?>" min="1" required>
                            </div>
                            <div class="col-6">
                                <label class="cc-form-label">Distance (KM)</label>
                                <input type="number" name="distance" class="cc-form-control" value="<?php echo $_POST['distance'] ?? '500'; ?>" min="1" required>
                            </div>
                        </div>

                        <!-- Result -->
                        <div class="cc-calc-result mb-4">
                            <?php if ($result): ?>
                            <div class="cc-calc-result-label">Estimated Total Cost</div>
                            <div class="cc-calc-result-value">
                                $ <?php echo number_format(floor($result['total']), 0, '.', ','); ?><span class="cents">.<?php echo str_pad(round(($result['total'] - floor($result['total'])) * 100), 2, '0', STR_PAD_LEFT); ?></span>
                            </div>
                            <small style="color:var(--cc-text-muted);">
                                Base: $<?php echo number_format($result['base_cost'], 2); ?> 
                                (<?php echo $result['weight']; ?>kg × $<?php echo number_format($result['base_rate'], 2); ?>/kg)
                                + Distance: $<?php echo number_format($result['distance_surcharge'], 2); ?>
                            </small>
                            <?php else: ?>
                            <div class="cc-calc-result-label">Estimated Cost</div>
                            <div class="cc-calc-result-value">$ 0<span class="cents">.00</span></div>
                            <small style="color:var(--cc-text-muted);">Fill in the form and click Calculate.</small>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="cc-btn cc-btn-primary justify-content-center" style="padding: 14px;">
                                <i class="fas fa-calculator"></i> Calculate Rate
                            </button>
                            <?php if ($result): ?>
                            <a href="book.php" class="cc-btn cc-btn-orange justify-content-center" style="padding: 14px;">
                                <i class="fas fa-arrow-right"></i> Proceed with Booking
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/app_foot.php'; ?>
