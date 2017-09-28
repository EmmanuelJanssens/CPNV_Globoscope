class Meridian
{
    constructor(maxW,maxH,celW,celH,posX,posY,scale,cellSpace,id)
    {
        this.ID = id;


        
        this._maxW = maxW;
        this._maxH = maxH;
        
        this._minW = 2;
        this._minH = 11;        
        
        this._celW = celW * scale ;
        this._celH = celH * scale;

        this.position = new Point(posX,posY);

        this._posX = this.position.x  ;
        this._posY = this.position.y  ;


        
        this._Width =  maxW * celW * scale ;
        this._Height =  maxH * celH * scale;

        this.cell = new Array(maxW * maxH);
        this._cellSpace = cellSpace;
        this._totalCells =0;

        this._data = [] ;
        
    }

    drawMeridianCells(scene,rayon,spacing)
    {
        var startCollPos = 6;
        var currentCounter = 0;
        var currentRow = 0;
        var longSpacing = spacing;

        var calcSpacing = 0;
        var counter = 0;


        let cell = new Array(this._totalCells);
        var rows = [4,3,4,4,4,5,6,6,5,4,4,4,3,4];
        var col = [2,4,6,8,10,12,14,14,12,10,8,6,4,2];
        var res = Number(col[counter]) * Number(rows[counter])  ;

        var currentIndex = 0;

        var north = true;
        
        this._totalCells = 0;

        for(var i = 0; i < 14; i ++)
        {
            this._totalCells  += col[i] * rows[i];
        }

        console.log(this._totalCells);
        for(var i = this._totalCells; i > 0; i--)
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
                    startCollPos = 0;
                    north =  false;
                }       
                
            }
            
            //spherical W/ mercator projection
            //https://stackoverflow.com/questions/12732590/how-map-2d-grid-points-x-y-onto-sphere-as-3d-points-x-y-z
            
            /*var long = (this._posX  +(currentCounter+startCollPos) * (this._celW*(longSpacing)))/rayon;
            var lat = 2*Math.atan(Math.exp(  (this._posY + (currentRow) * (this._celH*spacing))/rayon )) - Math.PI/2;

            var _x = rayon* (Math.cos(lat) * Math.cos(long)) ;
            var _y =  rayon* (Math.sin(lat));
            var _z = rayon* (Math.cos(lat)  * Math.sin(long));

            cell[i] = new Square(new Point(_x,_y,_z ),
            this._celW  ,
            this._celH  );*/

            
            //flat
            cell[i] = new Square(   new Point((this._posX  +(currentCounter+startCollPos) * (this._celW*spacing)),
            (this._posY + (currentRow) * (this._celH*spacing)),0 ),
            this._celW,
            this._celH  );
            //cell[i].drawSquare(scene,0xffffff);
            cell[i].drawSquare(scene,this.ID,currentRow,currentCounter);  
            //cell[i].lookAtZero();   
            currentCounter++;        
            currentIndex++;   

        }         
    }
    getData()
    {
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