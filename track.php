<?php include 'includes/header.php'; ?>

<main class="flex-grow-1 bg-light pb-5 pt-4" style="background-color: #f8fafc !important;">
    <div class="container mb-5">
        
        <!-- Search Bar -->
        <div class="row justify-content-center mb-5 mt-3">
            <div class="col-md-8 col-lg-6">
                <div class="bg-white p-1 d-flex align-items-center shadow-sm" style="border-radius: 8px;">
                    <input type="text" class="form-control border-0 shadow-none px-3 text-muted" placeholder="Tracking ID" style="background: transparent; font-size: 15px;">
                    <button class="btn border-0 shadow-none text-dark pe-3"><i class="fa-solid fa-magnifying-glass fs-5" style="color: #0f172a;"></i></button>
                </div>
            </div>
        </div>

        <!-- Progress Tracker -->
        <div class="row justify-content-center mb-5 mt-4">
            <div class="col-lg-10">
                <!-- Wrapper for stepper -->
                <div class="position-relative w-75 mx-auto">
                    <!-- Connection Lines -->
                    <div class="position-absolute w-100" style="top: 35px; left: 0; z-index: 1;">
                        <div class="d-flex w-100">
                            <!-- Line 1 to 2 -->
                            <div style="height: 4px; width: 50%; background-color: #ff7f00;"></div>
                            <!-- Line 2 to 3 -->
                            <div style="height: 4px; width: 50%; background-color: #9cb4fc;"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                        <!-- Step 1 -->
                        <div class="d-flex flex-column align-items-center text-center" style="width: 120px; margin-left: -60px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mb-3" style="width: 75px; height: 75px; background-color: #ff7f00;">
                                <i class="fa-solid fa-check fs-3"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Order Placed</h6>
                            <small class="text-muted mt-1" style="font-size: 13px;">Completed</small>
                        </div>

                        <!-- Step 2 -->
                        <div class="d-flex flex-column align-items-center text-center" style="width: 120px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mb-3" style="width: 75px; height: 75px; background-color: #ff7f00;">
                                <i class="fa-solid fa-truck fs-3"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">In Transit</h6>
                            <small class="text-muted mt-1" style="font-size: 13px;">Active</small>
                        </div>

                        <!-- Step 3 -->
                        <div class="d-flex flex-column align-items-center text-center" style="width: 120px; margin-right: -60px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mb-3" style="width: 75px; height: 75px; background-color: #9cb4fc;">
                                <i class="fa-solid fa-warehouse fs-4"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Arrived</h6>
                            <small class="text-muted mt-1" style="font-size: 13px;">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details & Map -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row g-4">
                    <!-- Map Card -->
                    <div class="col-md-8">
                        <div class="card bg-white border-0 shadow-sm h-100" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-3">Current Location</h6>
                                <div class="rounded overflow-hidden w-100 position-relative" style="height: 250px; background-color: #e2e8f0;">
                                    <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=121.05%2C13.9%2C121.25%2C14.1&amp;layer=mapnik" style="border: none;"></iframe>
                                    <div class="position-absolute" style="top:50%; left:50%; transform:translate(-50%,-100%); pointer-events: none;">
                                        <i class="fa-solid fa-location-dot text-danger" style="font-size: 3rem; text-shadow: 0px 2px 4px rgba(0,0,0,0.3);"></i>
                                        <div class="position-absolute bg-danger rounded-circle" style="width: 12px; height: 12px; top: 12px; left: 50%; transform: translateX(-50%); border: 3px solid white;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Details Card -->
                    <div class="col-md-4">
                        <div class="card bg-white border-0 shadow-sm h-100" style="border-radius: 12px;">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-4">Shipment Details</h6>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1" style="font-size: 13px;">Origin</small>
                                        <span class="text-dark fw-medium" style="font-size: 15px;">Manila</span>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1" style="font-size: 13px;">Destination</small>
                                        <span class="text-dark fw-medium" style="font-size: 15px;">Batangas</span>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1" style="font-size: 13px;">Carrier</small>
                                        <span class="text-dark fw-medium" style="font-size: 15px;">LBC</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted d-block mb-1" style="font-size: 13px;">Estimated Delivery</small>
                                    <span class="text-dark fw-medium" style="font-size: 15px;">April 6, 2026</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<style>
    body {
        background-color: #f8fafc;
    }
    input::placeholder {
        color: #94a3b8 !important;
    }
    .text-muted {
        color: #64748b !important;
    }
</style>

<?php include 'includes/footer.php'; ?>
