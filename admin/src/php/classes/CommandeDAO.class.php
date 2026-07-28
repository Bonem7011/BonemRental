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
}
?>