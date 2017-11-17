<?php

    //Variables d'authentifications
    /*$servername = "localhost";
    $username = "globescope_img";
    $password = "aPH7p33G9t";
    $dbname = "globescope_img";*/

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "globoscope"; 

    //Connection à la BD
    $bdd = new PDO('mysql:host='.$servername.';dbname='.$dbname.';charset=utf8', $username, $password);

    // permet d'avoir plus de détails sur les erreurs retournées
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Gert JSON Params
    $obj = json_decode($_POST["x"], false);    
    
    $query = "";
    if(isset($_POST['Mode']))
    {
        $mode = $_POST["Mode"];
        
        if($mode == "search")
        {
            $query = 'SELECT Pseudo,IDPlace,IDImage,ImageOK  FROM images WHERE Pseudo LIKE \'%'.$obj->Pseudo.'%\'';
        }
        else if($mode =="click")
        {
            $query =  "SELECT IDPlace,IDImage,Pseudo,Slogan,Droit,ImageOK FROM images WHERE IDPlace = ".$obj->ID;
        }
        else if ($mode == "load")
        {
            $query = "SELECT IDPlace,IDImage,mer,lat,lon,ImageOK FROM ".$obj->table;
        }
        else
        {
            //nothing
        }
    }

    $result = $bdd->query($query);

    $output = array();
    $output = $result->fetchAll ();    

    echo json_encode($output); 
?>
