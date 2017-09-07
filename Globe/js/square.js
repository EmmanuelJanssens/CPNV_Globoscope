
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
    }   
    drawSquare(scene,color)
    {

        var planeGeometry,planeMaterial,numCollisions;
            
        planeGeometry = new THREE.PlaneGeometry(this.w,this.h,2,2);
        planeGeometry.dynamic = true;

        planeMaterial = new THREE.MeshPhongMaterial({
            color
        });

        this.planeMesh = new THREE.Mesh(planeGeometry, planeMaterial);
        this.planeMesh.position.set(this.x,this.y,this.z);
      


        
        scene.add(this.planeMesh);
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

        console.log(this.planeMesh.rotation);
        
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
