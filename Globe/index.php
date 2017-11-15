<!DOCTYPE html>
<html>
	<head>
		<title>Globoscope</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" href="css/style.css?d=<?php echo time(); ?>"> 	
		<link rel="stylesheet" href="css/sideBarStyle.css?d=<?php echo time(); ?>"> 	
		<link rel="stylesheet" href="css/searchBar.css?d=<?php echo time(); ?>"> 	
		<link rel="stylesheet" href="css/searchResults.css?d=<?php echo time(); ?>"> 	
		<link rel="stylesheet" href="css/helpStyle.css?d=<?php echo time(); ?>">
		<link rel="stylesheet" href="css/progressBar.css?d=<?php echo time(); ?>"> 	

	</head>

	<body>
	<span><img id="helpButton" src="images/questionMark.png"></span>
	<div id="Help">
		<div id="box">
			<div id="header">
				<h3 class="aide" a href="">Help</h3>
			</div>
			<p id="closeHelp">X</p>
			<div id="direction">
				<img src="images/arrowKeys.png" height="50" width="80" alt="touches directions" />
				<p id="aideDeplacementSouris"> pour se déplacer verticalement et horizontalement ou maintenez la souris puis relâchez</p>
			</div>
			<div id="Aidereste" class="Aide">
				<p id="aideZoom">+ et - : pour zoomer et dézoomer</p>
				<hr></hr>
				<p id="aideAgrandirImage">Cliquez sur l'image pour l'agrandir et afficher ses informations</p>
				<hr></hr>
				<p id="aideRecherche">Ecrivez le pseudo dans la barre de recherche afin d'afficher votre image</p>
			</div>
			<div id="languageSelect">
				<span id="FR" onclick="aideFr()">FR</span>/<span id="EN" onclick="aideAng()">EN</span>
			</div>
		</div>
	</div>
	
	<div id="sideBar">
		<p id="closeSideBar">X</p>
		<div class ="loader" id="imageLoader"></div>
		<div id="onClickDetails" >
				<img id="childImage">
				<span id="separator"></span>
				<div id="description">
					<p id="childPseudo"></p>
					<p id="childCitation"></p>
					<p id="childRight"></p>
				</div>
		</div>
		<div id="onSearchDetails" class ="flexContainer">
		<h1>Resultat de la recherche</h1>
		
		</div>
	</div>
	
	<span><img id="showSearch" src = "images/searchIcon.png"></span>

	<div id="searchBar">
		<input type="text" id="searchText">                                        
		<span id="searchButton">Recherche</span>
		</input>
		<div id="onDynamicSearch">

		</div>
	</div>



	<script src= "js/three.min.js"></script>
	<script src= "js/three/controls/OrbitControls.js"></script>
	<script src="js/three/loaders/DDSLoader.js"></script>
	<script src ="js/loader.js"></script>
	<script src="js/searchChild.js"></script>
	<script src="js/childClicked.js"></script>
	<script src="js/Tween.js"></script>

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
		var camSpherical = new THREE.Spherical();
		var camPos = new THREE.Vector3();
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
		var showSearchButton = document.getElementById('showSearch');
		showSearchButton.onclick =  showSearch;

		var SearchBox = document.getElementById('searchBar');
		SearchBox.style.display = 'none';

		var xmlSearch;
		var SearchTextBox = document.getElementById('searchText');
		SearchTextBox.oninput = OnWriting;

		SearchTextBox.onfocus = function()
		{
			xmlSearch = new XMLHttpRequest();
		}

		var SearchButton = document.getElementById('searchButton');
		SearchButton.onclick = showSearchResults;

		var dynamicSearchResult = document.getElementById('onDynamicSearch');
		dynamicSearchResult.style.display = 'none';

		/**fin de la barre de recherche */

		/**tout les composant concernant le sidebar */
		var sideBar = document.getElementById('sideBar');
		var closeSideBar = document.getElementById('closeSideBar');
		var onClickDetails = document.getElementById('onClickDetails');
		var onSearchDetails = document.getElementById('onSearchDetails');

		var childImage =document.getElementById("childImage");
		var childPseudo = document.getElementById("childPseudo");
		var childCitation = document.getElementById("childCitation");
		var childRight = document.getElementById("childRight");

		childImage.onload = showOnClickDetails;
		closeSideBar.onclick = hideSideBar;
		sideBar.style.display='none';
		
		/*Loader pour l'image*/
		var imageLoader =  document.getElementById("imageLoader");
		imageLoader.className="loader";
		/**Fin des composant de la side Bar */

		/* Div d'aide */
		var helpDiv = document.getElementById('Help');
		helpDiv.style.display = 'none';

		var helpButton = document.getElementById('helpButton');
		helpButton.style.display = 'block';
		helpButton.onclick = showHelp;

		var closeHelpDiv = document.getElementById('closeHelp');
		closeHelpDiv.onclick= closeHelp;

		/*Loader*/

		window.addEventListener('resize',onWindowResize,false);
		window.addEventListener("keydown", closeSideBarEsc);

		document.onmousedown = onMouseClick;
		document.onmousemove = onMouseMove;
		document.body.appendChild(container);


		container.appendChild(renderer.domElement);

		loadData(scene,container);
		animate();
		//https://medium.com/@lachlantweedie/animation-in-three-js-using-tween-js-with-examples-c598a19b1263
		function animateVector3(vectorToAnimate, target, options)
		{

			options = options || {};

			// get targets from options or set to defaults
			var to = target || THREE.Vector3(),
				easing = options.easing || TWEEN.Easing.Quadratic.In,
				duration = options.duration || 2000;

			// create the tween
			var tweenVector3 = new TWEEN.Tween(vectorToAnimate)
				.to({ x: to.x, y: to.y, z: to.z, }, duration)
				.easing(easing)
				.onUpdate(function(d) {
					if(options.update){ 
						options.update(d);
					}
				})
				.onComplete(function(){
				if(options.callback) options.callback();
				});

			// start the tween
			tweenVector3.start();

			// return the tween in case we want to manipulate it later on
			return tweenVector3;

		}

		function aideFr()
		{
            var deplacementSouris = document.getElementById('aideDeplacementSouris');
			deplacementSouris.textContent = "pour se déplacer verticalement et horizontalement ou maintenez la souris puis relâchez";
			
            var aideZoom = document.getElementById('aideZoom');
			aideZoom.textContent = "+ et - : pour zoomer et dézoomer";
			
            var aideAgrandirImage = document.getElementById('aideAgrandirImage');
			aideAgrandirImage.textContent = "Cliquez sur l'image pour l'agrandir et afficher ses informations";
			
            var aideRecherche = document.getElementById('aideRecherche');
            aideRecherche.textContent = "Ecrivez le pseudo dans la barre de recherche afin d'afficher votre image";
        }
		function aideAng()
		{
            var deplacementSouris = document.getElementById('aideDeplacementSouris');
			deplacementSouris.textContent = "Drag the mouse arround to explore the globe";
			
            var aideZoom = document.getElementById('aideZoom');
			aideZoom.textContent = "+ and - : to zoom in and zoom out";
			
            var aideAgrandirImage = document.getElementById('aideAgrandirImage');
			aideAgrandirImage.textContent = "Click on the picture to enlarge and display the informations";
			
            var aideRecherche = document.getElementById('aideRecherche');
            aideRecherche.textContent = "Write the nickname in the research tool to find your picture";            
        }
		function closeSideBarEsc(e)
		{
			if(sideBar.style.display != 'none')
			{
				if(e.keyCode == 27 )
				{
					hideSideBar();
				}
			}
			if(helpDiv.style.display != 'none')
			{
				if(e.keyCode == 27 )
				{
					closeHelp();
				}
			}
			if(	SearchBox.style.display != 'none')
			{
				if(e.keyCode == 27 )
				{
					hideSearch();
				}				
			}
		}
		function showSearch()
		{
			SearchBox.style.display='flex';
			showSearchButton.style.display ='none';
			SearchBox.className = "w3-animate-top";
			SearchTextBox.focus();
		}
		function hideSearch()
		{
			SearchBox.style.display = 'none';
			showSearchButton.style.display = 'block';
		}
		function showHelp()
		{
			helpDiv.style.display = 'block';
			helpDiv.className = "w3-animate-left";

			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))
			{
				helpButton.style.display = 'none';
				SearchBox.style.display = 'none';
				showSearchButton.style.display ='none';
			}
		}
		function closeHelp()
		{
			helpDiv.style.display = 'none';
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
			{
				helpButton.style.display = 'block';
				showSearchButton.style.display ='block';
			}			
		}
		function showSearchResults()
		{
			searchChild(camera,scene);
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
			{
				SearchBox.style.display = 'none';
				showSearchButton.style.display = 'none';
				helpButton.style.display = 'none';			
			}
			else
			{
				SearchBox.style.display = 'none';
				showSearchButton.style.display = 'block';
			}
			
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
			onClickDetails.style.display = 'flex';

			imageLoader.style.display = 'none';
			var nodes = onClickDetails.childNodes;
			var i= 0;
			for (i = 0; i < nodes.length;i++)
			{
				if(nodes[i].style != null)
					nodes[i].style.display = "flex";
			}
		}
		function hideSideBar()
		{
			sideBar.style.display='none';
			sideBar.className = "";
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
			{
				document.onmousedown = onMouseClick;
				helpButton.style.display = 'block';
				showSearchButton.style.display = 'block';
			}
		}
		function showSideBar()
		{
			imageLoader.style.display = 'block';
			

			sideBar.className = "w3-animate-right";
			sideBar.style.display='block';

			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
			{
				document.onmousedown = null;
				helpButton.style.display = 'none';
				showSearchButton.style.display = 'none';
			}

		}
		function onWindowResize()
		{
			rendererW = window.innerWidth;
			rendererH = window.innerHeight;
			camera.aspect =  rendererW/rendererH;
			camera.updateProjectionMatrix();


			renderer.setSize(rendererW,rendererH );	
		}

		function OnWriting()
		{
			var objJSON,dbParam,xmlhttp,myObj;
			//les paramètres a passer dans la requête SQL
			//SearchTextBox => input de la barre de recherche
			objJSON = {"Pseudo":SearchTextBox.value }
			dbParam = JSON.stringify(objJSON);
			
			var div,img,pseudo,sep;
			dynamicSearchResult.innerHTML = "";

			
			xmlSearch.abort();

			xmlSearch.onreadystatechange = function()
			{
				if(this.readyState==4 && this.status == 0)
				{
					console.log("canceled "+SearchTextBox.value);
				}
				if(this.readyState ==4 && this.status==200)
				{
					dynamicSearchResult.style.display ='flex';

					///Afficher un tableau avec tout les résultats
					if(this.responseText != "")
					{
						myObj = JSON.parse(this.responseText);
						//pour partir d'une div vide
						//tableau de résultat de la recherche/requete SQL
						//https://stackoverflow.com/questions/15860683/onclick-event-in-a-for-loop						
						if(SearchTextBox.value != "")
						{						
							var total = myObj.length;

							if(total > 0)
							{
								for(var i = 0; i < total; i++)
								(function(i)
								{
									if(myObj[i].ImageOK != 0)
									{		

										div = document.createElement('div');
										sep = document.createElement('span');
										sep.id = "separator";
										img = document.createElement('img');
										img.src =  "images/64-64/"+myObj[i].IDImage+".png";
										img.onclick = function()
										{
											var plane = scene.getObjectByName( myObj[i].IDPlace );


											var target = new THREE.Vector3((plane.position.x) * (-1) * 1.1  ,(plane.position.y) * (-1) * 1.1 ,plane.position.z * 1.1); // create on init
											
											//https://medium.com/@lachlantweedie/animation-in-three-js-using-tween-js-with-examples-c598a19b1263
											animateVector3(camera.position, target, {
												
												duration: 2000, 
												
												easing : TWEEN.Easing.Cubic.InOut,
											});
											onImageClick(myObj[i].IDPlace);
											hideSearch();
										}
										pseudo = document.createElement('p');
										pseudo.innerHTML =  myObj[i].Pseudo;

										div.appendChild(img);
										div.appendChild(pseudo);
										dynamicSearchResult.appendChild(sep);
										dynamicSearchResult.appendChild(div);

									}
								})(i);
							}
							else
							{
								dynamicSearchResult.style.display ='none';
							}
						}
					}					
				}
  		 	 }
				
			if(	SearchTextBox.value != "")
			{
				xmlSearch.open("POST", "searchChild.php", true);
				xmlSearch.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlSearch.send("x=" + dbParam); 
			}
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
						if(intersects[0].object.type =="VRAI")
						{
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
			TWEEN.update();
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


	</script>

	</body>
</html>