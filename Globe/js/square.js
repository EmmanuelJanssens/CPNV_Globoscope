
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
        
    }   
    drawSquare(scene,color,sphere)
    {

        var planeGeometry,planeMesh,planeMaterial,numCollisions;
            
        planeGeometry = new THREE.PlaneGeometry(this.w,this.h,2,2);
        planeGeometry.dynamic = true;

        planeMaterial = new THREE.MeshPhongMaterial({
            color
        });

        planeMesh = new THREE.Mesh(planeGeometry, planeMaterial);
        planeMesh.material.side = THREE.DoubleSide;
        planeMesh.position.set(this.x,this.y,this.z);
        

        for (var vertexIndex = 0; vertexIndex < planeMesh.geometry.vertices.length; vertexIndex++) {

            var localVertex = planeMesh.geometry.vertices[vertexIndex].clone();
            localVertex.z = 201;
            var directionVector = new THREE.Vector3();
            directionVector.subVectors(sphereMesh.position, localVertex);
            directionVector.normalize();
            //var ray = new THREE.Raycaster(localVertex, new THREE.Vector3(0, 0, -1));
            var ray = new THREE.Raycaster(localVertex, directionVector);

            var collisionResults = ray.intersectObject(sphereMesh);
            numCollisions += collisionResults.length;

            if (collisionResults.length > 0) {

                planeMesh.geometry.vertices[vertexIndex].z = collisionResults[0].point.z + 5;
            }
        }

        $('#Text').text('Number of collisions: ' + numCollisions);

        planeMesh.geometry.verticesNeedUpdate = true;
        planeMesh.geometry.normalsNeedUpdate = true;

        scene.add(planeMesh);
    }
    getPosition()
    {
        return this.position;
    }
}
