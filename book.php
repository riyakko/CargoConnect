<?php include 'includes/header.php'; ?>

<main class="flex-grow-1 py-5 position-relative overflow-hidden" style="background-color: #f8f9fa;">
    <!-- Abstract Background -->
    <div class="decorative-swoosh left-swoosh" style="opacity: 0.15;"></div>

    <div class="container position-relative z-2">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="fw-bolder text-dark">Book a <span class="text-orange">Shipment</span></h1>
                <p class="text-muted">Enter your cargo details below to initiate a booking request.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <form action="dashboard.php" method="GET">
                            <!-- Section 1: Route -->
                            <h5 class="fw-bold mb-4" style="color:#0d47a1;"><i class="fa-solid fa-route me-2"></i>Route Configuration</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Origin</label>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-white border-0"><i class="fa-solid fa-location-dot text-muted"></i></span>
                                        <input type="text" class="form-control bg-white border-0 ps-0 py-2" placeholder="City or Port" required>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-center justify-content-center pt-4">
                                    <div class="bg-light rounded-circle p-2 shadow-sm"><i class="fa-solid fa-arrow-right-arrow-left text-muted"></i></div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Destination</label>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-white border-0"><i class="fa-solid fa-location-dot text-orange"></i></span>
                                        <input type="text" class="form-control bg-white border-0 ps-0 py-2" placeholder="City or Port" required>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="border-light-subtle my-5">

                            <!-- Section 2: Cargo Details -->
                            <h5 class="fw-bold mb-4" style="color:#0d47a1;"><i class="fa-solid fa-box-open me-2"></i>Cargo Details</h5>
                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Freight Type</label>
                                    <select class="form-select bg-white border-0 py-2 shadow-sm rounded-3 text-dark">
                                        <option>FCL (Full Container)</option>
                                        <option>LCL (Less than Container)</option>
                                        <option>Air Freight</option>
                                        <option>Ground Transport</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Total Weight (KG)</label>
                                    <input type="number" class="form-control bg-white border-0 py-2 shadow-sm rounded-3 text-dark" placeholder="e.g., 5000">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Cargo Value ($)</label>
                                    <input type="number" class="form-control bg-white border-0 py-2 shadow-sm rounded-3 text-dark" placeholder="e.g., 50000">
                                </div>
                            </div>

                            <hr class="border-light-subtle my-5">

                            <!-- Section 3: Dates -->
                            <h5 class="fw-bold mb-4" style="color:#0d47a1;"><i class="fa-solid fa-calendar-days me-2"></i>Schedule</h5>
                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted text-uppercase">Ready Date</label>
                                    <input type="date" class="form-control bg-white border-0 py-2 shadow-sm rounded-3 text-muted">
                                </div>
                            </div>

                            <div class="d-grid mt-2">
                                <button type="submit" class="btn btn-primary py-3 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(90deg, #f57c00, #ff9800); border:none;">Proceed to Quote <i class="fa-solid fa-arrow-right ms-2"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
