
//dessiner un carré
class Square
{
    constructor(pos,_w,_h)
    {
        this.position = pos;

        this.x = this.position.x;
        this.y = this.position.y;

        this.w = _w;
        this.h = _h;
        
    }   
    drawSquare(scene,color)
    {
        var material,plane;

        material = new THREE.MeshBasicMaterial({color : color, transparent: true,opacity : 0.5});
        plane = new THREE.Mesh(new THREE.PlaneGeometry(this.w,this.h), material);
        plane.position.set(this.x,this.y,0);
        scene.add( plane );
    }
    getPosition()
    {
        return this.position;
    }
}
