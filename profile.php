<?php include 'includes/header.php'; ?>

<main class="flex-grow-1 bg-light py-5">
    <div class="container">
        
        <div class="row justify-content-center">
            
            <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
                <!-- User Profile Card -->
                <div class="card border-0 shadow-sm rounded-4 text-center overflow-hidden">
                    <div class="bg-primary" style="height: 100px; background: linear-gradient(90deg, #0d47a1, #1976d2);"></div>
                    <div class="card-body px-4 pb-4 position-relative">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm border border-light" style="width: 100px; height: 100px; position: absolute; top: -50px; left: 50%; transform: translateX(-50%);">
                            <i class="fa-solid fa-user-tie fs-1 text-secondary"></i>
                        </div>
                        <div class="pt-5 mt-2">
                            <h5 class="fw-bolder text-dark mb-1">John Doe</h5>
                            <p class="text-muted small mb-3">Logistics Manager<br>Global Tech Inc.</p>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25 w-100"><i class="fa-solid fa-circle-check me-2"></i>Account Verified</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-0 list-group list-group-flush text-start">
                        <a href="#personal" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-semibold text-dark active-nav"><i class="fa-solid fa-id-card text-muted me-3"></i>Personal Info</a>
                        <a href="#billing" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-semibold text-dark"><i class="fa-solid fa-credit-card text-muted me-3"></i>Billing Details</a>
                        <a href="#security" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-semibold text-dark"><i class="fa-solid fa-shield-halved text-muted me-3"></i>Security</a>
                        <a href="#notifications" class="list-group-item list-group-item-action border-0 px-4 py-3 fw-semibold text-dark"><i class="fa-solid fa-bell text-muted me-3"></i>Notifications</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-8">
                <!-- Settings Form Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-bolder text-dark mb-4">Personal Information</h4>
                        <form>
                            <div class="row g-4 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold text-muted text-uppercase small">First Name</label>
                                    <input type="text" class="form-control bg-light border-0 py-2 shadow-sm rounded-3" value="John">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold text-muted text-uppercase small">Last Name</label>
                                    <input type="text" class="form-control bg-light border-0 py-2 shadow-sm rounded-3" value="Doe">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold text-muted text-uppercase small">Email Address</label>
                                    <input type="email" class="form-control bg-light border-0 py-2 shadow-sm rounded-3" value="john.doe@globaltech.com" readonly>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold text-muted text-uppercase small">Phone Number</label>
                                    <input type="tel" class="form-control bg-light border-0 py-2 shadow-sm rounded-3" value="+1 (555) 123-4567">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-muted text-uppercase small">Company Details</label>
                                    <textarea class="form-control bg-light border-0 py-2 shadow-sm rounded-3" rows="3">Global Tech Inc.
123 Innovation Drive, Silicon Valley, CA 94025</textarea>
                                </div>
                            </div>
                            
                            <hr class="border-light-subtle my-5">
                            
                            <h4 class="fw-bolder text-dark mb-4 mt-2">Preferences</h4>
                            <div class="row g-4 mb-5">
                                <div class="col-12">
                                    <div class="form-check form-switch d-flex align-items-center justify-content-between p-0 mb-3">
                                        <div>
                                            <label class="form-check-label fw-semibold text-dark mb-0 d-block">Email Notifications</label>
                                            <small class="text-muted">Receive status updates about your shipments via email.</small>
                                        </div>
                                        <input class="form-check-input ms-3 mt-0 fs-4" type="checkbox" role="switch" checked style="float:none!important;">
                                    </div>
                                    <div class="form-check form-switch d-flex align-items-center justify-content-between p-0 mb-3">
                                        <div>
                                            <label class="form-check-label fw-semibold text-dark mb-0 d-block">SMS Alerts</label>
                                            <small class="text-muted">Receive critical alerts directly to your phone.</small>
                                        </div>
                                        <input class="form-check-input ms-3 mt-0 fs-4" type="checkbox" role="switch" checked style="float:none!important;">
                                    </div>
                                    <div class="form-check form-switch d-flex align-items-center justify-content-between p-0">
                                        <div>
                                            <label class="form-check-label fw-semibold text-dark mb-0 d-block">Monthly Digest</label>
                                            <small class="text-muted">Get a summary of your logistics volume and costs.</small>
                                        </div>
                                        <input class="form-check-input ms-3 mt-0 fs-4" type="checkbox" role="switch" style="float:none!important;">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-light fw-bold px-4 rounded-pill">Cancel</button>
                                <button type="button" class="btn btn-primary fw-bold px-4 rounded-pill border-0 shadow-sm" style="background:#0d47a1;">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>
<style>
    .active-nav { background-color: #f8f9fa !important; border-left: 3px solid #f57c00 !important; color: #f57c00 !important; }
    .active-nav i { color: #f57c00 !important; }
</style>

<?php include 'includes/footer.php'; ?>
