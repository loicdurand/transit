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

if (list !== null) {

    const envoi_id = list.dataset.envoi;

    // Lors du clic sur une tâche de la liste, on bascule la classe "checked" sur l'élément cliqué, afin qu'elle apparaisse barrée ou non. 
    list?.addEventListener('click', function (event) {
        const target = event.target as HTMLElement;

        if (target.tagName === 'LI' && target.classList.contains('cliquable')) {
            target.classList.toggle('checked');

            // Données à transmettre au serveur
            const // 
                checked = target.classList.contains('checked'),
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
    if (list != null) {
        ['reference', 'type', 'quantite'].forEach(field => {
            const target = document.getElementById(`envoi_completion_${field}`) as HTMLInputElement;
            target?.addEventListener('input', (event) => {
                const value = target.value;
                axios.post(`/${prefix}/envoi/sauver-donnee`, { envoi_id, field, value })
                    .then((response) => {
                        const { success, data } = response.data;
                        if (success)
                            location.reload();
                    });
            })
        })

        manage_taches_cliquables();

        /**
         * MODALE D'AJOUT / GESTION DE RÉFÉRENCE
         */

        const // 
            modale_numero = document.getElementById('modal-creer-numero'),
            modale_numero_trigger = document.getElementById('modal-creer-numero--trigger'),
            modale_numero_submit = document.getElementById('modal-creer-numero--submit'),
            modale_numero_loader = document.getElementById('modal-creer-numero--loader'),

            inputs_numero = modale_numero?.getElementsByTagName('input'),

            libelle_input = (document.getElementById('numero_libelle') as HTMLInputElement),
            valeur_input = (document.getElementById('numero_valeur') as HTMLInputElement);

        if (modale_numero_submit && inputs_numero && inputs_numero.length) {

            [...inputs_numero].forEach(input_numero => {
                input_numero?.addEventListener('keyup', (event) => {
                    const values = [...inputs_numero].filter(input => input.value === '');
                    if (values.length) {
                        modale_numero_submit?.setAttribute('disabled', 'disabled');
                    } else {
                        modale_numero_submit?.removeAttribute('disabled');
                    }
                });
            });

            /**
            * Lors du clic sur le Submit (de la modale des numéros), on envoie les données saisies au serveur pour sauvegarde en BDD
            */

            modale_numero_submit?.addEventListener('click', (event) => {

                if (modale_numero_loader === null)
                    return false;

                modale_numero_loader.classList.remove('fr-hidden');
                const // 
                    target = event.target,
                    numero_id = target && 'dataset' in target && (target.dataset as { id: string }).id,
                    libelle = libelle_input?.value,
                    valeur = valeur_input?.value;

                axios.post(`/${prefix}/envoi/sauver-numero`, { envoi_id, numero_id, libelle, valeur })
                    .then((response) => {
                        const { success, data } = response.data;
                        modale_numero_loader.classList.add('fr-hidden');
                        modale_numero_submit.setAttribute('disabled', 'disabled');
                        modale_numero_submit.dataset.id = '';
                        fermer_modale_numero();
                        if (success)
                            location.reload();
                    })
                    .catch(err => {
                        modale_numero_loader.classList.add('fr-hidden');
                    });
            });

            const reference_triggers = [...document.getElementsByClassName('modal-creer-numero--trigger')];
            reference_triggers.forEach(trigger => {
                trigger.addEventListener('click', (event) => {
                    const // 
                        target = event.currentTarget;
                    if (target && 'dataset' in target) {
                        const { id, libelle, valeur } = target.dataset as { id: string, libelle: string, valeur: string };
                        libelle_input.value = libelle;
                        valeur_input.value = valeur;
                        modale_numero_submit.dataset.id = id;
                        modale_numero_submit?.removeAttribute('disabled');
                        ouvrir_modale_numero();
                    }
                });
            })

        }

        const boutons_suppr_numero = [...document.getElementsByClassName('bouton-supprimer-numero')];
        boutons_suppr_numero.forEach(btn => {
            btn.addEventListener('click', (event) => {
                const // 
                    target = event.target,
                    numero_id = target && 'dataset' in target && (target.dataset as { id: string }).id;
                if (confirm('Êtes-vous sûr de vouloir supprimer cette référence?'))
                    axios.delete(`/${prefix}/envoi/supprimer-numero/${numero_id}`)
                        .then((response) => {
                            const { success, data } = response.data;
                            if (success)
                                location.reload();
                        });
            })
        });

        function ouvrir_modale_numero() {
            modale_numero_trigger?.setAttribute('data-fr-opened', 'true');
        }

        function fermer_modale_numero() {
            modale_numero_trigger?.setAttribute('data-fr-opened', 'false');
        }

    }

    const boutons_suppr_fichier = [...document.getElementsByClassName('bouton-supprimer-fichier')];
    boutons_suppr_fichier.forEach(btn => {
        btn.addEventListener('click', (event) => {
            const // 
                target = event.target,
                fichier_id = target && 'dataset' in target && (target.dataset as { id: string }).id;
            if (confirm('Êtes-vous sûr de vouloir supprimer ce document?'))
                axios.delete(`/${prefix}/envoi/supprimer-fichier/${fichier_id}`)
                    .then((response) => {
                        const { success, data } = response.data;
                        if (success)
                            location.reload();
                    });
        })
    });


    /**
     * GESTION DE L'UPLOAD DE FICHIERS (fait avec Gemini)
     */

    const uploadFiles = async (): Promise<void> => {

        const modale_fichier_loader = document.getElementById('modal-creer-fichier--loader'),

        if (modale_fichier_loader === null)
            return;

        modale_fichier_loader.classList.remove('fr-hidden');

        // 1. Récupération de l'élément input via son ID
        const fileInput = document.getElementById('upload-id') as HTMLInputElement;

        // Vérification de la présence de fichiers
        if (!fileInput.files || fileInput.files.length === 0) {
            console.error("Aucun fichier sélectionné.");
            return;
        }

        // 2. Préparation des données avec FormData
        const formData = new FormData();

        // On boucle sur les fichiers (puisque l'attribut 'multiple' est présent)
        Array.from(fileInput.files).forEach((file) => {
            formData.append('upload', file); // 'upload' correspond au nom attendu par votre serveur
        });

        try {
            // 3. Envoi de la requête avec Axios
            axios.post(`/${prefix}/envoi/upload/${envoi_id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                // Optionnel : Suivre la progression de l'upload
                onUploadProgress: (progressEvent) => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / (progressEvent.total ?? 1));
                    console.log(`Progression : ${percentCompleted}%`);
                },
            })
                .then((response) => {
                    const { success, data } = response.data;
                    if (success) {
                        location.reload();
                    }
                });

        } catch (error) {
            console.error('Erreur lors de l\'envoi :', error);
        }
    };

    // Exemple d'attachement à votre bouton
    const submitBtn = document.getElementById('modal-creer-fichier--submit');
    submitBtn?.addEventListener('click', (e) => {
        e.preventDefault(); // Empêche le comportement par défaut du formulaire
        uploadFiles();
    });

}