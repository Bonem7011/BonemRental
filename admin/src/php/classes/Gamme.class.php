<?php
declare(strict_types=1);

class Gamme {
    public function __construct(
        public readonly int $id_gamme,
        public readonly string $nom_gamme
    ) {}
}
?>
