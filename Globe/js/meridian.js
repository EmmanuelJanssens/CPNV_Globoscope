class Meridian
{
    constructor(maxW,maxH,minW,minH,celW,celH,posX,posY,scale,cellSpace,id)
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


        
        this._breakPoints = new Array(4,7,11,15,21,33,39,44,47,50,54);    
        this._currentBreakPoint = 1;

        this._Width =  maxW * celW * scale ;
        this._Height =  maxH * celH * scale;

        this.cell = new Array(maxW * maxH);
        this._cellSpace = cellSpace;
        this._totalCells =416;

        this._data = [] ;
        
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

    drawMeridianCells(scene,rayon,spacing)
    {
        var startCollPos = 5;
        var currentCounter = 0;
        var currentRow = 0;
        var longSpacing = spacing;

        var calcSpacing = 0;
        var counter = 0;


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
                if(north)
                {
                    calcSpacing++;
                }
                else
                {
                    calcSpacing--;
                }   
                
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
            
            //spherical W/ mercator projection
            //https://stackoverflow.com/questions/12732590/how-map-2d-grid-points-x-y-onto-sphere-as-3d-points-x-y-z
            
           /* var long = (this._posX  +(currentCounter+startCollPos) * (this._celW*(longSpacing)))/rayon;
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