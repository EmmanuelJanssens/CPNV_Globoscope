function loadData(scene,canvContainer,controls)
{
    var searchObj, dbParam, xmlhttp, data, x = "";
    textureLoader = new THREE.TextureLoader();
    
    //données JSON
    searchObj = { "table":"images" };
    //convertir les données JSON
    dbParam = JSON.stringify(searchObj);
    
    xmlhttp = new XMLHttpRequest();

    //afficher un element de chargement 
    showSearchButton.style.display = "block";
    canvContainer.style.display = "block";

    var index = 0;


    xmlhttp.onreadystatechange = function() 
    {
        if(this.readyState == 4 && this.status == 200) 
        {            
            //convertir la requète php dans loader.php qui à été encodé pour pouvoir lire en JSON
            data = JSON.parse(this.responseText);

            //Plane de base
            var plane = new THREE.PlaneGeometry(100,125);   
            var material = new THREE.MeshBasicMaterial( {  color: 0xffffff });
            
            var nbImagesLat = [0,0,0,0,2,2,2,2,4,4,4,6,6,6,6,8,8,8,8,10,10,10,10,10,10,12,12,12,12,12,12,12,12,12,12,12,12,10,10,10,10,10,10,8,8,8,8,6,6,6,6,4,4,4,2,2,2,2];

            //coordonées
            var _x,_y,_z;
            var long,lat;

            var cellW = 100;
            var cellH = 125;

            var originalSpacing =1.1;    
            var xSpacing = originalSpacing;
            var ySpacing = originalSpacing;        
           
            var totalMeridians = 12;
            var meridianWidth = 12;
            var meridianHeight = 54;
            var rayon =  (cellW*originalSpacing*meridianWidth*totalMeridians)/(2*Math.PI);
            
            var Spherical = new THREE.Spherical();
            var spherePos = new THREE.Vector3();


            var TextureLoader = new THREE.TextureLoader();



            var totalImages = 0;
            var imageLoaded = 0;
            var i;
            for(i in data)
            {
                if(data[i].ImageOK == "VRAI")
                    totalImages++;
            }
            TextureLoader.load( 'images/earth.jpg', function ( texture )
            {
                var geometry = new THREE.SphereGeometry( rayon - 15, 30, 30 );
                var material = new THREE.MeshBasicMaterial( { map: texture, overdraw: 0.5 } );
                var sphere = new THREE.Mesh( geometry, material );
                var mesh;
                sphere.rotateZ(Math.PI);
                sphere.rotateY(-Math.PI/1.7);
                scene.add(sphere);
                
                for(x = 0; x < data.length;x++)
                {      
                    //charger une image 
                    if(data[x].ImageOK != 0)
                    {
                        index++;
                        //afficher le canvas lorsque la dernière image est chargée
                        
                        file ="images/64-64/"+data[x].IDImage+".png";                   
                        texture =  textureLoader.load( file, function()
                        {
                            imageLoaded++;
                            if(imageLoaded == totalImages)
                            {
                                document.getElementById("loading").style.display = "none";
                                controls.autoRotate = false;                                
                            }
                        });
                        //Inversion des textures --
                        texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
                        texture.repeat.x = -1;
                        texture.repeat.y = -1;

                        material = new THREE.MeshBasicMaterial( {  color: 0xffffff,map: texture } );    

                        //creer le plane 
                        mesh = new THREE.Mesh( plane, material );

                        //https://stackoverflow.com/questions/12732590/how-map-2d-grid-points-x-y-onto-sphere-as-3d-points-x-y-z
                        long = (-( (data[x].mer ) * cellW * meridianWidth * originalSpacing + (data[x].lon-7)  * cellW * 12/nbImagesLat[data[x].lat] * (xSpacing))/rayon);
                        lat  = ( ( cellH * meridianHeight * ySpacing + data[x].lat *cellH *originalSpacing ) /rayon) + Math.PI/30  ;

                        Spherical.set(rayon,lat,long);
                        spherePos.setFromSpherical(Spherical);
                        mesh.lookAt(spherePos);                    
                        mesh.position.set(spherePos.x,spherePos.y,spherePos.z);

                        //nomer les planes pour pouvoir réutiliser les données dans la recherche d'image
                        mesh.name = data[x].IDPlace;
                        mesh.type = data[x].ImageOK;
                        mesh.id = index;

                        scene.add(mesh);
                    }          
                }
                scene.rotation.z = Math.PI;
            });         
        }
    };

    xmlhttp.open("POST", "GetData.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    
    xmlhttp.send("x=" + dbParam + "&Mode=load");    

}

