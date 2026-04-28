<?php
require_once 'includes/auth_check.php';
$page_title = 'Bookings';
$active_page = 'bookings';

$success_msg = '';
$error_msg = '';

// Rates per km by cargo size
$rates = [
    'Small'       => 2,
    'Medium'      => 4,
    'Large'       => 6,
    'Extra Large' => 8,
];

// CREATE booking + shipment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create' && $conn) {
        $tracking_id    = 'CRG-' . strtoupper(substr(uniqid(), -5));
        $origin         = trim($_POST['origin']);
        $destination    = trim($_POST['destination']);
        $cargo_type     = $_POST['cargo_type'];
        $cargo_size     = $_POST['cargo_size'];
        $shipping_method = $_POST['shipping_method'];
        $estimated_cost = floatval($_POST['estimated_cost'] ?? 0);

        $stmt = $conn->prepare(
            "INSERT INTO shipments (user_id, tracking_id, origin, destination, cargo_type, cargo_size, shipping_method, estimated_cost, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')"
        );
        $stmt->bind_param('issssssd', $user_id, $tracking_id, $origin, $destination, $cargo_type, $cargo_size, $shipping_method, $estimated_cost);

        if ($stmt->execute()) {
            $shipment_id = $conn->insert_id;
            $stmt->close();

            $bstmt = $conn->prepare(
                "INSERT INTO bookings (user_id, shipment_id, origin, destination, cargo_type, cargo_size, shipping_method, estimated_cost, booking_status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')"
            );
            $bstmt->bind_param('iisssssd', $user_id, $shipment_id, $origin, $destination, $cargo_type, $cargo_size, $shipping_method, $estimated_cost);
            $bstmt->execute();
            $bstmt->close();

            $tstmt = $conn->prepare(
                "INSERT INTO tracking_updates (shipment_id, location, status, timestamp) VALUES (?, ?, 'Booking Confirmed', NOW())"
            );
            $tstmt->bind_param('is', $shipment_id, $origin);
            $tstmt->execute();
            $tstmt->close();

            $success_msg = "Booking created! Tracking ID: <strong>$tracking_id</strong>";
        } else {
            $error_msg = "Error creating booking: " . $stmt->error;
            $stmt->close();
        }
    }

    if ($_POST['action'] === 'delete' && $conn && isset($_POST['booking_id'])) {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
        $stmt->bind_param('i', $_POST['booking_id']);
        $stmt->execute();
        $stmt->close();
        $success_msg = "Booking deleted.";
    }
}

// Fetch bookings
$bookings = [];
if ($conn) {
    $r = $conn->query(
        "SELECT b.*, s.tracking_id, s.status as shipment_status
         FROM bookings b
         LEFT JOIN shipments s ON b.shipment_id = s.shipment_id
         ORDER BY b.date_requested DESC LIMIT 20"
    );
    if ($r) { while ($row = $r->fetch_assoc()) $bookings[] = $row; }
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
            <span class="cc-topbar-title"><i class="fas fa-calendar-check text-blue me-2"></i>Bookings</span>
        </div>
        <div class="cc-topbar-actions"><div class="cc-avatar"><?php echo $user_initials; ?></div></div>
    </div>

    <div class="cc-page">
        <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?php echo $success_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <h2 class="cc-page-title">Book Your Cargo</h2>
        <p class="cc-page-subtitle">Fill in the details below to create a new shipment booking.</p>

        <!-- Booking Form -->
        <div class="cc-card mb-4">
            <div class="cc-card-body cc-book-card-body">
                <form method="POST" action="book.php" id="bookingForm" autocomplete="off">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" id="origin_lat" name="origin_lat" value="">
                    <input type="hidden" id="origin_lng" name="origin_lng" value="">
                    <input type="hidden" id="dest_lat" name="dest_lat" value="">
                    <input type="hidden" id="dest_lng" name="dest_lng" value="">

                    <div class="row g-4 cc-book-form-row">
                        <!-- Route -->
                        <div class="col-12 col-lg-6">
                            <h6 class="fw-bold mb-3" style="color:var(--cc-text-muted);text-transform:uppercase;font-size:0.72rem;letter-spacing:0.06em;">📍 Route Details</h6>
                            <div class="cc-form-group mb-3 position-relative">
                                <label class="cc-form-label">Origin</label>
                                <input type="text" id="origin_input" name="origin" class="cc-form-control" placeholder="e.g. Manila, Philippines" required oninput="suggest(this, 'origin_suggestions')">
                                <ul id="origin_suggestions" class="cc-suggest-list"></ul>
                            </div>
                            <div class="cc-form-group position-relative">
                                <label class="cc-form-label">Destination</label>
                                <input type="text" id="dest_input" name="destination" class="cc-form-control" placeholder="e.g. Los Angeles, USA" required oninput="suggest(this, 'dest_suggestions')">
                                <ul id="dest_suggestions" class="cc-suggest-list"></ul>
                            </div>
                        </div>

                        <!-- Cargo -->
                        <div class="col-12 col-lg-6">
                            <h6 class="fw-bold mb-3" style="color:var(--cc-text-muted);text-transform:uppercase;font-size:0.72rem;letter-spacing:0.06em;">📦 Cargo Details</h6>
                            <div class="cc-form-group mb-3">
                                <label class="cc-form-label">Cargo Type</label>
                                <select name="cargo_type" class="cc-form-control cc-form-select" required>
                                    <option value="General">General</option>
                                    <option value="Fragile">Fragile</option>
                                    <option value="Perishable">Perishable</option>
                                    <option value="Hazardous">Hazardous</option>
                                    <option value="Oversized">Oversized</option>
                                    <option value="Electronics">Electronics</option>
                                </select>
                            </div>
                            <div class="cc-form-group">
                                <label class="cc-form-label">Cargo Size <small style="color:var(--cc-text-muted);">(affects rate)</small></label>
                                <div class="cc-size-group">
                                    <label class="cc-size-option">
                                        <input type="radio" name="cargo_size" value="Small" checked onchange="recalculate()">
                                        <span><strong>Small</strong><small>$2/km</small></span>
                                    </label>
                                    <label class="cc-size-option">
                                        <input type="radio" name="cargo_size" value="Medium" onchange="recalculate()">
                                        <span><strong>Medium</strong><small>$4/km</small></span>
                                    </label>
                                    <label class="cc-size-option">
                                        <input type="radio" name="cargo_size" value="Large" onchange="recalculate()">
                                        <span><strong>Large</strong><small>$6/km</small></span>
                                    </label>
                                    <label class="cc-size-option">
                                        <input type="radio" name="cargo_size" value="Extra Large" onchange="recalculate()">
                                        <span><strong>XL</strong><small>$8/km</small></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="col-12 col-lg-6">
                            <h6 class="fw-bold mb-3" style="color:var(--cc-text-muted);text-transform:uppercase;font-size:0.72rem;letter-spacing:0.06em;">🚢 Shipping Method</h6>
                            <div class="cc-mode-group">
                                <input type="radio" name="shipping_method" id="modeContainer" value="container" checked>
                                <label class="cc-mode-label" for="modeContainer"><i class="fas fa-ship text-blue me-2"></i>Container</label>
                                <input type="radio" name="shipping_method" id="modeTruck" value="truck">
                                <label class="cc-mode-label" for="modeTruck"><i class="fas fa-truck me-2" style="color:#6b7280"></i>Truck</label>
                            </div>
                        </div>

                        <!-- Live Cost Estimate -->
                        <div class="col-12 col-lg-6">
                            <h6 class="fw-bold mb-3" style="color:var(--cc-text-muted);text-transform:uppercase;font-size:0.72rem;letter-spacing:0.06em;">💵 Cost Estimate</h6>
                            <div class="cc-calc-result" style="padding:20px 24px;min-height:90px;">
                                <div class="cc-calc-result-label" style="font-size:0.78rem;">Estimated Total</div>
                                <div class="cc-calc-result-value" id="costDisplay" style="font-size:2rem;">
                                    $ <span id="costWhole">—</span>
                                </div>
                                <small id="costBreakdown" style="color:var(--cc-text-muted);font-size:0.78rem;">Select origin &amp; destination to calculate.</small>
                            </div>
                            <!-- Hidden field submitted with form -->
                            <input type="hidden" name="estimated_cost" id="estimated_cost_field" value="0">
                        </div>
                    </div>

                    <div class="text-center mt-4 cc-book-submit-wrap">
                        <button type="submit" class="cc-btn cc-btn-primary cc-book-submit">
                            <i class="fas fa-check"></i> CONFIRM BOOKING
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="cc-card">
            <div class="cc-card-header">
                <h5><i class="fas fa-list text-orange me-2"></i>Your Bookings</h5>
            </div>
            <div class="cc-card-body p-0">
                <div class="cc-table-wrapper">
                <table class="cc-table">
                    <thead>
                        <tr>
                            <th>Tracking</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Method</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($bookings)): ?>
                            <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td class="fw-semibold">
                                    <?php if ($b['tracking_id']): ?>
                                        <a href="track.php?id=<?php echo urlencode($b['tracking_id']); ?>" class="text-decoration-none"><?php echo htmlspecialchars($b['tracking_id']); ?></a>
                                    <?php else: ?>—<?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($b['origin']); ?></td>
                                <td><?php echo htmlspecialchars($b['destination']); ?></td>
                                <td><?php echo htmlspecialchars($b['cargo_type']); ?></td>
                                <td><?php echo htmlspecialchars($b['cargo_size']); ?></td>
                                <td>
                                    <?php if ($b['shipping_method'] === 'container'): ?>
                                        <i class="fa-solid fa-ship text-muted me-1"></i>Container
                                    <?php else: ?>
                                        <i class="fa-solid fa-truck text-muted me-1"></i>Truck
                                    <?php endif; ?>
                                </td>
                                <td>$<?php echo number_format($b['estimated_cost'] ?? 0, 2); ?></td>
                                <td>
                                    <?php
                                    $st = $b['booking_status'];
                                    $cls = 'cc-badge-gray';
                                    if ($st === 'Approved') $cls = 'cc-badge-green';
                                    elseif ($st === 'Pending') $cls = 'cc-badge-orange';
                                    elseif ($st === 'Rejected') $cls = 'cc-badge-red';
                                    ?>
                                    <span class="cc-badge <?php echo $cls; ?>"><?php echo htmlspecialchars($st); ?></span>
                                </td>
                                <td class="text-end">
                                    <?php if ($b['tracking_id']): ?>
                                    <a href="track.php?id=<?php echo urlencode($b['tracking_id']); ?>" class="cc-btn cc-btn-light cc-btn-sm me-1" title="Track"><i class="fas fa-eye"></i></a>
                                    <?php endif; ?>
                                    <form method="POST" action="book.php" style="display:inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="booking_id" value="<?php echo $b['booking_id']; ?>">
                                        <button type="submit" class="cc-btn cc-btn-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;" onclick="return confirm('Delete this booking?')" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="9" class="text-center" style="padding:40px;color:var(--cc-text-muted);">
                                <i class="fas fa-inbox me-2" style="font-size:1.5rem;"></i><br>No bookings yet.
                            </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div><!-- /.cc-table-wrapper -->
            </div>
        </div>
    </div>
</div>

<style>
/* Autocomplete */
.cc-suggest-list {
    position: absolute;
    top: calc(100% + 2px);
    left: 0; right: 0;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.10);
    list-style: none;
    margin: 0; padding: 4px 0;
    z-index: 999;
    max-height: 220px;
    overflow-y: auto;
    display: none;
}
.cc-suggest-list.open { display: block; }
.cc-suggest-list li {
    padding: 10px 16px;
    font-size: 0.85rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #374151;
    transition: background 0.15s;
}
.cc-suggest-list li:hover { background: #f0f9ff; color: #2563eb; }
.cc-suggest-list li i { color: #9ca3af; font-size: 0.8rem; flex-shrink: 0; }

/* Cargo size radio buttons */
.cc-book-card-body {
    padding: clamp(1rem, 2.4vw, 2rem);
}
.cc-book-form-row {
    --bs-gutter-x: clamp(0.9rem, 2vw, 1.5rem);
    --bs-gutter-y: clamp(0.9rem, 2vw, 1.5rem);
}
.cc-book-form-row > [class*="col-"] {
    min-width: 0;
}
.cc-form-group,
.cc-form-group .cc-form-control,
.cc-form-group .cc-form-select,
.cc-suggest-list,
.cc-calc-result,
.cc-mode-group,
.cc-size-group,
.cc-book-submit,
.cc-book-submit-wrap {
    width: 100%;
    max-width: 100%;
}
.cc-form-group .cc-form-control,
.cc-form-group .cc-form-select,
.cc-calc-result,
.cc-size-option span,
.cc-mode-label,
.cc-book-submit {
    min-width: 0;
}
.cc-size-group {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 8px;
}
.cc-size-option { display: block; cursor: pointer; }
.cc-size-option input { display: none; }
.cc-size-option span {
    display: flex; flex-direction: column; align-items: center;
    padding: 10px 6px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    text-align: center;
    font-size: 0.78rem;
    transition: all 0.18s;
    background: #fff;
    height: 100%;
    box-sizing: border-box;
}
.cc-size-option span strong { font-size: 0.85rem; color: #111827; }
.cc-size-option span small { color: #9ca3af; font-size: 0.7rem; margin-top: 2px; }
.cc-size-option input:checked + span {
    border-color: var(--cc-orange);
    background: #fff7ed;
}
.cc-size-option input:checked + span strong { color: var(--cc-orange); }

/* Submit button — desktop */
.cc-book-submit {
    min-width: min(260px, 100%);
    padding: 14px 28px;
    font-size: 0.95rem;
    justify-content: center;
}

@media (max-width: 991.98px) {
    .cc-size-group {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* ── Mobile overrides ─────────────────────────────────────── */
@media (max-width: 768px) {
    .cc-book-card-body {
        padding: 18px;
    }

    .cc-book-form-row {
        --bs-gutter-x: 0;
        --bs-gutter-y: 1rem;
    }

    /* Cargo size: 2×2 grid */
    .cc-size-group { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }

    /* Shipping method: stack buttons vertically */
    .cc-mode-group {
        flex-direction: column;
        border-radius: 10px;
    }
    .cc-mode-label {
        border-right: none;
        border-bottom: 1px solid var(--cc-border);
        border-radius: 0;
        padding: 13px 16px;
    }
    .cc-mode-label:last-of-type { border-bottom: none; }

    /* Cost estimate — prevent overflow */
    .cc-calc-result {
        padding: 16px !important;
        word-break: break-word;
    }
    .cc-calc-result-value { font-size: 1.6rem !important; }
    #costBreakdown { font-size: 0.72rem; word-break: break-word; }

    /* Submit button — full width on mobile */
    .cc-book-submit {
        min-width: unset !important;
        width: 100%;
        padding: 14px;
        font-size: 0.9rem;
    }

    /* Autocomplete dropdown — keep within viewport */
    .cc-suggest-list { max-height: 160px; }
}

@media (max-width: 480px) {
    .cc-book-card-body {
        padding: 14px;
    }
    .cc-size-group { grid-template-columns: 1fr; gap: 6px; }
    .cc-size-option span { padding: 10px 8px; }
    .cc-size-option span strong { font-size: 0.78rem; }
    .cc-size-option span small { font-size: 0.65rem; }
    .cc-calc-result-value {
        font-size: 1.35rem !important;
    }
}
</style>

<script>
// ── Rates by method ───────────────────────────────────────────
const RATES = {
    container: { 'Small': 2, 'Medium': 4, 'Large': 6, 'Extra Large': 8 },
    truck:     { 'Small': 1, 'Medium': 2, 'Large': 3, 'Extra Large': 4 },
};

// ── Stored coordinates ────────────────────────────────────────
let originCoords = null;
let destCoords   = null;

// ── Autocomplete ──────────────────────────────────────────────
let debounceTimers = {};

function suggest(input, listId) {
    const query = input.value.trim();
    const list  = document.getElementById(listId);

    // Reset coords when user retypes
    if (listId === 'origin_suggestions') {
        originCoords = null;
        document.getElementById('origin_lat').value = '';
        document.getElementById('origin_lng').value = '';
    } else {
        destCoords = null;
        document.getElementById('dest_lat').value = '';
        document.getElementById('dest_lng').value = '';
    }
    recalculate();

    if (query.length < 2) { list.classList.remove('open'); return; }

    clearTimeout(debounceTimers[listId]);
    debounceTimers[listId] = setTimeout(async () => {
        try {
            const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=6&addressdetails=1`;
            const res = await fetch(url, { headers: { 'Accept-Language': 'en' } });
            const data = await res.json();

            list.innerHTML = '';
            if (!data.length) { list.classList.remove('open'); return; }

            data.forEach(place => {
                const li = document.createElement('li');
                const name = place.display_name;
                li.innerHTML = `<i class="fas fa-location-dot"></i><span>${name}</span>`;
                li.addEventListener('click', () => {
                    input.value = name;
                    list.classList.remove('open');
                    if (listId === 'origin_suggestions') {
                        originCoords = { lat: parseFloat(place.lat), lng: parseFloat(place.lon) };
                        document.getElementById('origin_lat').value = place.lat;
                        document.getElementById('origin_lng').value = place.lon;
                    } else {
                        destCoords = { lat: parseFloat(place.lat), lng: parseFloat(place.lon) };
                        document.getElementById('dest_lat').value = place.lat;
                        document.getElementById('dest_lng').value = place.lon;
                    }
                    recalculate();
                });
                list.appendChild(li);
            });
            list.classList.add('open');
        } catch(e) {
            console.error('Nominatim error', e);
        }
    }, 350);
}

// Close suggestions when clicking outside
document.addEventListener('click', (e) => {
    document.querySelectorAll('.cc-suggest-list').forEach(l => {
        if (!l.previousElementSibling?.contains(e.target)) l.classList.remove('open');
    });
});

// ── Distance (Haversine) ──────────────────────────────────────
function haversineKm(lat1, lng1, lat2, lng2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2)**2 +
              Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLng/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}

// ── Recalculate cost ──────────────────────────────────────────
function recalculate() {
    const sizeEl   = document.querySelector('input[name="cargo_size"]:checked');
    const methodEl = document.querySelector('input[name="shipping_method"]:checked');
    const size     = sizeEl   ? sizeEl.value   : 'Small';
    const method   = methodEl ? methodEl.value : 'container';
    const rate     = RATES[method][size] ?? 2;

    const costDisplay   = document.getElementById('costWhole');
    const costBreakdown = document.getElementById('costBreakdown');
    const costField     = document.getElementById('estimated_cost_field');

    // Update size labels to reflect current method's rates
    updateSizeLabels(method);

    if (!originCoords || !destCoords) {
        costDisplay.textContent = '—';
        costBreakdown.textContent = 'Select origin & destination to calculate.';
        costField.value = '0';
        return;
    }

    const km   = haversineKm(originCoords.lat, originCoords.lng, destCoords.lat, destCoords.lng);
    const cost = km * rate;

    costDisplay.textContent = '$' + cost.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    costBreakdown.innerHTML = `${Math.round(km).toLocaleString()} km × $${rate}/km (${method}) = <strong>$${cost.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</strong>`;
    costField.value = cost.toFixed(2);
}

function updateSizeLabels(method) {
    const m = RATES[method];
    const sizes = ['Small','Medium','Large','Extra Large'];
    document.querySelectorAll('.cc-size-option').forEach((opt, i) => {
        const small = opt.querySelector('small');
        if (small && sizes[i]) small.textContent = `$${m[sizes[i]]}/km`;
    });
}

// Hook shipping method change
document.querySelectorAll('input[name="shipping_method"]').forEach(r => r.addEventListener('change', recalculate));
// Init labels on load
document.addEventListener('DOMContentLoaded', () => updateSizeLabels('container'));
</script>

<?php include 'includes/app_foot.php'; ?>
