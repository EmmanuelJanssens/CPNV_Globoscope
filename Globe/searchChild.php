<?php

    //Variables d'authentifications
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "globoscope";
        
    //Connection à la BD
    $bdd = new PDO('mysql:host='.$servername.';dbname='.$dbname.';charset=utf8', $username, $password);

    // permet d'avoir plus de détails sur les erreurs retournées
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlPhoto ='SELECT Pseudo,IDPlace,IDImage,ImageOK  FROM images WHERE Pseudo LIKE \'%'.$obj->Pseudo.'%\'';
    $reponse = $bdd->query($sqlPhoto);

    $output = array();
    $output = $reponse->fetchAll ();    
    echo json_encode($output); 
?>
