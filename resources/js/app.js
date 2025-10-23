import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'BloomTrack';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const layouts = import.meta.glob('./Layouts/**/*.vue', { eager: true });
        const components = import.meta.glob('./Components/**/*.vue', { eager: true });
        
        const page = pages[`./Pages/${name}.vue`];
        const layout = layouts[`./Layouts/${name}.vue`];
        const component = components[`./Components/${name}.vue`];
        
        if (page) return page;
        if (layout) return layout;
        if (component) return component;
        
        throw new Error(`Page not found: ${name}`);
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#3B82F6',
    },
});
