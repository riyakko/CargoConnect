<?php include 'includes/header.php'; ?>

<style>.wave { animation-name: wave-animation; animation-duration: 2.5s; animation-iteration-count: infinite; transform-origin: 70% 70%; display: inline-block; } @keyframes wave-animation { 0% { transform: rotate( 0.0deg) } 10% { transform: rotate(14.0deg) }  20% { transform: rotate(-8.0deg) } 30% { transform: rotate(14.0deg) } 40% { transform: rotate(-4.0deg) } 50% { transform: rotate(10.0deg) } 60% { transform: rotate( 0.0deg) } 100% { transform: rotate( 0.0deg) } }</style>

<main class="flex-grow-1 bg-light py-4 position-relative">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
            <h2 class="fw-bold mb-0 text-dark">Hello, John <span class="wave fs-3">👋</span></h2>
            <div class="d-flex gap-2">
                <a href="book.php" class="btn btn-primary rounded-pill px-4 fw-semibold border-0 text-white shadow-sm" style="background: linear-gradient(90deg, #0d47a1, #1976d2);"><i class="fa-solid fa-plus me-2"></i>New Shipment</a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-2">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fa-solid fa-box-open fs-3 text-primary" style="color:#0d47a1!important;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 fw-semibold small text-uppercase">Active Shipments</p>
                            <h3 class="fw-bold mb-0 text-dark">12</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-2">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fa-solid fa-check-double fs-3 text-success"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 fw-semibold small text-uppercase">Delivered</p>
                            <h3 class="fw-bold mb-0 text-dark">48</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-2">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fa-solid fa-clock-rotate-left fs-3 text-warning"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 fw-semibold small text-uppercase">Pending</p>
                            <h3 class="fw-bold mb-0 text-dark">5</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-2">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fa-solid fa-triangle-exclamation fs-3 text-danger"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 fw-semibold small text-uppercase">Exceptions</p>
                            <h3 class="fw-bold mb-0 text-dark">1</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Shipments Table -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h5 class="fw-bold mb-0 text-dark">Recent Activity</h5>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3 border-0 rounded-start">Tracking No.</th>
                                <th class="border-0">Destination</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 rounded-end text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3"><a href="track.php" class="fw-semibold text-decoration-none">CRG-99382</a></td>
                                <td>Rotterdam, NL</td>
                                <td><i class="fa-solid fa-ship text-muted me-2"></i>Ocean</td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 border border-primary border-opacity-25" style="color:#0d47a1!important;">In Transit</span></td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-light rounded-circle"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3"><a href="track.php" class="fw-semibold text-decoration-none">CRG-10254</a></td>
                                <td>New York, USA</td>
                                <td><i class="fa-solid fa-plane text-muted me-2"></i>Air</td>
                                <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">Delivered</span></td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-light rounded-circle"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3"><a href="track.php" class="fw-semibold text-decoration-none">CRG-88412</a></td>
                                <td>Shanghai, CN</td>
                                <td><i class="fa-solid fa-truck text-muted me-2"></i>Ground</td>
                                <td><span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 border border-warning border-opacity-25">Pending Custom</span></td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-light rounded-circle"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
