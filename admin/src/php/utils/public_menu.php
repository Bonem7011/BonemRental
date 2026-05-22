<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index_.php?page=accueil.php">
            <img src="admin/assets/images/logo.png" alt="Logo BonemRental" style="height: 40px; margin-right: 10px;">
            BonemRental
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index_.php?page=accueil.php">Catalogue</a>
                </li>
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