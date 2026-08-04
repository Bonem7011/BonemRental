<?php
// On vérifie que l'ID est bien présent et n'est pas vide dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {

    // On sécurise l'ID en s'assurant que c'est un entier
    $id_commande = intval($_GET['id']);

    // Instanciation du DAO
    $commandeDAO = new CommandeDAO($cnx);

    // Appel de la méthode qui déclenche la fonction PostgreSQL
    $success = $commandeDAO->restituerVehicule($id_commande);

    if ($success) {
        // Redirection vers la liste avec un paramètre de succès
        header("Location: index_.php?page=locations.php&msg=restitution_ok");
        exit;
    } else {
        // Redirection vers la liste avec un paramètre d'erreur
        header("Location: index_.php?page=locations.php&msg=erreur");
        exit;
    }
} else {
    // Si on essaie d'accéder à la page sans ID, on renvoie vers la liste
    header("Location: index_.php?page=locations.php");
    exit;
}
?>