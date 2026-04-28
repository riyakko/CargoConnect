<?php
require_once 'includes/auth_check.php';
$page_title = 'Tracking';
$active_page = 'tracking';

$tracking_id = $_GET['id'] ?? '';
$shipment = null;
$history  = [];

if ($tracking_id && $conn) {
    $stmt = $conn->prepare("SELECT * FROM shipments WHERE tracking_id = ?");
    $stmt->bind_param('s', $tracking_id);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r->num_rows > 0) {
        $shipment = $r->fetch_assoc();
        $hstmt = $conn->prepare("SELECT * FROM tracking_updates WHERE shipment_id = ? ORDER BY timestamp DESC");
        $hstmt->bind_param('i', $shipment['shipment_id']);
        $hstmt->execute();
        $hr = $hstmt->get_result();
        while ($row = $hr->fetch_assoc()) $history[] = $row;
        $hstmt->close();
    }
    $stmt->close();
}
?>
<?php include 'includes/app_head.php'; ?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#trackMap { width:100%; height:420px; border-radius:0 0 12px 12px; z-index:1; }
.leaflet-popup-content-wrapper { border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
.leaflet-popup-content { font-family: 'Inter', sans-serif; font-size: 0.82rem; }
.map-loading { display:flex;align-items:center;justify-content:center;height:420px;background:#f1f5f9;color:#9ca3af;font-size:0.9rem;border-radius:0 0 12px 12px; }
</style>

<?php include 'includes/sidebar.php'; ?>

<div class="cc-main">
    <div class="cc-topbar">
        <div style="display:flex;align-items:center;">
            <button class="cc-menu-toggle" id="menuToggle" aria-label="Open sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <span class="cc-topbar-title"><i class="fas fa-location-dot text-blue me-2"></i>Tracking</span>
        </div>
        <div class="cc-topbar-actions"><div class="cc-avatar"><?php echo $user_initials; ?></div></div>
    </div>

    <div class="cc-page">
        <h2 class="cc-page-title">Track <span class="text-orange">Shipment</span></h2>
        <p class="cc-page-subtitle">Enter your tracking ID to see real-time status.</p>

        <!-- Search -->
        <div class="cc-track-search">
            <form method="GET" action="track.php">
                <div class="cc-track-input-group">
                    <input type="text" name="id" placeholder="Enter tracking ID e.g. CRG-A1B2C" value="<?php echo htmlspecialchars($tracking_id); ?>">
                    <button type="submit" class="cc-btn cc-btn-orange" style="border-radius:0"><i class="fas fa-search me-2"></i>Track</button>
                </div>
            </form>
            <?php if ($shipment): ?>
            <p class="mt-3 mb-0" style="font-size:0.85rem;color:var(--cc-text-muted);">
                <i class="fas fa-circle-check text-success me-1"></i>Found: <strong><?php echo htmlspecialchars($tracking_id); ?></strong>
            </p>
            <?php elseif ($tracking_id): ?>
            <p class="mt-3 mb-0" style="font-size:0.85rem;color:#dc2626;">
                <i class="fas fa-circle-xmark me-1"></i>No shipment found for <strong><?php echo htmlspecialchars($tracking_id); ?></strong>
            </p>
            <?php endif; ?>
        </div>

        <?php if ($shipment): ?>
        <div class="row g-4">
            <!-- Map -->
            <div class="col-lg-7">
                <div class="cc-card" style="height:100%;overflow:hidden;">
                    <div class="cc-card-header">
                        <h5><i class="fas fa-map-location-dot text-blue me-2"></i>Live Location</h5>
                        <?php
                        $sbadge = 'cc-badge-gray';
                        if ($shipment['status'] === 'In Transit') $sbadge = 'cc-badge-blue';
                        elseif (in_array($shipment['status'], ['Arrived','Completed'])) $sbadge = 'cc-badge-green';
                        elseif ($shipment['status'] === 'Pending') $sbadge = 'cc-badge-orange';
                        ?>
                        <span class="cc-badge <?php echo $sbadge; ?>"><?php echo htmlspecialchars($shipment['status']); ?></span>
                    </div>
                    <div id="mapLoadingMsg" class="map-loading">
                        <span><i class="fas fa-circle-notch fa-spin me-2"></i>Loading map...</span>
                    </div>
                    <div id="trackMap" style="display:none;"></div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="col-lg-5">
                <div class="cc-card" style="height:100%;">
                    <div class="cc-card-header">
                        <h5><i class="fas fa-list-ul text-orange me-2"></i>Status History</h5>
                    </div>
                    <div class="cc-card-body" style="max-height:500px;overflow-y:auto;">
                        <!-- Details -->
                        <div class="mb-3 p-3" style="background:#f9fafb;border-radius:8px;font-size:0.88rem;">
                            <div style="font-size:0.72rem;color:var(--cc-text-muted);font-weight:700;text-transform:uppercase;margin-bottom:8px;">Shipment Details</div>
                            <div><strong>Tracking:</strong> <?php echo htmlspecialchars($shipment['tracking_id']); ?></div>
                            <div><strong>From:</strong> <?php echo htmlspecialchars($shipment['origin']); ?></div>
                            <div><strong>To:</strong> <?php echo htmlspecialchars($shipment['destination']); ?></div>
                            <div><strong>Cargo:</strong> <?php echo htmlspecialchars($shipment['cargo_type']); ?> — <?php echo htmlspecialchars($shipment['cargo_size']); ?></div>
                            <div><strong>Method:</strong> <?php echo ucfirst(htmlspecialchars($shipment['shipping_method'])); ?></div>
                            <?php if ($shipment['estimated_cost']): ?>
                            <div><strong>Est. Cost:</strong> $<?php echo number_format($shipment['estimated_cost'], 2); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Timeline -->
                        <div class="cc-timeline">
                            <?php if (!empty($history)):
                                $icons = [
                                    'Booking Confirmed' => ['fa-file-signature','#6b7280'],
                                    'Customs Cleared'   => ['fa-boxes-packing','#16a34a'],
                                    'Departed Port'     => ['fa-ship','#2563eb'],
                                    'In Transit'        => ['fa-truck-fast','#2563eb'],
                                    'Arrived'           => ['fa-flag-checkered','#16a34a'],
                                    'Completed'         => ['fa-circle-check','#16a34a'],
                                    'Pending'           => ['fa-clock','#f59e0b'],
                                ];
                                foreach ($history as $h):
                                    $icon_info = $icons[$h['status']] ?? ['fa-circle-dot','#6b7280'];
                            ?>
                            <div class="cc-timeline-item">
                                <div class="cc-timeline-icon" style="background:<?php echo $icon_info[1]; ?>"><i class="fas <?php echo $icon_info[0]; ?>"></i></div>
                                <div class="cc-timeline-content">
                                    <h6><?php echo htmlspecialchars($h['status']); ?></h6>
                                    <p><?php echo htmlspecialchars($h['location']); ?></p>
                                    <span class="cc-badge cc-badge-gray"><?php echo date('M d, Y — h:i A', strtotime($h['timestamp'])); ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <p style="color:var(--cc-text-muted);text-align:center;padding:20px 0;">No tracking updates yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pass PHP data to JS -->
        <script>
        const SHIP_ORIGIN = <?php echo json_encode($shipment['origin']); ?>;
        const SHIP_DEST   = <?php echo json_encode($shipment['destination']); ?>;
        const SHIP_STATUS = <?php echo json_encode($shipment['status']); ?>;
        const SHIP_TRACKING = <?php echo json_encode($shipment['tracking_id']); ?>;
        const HISTORY_LOCS  = <?php echo json_encode(array_map(fn($h) => $h['location'], $history)); ?>;
        </script>

        <?php endif; ?>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(async function initMap() {
    if (typeof SHIP_ORIGIN === 'undefined') return;

    const mapEl    = document.getElementById('trackMap');
    const loadMsg  = document.getElementById('mapLoadingMsg');

    // Geocode using Nominatim
    async function geocode(place) {
        const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(place)}&format=json&limit=1`;
        try {
            const res  = await fetch(url, { headers: { 'Accept-Language': 'en' } });
            const data = await res.json();
            if (data.length > 0) return { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon), name: data[0].display_name };
        } catch(e) { console.error('Geocode error', e); }
        return null;
    }

    const [origin, destination] = await Promise.all([geocode(SHIP_ORIGIN), geocode(SHIP_DEST)]);

    if (!origin && !destination) {
        loadMsg.innerHTML = '<span><i class="fas fa-map-location-dot me-2"></i>Could not locate this shipment on the map.</span>';
        return;
    }

    loadMsg.style.display = 'none';
    mapEl.style.display   = 'block';

    // Center between the two points
    const centerLat = origin && destination ? (origin.lat + destination.lat) / 2 : (origin || destination).lat;
    const centerLng = origin && destination ? (origin.lng + destination.lng) / 2 : (origin || destination).lng;

    const map = L.map('trackMap', { zoomControl: true, scrollWheelZoom: false }).setView([centerLat, centerLng], 4);

    // Tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 18,
    }).addTo(map);

    // Custom icons
    function makeIcon(color, iconClass) {
        return L.divIcon({
            className: '',
            html: `<div style="
                width:36px;height:36px;border-radius:50% 50% 50% 0;
                background:${color};transform:rotate(-45deg);
                border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.3);
                display:flex;align-items:center;justify-content:center;">
                <i class='fas ${iconClass}' style='transform:rotate(45deg);color:#fff;font-size:14px;'></i>
            </div>`,
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -38],
        });
    }

    // Add origin marker (blue)
    if (origin) {
        L.marker([origin.lat, origin.lng], { icon: makeIcon('#2563eb', 'fa-box') })
            .addTo(map)
            .bindPopup(`<strong>📦 Origin</strong><br>${SHIP_ORIGIN}`)
            .openPopup();
    }

    // Add destination marker (green)
    if (destination) {
        L.marker([destination.lat, destination.lng], { icon: makeIcon('#16a34a', 'fa-flag-checkered') })
            .addTo(map)
            .bindPopup(`<strong>🏁 Destination</strong><br>${SHIP_DEST}`);
    }

    // Draw a dashed arc line between origin and destination
    if (origin && destination) {
        const polyline = L.polyline(
            [[origin.lat, origin.lng], [destination.lat, destination.lng]],
            { color: '#2563eb', weight: 2.5, opacity: 0.6, dashArray: '8, 8' }
        ).addTo(map);

        // Fit the map to show both markers
        map.fitBounds(polyline.getBounds(), { padding: [40, 40] });
    }

    // "In Transit" pulse marker at midpoint
    if (SHIP_STATUS === 'In Transit' && origin && destination) {
        const midLat = (origin.lat + destination.lat) / 2;
        const midLng = (origin.lng + destination.lng) / 2;
        const pulseIcon = L.divIcon({
            className: '',
            html: `<div style="position:relative;width:20px;height:20px;">
                <div style="position:absolute;inset:0;border-radius:50%;background:#f97316;animation:pulse 1.5s infinite;opacity:0.6;"></div>
                <div style="position:absolute;inset:4px;border-radius:50%;background:#f97316;border:2px solid #fff;"></div>
            </div>
            <style>@keyframes pulse{0%{transform:scale(1);opacity:0.6}70%{transform:scale(2.2);opacity:0}100%{transform:scale(1);opacity:0}}</style>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10],
            popupAnchor: [0, -14],
        });
        L.marker([midLat, midLng], { icon: pulseIcon })
            .addTo(map)
            .bindPopup(`<strong>🚢 In Transit</strong><br>Tracking: ${SHIP_TRACKING}`);
    }
})();
</script>

<?php include 'includes/app_foot.php'; ?>
