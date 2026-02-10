import './stimulus_bootstrap.js';

import '/node_modules/@gouvfr/dsfr/dist/dsfr.css';
import "/node_modules/@gouvfr/dsfr/dist/utility/icons/icons.main.min.css";
import './styles/app.scss';

// JAVASCRIPTS
import "/node_modules/@gouvfr/dsfr/dist/dsfr/dsfr.module";
import Router from '@bleckert/router';

const prefix = 'transit';

new Router('/', {
    [`/${prefix}/creer-envoi`]: () => {
        import('./javascripts/creer-envoi.ts')
    }
});