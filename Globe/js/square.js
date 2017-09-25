
//dessiner un carré
class Square
{
    constructor(pos,_w,_h,)
    {
        this.position = pos;

        this.x = this.position.x;
        this.y = this.position.y;
        this.z = this.position.z;

        this.w = _w;
        this.h = _h;

        this.planeMesh;
        this.vector = new THREE.Vector3();
        this.textureLoader = new THREE.DDSLoader();
        
    }   
    fileExist(file)
    {
        var http = new XMLHttpRequest();
        http.open('HEAD',file,false);
        http.send();

        return http.status != 404;
    }
    drawSquare(scene,mer,lat,long)
    {

        var planeGeometry,planeMaterial,numCollisions;
            
        planeGeometry = new THREE.PlaneBufferGeometry(this.w,this.h,2,2);
        planeGeometry.dynamic = true;



       
        var file = "images/lot2/"+mer+"-"+lat+"-"+long+".dds";
        
        var planeMaterial;
        var texture; 

        texture =  this.textureLoader.load( file );

        var f = file;
        if(texture != null)
        {
            texture.wrapT = THREE.RepeatWrapping;
            texture.repeat.y = - 1;
            planeMaterial = new THREE.MeshPhongMaterial( {  color: 0xffffff,map: texture } );
    
    
    
           // var planeMaterial; new THREE.MeshPhongMaterial( {  color: 0xffffff});
    
            this.planeMesh = new THREE.Mesh(planeGeometry, planeMaterial);
            this.planeMesh.position.set(this.x,this.y,this.z);
                
            scene.add(this.planeMesh);
        }

    }


    setPosition(x,y,z)
    {
        this.planeMesh.position.set(x,y,z);       
    }
    lookAtZero()
    {
        var v = new THREE.Vector3();
        v.subVectors(this.planeMesh.position, new THREE.Vector3(0,0,0)).add(this.planeMesh.position);
        this.planeMesh.lookAt(v);

        
    }
    setPosSpherical(spherical)
    {
        this.planeMesh.position.setFromSpherical(spherical);       

    }
    getPosition()
    {
        return this.position;
    }
}
