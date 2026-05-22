<?php
if (isset($_POST['submit_inscription'])) {
    extract($_POST);
    $clientDAO = new ClientDAO($cnx);

    // Vérification basique des mots de passe
    if ($password !== $password_confirm) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        $success = $clientDAO->addClient($email, $password, $nom, $prenom, $adresse, $numero, $telephone);
        if ($success) {
            $message_ok = "Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.";
        } else {
            $erreur = "Cet email est déjà utilisé ou une erreur est survenue.";
        }
    }
}
?>

<div class="row justify-content-center my-5">
    <div class="col-md-8">
        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white text-center py-3">
                <h4 class="mb-0"><i class="fa-solid fa-user-plus"></i> Créer un compte</h4>
            </div>
            <div class="card-body p-4">
                <?php if (isset($erreur)) echo "<div class='alert alert-danger'>$erreur</div>"; ?>
                <?php if (isset($message_ok)) echo "<div class='alert alert-success'>$message_ok <a href='index_.php?page=connexion.php' class='alert-link'>Se connecter</a></div>"; ?>

                <form method="post" action="index_.php?page=inscription.php" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Prénom</label>
                        <input type="text" name="prenom" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nom</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Adresse Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mot de passe</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirm" class="form-control" required>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-muted">Coordonnées (Pour la facturation)</h5>

                    <div class="col-md-8">
                        <label class="form-label fw-bold">Rue</label>
                        <input type="text" name="adresse" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Numéro</label>
                        <input type="text" name="numero" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" required>
                    </div>

                    <div class="col-12 mt-4 text-center">
                        <button type="submit" name="submit_inscription" class="btn btn-dark w-50 fw-bold">S'inscrire</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>