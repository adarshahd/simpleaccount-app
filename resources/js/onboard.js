import Vue from 'vue'

import { Dropdown } from 'buefy'
import introductionContainer from './views/onboarding/Container'

Vue.use(Dropdown, {defaultLocale: 'en-IN'})
new Vue({
    render: h => h(introductionContainer)
}).$mount("#app")
