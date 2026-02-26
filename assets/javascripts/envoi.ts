import '../styles/envoi.scss';

import axios from 'axios';

const // 
    prefix = 'transit',
    list = document.getElementById('task-list'),
    tasks = list?.children || [];

// Les tâches doivent être marquées comme terminées l'une après l'autre.
// Donc on parcours les tâches jusqu'à la dernière terminée. Seules la suivante ET celle-ci pourront être dé-cochées.

function manage_taches_cliquables() {

    // On vire toutes les tâches précédemment marquées comme cliquables, pour éviter toute erreur.
    [...document.getElementsByClassName('cliquable')].forEach(task => task.classList.remove('cliquable'));

    let prev_task;
    for (let task of tasks) {
        if (task.classList.contains('checked')) {
            task.classList.remove('cliquable');
            prev_task = task;
        } else {
            prev_task?.classList.add('cliquable');
            task.classList.add('cliquable');
            break;
        }
    }

    // Gestion du cas où toutes les tâches ont été faites
    if (list?.querySelector('.cliquable') === null) {
        tasks[tasks.length - 1]?.classList.add('cliquable');
    }
}

// Lors du clic sur une tâche de la liste, on bascule la classe "checked" sur l'élément cliqué, afin qu'elle apparaisse barrée ou non. 
list?.addEventListener('click', function (event) {
    const target = event.target as HTMLElement;

    if (target.tagName === 'LI' && target.classList.contains('cliquable')) {
        target.classList.toggle('checked');

        // Données à transmettre au serveur
        const // 
            checked = target.classList.contains('checked'),
            envoi_id = list.dataset.envoi,
            action_id = target.dataset.action;

        axios.post(`/${prefix}/envoi/marquer-action-traitee`, { envoi_id, action_id, checked })
            .then((response) => {
                const { success, statut_suivant } = response.data;
                // sauverObjetButton_loader.classList.add('fr-hidden');
                if (success) {
                    const badge_statut_libelle = document.getElementById('badge_statut_libelle');
                    if (badge_statut_libelle !== null)
                        badge_statut_libelle.innerText = statut_suivant !== null ? statut_suivant.libelle : 'Finalisé';
                }
            });
    }

    manage_taches_cliquables();

});

// Envoi des saisies dans le formulaire (référence, type d'envoi et quantité) dès qu'un changement survient
if (list != null)
    ['reference', 'type', 'quantite'].forEach(field => {
        const // 
            envoi_id = list.dataset.envoi,
            target = document.getElementById(`envoi_completion_${field}`) as HTMLInputElement;
        target?.addEventListener('input', (event) => {
            const value = target.value;
            axios.post(`/${prefix}/envoi/sauver-donnee`, { envoi_id, field, value })
                .then((response) => {
                    const { success, data } = response.data;
                    // if (success) {

                    // }
                });
        })
    })

manage_taches_cliquables();
