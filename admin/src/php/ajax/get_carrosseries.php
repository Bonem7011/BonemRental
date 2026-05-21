<?php
// On active l'affichage des erreurs au cas où
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Ce fichier est appelé en arrière-plan, on a besoin de la connexion à la DB
require_once "../db/db_pg_connect.php";
require_once "../classes/Connexion.class.php";

$cnx = Connexion::getInstance($dsn, $user, $pass);

// On récupère l'ID de la gamme envoyé par JavaScript
$id_gamme = isset($_GET['id_gamme']) ? (int)$_GET['id_gamme'] : 0;

if ($id_gamme > 0) {
    // Requête pour récupérer les carrosseries liées à cette gamme
    $query = "SELECT c.id_carrosserie, c.nom_carrosserie 
              FROM carrosserie c
              JOIN gamme_carrosserie gc ON c.id_carrosserie = gc.id_carrosserie
              WHERE gc.id_gamme = :id_gamme
              ORDER BY c.nom_carrosserie ASC";

    $stmt = $cnx->prepare($query);
    $stmt->bindValue(':id_gamme', $id_gamme, PDO::PARAM_INT);
    $stmt->execute();

    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // On renvoie la réponse au format JSON pour JavaScript
    header('Content-Type: application/json');
    echo json_encode($resultats);
    exit();
}

echo json_encode([]);
?>
