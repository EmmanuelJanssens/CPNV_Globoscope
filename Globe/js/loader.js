


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
    canvContainer.style.display = "none";
    loadSpinner.style.display = "block";

    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) 
        {
            //convertir la requète php dans loader.php qui à été encodé pour pouvoir lire en JSON
            data = JSON.parse(this.responseText);

            //Plane de base
            var plane = new THREE.PlaneBufferGeometry(100,125);   
            var material = new THREE.MeshBasicMaterial( {  color: 0xffffff });
            //nombre de ligne         
            var rowsNum = [4,3,4,6,6,6,6,6,5,3,3,4];
            //nombre de collones
            var collNum = [2,4,6,8,10,12,12,10,8,6,4,2];
    
            var _totalCells = 0;  
            var i = 0;      
            for( i = 0; i < 12; i ++)
            {
                _totalCells  += collNum[i] * rowsNum[i];
            }

            //coordonées et orientation
            var _x,_y,_z,orientation,zero;
            var long,lat;
            orientation = new THREE.Vector3();
            zero = new THREE.Vector3();

            var cellW = 128;
            var cellH = 128;

            var originalSpacing =1.5;    
            var xSpacing = originalSpacing;
            var ySpacing = originalSpacing;        
            var totalWidth = collNum.length * originalSpacing;

           
            var totalMeridians = 12;
            var meridianWidth = 12;
            var meridianHeight = 60;

            var meridianCounter = 0;
            var currentMeridian = 0;

            var rayon =  (cellW*originalSpacing*meridianWidth*totalMeridians)/(2*Math.PI);
            
            var i = 0;
            var imageLoaded = 0;
            for(i in data)
            {
                if(data[i].ImageOK == "VRAI")
                    imageLoaded++;
            }
            for(x = 0; x < data.length;x++)
            {      
                //charger une image 
                if(data[x].ImageOK != 0)
                {
                    var image = new Image();

                    image.onload = function()
                    {
                        imageLoaded--;
                        if(imageLoaded <= 0)
                        {
                            showCanvas(canvContainer,loadSpinner);                                
                        }
                    }
                    file ="images/DB/128-128/"+data[x].IDImage+".jpg";
                    
                    image.src = file;
                    texture =  textureLoader.load( file );
                    material = new THREE.MeshBasicMaterial( {  color: 0xffffff,map: texture } );                
                }
                else
                {                      
                    material = new THREE.MeshBasicMaterial( {  color: 0xffffff } );
                    mesh = new THREE.Mesh( plane, material );        
                }  
                 //Gesition de l'espacement en fonction de la latitude 
                /*if(data[x].lat == 4 ||data[x].lat == 5 ||data[x].lat == 6 ||data[x].lat == 7  )
                {
                    ySpacing = originalSpacing;                    
                }
                else if(data[x].lat == 8 ||data[x].lat == 9 ||data[x].lat == 10)
                {
                    ySpacing = originalSpacing;                    
                }     
                else if(data[x].lat == 11 ||data[x].lat == 12 ||data[x].lat == 13||data[x].lat ==14)
                {                  
                    ySpacing = originalSpacing;                    
                }      
                else if(data[x].lat == 15 ||data[x].lat == 16 ||data[x].lat == 17||data[x].lat ==18)
                {
                    ySpacing = originalSpacing;                    
                }    
                else if(data[x].lat == 19 ||data[x].lat == 20 ||data[x].lat == 21||data[x].lat ==22||data[x].lat ==23||data[x].lat ==24 )
                {
                    ySpacing = originalSpacing;                    
                }
                else if(data[x].lat == 37||data[x].lat == 38||data[x].lat == 39||data[x].lat == 40||data[x].lat == 41||data[x].lat == 42 )
                {
                    ySpacing = originalSpacing;                    
                }          
                else if(data[x].lat == 43||data[x].lat == 44||data[x].lat == 45||data[x].lat == 46)   
                {
                    ySpacing = originalSpacing;                    
                }
                else if(data[x].lat == 47||data[x].lat == 48||data[x].lat == 49||data[x].lat == 50)   
                {
                    ySpacing = originalSpacing;                    
                }
                else if(data[x].lat == 51||data[x].lat == 52||data[x].lat == 53)   
                {
                    ySpacing = originalSpacing;
                    
                }
                else if(data[x].lat == 54||data[x].lat == 55||data[x].lat == 56||data[x].lat == 57)   
                {
                    ySpacing = .1;
                }
                else
                {
                    ySpacing = originalSpacing;
                }*/

                //creer le plane 
                mesh = new THREE.Mesh( plane, material );
                
                //https://stackoverflow.com/questions/12732590/how-map-2d-grid-points-x-y-onto-sphere-as-3d-points-x-y-z
                long = ( data[x].mer * cellW * meridianWidth * originalSpacing + (data[x].lon ) * cellW * (xSpacing))/rayon;
                lat = 2*Math.atan(Math.exp( (-(  cellH * meridianHeight * ySpacing)/2+ (data[x].lat) *cellH *originalSpacing ) /rayon)) - Math.PI/2;

                _x = rayon* (Math.cos(lat) * Math.cos(long)) ;;
                _y = rayon* (Math.sin(lat)) ;
                _z = rayon* (Math.cos(lat)  * Math.sin(long));

                //nomer les planes pour pouvoir réutiliser les données dans la recherche d'image
                mesh.name = data[x].IDPlace;
                mesh.type = data[x].ImageOK;
                mesh.position.set(_x ,_y,_z);

                //orienter les planes vers le centre du cercle
                orientation.subVectors(mesh.position, zero).add(mesh.position);
                mesh.lookAt(orientation);

                scene.add( mesh );   
            }
            
        }
    };

    xmlhttp.open("POST", "loader.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("x=" + dbParam);    

}
function showCanvas(canvContainer,loadSpinner)
{
    canvContainer.style.display = "block";
    loadSpinner.style.display = "none";
}

