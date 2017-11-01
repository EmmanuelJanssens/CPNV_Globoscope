

<!DOCTYPE html>
<html>
    
    <head>
        <meta charset=utf-8>
        <meta name="viewport" content="width=device-width, maximum-scale=1" />
        
        <title>Un monde plus juste</title>
        
        <link rel="icon" href="textures/terre.ico" type="image/x-icon">
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
        
    <body onclick="fermerViewer()">
        
        <script src="js/three.js"></script>
        <script src="js/OrbitControlsModif.js"></script>
        
        <!--Entête du site-->
        <div id="header">
            
            <div id="title">Un monde plus juste</div>
            <div id="container">
                <img src="textures/loupe.png" id="boutonRecherche" onclick="search()" />
                <input type="text" id="recherche" placeholder="Entrer une position ou un pseudo" />
            </div>
            
        </div>
        
        <script>
        
            //importation des résultats de la bd
            
            //Elements de bases à la création d'une scène Three.js
            var scene = new THREE.Scene();
            var camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 40000);
            camera.position.z = 8000; //décalage de la caméra par rapport aux objet centrés en 0:0:0 (Right Hand coordinate system)
            
            //variables camera pour le calcul de la position après recherche d'image
            var cameraDistance = 2900;
            var cameraSpherical = new THREE.Spherical();
            var cameraVector = new THREE.Vector3();
            
            var renderer = new THREE.WebGLRenderer();
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.body.appendChild(renderer.domElement);
            
            //création des contrôles pour tourner autours de la planète
            var controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.maxDistance = 7000;
            controls.minDistance = 2900;
            
            //Ajout du raycaster
            var raycaster = new THREE.Raycaster();
            var mouse = new THREE.Vector2(); //pos x-y
            var objects = []; //tableau qui garde les objects qui vont intéragir avec le raycaster
            
            //texture
            var loader = new THREE.TextureLoader(); //JPG//PNG etc -> planète et skybox
           
            //pour la création des différents Actors.
            var geometry;
            var texture;
            var material;
            var mesh;
            
            var angles = new Array(); //stoque les angles de placement pour faciliter les recherches
           
            var imageVisibility = 0; //Définit si l'agrandisseur d'image est visible ou non 1=oui 0=non
            
            //nombre d'images par lattitude de chaque méridien
            var longueurParLattitude = [2, 2, 2, 2, 4, 4, 4, 6, 6, 6, 6, 8, 8, 8, 8, 10, 10, 10, 10, 10, 10, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 10, 10, 10, 10, 10, 10, 8, 8, 8, 8, 6, 6, 6, 6, 4, 4, 4, 2, 2, 2, 2];
            
            //Tayon du rayon qui affichera les enfants par rapport au centre
            var radius = 2600;
            var spherical = new THREE.Spherical(); //permet des calculer la position d'un point x,y,z relatif à une sphère
            var vector = new THREE.Vector3();
            
            //numero de l'image de départ
            var id = 1
            
            document.addEventListener("keydown", validateClavier);
            document.addEventListener("click", fermerViewer);
            
            //Function de détection si une tête est cliqué
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|BB|PlayBook|IEMobile|Windows Phone|Kindle|Silk|Opera Mini/i.test(navigator.userAgent)) //Test si on est sur un navigateur mobile
            {
                document.addEventListener('click', onDocumentMouseDown, false);
            }
            else
            {
                document.addEventListener('dblclick', onDocumentMouseDown, false);
            }
            
            document.addEventListener('mousemove', onMouseMove, false);
            
            //mise à jour de la zone de rendu et de la caméra en cas de changement de taille de la fenêtre
            window.addEventListener('resize', function () {
                var width = window.innerWidth;
                var height = window.innerHeight;
                renderer.setSize(width, height);
                camera.aspect = width / height;
                camera.updateProjectionMatrix();
            });
            
            createScene();

            function createScene()
            {    
                //Terre
                loader.load('textures/earthmap.jpg', function (texture)
                {
                    //création d'une géométrie
                    geometry = new THREE.SphereGeometry(2570, 32, 32); //Rayon (6300 V1), segments horizontaux et verticaux
                    material = new THREE.MeshBasicMaterial({
                        map: texture,
                        overdraw: 0.5
                });
                    
                var monde = new THREE.Mesh(geometry, material);
                    
                scene.add(monde);
                monde.rotateY(Math.PI / 2);
                });
                
                //Skybox
                var sky = new THREE.CubeGeometry(24000, 24000, 24000);
                
                var cubeMaterials = [
                    new THREE.MeshBasicMaterial({
                        map: new THREE.TextureLoader().load("textures/ciel/ciel_front.png"),
                        side: THREE.DoubleSide
                    })
                    , new THREE.MeshBasicMaterial({
                        map: new THREE.TextureLoader().load("textures/ciel/ciel_back.png"),
                        side: THREE.DoubleSide
                    })
                    , new THREE.MeshBasicMaterial({
                        map: new THREE.TextureLoader().load("textures/ciel/ciel_top.png"),
                        side: THREE.DoubleSide
                    })
                    , new THREE.MeshBasicMaterial({
                        map: new THREE.TextureLoader().load("textures/ciel/ciel_bottom.png"),
                        side: THREE.DoubleSide
                    })
                    , new THREE.MeshBasicMaterial({
                        map: new THREE.TextureLoader().load("textures/ciel/ciel_right.png"),
                        side: THREE.DoubleSide
                    })
                    , new THREE.MeshBasicMaterial({
                        map: new THREE.TextureLoader().load("textures/ciel/ciel_left.png"),
                        side: THREE.DoubleSide
                    })
                ];
                
                var cubeMaterial = new THREE.MeshFaceMaterial(cubeMaterials);
                
                var ciel = new THREE.Mesh(sky, cubeMaterial);
                scene.add(ciel);
                
                //Génération des enfants
                //par meridien
                for (var k = 0; k <= 11; k++)
                {    
                    //par lattitude
                    for (var j = 0, m = longueurParLattitude.length; j < m; j++)
                    {    
                        var lattitude = (10 + (180 - 20) / 53.5 * j) * (Math.PI / 180);
                        
                        //a chaque longitude 
                        for (var i = 1, l = longueurParLattitude[j]; i <= l; i++)
                        {    
                            //var phi = Math.acos( -1 + ( 2 * i ) / l );
                            var longitude = (360 / (longueurParLattitude[j] * 12) * i + (360 / 12 * k)) * (Math.PI / 180);
                            
                            //Position des planes
                            spherical.set(radius, lattitude, longitude);
                            vector.setFromSpherical(spherical);
                            angles.push([lattitude, longitude]);
                            geometry = new THREE.PlaneGeometry(100, 125);
                            geometry.lookAt(vector);
                            geometry.translate(vector.x, vector.y, vector.z);
                            
                            if (tableaujs[id][5] == "VRAI")
                            {    
                                //Chargement des têtes
                                texture = loader.load("textures/eleves_mini/" + tableaujs[id][1] + ".png");
                                texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
                                texture.repeat.set(1, 1);
                                material = new THREE.MeshBasicMaterial({ map: texture });
                            }
                            else
                            {
                                material = new THREE.MeshLambertMaterial({color: 0x000000});
                            }
                            
                            mesh = new THREE.Mesh(geometry, material);
                            mesh.name = id;
                            
                            id++;
                            
                            scene.add(mesh);
                            objects.push(mesh);
                        }
                    }
                }
            }
            
            var update = function ()
            {
                controls.update();
                raycaster.setFromCamera(mouse, camera);
            }
            
            //dessine la scène
            var render = function ()
            {
                renderer.render(scene, camera);
            }
            
            //Execute la boucle de "jeu" (mise à jour, rendu, répetition)
            var GameLoop = function ()
            {
                requestAnimationFrame(GameLoop);
                update();
                render();
            }
            
            GameLoop();
            
            // Controle si la touche "enter" est pressée pour valider la recherche
            function validateClavier(e)
            {
                if (e.keyCode == 13)
                {
                    search()
                }
            }

            function search() {
                
                var id; //stock l'id pour rechercher l'image
                
                //par numéro d'image
                if (parseInt(document.getElementById('recherche').value) >= 1 && parseInt(document.getElementById('recherche').value) <= 4992)
                {
                    id = parseInt(document.getElementById('recherche').value);
                }
                //par pseudo
                else
                {
                    var pseudo = document.getElementById('recherche').value; //récupération du potentiel pseudo
                    
                    var i;
                    for (i = 1; i <= 4992; i++) //Recherche d'un match total avec un des pseudo du tableau de données
                    {
                        if (tableaujs[i][2] === pseudo)
                        {
                            id = tableaujs[i][0];
                            break;
                        }
                    }
                }
                
                if (id)
                {
                    cameraSpherical.set(cameraDistance, angles[id - 1][0], angles[id - 1][1]);
                    cameraVector.setFromSpherical(cameraSpherical);
                    camera.lookAt(cameraVector);
                    camera.position.set(cameraVector.x, cameraVector.y, cameraVector.z);
                }
            }
            
            function imageViewer(positionImage)
            {
                if (imageVisibility === 1) //le afficheur d'image est affiché
                {
                    document.body.removeChild(document.getElementById("zoom"));
                    imageVisibility = 2;
                }
                else if (imageVisibility !== 1) //le afficheur d'image est caché
                {
                    positionImage = parseInt(positionImage);
                    
                    var imageViewer = document.createElement("div");
                    imageViewer.id = "zoom"; // Définition de son identifiant
                    
                    document.body.appendChild(imageViewer); 
                    
                    //mise en place de la "structure" HTML
                    document.getElementById("zoom").innerHTML = "<div id='img'><img id='photo' src='textures/eleves_normal/" + tableaujs[positionImage][1] + ".jpg'></div><table id = 'info'><tr><td class ='label'>P<span class ='label2'>seudo: " + tableaujs[positionImage][2] + "</span></td></tr><tr><td class ='label'>D<span class ='label2'>roit: " + tableaujs[positionImage][3]+
                    "</span></td></tr><tr><td class ='label'>S<span class ='label2'>logan: " + tableaujs[positionImage][4]+
                    "</span></td ></tr></table>";
                    
                    document.getElementById('zoom').setAttribute("onclick","fermerViewer()");
                    
                    imageVisibility = 1;
                }
            }

            function onMouseMove(event)
            {
                mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
                mouse.y = (event.clientY / window.innerHeight) * 2 - 1;
                mouse.y *= -1;
            }

            function onDocumentMouseDown(event)
            {
                var intersects = raycaster.intersectObjects(objects);
                if (intersects.length > 0)
                {
                    imageViewer(intersects[0].object.name);
                }
            }

            //appelé si le body est cliqué
            function fermerViewer()
            {
                if (imageVisibility === 1)//le afficheur d'image est affiché
                {
                    imageViewer(0)
                }
            }
        </script>
    </body>
</html>