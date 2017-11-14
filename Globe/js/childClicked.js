
//Requete XMLHTTP pour récupéerer des détails d'un enfant
//en paramètre (x) l'ID de l'image recherché
function onImageClick(x)
{
    var obj, dbParam, xmlhttp, myObj;
    obj = { "ID":x };
    dbParam = JSON.stringify(obj);


    xmlhttp = new XMLHttpRequest();
    //Declaré dans index.php
    showSideBar();
    onSearchDetails.innerHTML = "";
    
    //Quand la requête xml à été exécuté
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) 
        {
            if(this.responseText != "")
            {
                //convertit les données reçues depuis le fichier PHP correspondent (JsonEncode)
                myObj = JSON.parse(this.responseText);

                //Si l'image existe
                if(myObj[0].ImageOK != 0)
                {
                    var details =  document.getElementById("onClickDetails").childNodes;
                    childImage.src = "images/400-500/"+myObj[0].IDImage+".jpg";
                    childPseudo.innerHTML = myObj[0].Pseudo;
                    childCitation.innerHTML =  myObj[0].Slogan;
                    childRight.innerHTML = myObj[0].Droit;
                }
            }
        }
    }

    //exécuter la requete en mode POST avec les paramètres voulus (x) => ID
    xmlhttp.open("POST", "selectImage.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("x=" + dbParam);   

}