<?php include 'includes/header.php'; ?>

    <div id="book-page-wrapper">
    <nav id="book-sidebar">
        <div class="logo">Cargo<span>Connect.</span></div>
        <div class="nav-item">Dashboard</div>
        <div class="nav-item active">Bookings</div>
        <div class="nav-item">Calculator</div>
        <div class="nav-item">Tracking</div>
        <div class="nav-item">Manifests</div>
        <div class="nav-item">Settings</div>
    </nav>

    <main id="book-main">
        <header>
            <div style="font-weight: bold; font-size: 1.2rem;">Booking</div>
            <div class="profile-icon"></div>
        </header>

        <div class="container">
            <h1>Book Your Cargo</h1>

            <div class="booking-card">
                <form class="form-grid">
                    
                    <div>
                        <div class="section-title">Shipper Details</div>
                        <div class="form-group">
                            <label>Shipper Name</label>
                            <input type="text" placeholder="Enter Name">
                        </div>
                        <div class="form-group">
                            <label>Contact No.</label>
                            <input type="text" placeholder="Enter Number">
                        </div>
                        <div class="form-group">
                            <label>Pick Up Address</label>
                            <input type="text" placeholder="Enter Address">
                        </div>
                    </div>

                    <div>
                        <div class="section-title">Consignee Details</div>
                        <div class="form-group">
                            <label>Consignee Name</label>
                            <input type="text" placeholder="Enter Name">
                        </div>
                        <div class="form-group">
                            <label>Contact No.</label>
                            <input type="text" placeholder="Enter Number">
                        </div>
                        <div class="form-group">
                            <label>Delivery Address</label>
                            <input type="text" placeholder="Enter Address">
                        </div>
                    </div>

                    <div>
                        <div class="section-title">Item Description</div>
                        <div class="form-group">
                            <label>Item Name</label>
                            <input type="text" placeholder="Enter Item">
                        </div>
                        <div class="input-row">
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="text" placeholder="Enter Quantity">
                            </div>
                            <div class="form-group">
                                <label>Weight</label>
                                <input type="text" placeholder="Enter kg">
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="form-group">
                                <label>Cargo Type</label>
                                <select>
                                    <option>Cargo</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Number of Packages</label>
                                <input type="text" placeholder="Qty">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Pick-up Date</label>
                            <input type="date">
                        </div>
                    </div>

                    <button type="submit" class="btn-confirm">CONFIRM BOOKING</button>
                </form>
            </div>
        </div>
    </main>
    </div>

<?php include 'includes/footer.php'; ?>
