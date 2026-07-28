document.addEventListener('DOMContentLoaded', function() {
    const conteneur = document.getElementById('calculateur-location');

    // Si on n'est pas sur la page de location, on arrête l'exécution du script
    if (!conteneur) return;

    // Récupération sécurisée de la donnée provenant du PHP
    const prixBaseJour = parseFloat(conteneur.dataset.prixBase);

    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const affichagePrixJour = document.getElementById('affichage_prix_jour');
    const affichagePrixTotal = document.getElementById('affichage_prix_total');
    const affichageJours = document.getElementById('affichage_jours');
    const optionRadios = document.querySelectorAll('.option-calc');

    function calculerPrix() {
        let jours = 0;

        if (dateDebut.value && dateFin.value) {
            const d1 = new Date(dateDebut.value);
            const d2 = new Date(dateFin.value);

            const difference = d2.getTime() - d1.getTime();
            jours = Math.ceil(difference / (1000 * 3600 * 24));

            if (jours === 0) jours = 1;

            if (jours < 0) {
                jours = 0;
                dateFin.value = dateDebut.value;
            }
        }

        let prixOptionsJour = 0;

        const paiementSelect = document.querySelector('input[name="option_paiement"]:checked');
        if (paiementSelect) prixOptionsJour += parseFloat(paiementSelect.value);

        const kmSelect = document.querySelector('input[name="option_km"]:checked');
        if (kmSelect) prixOptionsJour += parseFloat(kmSelect.value);

        const prixFinalJour = prixBaseJour + prixOptionsJour;
        const prixTotal = prixFinalJour * jours;

        affichageJours.innerText = jours;
        affichagePrixJour.innerText = prixFinalJour.toFixed(2).replace('.', ',') + ' €';
        affichagePrixTotal.innerText = prixTotal.toFixed(2).replace('.', ',') + ' €';
    }

    dateDebut.addEventListener('change', calculerPrix);
    dateFin.addEventListener('change', calculerPrix);

    optionRadios.forEach(radio => {
        radio.addEventListener('change', calculerPrix);
    });

    dateDebut.addEventListener('change', function() {
        dateFin.min = this.value;
    });
});