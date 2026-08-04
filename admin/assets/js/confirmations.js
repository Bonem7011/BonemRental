document.addEventListener('DOMContentLoaded', function() {
    // On récupère tous les boutons de suppression
    const deleteButtons = document.querySelectorAll('.js-confirm-delete');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            // On récupère le message spécifique au bouton, ou on met un message par défaut
            const message = this.getAttribute('data-message') || 'Êtes-vous sûr de vouloir supprimer cet élément ?';

            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // On sélectionne tous les boutons de restitution
    const btnsRestitution = document.querySelectorAll('.btn-confirm-restitution');

    btnsRestitution.forEach(function(btn) {
        btn.addEventListener('click', function(event) {
            // On demande confirmation
            const confirmation = confirm('Confirmez-vous la restitution de ce véhicule ? Le statut passera en Terminé.');

            // Si l'administrateur clique sur "Annuler", on empêche le lien de s'ouvrir
            if (!confirmation) {
                event.preventDefault();
            }
        });
    });
});