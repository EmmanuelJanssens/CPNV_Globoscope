

var _canv = document.getElementById("mainCanvas");
var _ctx = _canv.getContext("2d");

let meridians;


//dimensions d'un méridien
var meridianW = 12;
var meridianH = 54;

var meridianMinW = 2;
var meridianMinH = 11;

var cellW = 30;
var cellH = 50;

var totalMeridians = 2;
//dimensions totales du canvas
var totalW;
var totalH;

//dimensions d'une cellule
var textSpacing = 1;

var scale = .2;
//méridien de base 
class Meridian
{
    constructor(maxW,maxH,minW,minH,celW,celH,posX,posY)
    {
        this._maxW = maxW;
        this._maxH = maxH;
        
        this._minW = 2;
        this._minH = 11;        
        
        this._celW = celW * scale;
        this._celH = celH * scale;

        this.position = new Point(posX,posY);

        this._posX = this.position.x;
        this._posY = this.position.y;


        
        this._breakPoints = new Array(4,7,11,15,21,33,39,44,47,50,54);    
        this._currentBreakPoint = 1;

        this._Width =  maxW * celW * scale ;
        this._Height =  maxH * celH * scale ;

        this.cell = new Array(maxW * maxH);

        this._totalCells =416;
    }

    drawMeridianTable()
    {
        let rows = new Array(this._maxW);
        let col = new Array(this._maxH);
    
        //draw collumn count
        for(var i = 0; i < this._maxW;i++)
        {
            rows[i] = new Square(   new Point(this._posX + i * this._celW + this._celW,
                                    this._posY),
                                    this._celW,
                                    this._celH  );
            rows[i].drawSquare();
            _ctx.font= "10px Arial";
            _ctx.fillText(  i+1,
                            this._posX + i * this._celW + textSpacing + this._celW,
                            this._posY + this._celW + textSpacing   );
        }
        //draw row count
        for(var i = 0; i < this._maxH; i++)
        {
            col[i] = new Square(    new Point(this._posX,
                                    this._posY + i*this._celH + this. _celH),
                                    this._celW,
                                    this._celH  );
            col[i].drawSquare();
            _ctx.font= "10px Arial";
            _ctx.fillText(  i+1,
                            this._posX + textSpacing,
                            this._posY + i*this._celH+this._celW + textSpacing + this._celH );        
        }
    }

    initMeridianCells()
    {

    }

    drawMeridianCells()
    {
        var startCollPos = 5;
        var currentCounter = 0;
        var currentRow = 0;

        var counter = 0;

        var row = 0;

        let cell = new Array(this._totalCells);
        var rows = [4,3,4,4,6,6,6,6,4,4,3,4];
        var col = [2,4,6,8,10,12,12,10,8,6,4,2];
        var res = Number(col[counter]) * Number(rows[counter])  ;

        var currentIndex = 0;

        var north = true;

        for(var i = 0; i < this._totalCells; i++)
        {
            if(currentCounter >= col[counter])
            {       
                currentRow++;                                        
                currentCounter = 0;     
            }     
        
            
            if( currentIndex >= res)
            {           
                counter++;  
                res = Number(col[counter]) * Number(rows[counter])  ;                        
                currentIndex = 0;

                if(north)
                    startCollPos--;
                else
                    startCollPos++;
                
                if(startCollPos < 0)
                {
                    startCollPos =0;
                    north =  false;
                }
            }

            cell[i] = new Square(   new Point(this._posX + (currentCounter+startCollPos) * this._celW ,
            this._posY + (currentRow) * this._celH ),
            this._celW,
            this._celH  );

            cell[i].drawSquare();              

            currentCounter++;        
            currentIndex++;   

        }         
    }
    getWidth()
    {
        return this._Width;
    }
    getHeight()
    {
        return this._Height;
    }

    getPosition()
    {
        return this.position;
    }
}

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
        
        this.ctx = document.getElementById("mainCanvas").getContext("2d");
    }   
    drawSquare()
    {
        this.ctx.rect(this.x,this.y,this.w,this.h);
        this.ctx.stroke();
    }
    getPosition()
    {
        return this.position;
    }
}

class Point
{
    constructor(x,y)
    {
        this.x = x;
        this.y = y;
    }

    getPoint()
    {
        return this;
    }
}



initialize();

for(var i = 0; i < totalMeridians; i++)
{
    meridians[i].drawMeridianCells();
}


function initialize()
{
    meridians = new Array(totalMeridians);
    
    for(var i = 0; i <totalMeridians; i++)
    {
        meridians[i] = new Meridian(meridianW, meridianH, meridianMinW, meridianMinH, cellW , cellH , i * meridianW * cellW * scale  ,0);   
        meridians[i].initMeridianCells();
    }

}



