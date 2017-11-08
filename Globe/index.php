<!DOCTYPE html>
<html>
	<head>
		<title>Globoscope</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<meta http-equiv="Cache-control" content="no-cache">
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" href="css/style.css?d=<?php echo time(); ?>"> 	
		<link rel="stylesheet" href="css/sideBarStyle.css?d=<?php echo time(); ?>"> 	
		<link rel="stylesheet" href="css/searchBar.css?d=<?php echo time(); ?>"> 	
		<link rel="stylesheet" href="css/searchResults.css?d=<?php echo time(); ?>"> 	

		
	</head>

	<body>
	<div id="sideBar">
		<p id="closeSideBar">X</p>
		<div class ="loader" id="imageLoader"></div>
		<div id="onClickDetails" >
				<img id="childImage">
				<hr id="separator">
				<p id="childPseudo"></p>
				<p id="childCitation"></p>
		</div>
		<div id="onSearchDetails" class ="flexContainer">
		<h1>Resultat de la recherche</h1>
		
		</div>
	</div>
	

	<div id="searchBar">
		<input type="text" id="searchText">                                        
		<span id="searchButton">Recherche</span>
		</input>
	</div>



	<script src= "js/three.min.js"></script>
	<script src= "js/three/controls/OrbitControls.js"></script>
	<script src="js/three/loaders/DDSLoader.js"></script>
	<script src ="js/loader.js"></script>
	<script src="js/searchChild.js"></script>
	<script src="js/childClicked.js"></script>

	<script type="application/x-glsl" id="sky-vertex">  
		varying vec2 vUV;

		void main() {  
		vUV = uv;
		vec4 pos = vec4(position, 1.0);
		gl_Position = projectionMatrix * modelViewMatrix * pos;
		}
	</script>

	<script type="application/x-glsl" id="sky-fragment">  
		uniform sampler2D texture;  
		varying vec2 vUV;

		void main() {  
		vec4 sample = texture2D(texture, vUV);
		gl_FragColor = vec4(sample.xyz, sample.w);
		}
	</script>  

	<script>

		/**Initialisation THREE JS */
		var scene = new THREE.Scene();
		var rendererW = window.innerWidth;
		var rendererH = window.innerHeight;
		var camera = new THREE.PerspectiveCamera( 75, rendererW/rendererH, 0.1, 50000 );
		var renderer = new THREE.WebGLRenderer();
		var controls =  new THREE.OrbitControls(camera,renderer.domElement);
		var raycaster = new THREE.Raycaster();
		var mouse = new THREE.Vector2();
		var data = [];

		/*désactiver le déplacement pour le globe*/
		controls.enablePan = false;
		controls.enableDamping = true;
        controls.minDistance = 2850;
        controls.maxDistance = 5000;
					
		/*Conteneur des détails de l'image recherchée //Onclick */
		camera.position.z = 7000;
		
		/*
		var axisHelper = new THREE.AxisHelper( 10000 );
		scene.add( axisHelper );
		*/

		/*Ajouer les élément principaux*/
		renderer.setSize( rendererW,rendererH);

		var geometry = new THREE.SphereGeometry(10000, 60, 40);  

		/*		SkyBox 
			http://www.ianww.com/blog/2014/02/17/making-a-skydome-in-three-dot-js/
		*/
		var uniforms = {  
		texture: { type: 't', value: THREE.ImageUtils.loadTexture('images/MilkyWay.jpg') }
		};

		var material = new THREE.ShaderMaterial( {  
		uniforms:       uniforms,
		vertexShader:   document.getElementById('sky-vertex').textContent,
		fragmentShader: document.getElementById('sky-fragment').textContent
		});

		skyBox = new THREE.Mesh(geometry, material);  
		skyBox.scale.set(-1, 1, 1);  
		skyBox.eulerOrder = 'XZY';  
		skyBox.renderDepth = 1000.0;  
		scene.add(skyBox);  
	

		/**Fin initialisation three JS */
		var container = document.createElement('div');
		container.id = "CanvContainer";

		/**Tout les composant concernant la barre de recherche */
		var SearchBox = document.getElementById('searchBar');

		var SearchTextBox = document.getElementById('searchText');

		var SearchButton = document.getElementById('searchButton');
		SearchButton.onclick = showSearchResults;
		/**fin de la barre de recherche */

		/**tout les composant concernant le sidebar */
		var sideBar = document.getElementById('sideBar');
		var closeSideBar = document.getElementById('closeSideBar');
		var onClickDetails = document.getElementById('onClickDetails');
		var onSearchDetails = document.getElementById('onSearchDetails');

		var childImage =document.getElementById("childImage");
		var childPseudo = document.getElementById("childPseudo");
		var childCitation = document.getElementById("childCitation");
		
		childImage.onload = showOnClickDetails;
		closeSideBar.onclick = hideSideBar;
		sideBar.style.display='none';
		
		/*Loader pour l'image*/
		var imageLoader =  document.getElementById("imageLoader");
		imageLoader.className="loader";
		/**Fin des composant de la side Bar */


		/*Loader*/
		var loader = document.createElement('div');
		loader.className="loader";
		loader.style.display="none";
		document.body.appendChild(loader);

		document.body.appendChild(loader);
	
		window.addEventListener('resize',onWindowResize,false);

		document.onmousedown = onMouseClick;
		document.onmousemove = onMouseMove;
		document.body.appendChild(container);

		container.appendChild(renderer.domElement);

		function showSearchResults()
		{
			searchChild();
			onClickDetails.style.display = 'none';
			onSearchDetails.style.display = 'flex';

			imageLoader.style.display = 'none';
			var nodes = onSearchDetails.childNodes;
			var i = 0;
			for( i = 0; i < nodes.length;i++)
			{
				if(nodes[i].style != null)
					nodes[i].style.display = "block";
			}
		}
		function showOnClickDetails()
		{
			onSearchDetails.style.display = 'none';
			onClickDetails.style.display = 'block';

			imageLoader.style.display = 'none';
			var nodes = onClickDetails.childNodes;
			var i= 0;
			for (i = 0; i < nodes.length;i++)
			{
				if(nodes[i].style != null)
					nodes[i].style.display = "block";
			}
		}
		function hideSideBar()
		{
			sideBar.style.display='none';
			sideBar.className = "";

		}
		function showSideBar()
		{
			imageLoader.style.display = 'block';
			

			sideBar.className = "w3-animate-right";
			sideBar.style.display='block';
		}
		function onWindowResize()
		{
			rendererW = window.innerWidth;
			rendererH = window.innerHeight;
			camera.aspect =  rendererW/rendererH;
			camera.updateProjectionMatrix();


			renderer.setSize(rendererW,rendererH );	
		}

		function onMouseMove(event)
		{
			mouse.x = (event.clientX /rendererW) * 2 -1;
			mouse.y = -(event.clientY / rendererH) * 2 + 1;		
		}
		function onMouseClick( event ) 
		{
			mouse.x = (event.clientX /rendererW) * 2 -1;
			mouse.y = -(event.clientY /rendererH) * 2 + 1;

			switch(event.button)
			{
				case 0:
					// update the picking ray with the camera and mouse position
					raycaster.setFromCamera( mouse, camera );
					
					// calculate objects intersecting the picking ray
					var intersects = raycaster.intersectObjects( scene.children );
				
					if(intersects.length > 0)
					{
						if(intersects[0].object.name != 0 && intersects[0].object.type =="VRAI")
						{
							console.log(intersects[ 0 ].object.name);
							onImageClick(intersects[ 0 ].object.name);
						}
					}
				break;
				case 2:
				break;
				default:
				break;
			}
		}
		function animate() 
		{
			requestAnimationFrame( animate );
			controls.update();
			render();
		}
		function render() 
		{
			
			// update the picking ray with the camera and mouse position
			raycaster.setFromCamera( mouse, camera );	
			// calculate objects intersecting the picking ray
			var intersects = raycaster.intersectObjects( scene.children );		
			if(intersects.length > 0)
			{
				container.style.cursor = "pointer";
			}
			else
			{
				container.style.cursor = "default";
			}
			renderer.render( scene, camera );
		}
		loadData(scene,container,loader);
		animate();

	</script>

	</body>
</html>