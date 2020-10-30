import Vue from 'vue'

import introductionContainer from './views/onboarding/Container'

new Vue({
    render: h => h(introductionContainer)
}).$mount("#app")
