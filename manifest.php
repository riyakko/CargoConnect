<?php include 'includes/header.php'; ?>

<main class="flex-grow-1 bg-light py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bolder text-dark mb-1"><i class="fa-solid fa-file-invoice text-orange me-2"></i>Manifest Generator</h2>
                <p class="text-muted mb-0">Compile and export official cargo itineraries and documents.</p>
            </div>
            <button class="btn btn-primary d-flex align-items-center gap-2 rounded-pill px-4" style="background:var(--blue-navy);"><i class="fa-solid fa-file-pdf"></i> Export PDF</button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-0">
                <div class="bg-white p-4 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-0">Vessel: <span class="text-blue">Evergreen MSC</span></h5>
                        <p class="text-muted small mb-0">Voyage V-9034 | Date: <?php echo date('M d, Y'); ?></p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-dark rounded-pill px-3 py-2 text-white"><i class="fa-solid fa-barcode me-2"></i>MNF-<?php echo time(); ?></span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">B/L Number</th>
                                <th class="py-3">Shipper</th>
                                <th class="py-3">Consignee</th>
                                <th class="py-3">Origin -> Dest</th>
                                <th class="py-3">Containers</th>
                                <th class="py-3">Weight (KG)</th>
                                <th class="pe-4 text-end py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <!-- Item 1 -->
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">BL-00192A</td>
                                <td>Global Tech Inc.</td>
                                <td>EuroDistro LLC</td>
                                <td>Shenzhen <i class="fa-solid fa-caret-right mx-1 text-muted"></i> Hamburg</td>
                                <td>2x 40FT HC</td>
                                <td>18,500</td>
                                <td class="pe-4 text-end"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Cleared</span></td>
                            </tr>
                            <!-- Item 2 -->
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">BL-00204B</td>
                                <td>FarmCo Exports</td>
                                <td>Middletown Markets</td>
                                <td>Santos <i class="fa-solid fa-caret-right mx-1 text-muted"></i> Miami</td>
                                <td>1x 20FT Reef</td>
                                <td>12,000</td>
                                <td class="pe-4 text-end"><span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Inspecting</span></td>
                            </tr>
                            <!-- Item 3 -->
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">BL-00445X</td>
                                <td>AutoParts Ltd.</td>
                                <td>Detroit Assembly</td>
                                <td>Yokohama <i class="fa-solid fa-caret-right mx-1 text-muted"></i> Los Angeles</td>
                                <td>5x 40FT Std</td>
                                <td>45,000</td>
                                <td class="pe-4 text-end"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Cleared</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
                <p class="text-muted small mb-0">Total Contains: <strong>8 TEUs</strong> | Total Weight: <strong>75,500 KG</strong></p>
                <nav aria-label="Manifest Pagination">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>

    </div>
</main>
<style>
    :root {
        --blue-navy: #0b1324;
    }
    .text-blue { color: #0d47a1 !important; }
</style>

<?php include 'includes/footer.php'; ?>
