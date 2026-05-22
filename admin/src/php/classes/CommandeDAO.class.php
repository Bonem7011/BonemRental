<?php
class CommandeDAO {
    private PDO $_cnx;

    public function __construct(PDO $cnx) {
        $this->_cnx = $cnx;
    }

    // Valider une transaction (Achat ou Location)
    public function creerCommande(int $id_client, int $id_vehicule, string $type, float $montant, ?string $date_debut = null, ?string $date_fin = null): bool {
        try {
            $this->_cnx->beginTransaction();

            // 1. Créer la commande
            $queryCmd = "INSERT INTO commande (id_client, id_vehicule, type_commande, montant_total, date_debut, date_fin) 
                         VALUES (:id_client, :id_vehicule, :type, :montant, :date_debut, :date_fin)";
            $stmtCmd = $this->_cnx->prepare($queryCmd);
            $stmtCmd->execute([
                ':id_client' => $id_client,
                ':id_vehicule' => $id_vehicule,
                ':type' => $type,
                ':montant' => $montant,
                ':date_debut' => $date_debut,
                ':date_fin' => $date_fin
            ]);

            // 2. Mettre à jour le statut du véhicule
            $nouveauStatut = ($type === 'Achat') ? 'Vendu' : 'Loué';
            $queryVeh = "UPDATE vehicule SET status = :statut WHERE id_vehicule = :id_vehicule";
            $stmtVeh = $this->_cnx->prepare($queryVeh);
            $stmtVeh->execute([
                ':statut' => $nouveauStatut,
                ':id_vehicule' => $id_vehicule
            ]);

            $this->_cnx->commit();
            return true;
        } catch (PDOException $e) {
            $this->_cnx->rollBack();
            return false;
        }
    }
}
?>