function searchChild()
{
    var objJSON,dbParam,xmlhttp,myObj;

    //les paramètres a passer dans la requête SQL
    //SearchTextBox => input de la barre de recherche
    objJSON = {"Pseudo":SearchTextBox.value }
    dbParam = JSON.stringify(objJSON);

    xmlhttp = new XMLHttpRequest();

    showSideBar();
    onSearchDetails.innerHTML = "";
    
    xmlhttp.onreadystatechange = function()
    {
        if(this.readyState ==4 && this.status==200)
        {

            ///Afficher un tableau avec tout les résultats
            if(this.responseText != "")
            {
                myObj = JSON.parse(this.responseText);

                
                //pour partir d'une div vide
                //tableau de résultat de la recherche/requete SQL
                //https://stackoverflow.com/questions/15860683/onclick-event-in-a-for-loop
                var total = myObj.length;
                for(var i = 0; i < myObj.length; i++)
                (function(i)
                {
                    if(myObj[i].ImageOK != 0)
                    {		
                        var details =  document.getElementById("onSearchDetails");
                        var det = document.createElement('div');

                        var img = document.createElement('img');
                        var searchPseudo = document.createElement('p');

                        img.id=myObj[i].IDImage;
                        img.src = "images/DB/64-64/"+myObj[i].IDImage+".jpg"
                        img.onclick = function()
                        {
                            onImageClick(myObj[i].IDPlace);
                        }

                        searchPseudo.innerHTML = myObj[i].Pseudo;
                        det.appendChild(img);
                        det.appendChild(searchPseudo);

                        details.appendChild(det);

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