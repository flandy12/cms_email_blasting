import '../css/app.css'

import { createInertiaApp } from '@inertiajs/vue3'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import type { DefineComponent } from 'vue'
import { createApp, h } from 'vue'
import { initializeTheme } from './composables/useAppearance'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: (title) => (title ? `${title} - ${appName}` : appName),
  resolve: (name) =>
    resolvePageComponent(
      `./pages/${name}.vue`,
      import.meta.glob<DefineComponent>('./pages/**/*.vue')
    ),

  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) })

    // Pasang Inertia plugin
    vueApp.use(plugin)

    // Initialize theme (dark/light)
    // initializeTheme()

    vueApp.mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})

// Initialize theme sekali lagi untuk page load
// initializeTheme()
