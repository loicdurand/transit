import './stimulus_bootstrap.js';

import '@gouvfr/dsfr/dist/dsfr.css';
import "@gouvfr/dsfr/dist/utility/icons/icons.main.min.css";
import './styles/app.scss';

// JAVASCRIPTS
import "@gouvfr/dsfr/dist/dsfr/dsfr.module";
import Router from '@bleckert/router';

const prefix = '/transit';

const router = new Router(prefix);

router.on('/creer-:envoi_ou_reception', async () => {
    const envoi = await import('./javascripts/creer-envoi.ts');
    envoi.default();

    console.log('creer-envoi.ts');
});

router.on('/init-objet', async () => {
    const envoi = await import('./javascripts/init-objet.ts');
    envoi.default();

    console.log('init-objet.ts');
});

router.on('/envoi', async () => {
    const envoi = await import('./javascripts/envoi.ts');
    envoi.default();

    console.log('envoi.ts');
});

// router.dispatch();

document.addEventListener('turbo:load', () => {
    router.dispatch();
});
