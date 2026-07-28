<?php
class ClientDAO {
    private PDO $_cnx;

    public function __construct(PDO $cnx) {
        $this->_cnx = $cnx;
    }

    // Vérifier si un email existe déjà (On la garde si tu en as besoin ailleurs en AJAX par exemple)
    public function emailExists(string $email): bool {
        $query = "SELECT COUNT(*) FROM client WHERE email_client = :email";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur dans ClientDAO::emailExists - " . $e->getMessage());
            return false;
        }
    }

    // Inscrire un nouveau client
    public function addClient(string $email, string $password, string $nom, string $prenom, string $adresse, string $numero, string $telephone): bool {
        // La vérification de l'email est maintenant aussi gérée par la fonction SQL par sécurité,
        // mais on conserve ton hachage ici, c'est la meilleure pratique.
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Appel de la fonction plpgsql
        $query = "SELECT add_client(:email, :password, :nom, :prenom, :adresse, :numero, :telephone)";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hash, PDO::PARAM_STR);
            $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindValue(':adresse', $adresse, PDO::PARAM_STR);
            $stmt->bindValue(':numero', $numero, PDO::PARAM_STR);
            $stmt->bindValue(':telephone', $telephone, PDO::PARAM_STR);

            $stmt->execute();
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur dans ClientDAO::addClient - " . $e->getMessage());
            return false;
        }
    }

    // Connecter un client (Le SELECT reste en PHP, c'est autorisé)
    public function getClient(string $email, string $password): ?Client {
        $query = "SELECT * FROM client WHERE email_client = :email";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data && password_verify($password, $data['password_client'])) {
                return new Client(
                    id_client: (int)$data['id_client'],
                    email_client: $data['email_client'],
                    nom_client: $data['nom_client'],
                    prenom_client: $data['prenom_client'],
                    adresse_client: $data['adresse_client'],
                    numero_client: $data['numero_client'],
                    telephone_client: $data['telephone_client']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erreur dans ClientDAO::getClient - " . $e->getMessage());
            return null;
        }
    }
}
?>