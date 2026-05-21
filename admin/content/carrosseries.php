<?php
$carrosserieDAO = new CarrosserieDAO($cnx);

// Ajout
if (isset($_POST['add_carrosserie'])) {
    $nom = trim($_POST['nom_carrosserie']);
    if (!empty($nom)) {
        $carrosserieDAO->addCarrosserie($nom);
        header("Location: index_.php?page=carrosseries.php");
        exit();
    }
}

// Suppression
if (isset($_GET['delete_id'])) {
    $carrosserieDAO->deleteCarrosserie((int)$_GET['delete_id']);
    header("Location: index_.php?page=carrosseries.php");
    exit();
}

$listeCarrosseries = $carrosserieDAO->getCarrosseries();
?>

<div class="container">
    <h2 class="mb-4"><i class="fa-solid fa-shapes"></i> Gestion des Carrosseries</h2>
    <div class="alert alert-info shadow-sm">
        <i class="fa-solid fa-circle-info"></i> Les gammes de BonemRental (Basique, Sport, Luxury) sont fixes. Toute nouvelle carrosserie ajoutée ici sera automatiquement disponible dans toutes les gammes.
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" action="index_.php?page=carrosseries.php" class="d-flex align-items-center">
                <input type="text" name="nom_carrosserie" class="form-control me-3" placeholder="Nouvelle carrosserie (ex: Break, Cabriolet...)" required>
                <button type="submit" name="add_carrosserie" class="btn btn-success fw-bold text-nowrap">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </button>
            </form>
        </div>
    </div>

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-dark">
            <tr>
                <th class="text-center" style="width: 10%;">ID</th>
                <th>Nom de la carrosserie</th>
                <th class="text-center" style="width: 15%;">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($listeCarrosseries as $c): ?>
                <tr>
                    <td class="text-center fw-bold"><?= $c['id_carrosserie'] ?></td>
                    <td><?= htmlspecialchars($c['nom_carrosserie']) ?></td>
                    <td class="text-center">
                        <a href="index_.php?page=carrosseries.php&delete_id=<?= $c['id_carrosserie'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette carrosserie ?');">
                            <i class="fa-solid fa-trash"></i> Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
