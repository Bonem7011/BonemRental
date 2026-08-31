document.addEventListener('DOMContentLoaded', function() {
    // On cherche l'élément qui affiche le total (il n'existe que sur la page Achat)
    const affichageTotal = document.getElementById('affichage-total');

    // Si l'élément existe, on exécute la logique
    if (affichageTotal) {
        // On récupère tous les boutons radio de la livraison
        const radios = document.querySelectorAll('input[name="mode_retrait"]');

        // On récupère le prix de base de la voiture stocké dans l'attribut HTML
        const basePrice = parseFloat(affichageTotal.getAttribute('data-base-price'));

        // Fonction qui recalcule et affiche le total
        function updateTotal() {
            let extraCost = 0;

            // On cherche le bouton radio qui est actuellement coché
            const selectedOption = document.querySelector('input[name="mode_retrait"]:checked');

            // Si une option est cochée, on récupère sa valeur (0 ou 250)
            if (selectedOption) {
                extraCost = parseFloat(selectedOption.value);
            }

            // On additionne le prix de la voiture et les frais éventuels
            const newTotal = basePrice + extraCost;

            // On met à jour le texte avec le bon format monétaire (ex: 165 250,00 €)
            affichageTotal.textContent = new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(newTotal);
        }

        // On écoute chaque changement sur les boutons radio pour déclencher le calcul
        radios.forEach(radio => {
            radio.addEventListener('change', updateTotal);
        });
    }
});