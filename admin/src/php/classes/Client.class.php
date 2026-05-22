<?php
declare(strict_types=1);

class Client {
    public function __construct(
        public readonly int $id_client,
        public readonly string $email_client,
        public readonly string $nom_client,
        public readonly string $prenom_client,
        public readonly ?string $adresse_client,
        public readonly ?string $numero_client,
        public readonly ?string $telephone_client
    ) {}
}
?>