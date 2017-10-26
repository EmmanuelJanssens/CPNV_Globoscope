<!DOCTYPE html>
<html>
	<head>
		<title>Globoscope</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> 
		<style>
				body 
				{ 
					background-color: #f0f0f0;
					margin: 0px;
					overflow: hidden;
				}
				#childDetails
				{
					display:block;
				}
				#childImage
				{
					display:block;

				}
				.loader
				{
					position: absolute;
					left: 50%;
					top: 50%;
					z-index: 1;
					width: 150px;
					height: 150px;
					margin: -75px 0 0 -75px;
					border: 16px solid #f3f3f3;
					border-radius: 50%;
					border-top: 16px solid #3498db;
					width: 120px;
					height: 120px;
					-webkit-animation: spin 2s linear infinite;
					animation: spin 2s linear infinite;

				}

				#SearchBar
				{
					position:absolute;
					z-index:1;
					height:40px;
					width:100%;
					top:5px;
				}
				#SearchBar form input
				{
					width:100%;
				}
				@keyframes spin 
				{
					0% { transform: rotate(0deg); }
					100% { transform: rotate(360deg); }
				}

			</style>
	</head>

	<body>
	<script src= "js/three.min.js"></script>
	<script src= "js/three/controls/OrbitControls.js"></script>
	<script src="js/three/loaders/DDSLoader.js"></script>
	<script src ="js/loader.js">
	</script>

	<script>

		//Initialisation
		var scene = new THREE.Scene();
		var rendererW = window.innerWidth;
		var rendererH = window.innerHeight;
		var camera = new THREE.PerspectiveCamera( 75, rendererW/rendererH, 0.1, 50000 );
		var renderer = new THREE.WebGLRenderer();
		var controls =  new THREE.OrbitControls(camera,renderer.domElement);
		var raycaster = new THREE.Raycaster();
		var mouse = new THREE.Vector2();
		var data = [];

		var container = document.createElement('div');
		container.id = "CanvContainer";
		var sideBar = document.createElement('div');
		var sidebarW = 600;
		var sidebarH = rendererH - 200;
		var sidebarStyle = sideBar.style;
		document.body.appendChild(container);
		
		var SearchBar = document.createElement('div');
		SearchBar.id="SearchBar";

		var SearchBarForm = document.createElement('form');
		SearchBarForm.method="post";
		SearchBarForm.action="index.php";

		var SearchTextBox = document.createElement('input');
		SearchTextBox.type="text";
		SearchTextBox.name="searchChild";

		var SearchButton = document.createElement('input');
		SearchButton.type="submit";
		SearchButton.name="searchButton";
		SearchButton.value="Search";
		SearchButton.onclick = searchChild;

		SearchBar.appendChild(SearchButton);
		SearchBar.appendChild(SearchTextBox);


		container.appendChild(SearchBar);
		
		/*Loader*/
		var loader = document.createElement('div');
		loader.className="loader";
		loader.style.display="none";
		document.body.appendChild(loader);
		
		/*Loader pour l'image*/
		var imageLoader = document.createElement('div');
		imageLoader.className="loader";

		/*désactiver le déplacement pour le globe*/
		controls.enablePan = false;
		controls.enableDamping = true;
		
		/*Ajouer les élément principaux*/
		renderer.setSize( rendererW,rendererH);
		container.appendChild(renderer.domElement);

		container.appendChild(sideBar);
			
		/*Conteneur des détails de l'image recherchée //Onclick */
		sidebarStyle.width = sidebarW+"px";
		sidebarStyle.height = sidebarH+"px";
		sidebarStyle.position = "absolute";
		sidebarStyle.right = "0px";
		sidebarStyle.top = 150+"px";
		sidebarStyle.backgroundColor = "blue";
		sidebarStyle.display = "none";
		sideBar.id = "sidebar";
		sideBar.appendChild(imageLoader);
		

		var childDetails = document.createElement('div');
		childDetails.id ="childDetails";
		childDetails.style.display = "none";
		var childImage = document.createElement('img');

		childImage.style.display="block";
		childImage.style.marginLeft="auto";
		childImage.style.marginRight = "auto";
		childImage.style.marginTop = "40px";
		childImage.id = "childImage";
		childImage.src = "images/DB/Lot2/400-500/3-37-3.jpg";
		childDetails.appendChild(childImage);

		var childPseudo = document.createElement('p');
		childPseudo.id = "childPseudo";
		childPseudo.style.textAlign = "center";
		childDetails.appendChild(childPseudo);

		var childCitation = document.createElement('p');
		childCitation.id = "childCitation";
		childCitation.style.textAlign = "center";
		childDetails.appendChild(childCitation);

		sideBar.appendChild(childDetails);
		
		var closeButton = document.createElement('button');
		closeButton.style.position = "absolute";
		closeButton.style.bottom = "0px";
		closeButton.innerHTML ="close";
		closeButton.className="w3-button w3-black w3-block w3-teal";
		closeButton.onclick = closeSideBar;
		sideBar.appendChild(closeButton);
		

		camera.position.z = 10000;
		
		
		document.onmousedown = onMouseClick;
		document.onmousemove = onMouseMove;

		//document.addEventListener( 'mousedown', onMouseClick, false );
		//document.addEventListener( 'mousemove', onMouseMove, false );
		
		window.addEventListener('resize',onWindowResize,false);
		
		function closeSideBar()
		{
			//Efface le contenu précédent du div -> childDetails
			childDetails.innerHTML = "";
			sidebarStyle.display = "none";
		}

		function searchChild()
		{
			var objJSON,dbParam,xmlhttp,myObj;

			//les paramètres a passer dans la requête SQL
			objJSON = {"Pseudo":SearchTextBox.value }
			dbParam = JSON.stringify(objJSON);

			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function()
			{
				if(this.readyState ==4 && this.status==200)
				{

					///Afficher un tableau avec tout les résultats
					//for(myObj)
					//creer un bouton
					myObj = JSON.parse(this.responseText);

					//pour partir d'une div vide
					childDetails.innerHTML = "";
					//tableau de résultat de la recherche/requete SQL
					//https://stackoverflow.com/questions/15860683/onclick-event-in-a-for-loop
					for(var i = 0; i < myObj.length; i++)
					(function(i)
					{
						if(myObj[i].IDImage != 0)
						{			
							var a = document.createElement("a");
							a.setAttribute("href","#");
							
							var img = document.createElement("img");
							var data = myObj[i].IDImage;
							img.id= myObj[i].IDImage;
							img.src = "images/DB/Lot2/100-125/"+myObj[i].NomFichier+".jpg";
							img.onclick = function(){onImageClick(myObj[i].IDImage);}
							childDetails.appendChild(img);
						}
					})(i);

					childDetails.style.display="block";
					imageLoader.style.display="none";
					sidebarStyle.display = "block";
				}
			}

			xmlhttp.open("POST", "searchChild.php", true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send("x=" + dbParam);   	
		}

		function onImageClick(x)
		{
			var obj, dbParam, xmlhttp, myObj;
			obj = { "ID":x };
			dbParam = JSON.stringify(obj);
			
			xmlhttp = new XMLHttpRequest();
			imageLoader.style.display="block";
			childDetails.style.display="none";
			childDetails.innerHTML = "";
			xmlhttp.onreadystatechange = function() 
			{
				if (this.readyState == 4 && this.status == 200) 
				{
					myObj = JSON.parse(this.responseText);

					if(myObj[0].IDImage != 0)
					{	
						childPseudo.innerHTML = myObj[0].Pseudo;
						childCitation.innerHTML =  myObj[0].Slogan;
						childImage.src = "images/DB/Lot2/400-500/"+myObj[0].NomFichier+".jpg";
						childDetails.appendChild(childImage);
						childDetails.appendChild(childPseudo);
						childDetails.appendChild(childCitation);

					}
					childDetails.style.display="block";
					imageLoader.style.display="none";
					sidebarStyle.display = "block";

				}
			}
			xmlhttp.open("POST", "selectImage.php", true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send("x=" + dbParam);   
 
		}

		function onWindowResize()
		{
			rendererW = window.innerWidth;
			rendererH = window.innerHeight;
			camera.aspect =  rendererW/rendererH;
			camera.updateProjectionMatrix();


			renderer.setSize(rendererW,rendererH );

			sidebarW = 600;
			sidebarH = rendererH - 200;
			sidebarStyle.width = sidebarW+"px";
			sidebarStyle.height = sidebarH+"px";
			closeButton.style.bottom = "0px";
		}

		function onMouseMove(event)
		{
			mouse.x = (event.clientX /rendererW) * 2 -1;
			mouse.y = -(event.clientY / rendererH) * 2 + 1;		
		}
		function onMouseClick( event ) 
		{
			mouse.x = (event.clientX /rendererW) * 2 -1;
			mouse.y = -(event.clientY / rendererH) * 2 + 1;

			switch(event.button)
			{
				case 0:
					// update the picking ray with the camera and mouse position
					raycaster.setFromCamera( mouse, camera );
					
					// calculate objects intersecting the picking ray
					var intersects = raycaster.intersectObjects( scene.children );
				

					if(intersects.length > 0)
					{
						if(intersects[0].object.name != 0)
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