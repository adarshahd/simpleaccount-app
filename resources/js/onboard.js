import Vue from 'vue'

import { Dropdown, Toast } from 'buefy'
import introductionContainer from './views/onboarding/Container'
import store from "./store";
import axios from "axios";

axios.defaults.withCredentials = true

Vue.use(Dropdown, {defaultLocale: 'en-IN'})
Vue.use(Toast, {defaultLocale: 'en-IN'})

store.dispatch("getCurrentUser").finally(() => {
    new Vue({
        store,
        render: h => h(introductionContainer)
    }).$mount("#app")
})
