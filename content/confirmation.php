<div class="container mt-5 mb-5 py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card border-0 shadow-lg p-5 rounded-4">

                <div class="mb-4">
                    <i class="fa-solid fa-circle-check text-success fa-6x"></i>
                </div>

                <h1 class="fw-bold mb-4">Félicitations !</h1>

                <?php if (isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success p-4 rounded-3 text-start mb-4">
                        <?= $_SESSION['flash_success'] ?>
                    </div>
                    <?php
                    // On efface le message pour qu'il ne réapparaisse pas si on actualise la page
                    unset($_SESSION['flash_success']);
                    ?>
                <?php else: ?>
                    <p class="text-muted fs-5 mb-4">Votre transaction a bien été enregistrée.</p>
                <?php endif; ?>

                <div class="p-4 bg-light rounded-3 mb-5 text-start">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-envelope me-2"></i> Et maintenant ?</h5>
                    <p class="mb-0 text-muted">Un récapitulatif complet de votre commande vient de vous être envoyé par e-mail. Il contient toutes les informations nécessaires pour la suite de la procédure.</p>
                </div>

                <div class="mt-2">
                    <a href="index_.php?page=choix_transaction.php" class="btn btn-dark btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm">
                        <i class="fa-solid fa-house me-2"></i> Retour à l'accueil
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>