<?php
function getBD()
    {
    $connexion = new mysqli("localhost", "root", "", "globoscope");
    return $connexion;
}
header("Content-Type: application/json; charset=UTF-8");

$obj = json_decode($_POST["x"], false);
$connexion = getBD();
$reponse = "SELECT places.IDImage,mer,lat,lon FROM ".$obj->table." INNER JOIN places ON images.IDImage = places.IDImage WHERE images.IDImage >= 0 ORDER BY mer,lat,lon ";
$resultats = $connexion->query($reponse);

$output = array();

$output = $resultats->fetch_all(MYSQLI_ASSOC);

    echo json_encode($output);
   

?>
