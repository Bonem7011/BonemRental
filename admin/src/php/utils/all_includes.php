<?php
// On cible les fichiers par rapport à l'emplacement exact de all_includes.php
$pathDb = __DIR__ . '/../db/db_pg_connect.php';
$pathAutoloader = __DIR__ . '/../classes/Autoloader.class.php';

require_once $pathDb;
require_once $pathAutoloader;

// Enregistrement de l'autoloader
Autoloader::register();

// Connexion à la base de données
$cnx = Connexion::getInstance($dsn, $user, $pass);