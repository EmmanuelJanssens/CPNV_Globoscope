

var imageLoaded = 0;
            
function loadData(scene,canvContainer,loadSpinner)
{
    var searchObj, dbParam, xmlhttp, data, x = "";
    textureLoader = new THREE.TextureLoader();
    
    //données JSON
    searchObj = { "table":"images" };
    //convertir les données JSON
    dbParam = JSON.stringify(searchObj);
    
    xmlhttp = new XMLHttpRequest();

    //afficher un element de chargement 
    showSearchButton.style.display = "none";
    canvContainer.style.display = "none";
    var loadValue = document.getElementById('progressValue');
    var loadStatus = document.getElementById('loadingState');

    xmlhttp.onreadystatechange = function() 
    {
        if(this.readyState == 0 && this.status == 200)
        {
            loadStatus.innerHTML  = "En attente";            
        }
        if(this.readyState == 1 && this.status == 200)
        {
            loadStatus.innerHTML  = "Connecté";            
        }
        else if(this.readyState == 2 && this.status == 200)
        {
            loadStatus.innerHTML  = "Recupération des données";            
        }
        else if(this.readyState == 2 && this.status == 200)
        {
            loadStatus.innerHTML  = "Chargement des données";            
        }
        else if (this.readyState == 4 && this.status == 200) 
        {            
            loadStatus.innerHTML  = "Chargemement des Images";
            //convertir la requète php dans loader.php qui à été encodé pour pouvoir lire en JSON
            data = JSON.parse(this.responseText);

            //Plane de base
            var plane = new THREE.PlaneBufferGeometry(100,125);   
            var material = new THREE.MeshBasicMaterial( {  color: 0xffffff });
            
            var nbImagesLat = [0,0,0,0,2,2,2,2,4,4,4,6,6,6,6,8,8,8,8,10,10,10,10,10,10,12,12,12,12,12,12,12,12,12,12,12,12,10,10,10,10,10,10,8,8,8,8,6,6,6,6,4,4,4,2,2,2,2];

            //coordonées et orientation
            var _x,_y,_z,orientation,zero;
            var long,lat;
            orientation = new THREE.Vector3();
            zero = new THREE.Vector3();

            var cellW = 100;
            var cellH = 125;

            var originalSpacing =1.05;    
            var xSpacing = originalSpacing;
            var ySpacing = originalSpacing;        
           
            var totalMeridians = 12;
            var meridianWidth = 12;
            var meridianHeight = 54;

            var meridianCounter = 0;
            var currentMeridian = 0;

            var rayon =  (cellW*originalSpacing*meridianWidth*totalMeridians)/(2*Math.PI);
            
            var i = 0;
            var totalImages = 0;
            
            for(i in data)
            {
                if(data[i].ImageOK == "VRAI")
                    totalImages++;
            }
            var TextureLoader = new THREE.TextureLoader();
            TextureLoader.load( 'images/earth.jpg', function ( texture ) {
                var geometry = new THREE.SphereGeometry( rayon - 15, 30, 30 );
                var material = new THREE.MeshBasicMaterial( { map: texture, overdraw: 0.5 } );
                var mesh = new THREE.Mesh( geometry, material );
                mesh.rotation.z = -Math.PI;
                mesh.rotation.y = Math.PI/1.7;
                scene.add( mesh );
            } );
            
            var Spherical = new THREE.Spherical();
            var spherePos = new THREE.Vector3();

            for(x = 0; x < data.length;x++)
            {      
                //charger une image 
                if(data[x].ImageOK != 0)
                {
                    var image = new Image();
                    //afficher le canvas lorsque la dernière image est chargée
                    
                    file ="images/64-64/"+data[x].IDImage+".jpg";                   
                    image.src = file;
                    texture =  textureLoader.load( file ,function()
                    {
                        imageLoaded++;
                        loadValue.style.width = (imageLoaded/totalImages)*100+"%";   
                        
                        if(imageLoaded >= totalImages-200)
                        {
                            canvContainer.className= "w3-animate-opacity";
                            showCanvas(canvContainer,loadSpinner);        
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
                    scene.add( mesh );  
                    
                }          
            }
            scene.rotation.z = Math.PI;
            
        }
    };

    xmlhttp.open("POST", "loader.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.setRequestHeader("Content-Length", "application/x-www-form-urlencoded");
    
    xmlhttp.send("x=" + dbParam);    

}
function showCanvas(canvContainer,loadSpinner)
{
    canvContainer.style.display = "block";
    loadSpinner.style.display = "none";
    showSearchButton.style.display = "block";
    
}

