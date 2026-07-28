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