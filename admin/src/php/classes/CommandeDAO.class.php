<?php
class CommandeDAO {
    private PDO $_cnx;

    public function __construct(PDO $cnx) {
        $this->_cnx = $cnx;
    }

    // Valider une transaction (Achat ou Location)
    public function creerCommande(int $id_client, int $id_vehicule, string $type, float $montant, ?string $date_debut = null, ?string $date_fin = null): bool {
        // Appel de la fonction plpgsql centralisant l'INSERT et l'UPDATE
        $query = "SELECT creer_commande(:id_client, :id_vehicule, :type, :montant, :date_debut, :date_fin)";

        try {
            $stmt = $this->_cnx->prepare($query);
            $stmt->execute([
                ':id_client' => $id_client,
                ':id_vehicule' => $id_vehicule,
                ':type' => $type,
                ':montant' => $montant,
                ':date_debut' => $date_debut,
                ':date_fin' => $date_fin
            ]);

            // Récupère le boolean retourné par la fonction PostgreSQL
            return (bool) $stmt->fetchColumn();

        } catch (PDOException $e) {
            // Journalisation de l'erreur pour ne pas polluer l'affichage
            error_log("Erreur dans CommandeDAO::creerCommande - " . $e->getMessage());
            return false;
        }
    }

    public function getToutesLesLocations() {
        // On appelle la nouvelle fonction qu'on a créée dans pgAdmin
        $sql = "SELECT * FROM get_toutes_les_locations()";

        try {
            $stmt = $this->_cnx->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            //  On utilise error_log et on renvoie un tableau vide
            error_log("Erreur dans CommandeDAO::getToutesLesLocations - " . $e->getMessage());
            return [];
        }
    }

    public function restituerVehicule($id_commande) {
        // Appel de la fonction PostgreSQL qui renvoie un booléen
        $sql = "SELECT restituer_vehicule(:id_commande) AS success";

        try {
            $stmt = $this->_cnx->prepare($sql);
            // Sécurisation du paramètre
            $stmt->bindParam(':id_commande', $id_commande, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['success']; // Retournera true ou false

        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la restitution (Cmd #$id_commande) : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les détails complets d'une location via son ID
     *
     * @param int $id_commande
     * @return array|false Retourne un tableau associatif avec les données, ou false si introuvable
     * @throws Exception en cas d'erreur SQL
     */
    public function getLocationById($id_commande) {
        // On utilise la fonction PostgreSQL
        $sql = "SELECT * FROM get_location_par_id(:id)";

        try {

            $stmt = $this->_cnx->prepare($sql);
            $stmt->bindParam(':id', $id_commande, PDO::PARAM_INT);
            $stmt->execute();

            // On utilise fetch car on attend un seul résultat (une seule commande)
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result;

        } catch (PDOException $e) {
            // On relance l'exception pour la traiter dans la vue
            throw new Exception("Erreur lors de la récupération des détails de la location : " . $e->getMessage());
        }
    }
}
