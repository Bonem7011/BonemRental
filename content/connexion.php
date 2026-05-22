<?php
if (isset($_POST['submit_connexion'])) {
    extract($_POST);
    $clientDAO = new ClientDAO($cnx);
    $client = $clientDAO->getClient($email, $password);

    if ($client !== null) {
        $_SESSION['client'] = 1;
        $_SESSION['id_client'] = $client->id_client;
        $_SESSION['prenom_client'] = $client->prenom_client;
        header("Location: index_.php?page=accueil.php");
        exit();
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<div class="row justify-content-center my-5">
    <div class="col-md-5">
        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white text-center py-3">
                <h4 class="mb-0"><i class="fa-solid fa-right-to-bracket"></i> Espace Client</h4>
            </div>
            <div class="card-body p-4">
                <?php if (isset($erreur)) echo "<div class='alert alert-danger'>$erreur</div>"; ?>

                <form method="post" action="index_.php?page=connexion.php">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Adresse Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Mot de passe</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="submit_connexion" class="btn btn-warning w-100 fw-bold mb-3">Se connecter</button>

                    <div class="text-center mt-3">
                        <span class="text-muted">Pas encore de compte ?</span>
                        <a href="index_.php?page=inscription.php" class="text-dark fw-bold text-decoration-none">S'inscrire</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>