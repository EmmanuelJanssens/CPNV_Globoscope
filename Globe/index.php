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
		<link rel="stylesheet" href="css/helpStyle.css?d=<?php echo time(); ?>">
		
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
				<p> pour se déplacer verticalement et horizontalement ou maintenez la souris puis relâchez</p>
			</div>
			<div id="Aidereste" class="Aide">
				<p>+ et - : pour zoomer et dézoomer</p>
				<hr></hr>
				<p>Cliquez sur l'image pour l'agrandir et afficher ses informations</p>
				<hr></hr>
				<p>Ecrivez le pseudo dans la barre de recherche afin d'afficher votre image</p>
			</div>
			<div id="languageSelect">
				<span id="FR">FR</span>/<span id="EN">EN</span>
			</div>
		</div>

	</div>

	
	<div id="sideBar">
		<p id="closeSideBar">X</p>
		<div class ="loader" id="imageLoader"></div>
		<div id="onClickDetails" >
				<img id="childImage">
				<hr id="separator">
				<div id="description">
					<p id="childPseudo"></p>
					<p id="childCitation"></p>
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
		var showSearchButton = document.getElementById('showSearch');
		showSearchButton.onclick =  showSearch;

		var SearchBox = document.getElementById('searchBar');
		SearchBox.style.display = 'none';

		var SearchTextBox = document.getElementById('searchText');
		SearchTextBox.oninput = OnWriting;

		SearchTextBox.onfocus = function(){ console.log("Writing");}

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
		var loader = document.createElement('div');
		loader.className="loader";
		loader.style.display="none";
		document.body.appendChild(loader);

		document.body.appendChild(loader);
		
		var searchMedia =window.matchMedia('@media all and (max-width: 480px)');

		window.addEventListener('resize',onWindowResize,false);

		document.onmousedown = onMouseClick;
		document.onmousemove = onMouseMove;
		document.body.appendChild(container);

		container.appendChild(renderer.domElement);

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
		function mobileButtonDisplays(disp)
		{
			showSearchButton.style.display = disp;
			helpButton.style.display = disp;		
		}
		function showHelp()
		{
			helpDiv.style.display = 'block';
			helpDiv.className = "w3-animate-left";
			helpButton.style.display = 'none';

			console.log(window.innerWidth);
			if(window.innerWidth <= 480)
			{
				mobileButtonDisplays('none');
			}
		}
		function closeHelp()
		{
			helpDiv.style.display = 'none';
			helpButton.style.display = 'block';
			console.log(window.innerWidth);
			if(window.innerWidth <= 480)
			{
				mobileButtonDisplays('block');			
			}			
		}
		function showSearchResults()
		{
			searchChild();

			if(window.innerWidth > 480)
				showSearchButton.style.display = 'block';
			
			SearchBox.style.display = 'none';
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
			showSearchButton.style.display = 'block';
			sideBar.style.display='none';
			sideBar.className = "";
			if(window.innerWidth <= 480)
			{
				mobileButtonDisplays('block');				
			}
		}
		function showSideBar()
		{
			imageLoader.style.display = 'block';
			

			sideBar.className = "w3-animate-right";
			sideBar.style.display='block';

			if(window.innerWidth <= 480)
			{
				mobileButtonDisplays('none');
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

			xmlhttp = new XMLHttpRequest();

			
			var div,img,pseudo,sep;
			dynamicSearchResult.innerHTML = "";


			dynamicSearchResult.style.display ='flex';
			xmlhttp.onreadystatechange = function()
			{
				if(this.readyState ==4 && this.status==200)
				{

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
								for(var i = 0; i < myObj.length; i++)
								(function(i)
								{
									if(myObj[i].ImageOK != 0)
									{		

										div = document.createElement('div');
										sep = document.createElement('hr');
										img = document.createElement('img');
										img.src =  "images/DB/128-128/"+myObj[i].IDImage+".jpg";
										img.onclick = function()
										{
											onImageClick(myObj[i].IDPlace);
											SearchBox.style.display = 'none';
											showSearchButton.style.display = 'block';
										}
										pseudo = document.createElement('p');
										pseudo.innerHTML =  myObj[i].Pseudo;

										div.appendChild(img);
										div.appendChild(pseudo);
										dynamicSearchResult.appendChild(sep);
										dynamicSearchResult.appendChild(div);

										console.log(myObj[i]);                        
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
				xmlhttp.open("POST", "searchChild.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam); 
			}
		}
		function onMouseMove(event)
		{
			mouse.x = (event.clientX /rendererW) * 2 -1;
			mouse.y = -(event.clientY / rendererH) * 2 + 1;		

			if(window.innerWidth < 480)
			{
				if(sideBar.style.display != "none"  || helpDiv.style.display != "none"	||	SearchBox.style.display !="none")
 
				{
					showSearchButton.style.display = "none";
				}
				else
				{
					showSearchButton.style.display = "block";
				}
			}
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
							onImageClick(intersects[ 0 ].object.name);
							SearchBox.style.display = 'none';
							showSearchButton.style.display = 'block';
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