<?php
require_once 'includes/auth_check.php';
$page_title = 'Manifests';
$active_page = 'manifests';

// Fetch recent shipments for autofill suggestions
$recent_shipments = [];
if ($conn) {
    $r = $conn->query("SELECT * FROM shipments ORDER BY date_created DESC LIMIT 20");
    if ($r) { while ($row = $r->fetch_assoc()) $recent_shipments[] = $row; }
}
?>
<?php include 'includes/app_head.php'; ?>

<style>
/* Manifest two-panel layout */
.manifest-layout {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 20px;
    align-items: start;
}
@media (max-width: 900px) {
    .manifest-layout { grid-template-columns: 1fr; }
}

/* Left panel */
.manifest-form-panel {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.mf-section-title {
    font-size: 1rem;
    font-weight: 800;
    color: #111827;
    margin: 0 0 14px;
    padding-bottom: 8px;
    border-bottom: 2px solid #f3f4f6;
}
.mf-section-title + .mf-section-title { margin-top: 20px; }
.mf-form-group { margin-bottom: 12px; }
.mf-form-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 4px;
}
.mf-form-control {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.85rem;
    font-family: 'Inter', sans-serif;
    color: #111827;
    background: #fff;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    box-sizing: border-box;
}
.mf-form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.10);
}
.mf-form-control::placeholder { color: #9ca3af; }
select.mf-form-control { cursor: pointer; }
.mf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.mf-section-gap { margin-top: 20px; }

/* Generate button */
.mf-generate-row {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
    align-items: end;
    margin-top: 4px;
}
.mf-btn-generate {
    background: #f97316;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 18px;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    cursor: pointer;
    text-transform: uppercase;
    transition: background 0.2s;
    white-space: nowrap;
}
.mf-btn-generate:hover { background: #ea580c; }

/* Right panel — Document Preview */
.manifest-preview-panel {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    min-height: 600px;
}
.preview-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f3f4f6;
}
.preview-header h5 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 700;
    color: #111827;
}
.preview-body {
    flex: 1;
    padding: 28px;
    overflow-y: auto;
}
.preview-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 400px;
    color: #d1d5db;
    text-align: center;
}
.preview-empty i { font-size: 3rem; margin-bottom: 12px; }
.preview-empty p { font-size: 0.88rem; margin: 0; }

/* Generated document styles */
#manifestDoc { display: none; }
#manifestDoc.visible { display: block; }
.doc-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #1e3a8a;
}
.doc-logo { font-size: 1.3rem; font-weight: 900; letter-spacing: -0.5px; }
.doc-logo span { color: #1e3a8a; }
.doc-logo b { color: #f97316; }
.doc-meta { text-align: right; font-size: 0.78rem; color: #6b7280; }
.doc-meta strong { color: #111827; font-size: 0.9rem; display: block; margin-bottom: 2px; }
.doc-title { font-size: 1.2rem; font-weight: 800; color: #1e3a8a; text-align: center; margin-bottom: 24px; text-transform: uppercase; letter-spacing: 0.05em; }
.doc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.doc-section { padding: 14px 16px; background: #f9fafb; border-radius: 8px; border: 1px solid #f3f4f6; }
.doc-section h4 { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; font-weight: 700; margin: 0 0 10px; }
.doc-row { margin-bottom: 6px; font-size: 0.82rem; }
.doc-row label { color: #6b7280; font-weight: 600; display: inline-block; width: 110px; }
.doc-row span { color: #111827; font-weight: 500; }
.doc-items-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; margin-bottom: 20px; }
.doc-items-table th { background: #1e3a8a; color: #fff; padding: 9px 12px; text-align: left; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; }
.doc-items-table td { padding: 9px 12px; border-bottom: 1px solid #f3f4f6; }
.doc-items-table tr:nth-child(even) td { background: #f9fafb; }
.doc-footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #e5e7eb; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.doc-sig-line { margin-top: 28px; border-top: 1.5px solid #374151; padding-top: 6px; font-size: 0.75rem; color: #6b7280; font-weight: 600; text-align: center; }

/* Action buttons */
.preview-actions {
    padding: 16px 24px;
    border-top: 1px solid #f3f4f6;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}
.btn-dl-pdf {
    background: #f97316;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 11px 22px;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-dl-pdf:hover { background: #ea580c; }
.btn-print {
    background: #2563eb;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 11px 22px;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-print:hover { background: #1d4ed8; }

@media print {
    .cc-sidebar, .cc-topbar, .manifest-form-panel, .preview-actions, .cc-topbar { display: none !important; }
    .manifest-preview-panel { border: none; box-shadow: none; }
    .preview-body { padding: 0; }
    #manifestDoc { display: block !important; }
}
</style>

<?php include 'includes/sidebar.php'; ?>

<div class="cc-main">
    <div class="cc-topbar">
        <div style="display:flex;align-items:center;">
            <button class="cc-menu-toggle" id="menuToggle" aria-label="Open sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <span class="cc-topbar-title"><i class="fas fa-file-invoice text-blue me-2"></i>Manifest</span>
        </div>
        <div class="cc-topbar-actions"><div class="cc-avatar"><?php echo $user_initials; ?></div></div>
    </div>

    <div class="cc-page">
        <h2 class="cc-page-title">Manifest Generator</h2>
        <p class="cc-page-subtitle">Fill in the details and generate an official cargo manifest document.</p>

        <div class="manifest-layout">
            <!-- ── Left: Form Panel ─────────────────────────────── -->
            <div class="manifest-form-panel">

                <!-- Shipper Details -->
                <div class="mf-section-title"><i class="fas fa-user-tie me-2 text-blue"></i>Shipper Details</div>
                <div class="mf-form-group">
                    <label class="mf-form-label">Shipper Name</label>
                    <input type="text" id="shipper_name" class="mf-form-control" placeholder="Enter Name">
                </div>
                <div class="mf-form-group">
                    <label class="mf-form-label">Contact No.</label>
                    <input type="tel" id="shipper_contact" class="mf-form-control" placeholder="Enter Number">
                </div>
                <div class="mf-form-group">
                    <label class="mf-form-label">Pick Up Address</label>
                    <input type="text" id="pickup_address" class="mf-form-control" placeholder="Enter Address">
                </div>

                <!-- Consignee Details -->
                <div class="mf-section-title mf-section-gap"><i class="fas fa-user me-2 text-orange"></i>Consignee Details</div>
                <div class="mf-form-group">
                    <label class="mf-form-label">Consignee Name</label>
                    <input type="text" id="consignee_name" class="mf-form-control" placeholder="Enter Name">
                </div>
                <div class="mf-form-group">
                    <label class="mf-form-label">Contact No.</label>
                    <input type="tel" id="consignee_contact" class="mf-form-control" placeholder="Enter Number">
                </div>
                <div class="mf-form-group">
                    <label class="mf-form-label">Delivery Address</label>
                    <input type="text" id="delivery_address" class="mf-form-control" placeholder="Enter Address">
                </div>

                <!-- Item Description -->
                <div class="mf-section-title mf-section-gap"><i class="fas fa-box me-2" style="color:#6b7280"></i>Item Description</div>
                <div class="mf-form-group">
                    <label class="mf-form-label">Item Name</label>
                    <input type="text" id="item_name" class="mf-form-control" placeholder="Enter Item">
                </div>
                <div class="mf-row mf-form-group">
                    <div>
                        <label class="mf-form-label">Quantity</label>
                        <input type="number" id="item_qty" class="mf-form-control" placeholder="Enter Quantity" min="1" value="1">
                    </div>
                    <div>
                        <label class="mf-form-label">Weight</label>
                        <input type="text" id="item_weight" class="mf-form-control" placeholder="Enter kg">
                    </div>
                </div>
                <div class="mf-row mf-form-group">
                    <div>
                        <label class="mf-form-label">Cargo Type</label>
                        <select id="cargo_type" class="mf-form-control">
                            <option>Cargo</option>
                            <option>General</option>
                            <option>Fragile</option>
                            <option>Perishable</option>
                            <option>Hazardous</option>
                            <option>Electronics</option>
                            <option>Oversized</option>
                        </select>
                    </div>
                    <div>
                        <label class="mf-form-label">Number of Packages</label>
                        <input type="number" id="num_packages" class="mf-form-control" placeholder="Qty" min="1" value="1">
                    </div>
                </div>
                <div class="mf-generate-row">
                    <div>
                        <label class="mf-form-label">Pick-up Date</label>
                        <input type="date" id="pickup_date" class="mf-form-control">
                    </div>
                    <button class="mf-btn-generate" onclick="generateManifest()">Generate</button>
                </div>
            </div>

            <!-- ── Right: Preview Panel ─────────────────────────── -->
            <div class="manifest-preview-panel">
                <div class="preview-header">
                    <h5><i class="fas fa-file-lines me-2 text-blue"></i>Document Preview</h5>
                </div>
                <div class="preview-body" id="previewBody">
                    <!-- Empty state -->
                    <div class="preview-empty" id="previewEmpty">
                        <i class="fas fa-file-invoice"></i>
                        <p>Fill in the form and click <strong>Generate</strong><br>to preview your manifest here.</p>
                    </div>

                    <!-- Generated document -->
                    <div id="manifestDoc">
                        <!-- Header -->
                        <div class="doc-header">
                            <div class="doc-logo">
                                <span>Cargo</span><b>Connect.</b>
                                <div style="font-size:0.72rem;color:#6b7280;font-weight:400;margin-top:2px;">Cargo Manifest Document</div>
                            </div>
                            <div class="doc-meta">
                                <strong id="doc_manifest_no">MNF-—</strong>
                                <span>Date: <span id="doc_date">—</span></span><br>
                                <span>Pickup: <span id="doc_pickup">—</span></span>
                            </div>
                        </div>

                        <div class="doc-title">Cargo Manifest</div>

                        <!-- Shipper + Consignee -->
                        <div class="doc-grid">
                            <div class="doc-section">
                                <h4><i class="fas fa-user-tie me-1"></i> Shipper</h4>
                                <div class="doc-row"><label>Name:</label><span id="doc_shipper_name">—</span></div>
                                <div class="doc-row"><label>Contact:</label><span id="doc_shipper_contact">—</span></div>
                                <div class="doc-row"><label>Pickup Addr:</label><span id="doc_pickup_addr">—</span></div>
                            </div>
                            <div class="doc-section">
                                <h4><i class="fas fa-user me-1"></i> Consignee</h4>
                                <div class="doc-row"><label>Name:</label><span id="doc_consignee_name">—</span></div>
                                <div class="doc-row"><label>Contact:</label><span id="doc_consignee_contact">—</span></div>
                                <div class="doc-row"><label>Delivery Addr:</label><span id="doc_delivery_addr">—</span></div>
                            </div>
                        </div>

                        <!-- Items table -->
                        <table class="doc-items-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Cargo Type</th>
                                    <th>Quantity</th>
                                    <th>Weight</th>
                                    <th>Packages</th>
                                </tr>
                            </thead>
                            <tbody id="doc_items_body">
                                <tr><td colspan="6" style="text-align:center;color:#9ca3af;">No items</td></tr>
                            </tbody>
                        </table>

                        <!-- Footer -->
                        <div class="doc-footer">
                            <div>
                                <div style="font-size:0.78rem;color:#6b7280;font-weight:600;margin-bottom:4px;">PREPARED BY</div>
                                <div style="font-size:0.88rem;font-weight:700;" id="doc_preparer">—</div>
                                <div class="doc-sig-line">Authorized Signature</div>
                            </div>
                            <div>
                                <div style="font-size:0.78rem;color:#6b7280;font-weight:600;margin-bottom:4px;">RECEIVED BY</div>
                                <div style="font-size:0.88rem;color:#9ca3af;">______________________</div>
                                <div class="doc-sig-line">Consignee / Authorized Representative</div>
                            </div>
                        </div>

                        <div style="margin-top:16px;padding:10px 14px;background:#f9fafb;border-radius:6px;font-size:0.72rem;color:#9ca3af;text-align:center;">
                            This manifest is computer-generated by CargoConnect. • Generated: <span id="doc_generated_at">—</span>
                        </div>
                    </div>
                </div>

                <div class="preview-actions">
                    <button class="btn-dl-pdf" onclick="downloadPDF()"><i class="fas fa-file-pdf me-2"></i>Download PDF</button>
                    <button class="btn-print" onclick="window.print()"><i class="fas fa-print me-2"></i>Print</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
// Set default pickup date to today
document.getElementById('pickup_date').valueAsDate = new Date();

function val(id) {
    return document.getElementById(id)?.value?.trim() || '—';
}
function setDoc(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text || '—';
}

function generateManifest() {
    const now = new Date();
    const manifestNo = 'MNF-' + now.getFullYear() + String(now.getMonth()+1).padStart(2,'0') + String(now.getDate()).padStart(2,'0') + '-' + String(now.getTime()).slice(-4);
    const dateStr = now.toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
    const pickupDate = document.getElementById('pickup_date').value
        ? new Date(document.getElementById('pickup_date').value).toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' })
        : '—';

    // Populate document fields
    setDoc('doc_manifest_no', manifestNo);
    setDoc('doc_date', dateStr);
    setDoc('doc_pickup', pickupDate);

    setDoc('doc_shipper_name',    val('shipper_name'));
    setDoc('doc_shipper_contact', val('shipper_contact'));
    setDoc('doc_pickup_addr',     val('pickup_address'));

    setDoc('doc_consignee_name',    val('consignee_name'));
    setDoc('doc_consignee_contact', val('consignee_contact'));
    setDoc('doc_delivery_addr',     val('delivery_address'));

    // Items table
    const tbody = document.getElementById('doc_items_body');
    const itemName  = val('item_name');
    const qty       = val('item_qty');
    const weight    = val('item_weight');
    const cargoType = document.getElementById('cargo_type').value;
    const packages  = val('num_packages');

    tbody.innerHTML = `<tr>
        <td>1</td>
        <td>${itemName}</td>
        <td>${cargoType}</td>
        <td>${qty}</td>
        <td>${weight} kg</td>
        <td>${packages}</td>
    </tr>`;

    setDoc('doc_preparer', '<?php echo htmlspecialchars($user_name); ?>');
    setDoc('doc_generated_at', now.toLocaleString('en-US'));

    // Show document, hide empty state
    document.getElementById('previewEmpty').style.display = 'none';
    const doc = document.getElementById('manifestDoc');
    doc.classList.add('visible');
}

function downloadPDF() {
    const doc = document.getElementById('manifestDoc');
    if (!doc.classList.contains('visible')) {
        alert('Please generate the manifest first.');
        return;
    }
    const opt = {
        margin:      [10, 10, 10, 10],
        filename:    `CargoConnect_Manifest_${new Date().toISOString().slice(0,10)}.pdf`,
        image:       { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF:       { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(doc).save();
}
</script>

<?php include 'includes/app_foot.php'; ?>
