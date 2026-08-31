<?php
// On empêche tout affichage d'erreur HTML qui casserait le JSON
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once "../db/db_pg_connect.php";
require_once "../classes/Autoloader.class.php";
Autoloader::register();

// Initialisation de la connexion et du DAO
$cnx = Connexion::getInstance($dsn, $user, $pass);
$carrosserieDAO = new CarrosserieDAO($cnx);

$id_gamme = isset($_GET['id_gamme']) ? (int)$_GET['id_gamme'] : 0;
$resultats = [];

// Si l'ID est valide, on fait appel au DAO
if ($id_gamme > 0) {
    $resultats = $carrosserieDAO->getCarrosseriesByGamme($id_gamme);
}

// On renvoie la réponse au format JSON propre
header('Content-Type: application/json');
echo json_encode($resultats);