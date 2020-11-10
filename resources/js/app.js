import Vue from 'vue'
import axios from 'axios'
import Buefy from 'buefy'

import router from './router'
import store from './store'

axios.defaults.withCredentials = true

Vue.use(Buefy, {
    defaultLocale: 'en-IN'
})

store.dispatch("getCurrentUser").then(response => {
    const app = new Vue({
        router,
        store
    }).$mount("#app")
}).catch(error => {
    const app = new Vue({
        router,
        store
    }).$mount("#app")
})
