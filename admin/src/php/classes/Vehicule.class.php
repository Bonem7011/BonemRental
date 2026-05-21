<?php
declare(strict_types=1);

class Vehicule {
    public function __construct(
        public readonly int $id_vehicule,
        public readonly int $id_categorie,
        public readonly string $marque,
        public readonly string $modele,
        public readonly ?float $prix_achat,
        public readonly ?float $prix_location,
        public readonly string $status,
        public readonly ?string $image
    ) {}
}
?>
