import axios from 'axios';

const // 
    prefix = 'transit',
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
    if (!value)
        modale_destinataire_submit?.setAttribute('disabled', 'disabled');
    else
        modale_destinataire_submit?.removeAttribute('disabled');
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
    axios.post(`/${prefix}/creer-destinataire`, { libelle })
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
})

function ouvrir_modale_destinataire() {
    modale_destinataire_trigger?.setAttribute('data-fr-opened', 'true');
}

function fermer_modale_destinataire() {
    modale_destinataire_trigger?.setAttribute('data-fr-opened', 'false');
}
