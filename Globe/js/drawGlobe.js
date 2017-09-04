

var scene,camera,renderer;

let meridians;


//dimensions d'un méridien
var meridianW = 12;
var meridianH = 54;

var meridianMinW = 2;
var meridianMinH = 11;

var cellW =100 ;
var cellH = 100;

var totalMeridians =1;
//dimensions totales du canvas
var totalW = meridianW * cellW;
var totalH = meridianH * cellH;

//dimensions d'une cellule
var textSpacing = 1;

var scale = .2;


initialize();
draw();

function draw()
{
  var numCollisions = 0;
  var pointLight = new THREE.DirectionalLight(0xbbbbbb);
  pointLight.position.set(100, 100, 500);
  scene.add(pointLight);

  var ambientLight = new THREE.AmbientLight(0xbbbbbb);
  scene.add(ambientLight);

  sphereGeometry = new THREE.SphereGeometry(200, 64, 64);
  sphereMaterial = new THREE.MeshPhongMaterial({
      color: 'darkgreen',
      opacity: 0.5,
      transparent: true
  });

  sphereMesh = new THREE.Mesh(sphereGeometry, sphereMaterial);
  scene.add(sphereMesh);


  for(var i = 0; i < totalMeridians; i++)
  {
      meridians[i].drawMeridianCells(scene,sphereMesh);
  } 

  var axes = new THREE.AxisHelper(1000);
  scene.add(axes);
}

function render()
{
    renderer.render(scene, camera);     
}

TweenLite.ticker.addEventListener('tick', render );

function initialize()
{

    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera( 75, window.innerWidth/window.innerHeight, 0.1, 100000 );
    camera.lookAt(scene.position);
    
    renderer = new THREE.WebGLRenderer();
    renderer.setSize( window.innerWidth, window.innerHeight );
    document.body.appendChild( renderer.domElement );



    camera.position.z = 1000;
    meridians = new Array(totalMeridians);

    for(var i = 0; i <totalMeridians; i++)
    {
        meridians[i] = new Meridian(meridianW, meridianH, meridianMinW, meridianMinH, cellW , cellH , i * meridianW * cellW  ,0,scale,1);   
        meridians[i].initMeridianCells();
    }


}

////////////////////////////////////////
var controls = new THREE.TrackballControls( camera );

controls.rotateSpeed = 3.6;
controls.zoomSpeed = 0.8;
controls.panSpeed = 1;

controls.noZoom = false;
controls.noPan = false;

controls.staticMoving = false;
controls.dynamicDampingFactor = 0.12;

controls.enabled = true;

TweenLite.ticker.addEventListener("tick", controls.update );
////////////////////////////////////////
var timeline = new TimelineLite({
  onStart: function(){
    TweenLite.ticker.removeEventListener("tick", controls.update );
    controls.enabled = false;
  },
  onComplete: function(){
    TweenLite.ticker.addEventListener("tick", controls.update );
    controls.position0.copy(camera.position);
    controls.reset();
    controls.enabled = true;
  }
});
easing = 'Expo.easeInOut';
////////////////////////////////////////
camera.reset = function(){

  var pos = { x: 0, y: 0 };
  var distance = 60;
  var speed = 1;
  
  if ( camera.parent !== scene ) {
    var pos = camera.position.clone();
    camera.parent.localToWorld(camera.position);
    scene.add(camera);
  }
  
  timeline.clear();
  timeline.to( camera.position, speed, { 
    x: pos.x, 
    y: pos.y, 
    z: distance, 
    ease: easing 
  }, 0);
  timeline.to( camera.rotation, speed, { x: 0, y: 0, z: 0, ease: easing}, 0);
  
}; 
////////////////////////////////////////
camera.getDistance = function(object) {

  var helper = new THREE.BoundingBoxHelper(object, 0xff0000);
  helper.update();

  var width = helper.scale.x,
      height = helper.scale.y;

  // Set camera distance
  var vFOV = camera.fov * Math.PI / 180,
      ratio = 2 * Math.tan( vFOV / 2 ),
      screen = ratio * camera.aspect, //( renderer.domElement.width / renderer.domElement.height ),
      size = Math.max(height,width),
      distance = (size / screen) + (helper.box.max.z / screen);

  return distance;
};
////////////////////////////////////////
camera.zoom = function(object){

  var pos = camera.position.clone();
  object.worldToLocal(camera.position);
  object.add(camera);

  var speed = 1;
  timeline.clear();

  timeline.to( camera.position, speed, {
    x: pos.x,
    y: pos.y,
    z: camera.getDistance(object),
    ease: easing
  },0);

};
////////////////////////////////////////
var startX, startY,
    $target = $(renderer.domElement),
    selected;

function mouseUp(e) {
  e = e.originalEvent || e;
  e.preventDefault();

  var x = ( e.touches ? e.touches[0].clientX : e.clientX ),
      y = ( e.touches ? e.touches[0].clientY : e.clientY ),
      diff = Math.max(Math.abs(startX - x), Math.abs(startY - y));

  if ( diff > 40 ) { return; }

  var mouse = {
    x: ( x / window.innerWidth ) * 2 - 1,
    y: - ( y / window.innerHeight ) * 2 + 1
  };

  var vector = new THREE.Vector3( mouse.x, mouse.y ).unproject( camera );
  var raycaster = new THREE.Raycaster( camera.position, vector.sub( camera.position ).normalize() );
  var intersects = raycaster.intersectObject( scene, true );

  if ( intersects.length > 0 && intersects[ 0 ].object !== selected ) {
    selected = intersects[ 0 ].object;
    camera.zoom(selected);
  } else {
    selected = null;
    camera.reset(); 
  }
}

function mouseDown( e ) {
  e = e.originalEvent || e;
  startX = ( e.touches ? e.touches[0].clientX : e.clientX );
  startY = ( e.touches ? e.touches[0].clientY : e.clientY );

  $target.one('mouseup touchend', mouseUp );

  setTimeout(function(){ $target.off('mouseup.part touchend.part'); },300);
}


$target.on('mousedown touchend', mouseDown );
