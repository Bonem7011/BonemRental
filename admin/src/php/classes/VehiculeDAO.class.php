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
            error_log("Erreur dans VehiculeDAO::getVehicules - " . $e->getMessage());
            return [];
        }
    }

    // Ajouter un véhicule avec id_gamme et id_carrosserie avec la caution
    public function addVehicule(int $id_gamme, int $id_carrosserie, string $marque, string $modele, ?float $prix_achat, ?float $prix_location, float $caution, string $status, string $image): bool {
        $query = "SELECT ajouter_vehicule(:id_gamme, :id_carrosserie, :marque, :modele, :prix_achat, :prix_location, :caution, :status, :image)";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->execute([
                ':id_gamme' => $id_gamme,
                ':id_carrosserie' => $id_carrosserie,
                ':marque' => $marque,
                ':modele' => $modele,
                ':prix_achat' => $prix_achat,
                ':prix_location' => $prix_location,
                ':caution' => $caution,
                ':status' => $status,
                ':image' => $image
            ]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur dans VehiculeDAO::addVehicule - " . $e->getMessage());
            return false;
        }
    }

    // Supprimer un véhicule
    public function deleteVehicule(int $id): bool {
        $query = "SELECT supprimer_vehicule(:id)";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->execute([':id' => $id]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur dans VehiculeDAO::deleteVehicule - " . $e->getMessage());
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
            error_log("Erreur dans VehiculeDAO::getVehiculeById - " . $e->getMessage());
            return null;
        }
    }

    // Mettre à jour les informations d'un véhicule existant
    public function updateVehicule(int $id_vehicule, int $id_gamme, int $id_carrosserie, string $marque, string $modele, ?float $prix_achat, ?float $prix_location, float $caution, string $status, string $image): bool {
        $query = "SELECT modifier_vehicule(:id_vehicule, :id_gamme, :id_carrosserie, :marque, :modele, :prix_achat, :prix_location, :caution, :status, :image)";
        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->execute([
                ':id_vehicule' => $id_vehicule,
                ':id_gamme' => $id_gamme,
                ':id_carrosserie' => $id_carrosserie,
                ':marque' => $marque,
                ':modele' => $modele,
                ':prix_achat' => $prix_achat,
                ':prix_location' => $prix_location,
                ':caution' => $caution,
                ':status' => $status,
                ':image' => $image
            ]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur dans VehiculeDAO::updateVehicule - " . $e->getMessage());
            return false;
        }
    }

    // Récupérer les véhicules selon des filtres (Recherche, Gamme, Carrosserie)
    public function getVehiculesFiltres(string $recherche = '', string $id_gamme = '', string $id_carrosserie = ''): array {
        $query = "SELECT v.*, g.nom_gamme, c.nom_carrosserie 
                  FROM vehicule v 
                  JOIN gamme g ON v.id_gamme = g.id_gamme
                  JOIN carrosserie c ON v.id_carrosserie = c.id_carrosserie 
                  WHERE 1=1";

        $params = [];

        // Filtre par texte (Marque ou Modèle)
        if (!empty($recherche)) {
            $query .= " AND (v.marque ILIKE :recherche OR v.modele ILIKE :recherche)";
            $params[':recherche'] = '%' . $recherche . '%';
        }
        // Filtre par Gamme
        if (!empty($id_gamme)) {
            $query .= " AND v.id_gamme = :id_gamme";
            $params[':id_gamme'] = (int)$id_gamme;
        }
        // Filtre par Carrosserie
        if (!empty($id_carrosserie)) {
            $query .= " AND v.id_carrosserie = :id_carrosserie";
            $params[':id_carrosserie'] = (int)$id_carrosserie;
        }

        $query .= " ORDER BY v.id_vehicule DESC";

        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans VehiculeDAO::getVehiculesFiltres - " . $e->getMessage());
            return [];
        }
    }

    // Récupère la liste des véhicules disponibles pour la location.
    public function getVehiculesLocation() {
        try {
            // Sélectionne les véhicules avec un prix de location défini, supérieur à zéro, et dont le statut est disponible
            $sql = "SELECT * FROM vehicule 
                    WHERE prix_location IS NOT NULL 
                    AND prix_location > 0 
                    AND status = 'Disponible'";

            $stmt = $this->_cnx->prepare($sql);
            $stmt->execute();

            // Retourne les résultats sous forme de tableau associatif
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Enregistrement de l'erreur dans les logs pour faciliter le débogage
            error_log("Erreur PDO dans getVehiculesLocation : " . $e->getMessage());

            // Retourne un tableau vide pour ne pas faire planter l'affichage de catalogue_location.php
            return [];
        }
    }
}




