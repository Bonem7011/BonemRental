<?php
class CarrosserieDAO {
    private PDO $_cnx;

    public function __construct(PDO $cnx) {
        $this->_cnx = $cnx;
    }

    public function getCarrosseries(): array {
        // Le SELECT classique est toléré
        $query = "SELECT * FROM carrosserie ORDER BY id_carrosserie ASC";
        try {
            $stmt = $this->_cnx->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans CarrosserieDAO::getCarrosseries - " . $e->getMessage());
            return [];
        }
    }

    public function addCarrosserie(string $nom): bool {
        // Appel de la fonction plpgsql
        $query = "SELECT add_carrosserie(:nom)";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->execute([':nom' => $nom]);

            // fetchColumn récupère le retour booléen de la fonction PostgreSQL
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur dans CarrosserieDAO::addCarrosserie - " . $e->getMessage());
            return false;
        }
    }

    public function deleteCarrosserie(int $id): bool {
        // Appel de la fonction plpgsql
        $query = "SELECT delete_carrosserie(:id)";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->execute([':id' => $id]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur dans CarrosserieDAO::deleteCarrosserie - " . $e->getMessage());
            return false;
        }
    }

    public function getCarrosseriesByGamme(int $id_gamme): array {
        $query = "SELECT c.id_carrosserie, c.nom_carrosserie 
                  FROM carrosserie c 
                  JOIN gamme_carrosserie gc ON c.id_carrosserie = gc.id_carrosserie 
                  WHERE gc.id_gamme = :id_gamme 
                  ORDER BY c.nom_carrosserie ASC";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->bindValue(':id_gamme', $id_gamme, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans CarrosserieDAO::getCarrosseriesByGamme - " . $e->getMessage());
            return [];
        }
    }
}
