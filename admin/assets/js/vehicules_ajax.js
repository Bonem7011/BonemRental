// On attend que tout le HTML soit bien chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', function() {

    const gammeSelect = document.getElementById('id_gamme');

    // On vérifie que l'élément existe bien sur la page pour éviter les erreurs
    if (gammeSelect) {
        gammeSelect.addEventListener('change', function() {
            const idGamme = this.value;
            const carrosserieSelect = document.getElementById('id_carrosserie');

            carrosserieSelect.innerHTML = '<option value="">Chargement...</option>';

            if (!idGamme) {
                carrosserieSelect.innerHTML = '<option value="">Sélectionner une gamme d\'abord...</option>';
                return;
            }

            fetch('src/php/ajax/get_carrosseries.php?id_gamme=' + idGamme)
                .then(response => response.json())
                .then(data => {
                    carrosserieSelect.innerHTML = '<option value="">Sélectionner une carrosserie...</option>';

                    if (data.length === 0) {
                        carrosserieSelect.innerHTML = '<option value="">Aucune carrosserie disponible</option>';
                        return;
                    }

                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id_carrosserie;
                        option.textContent = item.nom_carrosserie;
                        carrosserieSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erreur lors du chargement AJAX:', error);
                    carrosserieSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        });
    }
});