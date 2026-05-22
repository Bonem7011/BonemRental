<?php
class ClientDAO {
    private PDO $_cnx;

    public function __construct(PDO $cnx) {
        $this->_cnx = $cnx;
    }

    // Vérifier si un email existe déjà
    public function emailExists(string $email): bool {
        $query = "SELECT COUNT(*) FROM client WHERE email_client = :email";
        $stmt = $this->_cnx->prepare($query);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    // Inscrire un nouveau client
    public function addClient(string $email, string $password, string $nom, string $prenom, string $adresse, string $numero, string $telephone): bool {
        if ($this->emailExists($email)) {
            return false; // L'email est déjà pris
        }

        $hash = password_hash($password, PASSWORD_DEFAULT); // Sécurisation du mot de passe

        $query = "INSERT INTO client (email_client, password_client, nom_client, prenom_client, adresse_client, numero_client, telephone_client) 
                  VALUES (:email, :password, :nom, :prenom, :adresse, :numero, :telephone)";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hash, PDO::PARAM_STR); // On insère le hash, pas le texte en clair
            $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindValue(':adresse', $adresse, PDO::PARAM_STR);
            $stmt->bindValue(':numero', $numero, PDO::PARAM_STR);
            $stmt->bindValue(':telephone', $telephone, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Connecter un client
    public function getClient(string $email, string $password): ?Client {
        $query = "SELECT * FROM client WHERE email_client = :email";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data && password_verify($password, $data['password_client'])) {
                return new Client(
                    (int)$data['id_client'],
                    $data['email_client'],
                    $data['nom_client'],
                    $data['prenom_client'],
                    $data['adresse_client'],
                    $data['numero_client'],
                    $data['telephone_client']
                );
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }
}
?>
