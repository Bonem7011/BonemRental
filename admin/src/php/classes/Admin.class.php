<?php
declare(strict_types=1);

class Admin {
    public function __construct(
        public readonly int $id_admin,
        public readonly string $nom_admin,
        public readonly int $statut
    ) {}
}
?>
