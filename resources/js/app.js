require('./bootstrap');

import { createApp } from 'vue'
import mitt from 'mitt'

const emitter = mitt()
const app = createApp()

app.config.globalProperties.emitter = emitter

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => app.component(key.split('/').pop().split('.')[0], files(key).default))

// window.EventBus = new Vue()
app.mount('#app')
