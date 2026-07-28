<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index_.php?page=choix_transaction.php">
            <img src="admin/assets/images/logo.png" alt="Logo BonemRental" class="navbar-logo">
            BonemRental
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php
                // 1. On récupère le nom de la page actuellement visitée
                $page_courante = $_GET['page'] ?? ($_SESSION['page'] ?? 'choix_transaction.php');

                // 2. On définit la liste stricte des pages appartenant au "côté achat"
                $pages_achat = [
                        'accueil.php',
                        'catalogue.php',
                        'commande.php'
                ];
                // N'hésite pas à ajouter d'autres pages (ex: details_vehicule.php) dans ce tableau si besoin.

                // 3. On vérifie si la page courante fait partie de la liste des achats
                if (in_array($page_courante, $pages_achat)) {
                    ?>
                    <!-- Ce HTML ne s'affichera que si la condition au-dessus est vraie -->
                    <li class="nav-item">
                        <a class="nav-link" href="index_.php?page=catalogue.php">Catalogue</a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['client'])): ?>
                    <li class="nav-item me-3 text-light">
                        Bonjour, <strong><?= htmlspecialchars($_SESSION['prenom_client']) ?></strong>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger fw-bold" href="content/logout_client.php"><i class="fa-solid fa-power-off"></i> Quitter</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-light btn-sm" href="index_.php?page=connexion.php"><i class="fa-solid fa-user"></i> Mon Compte</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item ms-3 border-start ps-3">
                    <a class="nav-link text-warning" href="admin/index_.php">
                        <i class="fa-solid fa-lock"></i> Espace Admin
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>