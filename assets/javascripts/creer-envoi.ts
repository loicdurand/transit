import axios from 'axios';

const // 
    prefix = 'transit',
    input_titre = document.getElementById('envoi_titre');

// Forçage du titre en MAJUSCULES
input_titre?.addEventListener('keyup', (event) => {
    const target: (EventTarget | null) = event.target;
    if (target === null)
        return false;

    (target as HTMLInputElement).value = (target as HTMLInputElement).value.toUpperCase();
});

/**
* +=================================+
* | PARTIE CRÉATION DE DESTINATAIRE |
* +=================================+
*/

const //

    modale_destinataire_trigger = document.getElementById('modal-creer-destinataire--trigger'),
    modale_destinataire_submit = document.getElementById('modal-creer-destinataire--submit'),
    modale_destinataire_loader = document.getElementById('modal-creer-destinataire--loader'),

    select_destinataire = document.getElementById('envoi_destinataire'),
    input_destinataire = document.getElementById('destinataire_libelle') as HTMLInputElement;

/**
 * Lorsque l'on sélection "Autre" dans la liste, on ouvre la modale permettant de créer un nouveau destinataire
 */

select_destinataire?.addEventListener('change', (event) => {
    const target: (EventTarget | null) = event.target;
    if (target === null)
        return false;

    const // 
        select = target as HTMLSelectElement,
        selected_index = select.selectedIndex,
        selected_option = select.children[selected_index];

    if (selected_option.innerHTML === 'Autre') {
        ouvrir_modale_destinataire();
    }

});

/**
 * Lorsque l'on saisi le nom du destinataire, on active / désactive le bouton Submit
 */

input_destinataire?.addEventListener('keyup', (event) => {
    const value = (event.target as HTMLInputElement)?.value;
    if (!value) {
        modale_destinataire_submit?.setAttribute('disabled', 'disabled');
    } else {
        (event.target as HTMLInputElement).value = value.toUpperCase();
        modale_destinataire_submit?.removeAttribute('disabled');
    }
});

/**
 * Lors du clic sur le Submit, on envoie les données saisies au serveur pour sauvegarde en BDD
 */

modale_destinataire_submit?.addEventListener('click', (event) => {

    const // 
        select = select_destinataire as HTMLSelectElement,
        libelle = input_destinataire?.value;

    if (!libelle || modale_destinataire_loader === null)
        return false;

    modale_destinataire_loader.classList.remove('fr-hidden');
    axios.post(`/${prefix}/sauver-destinataire`, { libelle })
        .then((response) => {
            const { success, data } = response.data;
            modale_destinataire_loader.classList.add('fr-hidden');
            input_destinataire.value = '';
            modale_destinataire_submit.setAttribute('disabled', 'disabled');
            fermer_modale_destinataire();
            if (success) {
                const option = document.createElement('option');
                option.value = data.id;
                option.innerText = data.libelle;
                option.selected = true;
                const // 
                    selected_index = select.selectedIndex,
                    selected_option = select.children[selected_index];
                select.insertBefore(option, selected_option);
            } else {
                const option = [...select.children]
                    .filter(Boolean)
                    .find(option => option.innerHTML === libelle);
                if (option)
                    (option as HTMLOptionElement).selected = true;
            }
        })
        .catch(err => {
            modale_destinataire_loader.classList.add('fr-hidden');
        });
});

function ouvrir_modale_destinataire() {
    modale_destinataire_trigger?.setAttribute('data-fr-opened', 'true');
}

function fermer_modale_destinataire() {
    modale_destinataire_trigger?.setAttribute('data-fr-opened', 'false');
}

/**
 * +=========================+
 * | PARTIE CRÉATION D'OBJET |
 * +=========================+
 */

const // 
    modale_objet_trigger = document.getElementById('modal-creer-objet--trigger'),
    modale_objet_submit = document.getElementById('modal-creer-objet--submit'),
    modale_objet_loader = document.getElementById('modal-creer-objet--loader'),

    select_objet = document.getElementById('envoi_objet'),
    input_objet = document.getElementById('objet_libelle') as HTMLInputElement;

/**
 * Lorsque l'on sélection "Autre" dans la liste, on ouvre la modale permettant de créer un nouvel objet
 */

select_objet?.addEventListener('change', (event) => {
    const target: (EventTarget | null) = event.target;
    if (target === null)
        return false;

    const // 
        select = target as HTMLSelectElement,
        selected_index = select.selectedIndex,
        selected_option = select.children[selected_index];

    if (selected_option.innerHTML === 'Autre') {
        ouvrir_modale_objet();
    }

});

/**
 * Lorsque l'on saisi le nom de l'objet, on active / désactive le bouton Submit
 */

input_objet?.addEventListener('keyup', (event) => {
    const value = (event.target as HTMLInputElement)?.value;
    if (!value) {
        modale_objet_submit?.setAttribute('disabled', 'disabled');
    } else {
        (event.target as HTMLInputElement).value = value.toUpperCase();
        modale_objet_submit?.removeAttribute('disabled');
    }
});

/**
 * Lors du clic sur le Submit, on envoie les données saisies au serveur pour sauvegarde en BDD
 */

modale_objet_submit?.addEventListener('click', (event) => {

    const // 
        select = select_objet as HTMLSelectElement,
        libelle = input_objet?.value;

    if (!libelle || modale_objet_loader === null)
        return false;

    modale_objet_loader.classList.remove('fr-hidden');
    axios.post(`/${prefix}/sauver-objet`, { libelle })
        .then((response) => {
            const { success, data } = response.data;
            modale_objet_loader.classList.add('fr-hidden');
            input_objet.value = '';
            modale_objet_submit.setAttribute('disabled', 'disabled');
            fermer_modale_objet();
            if (success) {
                const option = document.createElement('option');
                option.value = data.id;
                option.innerText = data.libelle;
                option.selected = true;
                const // 
                    selected_index = select.selectedIndex,
                    selected_option = select.children[selected_index];
                select.insertBefore(option, selected_option);
            } else {
                const option = [...select.children]
                    .filter(Boolean)
                    .find(option => option.innerHTML === libelle);
                if (option)
                    (option as HTMLOptionElement).selected = true;
            }
        })
        .catch(err => {
            modale_objet_loader.classList.add('fr-hidden');
        });
});

function ouvrir_modale_objet() {
    modale_objet_trigger?.setAttribute('data-fr-opened', 'true');
}

function fermer_modale_objet() {
    modale_objet_trigger?.setAttribute('data-fr-opened', 'false');
}