// c:\Users\ADMIN\Downloads\CODES\CargoConnect\main.js

import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

init();

function init() {
    const container = document.getElementById('canvas-container');
    if (!container) return; // Exit if DOM element not found

    // SCENE
    const scene = new THREE.Scene();

    // CAMERA
    const width = container.clientWidth || 800; // fallback width
    const height = container.clientHeight || 400; // fallback height
    const camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 100);
    camera.position.set(0, 1.5, 7.5);

    // RENDERER
    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.setSize(width, height);
    renderer.shadowMap.enabled = true; // enable shadows
    renderer.shadowMap.type = THREE.PCFSoftShadowMap; 
    container.appendChild(renderer.domElement);

    // LIGHTS
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 2.0);
    directionalLight.position.set(5, 10, 7.5);
    directionalLight.castShadow = true;
    scene.add(directionalLight);

    // Orange Glow Light (Left/Back)
    const orangeLight = new THREE.PointLight(0xF26E21, 5, 20); 
    orangeLight.position.set(-3, 1, -3);
    scene.add(orangeLight);

    // Blue Glow Light (Right/Front)
    const blueLight = new THREE.PointLight(0x2A5BA1, 5, 20); 
    blueLight.position.set(3, 1, 3);
    scene.add(blueLight);

    // CONTROLS
    // Orbit controls allow user to click and drag to view from all angles
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableZoom = false; // Disable zoom to keep it neat inside HTML layout
    controls.enablePan = false;  // Disable panning
    controls.autoRotate = true;  // Auto-rotating slowly
    controls.autoRotateSpeed = 1.0; 
    
    // MODEL LOADER
    const loader = new GLTFLoader();
    
    // We attempt to load 'cargo connect.glb'. If it fails, we render a placeholder metallic box.
    loader.load(
        'CARGO CONNECT.glb', 
        (gltf) => {
            const modelInstance = gltf.scene;
            
            // Enable shadows for the loaded model meshes
            modelInstance.traverse((node) => {
                if (node.isMesh) {
                    node.castShadow = true;
                    node.receiveShadow = true;
                }
            });

            // Center the model roughly
            const box = new THREE.Box3().setFromObject(modelInstance);
            const size = box.getSize(new THREE.Vector3());
            const center = box.getCenter(new THREE.Vector3());
            
            // Compute scaling to fit within the viewport comfortably
            const maxDim = Math.max(size.x, size.y, size.z);
            let scaleFactor = 6.5 / maxDim; 
            modelInstance.scale.set(scaleFactor, scaleFactor, scaleFactor);
            
            modelInstance.position.x = -center.x * scaleFactor;
            modelInstance.position.y = -center.y * scaleFactor;
            modelInstance.position.z = -center.z * scaleFactor;
            
            scene.add(modelInstance);
        },
        undefined, // onProgress omitted
        (error) => {
            console.warn('Failed to load CARGO CONNECT.glb. Using fallback placeholder geometry.', error);
            createFallbackBox(scene);
        }
    );

    // RESIZE LISTENER
    window.addEventListener('resize', onWindowResize, false);

    function onWindowResize() {
        if (!container) return;
        const newWidth = container.clientWidth;
        const newHeight = container.clientHeight;
        
        camera.aspect = newWidth / newHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(newWidth, newHeight);
    }

    // ANIMATION LOOP
    function animate() {
        requestAnimationFrame(animate);
        controls.update(); // required if controls.enableDamping or controls.autoRotate are set
        renderer.render(scene, camera);
    }
    animate();
}

function createFallbackBox(scene) {
    // A placeholder container shape (metallic silver/grey texture)
    const geometry = new THREE.BoxGeometry(4.5, 1.8, 1.8);
    const material = new THREE.MeshStandardMaterial({ 
        color: 0x999999, // Silver/Grey
        metalness: 0.8,  // Realistic metallic
        roughness: 0.3   // Slight gloss
    });
    const cube = new THREE.Mesh(geometry, material);
    cube.castShadow = true;
    cube.receiveShadow = true;
    
    // Add some simple segments to look more like a container
    const edges = new THREE.EdgesGeometry(geometry);
    const lineMaterial = new THREE.LineBasicMaterial({ color: 0x333333, linewidth: 2 });
    const lines = new THREE.LineSegments(edges, lineMaterial);
    cube.add(lines);

    scene.add(cube);
}
