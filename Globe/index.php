<!DOCTYPE html>
<html>
	<head>
		<title>Globoscope</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

		<style>
				body { margin: 0; }
				canvas { width: 100%; height: 100% }
			</style>
	</head>

	<body>
	<p id="demo" ></p>

	<script src= "js/three.min.js"></script>
	<script src= "js/three/controls/OrbitControls.js"></script>

	<script src= "js/point.js"></script>
	<script src= "js/square.js"></script>
	<script src= "js/meridian.js"></script>
	<script src ="js/loader.js">
	</script>
	<script>
			var scene = new THREE.Scene();
			var camera = new THREE.PerspectiveCamera( 75, window.innerWidth/window.innerHeight, 0.1, 20000 );

			var renderer = new THREE.WebGLRenderer();

			var controls =  new THREE.OrbitControls(camera,renderer.domElement);
			controls.enablePan = false;
			renderer.setSize( window.innerWidth, window.innerHeight );
			document.body.appendChild( renderer.domElement );

			camera.position.z = 7000;
			loadData(scene);
			animate();

			function animate() {
				requestAnimationFrame( animate );
				controls.update();
				render();
			}
			function render() {
				renderer.render( scene, camera );
			}


	</script>
	</body>
</html>