function onImageClick(x)
{
    var obj, dbParam, xmlhttp, myObj;
    obj = { "ID":x };
    dbParam = JSON.stringify(obj);
    xmlhttp = new XMLHttpRequest();

    showSideBar();
    onSearchDetails.innerHTML = "";
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) 
        {
            if(this.responseText != "")
            {
                myObj = JSON.parse(this.responseText);
                if(myObj[0].ImageOK != 0)
                {
                    var details =  document.getElementById("onClickDetails").childNodes;
                    childImage.src = "images/DB/Lot2/400-500/"+myObj[0].IDImage+".jpg";
                    childPseudo.innerHTML = myObj[0].Pseudo;
                    childCitation.innerHTML =  myObj[0].Slogan;
                    
                }
            }
        }
    }
    xmlhttp.open("POST", "selectImage.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("x=" + dbParam);   

}