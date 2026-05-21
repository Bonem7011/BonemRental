<?php
class AdminDAO {
    private PDO $_cnx;

    public function __construct(PDO $cnx) {
        $this->_cnx = $cnx;
    }

    public function getAdmin(string $login, string $password): ?Admin {
        // Appel de la fonction plpgsql sous forme de vue
        $query = "SELECT * FROM get_admin(:login, :password)";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->bindValue(':login', $login, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password, PDO::PARAM_STR);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return null;
            }
            // Sécurité exigée par le cours en cas d'échec
            if ((int)$data['id_admin'] === -1 && $data['nom_admin'] === '' && (int)$data['statut'] === -1) {
                return null;
            }

            return new Admin(
                id_admin: (int)$data['id_admin'],
                nom_admin: $data['nom_admin'],
                statut: (int)$data['statut']
            );
        } catch (PDOException $e) {
            print "Erreur : " . $e->getMessage();
            return null;
        }
    }
}
?>
