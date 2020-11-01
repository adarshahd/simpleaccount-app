import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

const store = new Vuex.Store({
    strict: true,
    state: {
        currentUser: {
            id: null,
            name: '',
            email: ''
        }
    },
    getters: {
        getCurrentUser(state) {
            return state.currentUser;
        }
    },
    actions: {
        getCurrentUser({ commit, dispatch, state }) {
            return new Promise((resolve, reject) => {
                axios.get('/api/v1/user').then(response => {
                    commit('updateCurrentUser', response.data.data.user)
                    resolve(response)
                }).catch(error => {
                    reject(error)
                })
            })
        },
        sideBarItemClicked({ commit, dispatch, state }) {
            commit('sideBarItemClicked')
        }
    },
    mutations: {
        updateCurrentUser(state, val) {
            state.currentUser.id = val.id
            state.currentUser.name = val.name
            state.currentUser.email = val.email
        },
        sideBarItemClicked(state) {

        }
    }
})

export default store
