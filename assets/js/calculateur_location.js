document.addEventListener('DOMContentLoaded', function() {
    const conteneur = document.getElementById('calculateur-location');
    if (!conteneur) return;

    // On récupère le prix de base depuis le HTML
    const prixBaseJour = parseFloat(conteneur.dataset.prixBase);

    // Éléments de la page principale
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const affichagePrixJour = document.getElementById('affichage_prix_jour');
    const affichagePrixTotal = document.getElementById('affichage_prix_total');
    const affichageJours = document.getElementById('affichage_jours');
    const optionRadios = document.querySelectorAll('.option-calc');

    // Éléments du Modal (Popup)
    const modalJours = document.getElementById('modal_jours');
    const modalTotalLocation = document.getElementById('modal_total_location');
    const modalTotalOptions = document.getElementById('modal_total_options');
    const modalTva = document.getElementById('modal_tva');
    const modalTotalTtc = document.getElementById('modal_total_ttc');

    // Fonction maîtresse de calcul
    function calculerPrix() {
        let jours = 1; // Par défaut, on facture au moins 1 jour

        // 1. Calcul du nombre de jours exact
        if (dateDebut.value && dateFin.value) {
            const d1 = new Date(dateDebut.value);
            const d2 = new Date(dateFin.value);

            const difference = d2.getTime() - d1.getTime();
            const joursCalcules = Math.ceil(difference / (1000 * 3600 * 24));

            if (joursCalcules > 0) {
                jours = joursCalcules;
            }
        }

        // 2. Calcul des options supplémentaires (Paiement et Kilométrage)
        let prixOptionsJour = 0;

        const paiementSelect = document.querySelector('input[name="option_paiement"]:checked');
        if (paiementSelect) prixOptionsJour += parseFloat(paiementSelect.value);

        const kmSelect = document.querySelector('input[name="option_km"]:checked');
        if (kmSelect) prixOptionsJour += parseFloat(kmSelect.value);

        // 3. Totaux finaux
        const prixFinalJour = prixBaseJour + prixOptionsJour;

        const totalBaseLocation = prixBaseJour * jours;
        const totalOptions = prixOptionsJour * jours;
        const prixTotalTTC = prixFinalJour * jours;

        // Calcul d'une TVA simulative à 21% incluse dans le TTC (PrixTotal - PrixHT)
        const montantTva = prixTotalTTC - (prixTotalTTC / 1.21);

        // 4. Mise à jour des textes sur la page (format FR avec virgule)
        affichageJours.innerText = jours;
        affichagePrixJour.innerText = prixFinalJour.toFixed(2).replace('.', ',') + ' €';
        affichagePrixTotal.innerText = prixTotalTTC.toFixed(2).replace('.', ',') + ' €';

        // 5. Mise à jour de la Popup (Détails)
        if(modalJours) modalJours.innerText = jours;
        if(modalTotalLocation) modalTotalLocation.innerText = totalBaseLocation.toFixed(2).replace('.', ',') + ' €';
        if(modalTotalOptions) modalTotalOptions.innerText = totalOptions.toFixed(2).replace('.', ',') + ' €';
        if(modalTva) modalTva.innerText = montantTva.toFixed(2).replace('.', ',') + ' €';
        if(modalTotalTtc) modalTotalTtc.innerText = prixTotalTTC.toFixed(2).replace('.', ',') + ' €';
    }

    // ÉCOUTEURS D'ÉVÉNEMENTS (On utilise 'input' pour une réaction instantanée à la frappe/au clic)
    dateDebut.addEventListener('input', calculerPrix);
    dateFin.addEventListener('input', calculerPrix);

    optionRadios.forEach(radio => {
        radio.addEventListener('change', calculerPrix);
    });

    // Sécurité : Empêcher de rendre la voiture avant de l'avoir prise
    dateDebut.addEventListener('input', function() {
        dateFin.min = this.value;
        if(dateFin.value && dateFin.value < this.value) {
            dateFin.value = this.value;
            calculerPrix();
        }
    });

    // On lance un premier calcul dès le chargement de la page pour pré-remplir les prix
    calculerPrix();
});