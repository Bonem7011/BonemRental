<?php
class CarrosserieDAO {
    private PDO $_cnx;

    public function __construct(PDO $cnx) {
        $this->_cnx = $cnx;
    }

    public function getCarrosseries(): array {
        $query = "SELECT * FROM carrosserie ORDER BY id_carrosserie ASC";
        try {
            $stmt = $this->_cnx->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function addCarrosserie(string $nom): bool {
        try {
            // On commence une transaction pour sécuriser l'ajout multiple
            $this->_cnx->beginTransaction();

            // 1. On crée la carrosserie et on récupère son nouvel ID
            $stmt = $this->_cnx->prepare("INSERT INTO carrosserie (nom_carrosserie) VALUES (:nom) RETURNING id_carrosserie");
            $stmt->execute([':nom' => $nom]);
            $id_new = $stmt->fetchColumn();

            // 2. On l'associe automatiquement aux 3 gammes (1=Basique, 2=Sport, 3=Luxury)
            $stmtLink = $this->_cnx->prepare("INSERT INTO gamme_carrosserie (id_gamme, id_carrosserie) VALUES (1, :id), (2, :id), (3, :id)");
            $stmtLink->execute([':id' => $id_new]);

            $this->_cnx->commit();
            return true;
        } catch (PDOException $e) {
            $this->_cnx->rollBack();
            return false;
        }
    }

    public function deleteCarrosserie(int $id): bool {
        $query = "DELETE FROM carrosserie WHERE id_carrosserie = :id";
        try {
            $stmt = $this->_cnx->prepare($query);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>