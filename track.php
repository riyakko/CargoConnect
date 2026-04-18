<?php include 'includes/header.php'; ?>

<main class="flex-grow-1 bg-light pb-5 pt-3">
    <!-- Header Hero for Tracking -->
    <div class="py-5 bg-white mb-4 border-bottom shadow-sm">
        <div class="container text-center">
            <h1 class="fw-bolder text-dark mb-3">Track <span class="text-orange">Shipment</span></h1>
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <form action="track.php" method="GET" class="input-group input-group-lg shadow rounded-pill overflow-hidden border border-light">
                        <input type="text" name="id" class="form-control border-0 ps-4 bg-white text-dark fw-bold focus-ring focus-ring-light" placeholder="Track num e.g. CRG-99382" value="CRG-99382">
                        <button class="btn btn-primary px-4 px-md-5 fw-bold text-white shadow-none" style="background: linear-gradient(90deg, #f57c00, #ff9800); border:none;" type="submit">Track</button>
                    </form>
                    <p class="text-muted small mt-3"><i class="fa-solid fa-circle-check text-success me-1"></i>Found 1 match for <strong>CRG-99382</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row g-4 justify-content-center">
            
            <!-- Map Section -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-map-location-dot text-blue me-2"></i>Live Location</h6>
                    </div>
                    <!-- Using OpenStreetMap from Leader's changes -->
                    <div class="rounded overflow-hidden w-100 position-relative" style="height: 400px; background-color: #e2e8f0;">
                        <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=121.05%2C13.9%2C121.25%2C14.1&amp;layer=mapnik" style="border: none;"></iframe>
                        <div class="position-absolute" style="top:50%; left:50%; transform:translate(-50%,-100%); pointer-events: none;">
                            <i class="fa-solid fa-location-dot text-danger" style="font-size: 3rem; text-shadow: 0px 2px 4px rgba(0,0,0,0.3);"></i>
                            <div class="position-absolute bg-danger rounded-circle" style="width: 12px; height: 12px; top: 12px; left: 50%; transform: translateX(-50%); border: 3px solid white;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-list-ul text-orange me-2"></i>Status History</h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">In Transit</span>
                    </div>
                    <div class="card-body px-4 py-4 overflow-auto" style="max-height: 450px;">
                        
                        <div class="timeline">
                            <div class="timeline-item d-flex mb-4 position-relative">
                                <div class="timeline-icon bg-blue text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 40px; height: 40px; z-index: 2;">
                                    <i class="fa-solid fa-ship"></i>
                                </div>
                                <div class="timeline-content ms-3 pb-3 border-bottom w-100">
                                    <h6 class="fw-bold text-dark mb-1">Departed Port of Origin</h6>
                                    <p class="text-muted small mb-1">Rotterdam, Netherlands (NLRTM)</p>
                                    <span class="badge bg-light text-dark shadow-sm">Oct 24, 08:30 AM</span>
                                </div>
                                <div class="timeline-line bg-secondary bg-opacity-25 position-absolute" style="width: 2px; height: 100%; left: 19px; top: 40px;"></div>
                            </div>
                            
                            <div class="timeline-item d-flex mb-4 position-relative">
                                <div class="timeline-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 40px; height: 40px; z-index: 2;">
                                    <i class="fa-solid fa-boxes-packing"></i>
                                </div>
                                <div class="timeline-content ms-3 pb-3 border-bottom w-100">
                                    <h6 class="fw-bold text-dark mb-1">Customs Cleared</h6>
                                    <p class="text-muted small mb-1">Rotterdam Customs Authority</p>
                                    <span class="badge bg-light text-dark shadow-sm">Oct 23, 14:15 PM</span>
                                </div>
                                <div class="timeline-line bg-secondary bg-opacity-25 position-absolute" style="width: 2px; height: 100%; left: 19px; top: 40px;"></div>
                            </div>

                            <div class="timeline-item d-flex position-relative">
                                <div class="timeline-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 40px; height: 40px; z-index: 2;">
                                    <i class="fa-solid fa-file-signature"></i>
                                </div>
                                <div class="timeline-content ms-3 w-100">
                                    <h6 class="fw-bold text-dark mb-1">Booking Confirmed</h6>
                                    <p class="text-muted small mb-1">Online Portal</p>
                                    <span class="badge bg-light text-dark shadow-sm">Oct 22, 09:00 AM</span>
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
    .bg-blue { background-color: #0d47a1 !important; }
    .text-blue { color: #0d47a1 !important; }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(13, 71, 161, 0.4); }
        70% { box-shadow: 0 0 0 20px rgba(13, 71, 161, 0); }
        100% { box-shadow: 0 0 0 0 rgba(13, 71, 161, 0); }
    }
    body {
        background-color: #f8fafc;
    }
</style>

<?php include 'includes/footer.php'; ?>
