class Meridian
{
    constructor(maxW,maxH,celW,celH,posX,posY,scale,id)
    {
        this.ID = id;

        
        this._celW = celW * scale ;
        this._celH = celH * scale;

        var position = new Point(posX,posY);

        this._posX = position.x  ;
        this._posY = position.y  ;
    
        this._Width =  maxW * celW * scale ;
        this._Height =  maxH * celH * scale;

        this._totalCells =0;        

    }

    drawMeridianCells(scene,ray,orSpace)
    {
        var startCollPos = 6;

        var currentRow = 0;
        var currentIndex = 0;
        var north = true;

        var originalSpacing = orSpace;        
        var xSpacing = originalSpacing;
        var ySpacing = originalSpacing;
        var rayon = ray* xSpacing;   
        let cell;
        
        //utiliser pour ce reperer dans les nombres de colonnes/lignes maximal
        var counter = 0;
        //compe le nombre de collones que l'on a passé
        var collCounter = 0;
        //nombre de ligne
        var rowsNum = [4,3,4,4,4,5,6,6,5,4,4,4,3,4];
        //nombre de collones
        var collNum = [2,4,6,11,12,13,14,14,13,12,11,6,4,2];
        //total de carrés dans une section de méridiens
        //les sections sont définies par le changement de nombre de colionnes
        var block = Number(collNum[counter]) * Number(rowsNum[counter])  ;

        //initialiser a 0
        this._totalCells = 0;        
        for(var i = 0; i < 14; i ++)
        {
            this._totalCells  += collNum[i] * rowsNum[i];
        }
        cell = new Array(this._totalCells);
        
        

        //total cell width
        var totalWidth = collNum.length * originalSpacing;
        var totalHeight = 0;
        for( var i = 0; i < rowsNum.length; i++)
        {
            totalHeight += rowsNum[i];
        }
        totalHeight *=originalSpacing;

        for(var i = this._totalCells; i > 0; i--)
        {
            
            //si on dépasse le nombre de colones maximal on change de ligne
            if(collCounter >= collNum[counter])
            {       
                currentRow++;                                        
                collCounter = 0;               
            }     
            
            //si le compteur courrant est supérieur au nombre de block présent dans une section 
            //on diminue de 1 la position de départ de la prochaine section
            if( currentIndex >= block)
            {           
                counter++;  
                block = Number(collNum[counter]) * Number(rowsNum[counter])  ;                        
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
            
            /*if(collNum[counter] == 2)
            {
                //1 à 28
                //0 à 27
                //26
                xSpacing = (tot)/2;
            }
            else if(collNum[counter] == 4)
            {
                xSpacing = (tot)/4 ;
            }
            else if(collNum[counter] == 6)
            {
                xSpacing = (tot)/6;
            }
            else if(collNum[counter] == 8)
            {
                xSpacing = (tot)/8;    
            }
            else if(collNum[counter] == 10)
            {
                xSpacing = (tot)/10;    
            }
            else if(collNum[counter] == 12)
            {
                xSpacing = (tot)/12;  
            }
            else if(collNum[counter] == 14)
            {
                xSpacing = (tot)/14;
            }*/


            rayon = ray*originalSpacing;
            
            //Longitude
            //Avec espacement correct
            var long = ( this._posX * originalSpacing +(collCounter ) * (this._celW *(totalWidth/collNum[counter]) ))/rayon;
            //Avec espacement "incorect"
            //var long = ( this._posX * originalSpacing +(collCounter + startCollPos ) * (this._celW *xSpacing ))/rayon;   
            //Latitude
            var lat = 2*Math.atan(Math.exp( (this._posY *originalSpacing+ (currentRow) * (this._celH*originalSpacing))/rayon )) - Math.PI/2;

            var _x = rayon* (Math.cos(lat) * Math.cos(long)) ;
            var _y =  rayon* (Math.sin(lat));
            var _z = rayon* (Math.cos(lat)  * Math.sin(long));
            

            //flat
            /*  
            //Avec espacement correct
            var _x = this._posX * originalSpacing +(collCounter ) * (this._celW *(totalWidth/collNum[counter]) );
            //Avec espacement "incorrect"
            //var _x = this._posX * originalSpacing +(collCounter + startCollPos ) * (this._celW *xSpacing );            
            var _y = this._posY *originalSpacing+ (currentRow) * (this._celH*originalSpacing);
            var _z = 0;
            */
            cell[i] = new Square(new Point(_x,_y,_z ),
            this._celW  ,
            this._celH  );

            
            cell[i].drawSquare(scene,this.ID,currentRow,collCounter+startCollPos);  
            cell[i].lookAtZero();   
            collCounter++;        
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