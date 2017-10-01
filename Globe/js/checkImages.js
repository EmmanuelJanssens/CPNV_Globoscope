function check(file,mer,lat,long)
{
    var fs = require('fs');
    if(fs.existsSync(file))
    {
        console.log(file + " exists");        
        return true;
    }
    else
    {
        console.log(file + " doesnt exists");        
        return false;
    }
}

