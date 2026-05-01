<?php include 'includes/header.php'; ?>

    <!-- Main Hero Section -->
    <main class="hero-section text-center position-relative pt-5 pb-4" style="background: linear-gradient(180deg, #F0F4F8 0%, #E2E8F0 100%); min-height: 50vh;">
        <div class="container position-relative z-2">
            <!-- Headline -->
            <h1 class="display-4 mt-4 mb-2" style="font-weight: 400; color: #1f2937;">
                Streamline Your Shipping,<br>
                <span style="color: #F97316; font-weight: 800;">Track</span> <span style="font-weight: 800;">with Confidence</span>
            </h1>

            <!-- 3D Model Container -->
            <div class="model-container-wrapper mx-auto mt-4 mb-5 position-relative" style="height: 550px; max-width: 900px;">
                <!-- Decorative Arrow Left (from sam branch) -->
                <svg class="position-absolute d-none d-md-block" style="left: -90px; top: 120px; width: 140px; height: 120px; pointer-events: none;" viewBox="0 0 100 100" fill="none">
                    <path d="M100,50 C60,50 40,80 0,60" stroke="#2563EB" stroke-width="1.5" fill="none"/>
                    <path d="M5,62 L0,60 L2,55" stroke="#2563EB" stroke-width="1.5" fill="none"/>
                </svg>
                
                <!-- Decorative Arrow Right (from sam branch) -->
                <svg class="position-absolute d-none d-md-block" style="right: -80px; top: 120px; width: 120px; height: 100px; pointer-events: none;" viewBox="0 0 100 100" fill="none">
                    <path d="M0,80 C40,80 60,20 100,40" stroke="#2563EB" stroke-width="1.5" fill="none"/>
                    <rect x="95" y="35" width="6" height="6" stroke="#2563EB" stroke-width="1.5" fill="none" transform="rotate(25 98 38)"/>
                </svg>

                <div class="glow-effect left-glow" style="background: rgba(249, 115, 22, 0.4); top: 20%; left: 0; width: 250px; height: 250px; border-radius: 50%; filter: blur(70px); position: absolute; z-index: 0;"></div>
                <div class="glow-effect right-glow" style="background: rgba(42, 91, 161, 0.4); bottom: 10%; right: 0; width: 250px; height: 250px; border-radius: 50%; filter: blur(70px); position: absolute; z-index: 0;"></div>
                
                <div id="canvas-container" class="w-100 h-100 position-relative z-1" style="cursor: grab;">
                    <!-- Three.js Canvas goes here -->
                </div>
                </div>
            </div>
        </div>
        </div>
    </main>

    <!-- Calculator Card -->
    <section class="container py-4 mt-2">
        <div class="card bg-cc-navy text-white rounded-3 border-0 py-4 px-3 px-md-5 mx-auto" style="max-width: 900px; background: linear-gradient(135deg, #071022, #182645); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="card-body text-center">
                <h4 class="mb-4 text-light" style="font-weight: 300;">Need an instant <em class="fw-bold">shipping estimate?</em></h4>
                <form id="calculatorForm" class="row g-3 justify-content-center align-items-end">
                    <div class="col-md-3 text-start position-relative">
                        <label class="form-label text-light mb-1" style="font-size: 0.8rem;"><i class="fa-solid fa-location-dot me-1"></i> Origin</label>
                        <input type="text" class="form-control form-control-sm rounded-1 py-2" id="calcOrigin" autocomplete="off" placeholder="Origin" required>
                        <div id="originSuggestions" class="list-group position-absolute w-100 shadow-lg d-none text-start" style="z-index: 1000; top: 100%; max-height: 200px; overflow-y: auto;"></div>
                    </div>
                    <div class="col-md-3 text-start">
                        <label class="form-label text-light mb-1" style="font-size: 0.8rem;"><i class="fa-solid fa-weight-hanging me-1"></i> Weight</label>
                        <input type="number" class="form-control form-control-sm rounded-1 py-2" id="calcWeight" placeholder="Weight" min="1" required>
                    </div>
                    <div class="col-md-3 text-start position-relative">
                        <label class="form-label text-light mb-1" style="font-size: 0.8rem;"><i class="fa-solid fa-paper-plane me-1"></i> Destination</label>
                        <input type="text" class="form-control form-control-sm rounded-1 py-2" id="calcDestination" autocomplete="off" placeholder="Destination" required>
                        <div id="destinationSuggestions" class="list-group position-absolute w-100 shadow-lg d-none text-start" style="z-index: 1000; top: 100%; max-height: 200px; overflow-y: auto;"></div>
                    </div>
                </form>
                <div class="mt-4">
                    <button type="button" id="calcBtn" class="btn text-white fw-bold px-4 py-2 rounded-1 shadow-sm" style="background-color: #F97316; border: none; font-size: 0.9rem;">CALCULATE RATE</button>
                    <div id="calcResult" class="mt-3 text-white fw-bold d-none p-3 rounded" style="background: rgba(255,255,255,0.1); display: inline-block; width: fit-content; margin: 0 auto;"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- What We Offer Section -->
    <section class="container py-5 my-5">
        <h4 class="fw-bold mb-4">WHAT WE OFFER?</h4>
        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-4 d-flex">
                <div class="card text-white border-0 p-4 rounded-3 flex-fill text-start w-100" style="background-color: #294084; box-shadow: 0 10px 20px rgba(41, 64, 132, 0.2);">
                    <h6 class="fw-bold mb-3">Shipment Booking</h6>
                    <p class="mb-0" style="font-size: 0.85rem; line-height: 1.6;">Send packages faster with a simple and convenient booking process. Manage delivery details, schedule shipments, and handle multiple cargo requests in one seamless platform.</p>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="col-md-4 d-flex">
                <div class="card text-white border-0 p-4 rounded-3 flex-fill text-start w-100" style="background: linear-gradient(135deg, #3B82F6, #2563EB); box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);">
                    <h6 class="fw-bold mb-3">Live Tracking</h6>
                    <p class="mb-0" style="font-size: 0.85rem; line-height: 1.6;">Stay updated with your shipments anytime, anywhere through accurate real-time cargo tracking. Know exactly where your package is and enjoy a more reliable and stress-free delivery experience.</p>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="col-md-4 d-flex">
                <div class="card text-white border-0 p-4 rounded-3 flex-fill text-start w-100" style="background-color: #F97316; box-shadow: 0 10px 20px rgba(249, 115, 22, 0.2);">
                    <h6 class="fw-bold mb-3">Admin Control</h6>
                    <p class="mb-0" style="font-size: 0.85rem; line-height: 1.6;">Experience smooth and organized cargo operations with centralized monitoring and instant delivery updates. Our system helps ensure faster service, better coordination, and improved customer satisfaction.</p>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Predictive text data
        const predefinedLocations = [
            "Philippines", "Philadelphia, USA", "Phnom Penh, Cambodia",
            "New York, USA", "New Jersey, USA", "New Delhi, India",
            "Los Angeles, USA", "London, UK", "Liverpool, UK",
            "Manila, Philippines", "Manchester, UK", "Miami, USA",
            "Tokyo, Japan", "Toronto, Canada", "Taipei, Taiwan",
            "Shanghai, China", "Shenzhen, China", "Singapore",
            "Sydney, Australia", "San Francisco, USA", "Seattle, USA",
            "Hong Kong, China", "Hamburg, Germany", "Houston, USA",
            "Dubai, UAE", "Durban, South Africa", "Doha, Qatar"
        ];

        function setupAutocomplete(inputId, suggestionsId) {
            const input = document.getElementById(inputId);
            const suggestionsBox = document.getElementById(suggestionsId);
            let currentFocus = -1;

            input.addEventListener('input', function() {
                const val = this.value;
                suggestionsBox.innerHTML = '';
                if (!val) {
                    suggestionsBox.classList.add('d-none');
                    return;
                }
                currentFocus = -1;
                suggestionsBox.classList.remove('d-none');

                const matches = predefinedLocations.filter(loc => loc.toLowerCase().includes(val.toLowerCase()));

                if(matches.length === 0) {
                    suggestionsBox.classList.add('d-none');
                    return;
                }

                matches.forEach(match => {
                    const item = document.createElement('div');
                    item.className = 'list-group-item list-group-item-action text-dark py-2';
                    item.style.fontSize = '0.85rem';
                    item.style.cursor = 'pointer';
                    
                    // Highlight matching part
                    const matchIndex = match.toLowerCase().indexOf(val.toLowerCase());
                    if (matchIndex >= 0) {
                        item.innerHTML = match.substring(0, matchIndex) + 
                                         "<strong class='text-primary'>" + match.substring(matchIndex, matchIndex + val.length) + "</strong>" + 
                                         match.substring(matchIndex + val.length);
                    } else {
                        item.textContent = match;
                    }

                    item.addEventListener('click', function(e) {
                        input.value = match;
                        suggestionsBox.innerHTML = '';
                        suggestionsBox.classList.add('d-none');
                    });
                    suggestionsBox.appendChild(item);
                });
            });

            // Keyboard navigation
            input.addEventListener('keydown', function(e) {
                let items = suggestionsBox.getElementsByTagName('div');
                if (e.keyCode == 40) { // DOWN
                    currentFocus++;
                    addActive(items);
                } else if (e.keyCode == 38) { // UP
                    currentFocus--;
                    addActive(items);
                } else if (e.keyCode == 13) { // ENTER
                    e.preventDefault();
                    if (currentFocus > -1 && items.length > 0) {
                        items[currentFocus].click();
                    }
                }
            });

            function addActive(items) {
                if (!items || items.length === 0) return false;
                removeActive(items);
                if (currentFocus >= items.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (items.length - 1);
                items[currentFocus].classList.add('active', 'bg-primary', 'text-white');
                items[currentFocus].classList.remove('text-dark');
                
                // Adjust highlight colors
                const st = items[currentFocus].querySelector('strong');
                if(st) { st.classList.remove('text-primary'); st.classList.add('text-warning'); }
            }

            function removeActive(items) {
                for (let i = 0; i < items.length; i++) {
                    items[i].classList.remove('active', 'bg-primary', 'text-white');
                    items[i].classList.add('text-dark');
                    const st = items[i].querySelector('strong');
                    if(st) { st.classList.add('text-primary'); st.classList.remove('text-warning'); }
                }
            }

            // Close on outside click
            document.addEventListener('click', function (e) {
                if (e.target !== input) {
                    suggestionsBox.innerHTML = '';
                    suggestionsBox.classList.add('d-none');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupAutocomplete('calcOrigin', 'originSuggestions');
            setupAutocomplete('calcDestination', 'destinationSuggestions');

            // Shipping Calculator Logic
            document.getElementById('calcBtn').addEventListener('click', function() {
                const origin = document.getElementById('calcOrigin').value.trim();
                const weight = parseFloat(document.getElementById('calcWeight').value);
                const destination = document.getElementById('calcDestination').value.trim();
                const resultDiv = document.getElementById('calcResult');

                if (!origin || !weight || !destination) {
                    resultDiv.className = 'mt-3 text-warning fw-bold d-block p-3 rounded';
                    resultDiv.textContent = 'Please fill in all fields to calculate the rate.';
                    return;
                }

                // Mock fixed rate logic
                const ratePerKg = 15.5; 
                const total = weight * ratePerKg;
                
                resultDiv.className = 'mt-3 text-white fw-bold d-block p-3 rounded';
                resultDiv.innerHTML = `Estimated Cost: <span style="color: #F97316;">$${total.toFixed(2)}</span>`;
            });
        });
    </script>


<?php include 'includes/footer.php'; ?>