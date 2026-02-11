import './stimulus_bootstrap.js';

import '@gouvfr/dsfr/dist/dsfr.css';
import "@gouvfr/dsfr/dist/utility/icons/icons.main.min.css";
import './styles/app.scss';

// JAVASCRIPTS
import "@gouvfr/dsfr/dist/dsfr/dsfr.module";
import Router from '@bleckert/router';

const prefix = '/transit';

const router = new Router(prefix);

router.on('/creer-envoi', (route, path, event) => {
    console.log({ route, path, event });
    import('./javascripts/creer-envoi.ts')
});

router.dispatch();

document.addEventListener('click', (event) => {
    if (event.target instanceof HTMLAnchorElement) {
        location.href = event.target.href;
    }
})