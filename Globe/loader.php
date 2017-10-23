<?php
    function getBD()
        {
            //$connexion = new mysqli("localhost", "id3116159_admingloboscope", "flocon123", "id3116159_globoscope");
            $connexion = new mysqli("localhost", "root", "", "globoscope");

            return $connexion;
    }

    $obj = json_decode($_POST["x"], false);
    $connexion = getBD();
    $reponse = "SELECT places.IDImage,NomFichier,mer,lat,lon FROM ".$obj->table." INNER JOIN places ON images.IDImage = places.IDImage WHERE images.IDImage >= 0 ORDER BY mer,lat,lon ";
    $resultats = $connexion->query($reponse);

    $output = array();

    $output = $resultats->fetch_all(MYSQLI_ASSOC);

    echo json_encode($output);  
?>
