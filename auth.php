<?php
$page_title = "Login | CargoConnect";
include 'includes/header.php';
?>

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5 position-relative overflow-hidden" style="background-color: #f8f9fa;">
    <div class="decorative-swoosh left-swoosh" style="opacity: 0.15;"></div>
    <div class="decorative-swoosh right-swoosh" style="opacity: 0.15;"></div>

    <div class="container position-relative z-2">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4 bg-white bg-opacity-75" style="backdrop-filter: blur(10px);">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-circle-user fs-1 text-orange mb-3"></i>
                            <h2 class="fw-bold mb-1">Welcome Back</h2>
                            <p class="text-muted">Sign in to your CargoConnect account</p>
                        </div>
                        <form action="dashboard.php" method="GET">
                            <div class="mb-3 text-start">
                                <label class="form-label fw-semibold text-dark">Email address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control border-start-0 ps-0" placeholder="name@company.com" required>
                                </div>
                            </div>
                            <div class="mb-4 text-start">
                                <label class="form-label fw-semibold text-dark">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                    <input type="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                                </div>
                                <div class="text-end mt-2">
                                    <a href="#" class="text-blue text-decoration-none small">Forgot password?</a>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-login w-100 py-3 rounded-pill fw-bold mb-3 shadow-sm" style="background: linear-gradient(90deg, #f57c00 0%, #ff9800 100%); color: white; border: none;">Sign In</button>
                            <div class="text-center">
                                <span class="text-muted">Don't have an account?</span>
                                <a href="#" class="text-orange text-decoration-none fw-semibold ms-1">Register here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
