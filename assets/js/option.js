document.addEventListener("DOMContentLoaded", function() {
    const optionsInputs = document.querySelectorAll('.option-item');
    const affichageTotalHaut = document.getElementById('affichage_total_haut');
    const formOptions = document.getElementById('form-options');

    // Éléments de la modale
    const modalTotalOptions = document.getElementById('modal-total-options');
    const modalTotalTtc = document.getElementById('modal-total-ttc');

    // Récupération des données du PHP
    const sousTotalAvantOptions = parseFloat(formOptions.dataset.sousTotal) || 0;
    const nbJours = parseInt(formOptions.dataset.nbJours) || 1;

    function actualiserOptions() {
        let totalOptions = 0;

        optionsInputs.forEach(input => {
            let valeurOption = 0;
            let typeCalcul = input.dataset.type; // 'jour' ou 'fixe'

            // Cas d'une Checkbox (Switch)
            if (input.type === 'checkbox' && input.checked) {
                valeurOption = parseFloat(input.value) || 0;
            }
            // Cas d'un Select (Liste déroulante)
            else if (input.tagName === 'SELECT') {
                valeurOption = parseFloat(input.value) || 0; // On suppose que la value est déjà le prix * la quantité
            }

            // Application du multiplicateur si l'option est facturée par jour
            if (valeurOption > 0) {
                if (typeCalcul === 'jour') {
                    totalOptions += (valeurOption * nbJours);
                } else if (typeCalcul === 'fixe') {
                    totalOptions += valeurOption;
                }
            }
        });

        const nouveauTotalTtc = sousTotalAvantOptions + totalOptions;

        // Création du formateur (La touche Pro !)
        const formateurPrix = new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        });

        // Mise à jour de l'affichage principal (Haut de page)
        if(affichageTotalHaut) {
            affichageTotalHaut.textContent = 'Total : ' + formateurPrix.format(nouveauTotalTtc);
        }

        // Mise à jour de la Modale
        if(modalTotalOptions) {
            modalTotalOptions.textContent = formateurPrix.format(totalOptions);
        }
        if(modalTotalTtc) {
            modalTotalTtc.textContent = formateurPrix.format(nouveauTotalTtc);
        }

    }

    // Ajout des écouteurs d'événements sur chaque option
    optionsInputs.forEach(input => {
        input.addEventListener('change', actualiserOptions);
    });

    // Initialisation au chargement de la page
    actualiserOptions();
});