
var obj, dbParam, xmlhttp, myObj, x, txt = "";
var data;

textureLoader = new THREE.TextureLoader();

function loadData(scene)
{
    data = [];
    obj = { "table":"images" };
    dbParam = JSON.stringify(obj);
    
    xmlhttp = new XMLHttpRequest();

    
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            
            myObj = JSON.parse(this.responseText);

            plane = new THREE.PlaneBufferGeometry(100,125);   
            
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

            var _x,_y,_z,v;
            v = new THREE.Vector3();

            var cellW = 100;
            var cellH = 125;

            var 
            var originalSpacing =2;            
            var totalWidth = collNum.length * originalSpacing;

           
            var totalMeridians = 12;
            var meridianWidth = 12;
            var meridianHeight = 54;

            var meridianCounter = 0;
            var currentMeridian = 0;

            var rayon =  (cellW*originalSpacing*meridianWidth*totalMeridians)/(2*Math.PI);

            for (x in myObj) 
            {
                //definir dans quel méridien on se trouve
                if(meridianCounter > _totalCells)
                {
                    meridianCounter = 0;
                    currentMeridian++;
                }
                //nombres de cellules dans un méridien    
                data.push({ "IDImage":myObj[x].IDImage,"mer":myObj[x].mer,"lat":myObj[x].lat,"lon":myObj[x].lon });
                if(data[x].IDImage != 0)
                {
                    file ="images/DB/lot2/100-125/"+data[x].mer+"-"+data[x].lat+"-"+data[x].lon+".jpg";

                    texture =  textureLoader.load( file );      
                    
                    material = new THREE.MeshBasicMaterial( {  color: 0xffffff,map: texture } );
                    mesh = new THREE.Mesh( plane, material );

                    var long = ( currentMeridian * cellW * meridianWidth * originalSpacing + (data[x].lon ) * cellW *originalSpacing)/rayon;
                    var lat = 2*Math.atan(Math.exp( (-(  cellH * meridianHeight * originalSpacing)/2+(data[x].lat) *cellH *originalSpacing )/rayon)) - Math.PI/2;
        
                    _x = rayon* (Math.cos(lat) * Math.cos(long)) ;
                    _y =  rayon* (Math.sin(lat));
                    _z = rayon* (Math.cos(lat)  * Math.sin(long));

  
                    mesh.position.set(_x ,_y,_z);

                    v.subVectors(mesh.position, new THREE.Vector3(0,0,0)).add(mesh.position);
                    mesh.lookAt(v);

                    scene.add( mesh );                  
                }
                else
                {                      
                    material = new THREE.MeshBasicMaterial( {  color: 0xffffff } );
                    mesh = new THREE.Mesh( plane, material );

                    var long = ( currentMeridian * cellW * meridianWidth * originalSpacing + (data[x].lon ) * cellW *originalSpacing)/rayon;
                    var lat = 2*Math.atan( Math.exp( (-(cellH * meridianHeight * originalSpacing)/2+ (data[x].lat) *cellH *originalSpacing )/rayon)) - Math.PI/2;
        
                    _x = rayon* (Math.cos(lat) * Math.cos(long)) ;
                    _y =  rayon* (Math.sin(lat));
                    _z = rayon* (Math.cos(lat)  * Math.sin(long));

  
                    mesh.position.set(_x ,_y,_z);

                    v.subVectors(mesh.position, new THREE.Vector3(0,0,0)).add(mesh.position);
                    mesh.lookAt(v);

                    scene.add( mesh );                       
                }
                

                meridianCounter++;
            }
     

        }
    };
    xmlhttp.open("POST", "loader.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("x=" + dbParam);    

    return xmlhttp;
}

