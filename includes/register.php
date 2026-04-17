<?php
// includes/register.php - Standalone Register Page to match the custom design
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | CargoConnect</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --cc-blue: #1E3A8A;
            --cc-orange: #F97316;
            --cc-navy: #0b1324;
            --cc-light-bg: #f2f5f8;
        }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: var(--cc-light-bg);
            height: 100vh;
            display: flex;
        }
        
        /* Left Pane Styling */
        .left-pane {
            width: 50%;
            background-color: #ffffff;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Top part of left pane */
        .left-content {
            padding: 3rem 4rem;
            flex-grow: 1;
            z-index: 10;
        }

        /* Dark bottom container in left pane */
        .left-dark-bottom {
            background-color: var(--cc-navy);
            height: 35%;
            width: 100%;
            position: absolute;
            bottom: 0;
            left: 0;
            border-top-left-radius: 40px;
            border-top-right-radius: 40px;
            z-index: 1;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            margin-bottom: 4rem;
        }

        .hero-title {
            font-size: 3.2rem;
            line-height: 1.2;
            letter-spacing: -1px;
            margin-bottom: 2rem;
        }

        .font-light { font-weight: 300; color: #333; }
        .font-bold { font-weight: 700; }
        .text-blue { color: var(--cc-blue); }
        .text-orange { color: var(--cc-orange); }

        /* 3D Model Area */
        .model-container {
            position: absolute;
            bottom: 10%;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 60%;
            z-index: 5;
        }

        #canvas-container {
            width: 100%;
            height: 100%;
            cursor: grab;
        }
        #canvas-container:active { cursor: grabbing; }

        /* Right Pane Styling */
        .right-pane {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--cc-light-bg);
            padding: 2rem;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }

        /* Tab Switcher */
        .tab-switcher {
            background-color: #e2e6ea;
            border-radius: 6px;
            padding: 4px;
            display: flex;
            margin-bottom: 2.5rem;
        }
        .tab-btn {
            flex: 1;
            text-align: center;
            padding: 10px 0;
            font-weight: 700;
            font-size: 0.9rem;
            color: #555;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }
        .tab-btn.active {
            background-color: #ffffff;
            color: #000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);    
        }
        .tab-btn:hover:not(.active) {
            color: #111;
        }

        /* Form styling */
        .form-label {
            font-weight: 700;
            font-size: 0.95rem;
            color: #222;
        }
        
        .form-control-custom {
            display: block;
            width: 100%;
            padding: 12px 16px;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #333;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: var(--cc-blue);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }

        .password-container {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            cursor: pointer;
        }

        .btn-orange {
            background-color: var(--cc-orange);
            color: white;
            padding: 14px;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            border-radius: 6px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
            transition: all 0.3s;
        }
        .btn-orange:hover {
            background-color: #e06812;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(249, 115, 22, 0.3);
        }

        .bottom-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #111;
        }
        .bottom-text a {
            color: #3b82f6;
            text-decoration: none;
        }
        .bottom-text a:hover { text-decoration: underline; }

        /* Responsive handling */
        @media (max-width: 991px) {
            body { flex-direction: column; height: auto; min-height: 100vh; }
            .left-pane, .right-pane { width: 100%; }
            .left-pane { min-height: 500px; }
            .right-pane { padding: 4rem 2rem; }
        }
    </style>
</head>
<body>

    <!-- Left Pane with Graphics -->
    <div class="left-pane">
        <div class="left-content">
            <!-- Brand Logo -->
            <a href="../index.php" class="brand-logo">
                <!-- Custom SVG matching the image logo -->
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                    <rect x="2" y="6" width="6" height="20" fill="#F97316"/>
                    <path d="M11 6 H28 V26 H11 V20 H22 V12 H11 V6 Z" fill="#F97316"/>
                </svg>
                <span class="brand-text" style="color: #1E3A8A; font-weight: 800; font-size: 1.8rem; text-shadow: none;">Cargo<span style="color: #F97316;">Connect.</span></span>
            </a>

            <!-- Headline -->
            <div class="hero-title">
                <span class="font-light">Streamline Your </span><span class="font-light text-blue">Shipping,</span><br>
                <span class="font-bold text-orange">Track</span> <span class="font-bold text-dark">with Confidence</span>
            </div>
            
            <!-- SVG Swoosh matching the blue line in image -->
            <svg width="100%" height="300" style="position: absolute; top: 50%; left: 0; pointer-events: none; z-index: 6; overflow: visible;">
                <path d="M 40,150 Q 80,150 100,250 T 250,280 T 400,250 T 450,150" fill="none" stroke="#3b82f6" stroke-width="2" marker-start="url(#arrow-start)" marker-end="url(#arrow-end)" />
                <defs>
                    <marker id="arrow-start" markerWidth="10" markerHeight="10" refX="2" refY="5" orient="auto">
                        <path d="M 8 1 L 2 5 L 8 9" fill="none" stroke="#3b82f6" stroke-width="2"/>
                    </marker>
                    <marker id="arrow-end" markerWidth="10" markerHeight="10" refX="8" refY="5" orient="auto">
                        <path d="M 2 1 L 8 5 L 2 9" fill="none" stroke="#3b82f6" stroke-width="2"/>
                        <rect x="7" y="3" width="3" height="4" fill="none" stroke="#3b82f6" stroke-width="1"/>
                    </marker>
                </defs>
            </svg>
        </div>
        
        <div class="left-dark-bottom"></div>
        
        <!-- 3D Model -->
        <div class="model-container">
            <div id="canvas-container"></div>
        </div>
    </div>

    <!-- Right Pane with Form -->
    <div class="right-pane">
        <div class="login-box">
            <!-- Tabs -->
            <div class="tab-switcher">
                <a href="login.php" class="tab-btn">LOGIN</a>
                <div class="tab-btn active">REGISTER</div>
            </div>

            <!-- Form -->
            <form action="../dashboard.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control-custom" placeholder="Name" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control-custom" placeholder="Email Address" required>
                </div>
                
                <!-- Notice how the image had Email Address as placeholder for Phone Number as a likely design typo, but I've kept it as in the picture per your request! -->
                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-control-custom" placeholder="Email Address" required>
                </div>

                <div class="mb-3">
                    <label class="form-label mb-1">Password</label>
                    <div class="password-container">
                        <input type="password" id="passwordField" class="form-control-custom" placeholder="Password" required>
                        <i class="fa-solid fa-eye-slash password-toggle" onclick="togglePassword('passwordField', this)"></i>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label mb-1">Confirm Password</label>
                    <div class="password-container">
                        <input type="password" id="confirmPasswordField" class="form-control-custom" placeholder="Confirm Password" required>
                        <i class="fa-solid fa-eye-slash password-toggle" onclick="togglePassword('confirmPasswordField', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn-orange">REGISTER</button>

                <div class="bottom-text">
                    Already have an account? <a href="login.php">Log in</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Three.js Setup (Import Maps) -->
    <script type="importmap">
    {
      "imports": {
        "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
        "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
      }
    }
    </script>
    
    <!-- Inline main logic for the 3D container -->
    <script type="module">
        import * as THREE from 'three';
        import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

        const container = document.getElementById('canvas-container');
        if (container) {
            const scene = new THREE.Scene();
            const width = container.clientWidth;
            const height = container.clientHeight;
            const camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 100);
            camera.position.set(0, 1.5, 7.5);

            const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
            renderer.setPixelRatio(window.devicePixelRatio);
            renderer.setSize(width, height);
            container.appendChild(renderer.domElement);

            const ambientLight = new THREE.AmbientLight(0xffffff, 0.9);
            scene.add(ambientLight);

            const directionalLight = new THREE.DirectionalLight(0xffffff, 2.5);
            directionalLight.position.set(5, 10, 7.5);
            scene.add(directionalLight);

            // Orange and Blue glowing lights matching the image
            const orangeLight = new THREE.PointLight(0xF26E21, 15, 20); 
            orangeLight.position.set(-2, 0, 1);
            scene.add(orangeLight);

            const blueLight = new THREE.PointLight(0x2A5BA1, 5, 20); 
            blueLight.position.set(3, 1, 3);
            scene.add(blueLight);

            const controls = new OrbitControls(camera, renderer.domElement);
            controls.enableZoom = false;
            controls.enablePan = false;
            controls.autoRotate = true;
            controls.autoRotateSpeed = 2.0;

            const loader = new GLTFLoader();
            loader.load('../CARGO CONNECT.glb', (gltf) => {
                const model = gltf.scene;
                const box = new THREE.Box3().setFromObject(model);
                const size = box.getSize(new THREE.Vector3());
                const center = box.getCenter(new THREE.Vector3());
                
                const maxDim = Math.max(size.x, size.y, size.z);
                let scaleFactor = 6 / maxDim; 
                model.scale.set(scaleFactor, scaleFactor, scaleFactor);
                model.position.set(-center.x * scaleFactor, -center.y * scaleFactor, -center.z * scaleFactor);
                scene.add(model);
            });

            window.addEventListener('resize', () => {
                if(!container) return;
                camera.aspect = container.clientWidth / container.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(container.clientWidth, container.clientHeight);
            });

            function animate() {
                requestAnimationFrame(animate);
                controls.update();
                renderer.render(scene, camera);
            }
            animate();
        }
    </script>

    <!-- UI Scripts -->
    <script>
        function togglePassword(fieldId, iconElement) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                iconElement.classList.remove('fa-eye-slash');
                iconElement.classList.add('fa-eye');
            } else {
                field.type = 'password';
                iconElement.classList.remove('fa-eye');
                iconElement.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
