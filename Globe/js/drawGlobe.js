

var camera,renderer,controls,stats,scene;

var meridians;


//dimensions d'un méridien
var meridianW = 12;
var meridianH = 54;

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

var scale = 1;
var cellSpacing = 3;

initialize();
draw();
animate();

function draw()
{
  var numCollisions = 0;
  var pointLight = new THREE.DirectionalLight(0xbbbbbb);
  pointLight.position.set(100, 100, 500);
  scene.add(pointLight);

  var ambientLight = new THREE.AmbientLight(0xbbbbbb);
  scene.add(ambientLight);



  //D = 2*PI*R
  //R = D/(2*PI)
  //D = (140 * 12 * 3 * 12)/(2*3.14)
  for(var i = 0; i < totalMeridians; i++)
  {
      meridians[i].drawMeridianCells(scene, ((cellW) * totalMeridians *cellSpacing * 12)/(2*Math.PI),cellSpacing,i);
  } 

  var axes = new THREE.AxisHelper(5000);
  scene.add(axes);
}



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
        meridians[i] = new Meridian(meridianW, meridianH, meridianMinW, meridianMinH, cellW , cellH ,( i * meridianW * cellW*cellSpacing )   ,-(cellH * cellSpacing * meridianH)/2,scale,1);   
        meridians[i].initMeridianCells();
    }

    // CONTROLS
    controls = new THREE.OrbitControls( camera, renderer.domElement );
    controls.minDistance = 132;
    controls.maxDistance = 10000;

}

function animate() 
{
  requestAnimationFrame( animate );
  render();		
}


function render()
{
    controls.update();


    renderer.render(scene, camera);     
}
