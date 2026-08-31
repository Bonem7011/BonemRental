<?php
class GammeDAO {
    private PDO $_cnx;

    public function __construct(PDO $cnx) {
        $this->_cnx = $cnx;
    }

    public function getGammes(): array {
        $query = "SELECT * FROM gamme ORDER BY id_gamme ASC";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $gammes = [];
            foreach ($data as $row) {
                $gammes[] = new Gamme((int)$row['id_gamme'], $row['nom_gamme']);
            }
            return $gammes;
        } catch (PDOException $e) {
            // Remplacement du print pour éviter de polluer le HTML
            error_log("Erreur dans GammeDAO::getGammes - " . $e->getMessage());
            return [];
        }
    }
}
