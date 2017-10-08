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
				div 
				{
					position: absolute;
					margin: 0px;
				}

				#childDetails
				{
					display:block;
					margin-left:auto;
				}
				#childImage
				{
					display:block;
					margin-left:auto;
					margin-right:auto;
					margin-top: 40px;
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
		var sideBar = document.createElement('div');
		var sidebarW = 600;
		var sidebarH = rendererH - 200;
		var sidebarStyle = sideBar.style;
		document.body.appendChild(container);



		controls.enablePan = false;
		controls.enableDamping = false;

		renderer.setSize( rendererW,rendererH);
		container.appendChild(renderer.domElement);

		
		sidebarStyle.width = sidebarW+"px";
		sidebarStyle.height = sidebarH+"px";
		sidebarStyle.position = "absolute";
		sidebarStyle.right = "0px";
		sidebarStyle.top = 150+"px";
		sidebarStyle.backgroundColor = "blue";
		sidebarStyle.display = "none";
		sideBar.id = "sidebar";
		
		var childDetails = document.createElement('div');
		childDetails.id ="childDetails";
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

		container.appendChild(sideBar);
		
		camera.position.z = 10000;


		document.addEventListener( 'mousedown', onMouseClick, false );
		document.addEventListener( 'mousemove', onMouseMove, false );
		window.addEventListener('resize',onWindowResize,false);
		
		function closeSideBar()
		{
			sidebarStyle.display = "none";
		}

		function onImageClick(x)
		{

			var obj, dbParam, xmlhttp, myObj;
			obj = { "ID":x };
			dbParam = JSON.stringify(obj);
			
			xmlhttp = new XMLHttpRequest();


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
		
					}
				}
			}
			xmlhttp.open("POST", "selectImage.php", true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send("x=" + dbParam);    
			sidebarStyle.display = "block";
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
			event.preventDefault();
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
		var t ="";
		loadData(scene,data,t);
		animate();

		console.log(t);
	</script>
	</body>
</html>