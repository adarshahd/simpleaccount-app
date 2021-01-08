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
        },
        regionData: {
            country: '',
            currencyName: '',
            currencySymbol: '',
            language: 'English'
        }
    },
    getters: {
        getCurrentUser(state) {
            return state.currentUser;
        }
    },
    actions: {
        getCurrentUser({ commit, dispatch, state }) {
            dispatch('getRegionData')
            return new Promise((resolve, reject) => {
                axios.get('/api/v1/user').then(response => {
                    commit('updateCurrentUser', response.data.data.user)
                    resolve(response)
                }).catch(error => {
                    reject(error)
                })
            })
        },
        getRegionData({commit, dispatch, state}) {
            return new Promise((resolve, reject) => {
                axios.get('/api/v1/region').then(response => {
                    commit('updateRegionData', response.data.data)
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
        updateRegionData(state, val) {
            state.regionData.country = val.country
            state.regionData.currencyName = val.currency_name
            state.regionData.currencySymbol = (val.currency_symbol == null || val.currency_symbol === '') ? val.currency_name : val.currency_symbol
        },
        sideBarItemClicked(state) {

        }
    }
})

export default store
