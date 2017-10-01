

var camera,renderer,controls,stats,scene;

var meridians;


//dimensions d'un méridien
var meridianW = 14  ;
var meridianH = 60;

var meridianMinW = 2;
var meridianMinH = 11;

var cellW =60 ;
var cellH =100;

var totalMeridians = 12;
//dimensions totales du canvas
var totalW = meridianW * cellW;
var totalH = meridianH * cellH;

//dimensions d'une cellule
var textSpacing = 1;

var scale = .4;
var cellSpacing = 2;

initialize();
animate();




function initialize()
{

    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera( 75, window.innerWidth/window.innerHeight, 0.1, 100000 );
    camera.lookAt(scene.position);
    
    renderer = new THREE.WebGLRenderer();
    renderer.setSize( window.innerWidth, window.innerHeight );
    document.body.appendChild( renderer.domElement );



    camera.position.z = 5000;
    meridians = new Array(totalMeridians);

    for(var i = 0; i <totalMeridians; i++)
    {
        meridians[i] = new Meridian(meridianW, meridianH, cellW , cellH ,( i * meridianW * cellW )*scale   ,(-(cellH * cellSpacing * meridianH)/2)*scale,scale,i);   
    }

    // CONTROLS
    controls = new THREE.OrbitControls( camera, renderer.domElement );
    controls.minDistance = 132;
    controls.maxDistance = 10000;

    var pointLight = new THREE.DirectionalLight(0xbbbbbb);
    pointLight.position.set(100, 100, 500);
    scene.add(pointLight);

    var ambientLight = new THREE.AmbientLight(0xbbbbbb);
    scene.add(ambientLight);


    //Draw 
    //D = 2*PI*R
    //R = D/(2*PI)
    //D = (140 * 12 * 3 * 12)/(2*3.14)
    for(var i = 0; i < totalMeridians; i++)
    {
        meridians[i].drawMeridianCells(scene, (((cellW) * totalMeridians  * meridianW)/(2*Math.PI))*scale);
    } 

    var axes = new THREE.AxisHelper(5000);
    scene.add(axes);

}
function render()
{
    controls.update();


    renderer.render(scene, camera);     
}

function animate() 
{
  requestAnimationFrame( animate );
  render();		
}


