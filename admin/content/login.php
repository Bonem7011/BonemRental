<?php
// Traitement du formulaire au clic sur le bouton
if (isset($_POST['submit_login'])) {
    extract($_POST); // Récupère $login et $password

    $adminDAO = new AdminDAO($cnx);
    $admin = $adminDAO->getAdmin($login, $password);

    if ($admin !== null) {
        $_SESSION['admin'] = 1; // Création de la session sécurisée
        $_SESSION['nom_admin'] = $admin->nom_admin; // On mémorise le nom !
        header("Location: index_.php?page=accueil.php");
        exit();
    } else {
        $erreur = "Identifiants incorrects ou accès refusé.";
    }
}
?>
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-lg">
            <div class="card-header bg-dark text-warning text-center py-3">
                <h4><i class="fa-solid fa-lock"></i> Accès Réservé</h4>
            </div>
            <div class="card-body p-4">
                <?php if (isset($erreur)) { echo "<div class='alert alert-danger'>$erreur</div>"; } ?>
                <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                    <div class="mb-3">
                        <label for="login" class="form-label">Identifiant</label>
                        <input type="text" class="form-control" id="login" name="login" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" name="submit_login" class="btn btn-warning w-100 fw-bold">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</div>
