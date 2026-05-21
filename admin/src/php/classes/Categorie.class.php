<?php
declare(strict_types=1);

class Categorie {
    public function __construct(
        public readonly int $id_categorie,
        public readonly string $nom_categorie
    ) {}
}
?>
