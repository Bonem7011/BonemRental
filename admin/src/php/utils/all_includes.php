<?php

$isAdmin = (strpos(__DIR__, 'admin') !== false);

// 2. Sécurisation des chemins à l'aide de __DIR__
// __DIR__ ici vaut TOUJOURS le dossier "admin/src/php/utils"
if ($isAdmin) {
    // Si on est dans l'admin, on remonte de 2 niveaux (utils -> php -> src) pour atteindre src/
    $pathDb = __DIR__ . '/../db/db_pg_connect.php';
    $pathAutoloader = __DIR__ . '/../classes/Autoloader.class.php';
} else {
    // Si on est à la racine, on cible directement le dossier admin
    $pathDb = __DIR__ . '/../db/db_pg_connect.php';
    $pathAutoloader = __DIR__ . '/../classes/Autoloader.class.php';
}

// Grâce à __DIR__, le chemin est désormais absolu et indiscutable pour PHP
require_once $pathDb;
require_once $pathAutoloader;

// Enregistrement de l'autoloader
Autoloader::register();

// Connexion à la base de données
$cnx = Connexion::getInstance($dsn, $user, $pass);
?>