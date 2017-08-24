

var _canv = document.getElementById("mainCanvas");
var _ctx = _canv.getContext("2d");

class Meridien
{
    constructor(maxW,maxH,minW,minH)
    {
        this._maxW = 12;
        this._maxH = 54;
        
        this._minW = 2;
        this._minH = 11;        
        
        this._currentW = new Array(2,4,6,8,10,12);
        this._currentH = new Array(12,24,32,40,46,54);
        
        this._breakPoints = new Array(4,7,11,15,21,33,39,44,47,50,54);    
        this._currentBreakPoint = 1;

    }
}


class Square
{
    constructor(posX,posY,_w,_h)
    {
        this.x = posX;
        this.y = posY;
        this.w = _w;
        this.h = _h;
        
        this.ctx = document.getElementById("mainCanvas").getContext("2d");
    }
    
    drawSquare()
    {
        this.ctx.rect(this.x,this.y,this.w,this.h);
        this.ctx.stroke();
    }

}





initialize();
drawRow();



function drawRow()
{
    let c = new Array(12);
    
    
    for(var i = 0; i <12;i++)
    {
        c[i] = new Square(i*20,1,20,20);
        c[i].drawSquare();
        _ctx.font= "10px Arial";
        _ctx.fillText(i+1,i*20+5,15);
    }
}
function initialize()
{
    loadImages();
}

function loadImages()
{
}

function drawImage()
{
    
    const c = new Square(1,1,100,100);
    
    c.drawSquare();
}

function scaleImage()
{
    
}
