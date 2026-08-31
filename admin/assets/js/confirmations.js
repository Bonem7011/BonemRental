// On attend que tout le HTML soit bien chargé UNE SEULE FOIS
document.addEventListener('DOMContentLoaded', function() {

    // 1. GESTION DES SUPPRESSIONS GÉNÉRIQUES
    const deleteButtons = document.querySelectorAll('.js-confirm-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            const message = this.getAttribute('data-message') || 'Êtes-vous sûr de vouloir supprimer cet élément ?';
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });

    // 2. GESTION DES RESTITUTIONS DE VÉHICULES

    const btnsRestitution = document.querySelectorAll('.btn-confirm-restitution');
    btnsRestitution.forEach(function(btn) {
        btn.addEventListener('click', function(event) {
            const confirmation = confirm('Confirmez-vous la restitution de ce véhicule ? Le statut passera en Terminé.');
            if (!confirmation) {
                event.preventDefault();
            }
        });
    });

});