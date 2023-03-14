import './bootstrap';

import {createInertiaApp, usePage} from '@inertiajs/inertia-vue3';
import {InertiaProgress} from '@inertiajs/progress';
import 'primeicons/primeicons.css';
import 'primevue/resources/primevue.min.css';
import 'primevue/resources/themes/tailwind-light/theme.css';
import {createApp, h} from 'vue';
import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';
// import moment from 'moment';
import PrimeVue from 'primevue/config';
import VueTelInput from 'vue-tel-input';
import 'vue-tel-input/dist/vue-tel-input.css';
import {createPinia} from "pinia";
import VueGoogleMaps from '@fawmi/vue-google-maps';


const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
const mapKey = window.document.getElementsByTagName('meta')['google-api-key'].content;
const moment = require('moment-timezone');

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({el, app, props, plugin}) {
        return createApp({render: () => h(app, props)})
            .use(plugin)
            .use(PrimeVue)
            .use(ZiggyVue, Ziggy)
            .use(VueTelInput)
            .use(createPinia())
            .use(VueGoogleMaps, {
                load: {
                    key: mapKey,
                    libraries: "places",
                },
            })
            .mixin({
                    methods: {
                        route,
                        formatDate(date, format) {
                            let stillUtc = moment.utc(date).toDate();
                            if (usePage().props.value.user?.timezone) {
                                return moment(stillUtc).tz(usePage().props.value.user.timezone.toString()).format(format ? format : 'YYYY/MM/DD');
                            } else {
                                return moment(stillUtc).local().format(format ? format : 'YYYY-MM-DD');
                            }

                        },
                        toLocalDate(date, format) {
                            return this.formatDate(date, format ? format : 'YYYY/MM/DD');
                        },
                        toLocalTime(date, format) {
                            let stillUtc = moment.utc(date).toDate();
                            if (usePage().props.value.user?.timezone) {
                                return moment(stillUtc).tz(usePage().props.value.user.timezone.toString()).format(format ? format : 'HH:mm');
                            } else {
                                return moment(stillUtc).local().format(format ? format : 'HH:mm');
                            }

                        },
                        getAppName() {
                            return appName;
                        },
                    }
                }
            )
            .mount(el);
    },
});

InertiaProgress.init({color: '#4B5563'});

