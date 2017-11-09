<?php
    //Connexion a la base de donnée
    function getBD()
    {
        //Connexion sur hostinger
        //$connexion = new mysqli("localhost", "id3116159_admingloboscope", "flocon123", "id3116159_globoscope");

        //Connexion en local
        $connexion = new mysqli("localhost", "root", "", "globoscope");            
        return $connexion;
    }

    //récuperer les valeurs de l'objet JSON déclaré dans searchChild.js
    $obj = json_decode($_POST["x"], false);

    //Connexion à la base de donnée
    $connexion = getBD();

    //Requête pour récuperer les détails de l'enfant
    $reponse = 'SELECT Pseudo,IDPlace,IDImage,ImageOK  FROM images WHERE Pseudo LIKE "%'.$obj->Pseudo.'%"';

    //Executer la requête
    $resultats = $connexion->query($reponse);

    $output = array();

    //Recuperer les résultat de la requête
    $output = $resultats->fetch_all(MYSQLI_ASSOC);

    //Preparer les données pour être décodée dans searchCHild.js
    echo json_encode($output);  
?>
