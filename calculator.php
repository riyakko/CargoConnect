<?php include 'includes/header.php'; ?>

<main class="flex-grow-1 py-5 bg-light position-relative overflow-hidden">
    <div class="decorative-swoosh right-swoosh" style="opacity:0.1"></div>
    <div class="container position-relative z-2">
        
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="text-center mb-4">
                    <h2 class="fw-bolder text-dark">Instant <span class="text-blue">Rate Calculator</span></h2>
                    <p class="text-muted">Get a transparent, estimated shipping cost in seconds.</p>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <form action="calculator.php" method="POST">
                            
                            <!-- Mode Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-muted text-uppercase small">Transport Mode</label>
                                <div class="btn-group w-100 shadow-sm rounded-3">
                                    <input type="radio" class="btn-check" name="mode" id="modeOcean" checked>
                                    <label class="btn btn-outline-primary px-3 py-2 fw-semibold" for="modeOcean"><i class="fa-solid fa-anchor me-2 text-primary"></i>Ocean</label>

                                    <input type="radio" class="btn-check" name="mode" id="modeAir">
                                    <label class="btn btn-outline-primary px-3 py-2 fw-semibold" for="modeAir"><i class="fa-solid fa-plane text-info me-2"></i>Air</label>

                                    <input type="radio" class="btn-check" name="mode" id="modeRoad">
                                    <label class="btn btn-outline-primary px-3 py-2 fw-semibold" for="modeRoad"><i class="fa-solid fa-truck text-secondary me-2"></i>Road</label>
                                </div>
                            </div>

                            <hr class="border-light-subtle my-4">

                            <!-- Details -->
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-muted text-uppercase small">Weight (KG)</label>
                                    <input type="number" class="form-control bg-light border-0 py-2 shadow-sm rounded-3" value="1000">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-muted text-uppercase small">Volume (CBM)</label>
                                    <input type="number" class="form-control bg-light border-0 py-2 shadow-sm rounded-3" value="2.5">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-muted text-uppercase small">Origin Zone</label>
                                <select class="form-select bg-light border-0 py-2 shadow-sm rounded-3">
                                    <option>North America (East Coast)</option>
                                    <option>North America (West Coast)</option>
                                    <option>Europe (Western)</option>
                                    <option>Asia (East)</option>
                                    <option>Middle East</option>
                                </select>
                            </div>
                            <div class="mb-5">
                                <label class="form-label fw-semibold text-muted text-uppercase small">Destination Zone</label>
                                <select class="form-select bg-light border-0 py-2 shadow-sm rounded-3">
                                    <option>Europe (Western)</option>
                                    <option>North America (East Coast)</option>
                                    <option>North America (West Coast)</option>
                                    <option>Asia (East)</option>
                                    <option>Middle East</option>
                                </select>
                            </div>
                            
                            <!-- Placeholder Result -->
                            <div class="bg-primary bg-opacity-10 p-4 rounded-4 mb-4 text-center border border-primary border-opacity-25">
                                <p class="text-primary fw-bold text-uppercase small mb-1">Estimated Cost</p>
                                <h2 class="display-6 fw-bold text-dark mb-0">$ 1,245<span class="fs-5 text-muted fw-normal">.50</span></h2>
                                <small class="text-muted">Incl. standard customs clearance.</small>
                            </div>

                            <div class="d-grid">
                                <a href="book.php" class="btn btn-primary py-3 rounded-pill fw-bold border-0 shadow-sm" style="background:#0d47a1;">Proceed with Booking</a>
                            </div>

                        </form>
                    </div>
                </div>
                
            </div>
        </div>

    </div>
</main>
<style>
    .btn-check:checked + .btn-outline-primary {
        background-color: #0d47a1 !important;
        border-color: #0d47a1 !important;
        color: white !important;
    }
    .btn-check:checked + .btn-outline-primary i {
        color: white !important;
    }
    .btn-outline-primary {
        border-color: #e9ecef;
        color: #495057;
        background-color: #f8f9fa;
    }
    .btn-outline-primary:hover {
        background-color: #e9ecef;
        color: #212529;
        border-color: #e9ecef;
    }
</style>

<?php include 'includes/footer.php'; ?>
