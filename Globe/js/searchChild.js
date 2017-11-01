function searchChild()
{
    var objJSON,dbParam,xmlhttp,myObj;

    //les paramètres a passer dans la requête SQL
    objJSON = {"Pseudo":SearchTextBox.value }
    dbParam = JSON.stringify(objJSON);

    xmlhttp = new XMLHttpRequest();

    hideSearchBar();
    showSideBar();

    xmlhttp.onreadystatechange = function()
    {
        if(this.readyState ==4 && this.status==200)
        {

            ///Afficher un tableau avec tout les résultats
            if(this.responseText != "")
            {
                myObj = JSON.parse(this.responseText);

                //pour partir d'une div vide
                childDetails.innerHTML = "";
                //tableau de résultat de la recherche/requete SQL
                //https://stackoverflow.com/questions/15860683/onclick-event-in-a-for-loop
                var total = myObj.length;
                for(var i = 0; i < myObj.length; i++)
                (function(i)
                {
                    if(myObj[i].ImageOK != 0)
                    {			
                       			
                    }
                })(i);
            }
            else
            {
                hideSideBar();
                alert("Aucun resultat");
            }					
        }
    }

    xmlhttp.open("POST", "searchChild.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("x=" + dbParam);   	
}