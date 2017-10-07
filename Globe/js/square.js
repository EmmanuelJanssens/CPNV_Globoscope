
//dessiner un carré
class Square
{
    constructor(pos,_w,_h)
    {
        this.position = pos;

        this.x = this.position.x;
        this.y = this.position.y;
        this.z = this.position.z;

        this.w = _w;
        this.h = _h;

        this.planeMesh;
        this.vector = new THREE.Vector3();
        this.textureLoader = new THREE.TextureLoader();

        
    }   

  
    drawSquare(scene,texture,geometry,exist)
    {

        var planeMaterial;

        if(exist)
        {
            planeMaterial = new THREE.MeshPhongMaterial( {  color: 0xffffff,map: texture } );    
            this.planeMesh = new THREE.Mesh(geometry, planeMaterial);
            this.planeMesh.position.set(this.x,this.y,this.z);
            scene.add(this.planeMesh);
        }
        else
        {
            this.planeMesh = null;
        }

    }


    setPosition(x,y,z)
    {
        if(this.planeMesh != null)
            this.planeMesh.position.set(x,y,z);       
    }
    lookAtZero()
    {
        if(this.planeMesh != null)
        {
            var v = new THREE.Vector3();
            v.subVectors(this.planeMesh.position, new THREE.Vector3(0,0,0)).add(this.planeMesh.position);
            this.planeMesh.lookAt(v);
        }            


        
    }
    setPosSpherical(spherical)
    {
        if(this.planeMesh != null)            
            this.planeMesh.position.setFromSpherical(spherical);       

    }
    getPosition()
    {
        return this.position;
    }
}
