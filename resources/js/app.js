import Vue from 'vue'
import axios from 'axios'
import { Table, Toast, Datepicker, Collapse, Autocomplete, Field, Icon } from 'buefy'

import router from './router'
import store from './store'

axios.defaults.withCredentials = true

Vue.use(Table, {defaultLocale: 'en-IN'})
Vue.use(Toast, {defaultLocale: 'en-IN'})
Vue.use(Datepicker, {defaultLocale: 'en-IN'})
Vue.use(Collapse, {defaultLocale: 'en-IN'})
Vue.use(Autocomplete, {defaultLocale: 'en-IN'})
Vue.use(Field, {defaultLocale: 'en-IN'})
Vue.use(Icon, {defaultLocale: 'en-IN'})

store.dispatch("getCurrentUser").finally(() => {
    const app = new Vue({
        router,
        store
    }).$mount("#app")
})
