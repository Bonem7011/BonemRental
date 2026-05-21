<?php
class VehiculeDAO {
    private PDO $_cnx;

    public function __construct(PDO $cnx) {
        $this->_cnx = $cnx;
    }

    // Lire tous les véhicules avec leur gamme et leur carrosserie
    public function getVehicules(): array {
        $query = "SELECT v.*, g.nom_gamme, c.nom_carrosserie 
                  FROM vehicule v 
                  JOIN gamme g ON v.id_gamme = g.id_gamme
                  JOIN carrosserie c ON v.id_carrosserie = c.id_carrosserie 
                  ORDER BY v.id_vehicule DESC";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            print "Erreur : " . $e->getMessage();
            return [];
        }
    }

    // Ajouter un véhicule avec id_gamme et id_carrosserie avec la caution
    public function addVehicule(int $id_gamme, int $id_carrosserie, string $marque, string $modele, float $prix_achat, float $prix_location, float $caution, string $status, string $image): bool {
        $query = "INSERT INTO vehicule (id_gamme, id_carrosserie, marque, modele, prix_achat, prix_location, caution, status, image) 
                  VALUES (:id_gamme, :id_carrosserie, :marque, :modele, :prix_achat, :prix_location, :caution, :status, :image)";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->bindValue(':id_gamme', $id_gamme, PDO::PARAM_INT);
            $stmt->bindValue(':id_carrosserie', $id_carrosserie, PDO::PARAM_INT);
            $stmt->bindValue(':marque', $marque, PDO::PARAM_STR);
            $stmt->bindValue(':modele', $modele, PDO::PARAM_STR);
            $stmt->bindValue(':prix_achat', $prix_achat, PDO::PARAM_STR);
            $stmt->bindValue(':prix_location', $prix_location, PDO::PARAM_STR);
            $stmt->bindValue(':caution', $caution, PDO::PARAM_STR); // Liaison de la caution
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            $stmt->bindValue(':image', $image, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }

    }
    // Supprimer un véhicule
    public function deleteVehicule(int $id): bool {
        $query = "DELETE FROM vehicule WHERE id_vehicule = :id";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    // Récupérer un véhicule spécifique par son ID (pour pré-remplir le formulaire)
    public function getVehiculeById(int $id): ?array {
        $query = "SELECT * FROM vehicule WHERE id_vehicule = :id";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res ? $res : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    // Mettre à jour les informations d'un véhicule existant
    public function updateVehicule(int $id_vehicule, int $id_gamme, int $id_carrosserie, string $marque, string $modele, float $prix_achat, float $prix_location, float $caution, string $status, string $image): bool {
        $query = "UPDATE vehicule 
                  SET id_gamme = :id_gamme, id_carrosserie = :id_carrosserie, marque = :marque, modele = :modele, 
                      prix_achat = :prix_achat, prix_location = :prix_location, caution = :caution, status = :status, image = :image 
                  WHERE id_vehicule = :id_vehicule";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->bindValue(':id_vehicule', $id_vehicule, PDO::PARAM_INT);
            $stmt->bindValue(':id_gamme', $id_gamme, PDO::PARAM_INT);
            $stmt->bindValue(':id_carrosserie', $id_carrosserie, PDO::PARAM_INT);
            $stmt->bindValue(':marque', $marque, PDO::PARAM_STR);
            $stmt->bindValue(':modele', $modele, PDO::PARAM_STR);
            $stmt->bindValue(':prix_achat', $prix_achat, PDO::PARAM_STR);
            $stmt->bindValue(':prix_location', $prix_location, PDO::PARAM_STR);
            $stmt->bindValue(':caution', $caution, PDO::PARAM_STR);
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            $stmt->bindValue(':image', $image, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>