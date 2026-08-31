document.addEventListener("DOMContentLoaded", function() {
    const radios = document.querySelectorAll('input[name="protection"]');
    const affichageTotalHaut = document.querySelector('.text-end .fs-4.fw-bold');
    const formProtections = document.getElementById('form-protections');

    // Éléments de la modale
    const modalNomProtection = document.getElementById('modal-nom-protection');
    const modalPrixProtection = document.getElementById('modal-prix-protection');
    const modalTotalTtc = document.getElementById('modal-total-ttc');
    const modalRemise = document.getElementById('modal-remise');

    // Nouveaux éléments de la modale pour les taxes
    const modalTaxesTotal = document.getElementById('modal-taxes-total');
    const modalTaxeWltp = document.getElementById('modal-taxe-wltp');
    const modalTaxeLocal = document.getElementById('modal-taxe-local');

    // Récupération des valeurs du formulaire (HTML)
    const prixBase = parseFloat(formProtections.dataset.prixBase) || 0;
    const nbJours = parseInt(formProtections.dataset.nbJours) || 1;
    const paquetKm = parseFloat(formProtections.dataset.paquetKm) || 0;
    const taxeWltp = parseFloat(formProtections.dataset.taxeWltp) || 0;
    const taxeLocale = parseFloat(formProtections.dataset.taxeLocale) || 0;

    const totalTaxes = taxeWltp + taxeLocale;

    function actualiserProtection() {
        let prixProtectionJour = 0;
        let nomProtection = "Aucune protection supplémentaire";
        let montantRemise = 0;

        radios.forEach(radio => {
            const card = radio.closest('.card');

            card.classList.remove('border-dark');
            radio.classList.remove('bg-dark', 'border-dark');

            if (radio.checked) {
                card.classList.add('border-dark');
                radio.classList.add('bg-dark', 'border-dark');

                prixProtectionJour = parseFloat(radio.value) || 0;

                const labelElement = card.querySelector('h4');
                if(labelElement) {
                    nomProtection = labelElement.textContent.trim();
                }

                // Application de la remise selon l'assurance choisie
                if (nomProtection.includes("Intermédiaire") || nomProtection.includes("Complète")) {
                    montantRemise = -93.73; // Montant de la remise (à dynamiser via PHP plus tard si besoin)
                }
            }
        });

        // Le calcul exact : Frais de base + Km + Protection totale + Taxes + Remise (qui est négative)
        const totalProtection = prixProtectionJour * nbJours;
        const nouveauTotal = prixBase + paquetKm + totalProtection + totalTaxes + montantRemise;

        // 🌟 Création du formateur (Cohérence avec le reste du projet)
        const formateurPrix = new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        });

        // Mise à jour de l'affichage principal
        if(affichageTotalHaut) {
            affichageTotalHaut.textContent = 'Total : ' + formateurPrix.format(nouveauTotal);
        }

        // Mise à jour de toutes les lignes de la modale
        if(modalNomProtection) modalNomProtection.textContent = nomProtection;
        if(modalPrixProtection) modalPrixProtection.textContent = formateurPrix.format(totalProtection);
        if(modalTotalTtc) modalTotalTtc.textContent = formateurPrix.format(nouveauTotal);

        if(modalRemise) {
            modalRemise.textContent = montantRemise !== 0 ? formateurPrix.format(montantRemise) : '0,00 €';
        }

        // Mise à jour des taxes dans la modale
        if(modalTaxesTotal) modalTaxesTotal.textContent = formateurPrix.format(totalTaxes);
        if(modalTaxeWltp) modalTaxeWltp.textContent = formateurPrix.format(taxeWltp);
        if(modalTaxeLocal) modalTaxeLocal.textContent = formateurPrix.format(taxeLocale);

    }

    radios.forEach(radio => {
        radio.addEventListener('change', actualiserProtection);
    });

    actualiserProtection();
});