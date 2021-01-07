<template>
    <section class="hero is-fullheight has-background-primary">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-three-fifths">
                        <div class="box">
                            <welcome v-if="showWelcome"></welcome>
                            <admin
                                v-if="showAdmin"
                                :is-admin-created="isAdminCreated"
                                :login-data="loginData"
                                :registration-data="registrationData"
                                :errors="errors">
                            </admin>
                            <region v-if="showRegion" :errors="errors" v-on:country-selected="countrySelected"></region>
                            <hr/>

                            <label class="label has-text-centered" v-if="showWelcome">
                                <input class="checkbox" type="checkbox" @click="updateAgreement"/>
                                I agree to <a href="https://simpleaccount.app/terms">terms and conditions</a>
                            </label>
                            <div class="has-text-centered">
                                <span class="has-text-danger is-small"> {{ agreementText }}</span>
                            </div>
                            <div class="has-text-centered">
                                <button class="button is-link is-medium is-rounded" @click="continueNext" :class="{'is-loading' : isLoading}">
                                    Continue
                                </button>
                            </div>
                            <br/>
                            <div class="columns is-gapless is-centered is-mobile">
                                <div class="column is-1 is-1-mobile">
                                    <progress class="progress is-primary is-small" value="100"></progress>
                                </div>
                                <div class="column is-1 is-1-mobile">
                                    <progress class="progress is-primary is-small" :value="adminProgress"></progress>
                                </div>
                                <div class="column is-1 is-1-mobile">
                                    <progress class="progress is-primary is-small" :value="regionProgress"></progress>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import welcome from './Welcome'
    import admin from './Admin'
    import region from './Region'
    import axios from "axios";
    import store from '@/store'
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";

    export default {
        name: "Container",
        components: {
            ProgressBarIndeterminate,
            welcome,
            admin,
            region
        },
        computed: {
            isLoggedIn: {
                get() {
                    return store.getters.getCurrentUser.id != null
                }
            }
        },
        data () {
            return {
                isLoading: true,
                showWelcome: true,
                showAdmin: false,
                showRegion: false,
                isTermsAgreed: false,
                agreementText: "",
                errors: [],
                adminProgress: 0,
                regionProgress: 0,
                country: '',
                isAdminCreated: false,
                registrationData: {
                    name: '',
                    email: '',
                    password: '',
                    passwordConfirmation: ''
                },
                loginData: {
                    email: '',
                    password: ''
                }
            }
        },
        methods: {
            updateAgreement() {
                this.isTermsAgreed = !this.isTermsAgreed;

                if(this.agreementText.length !== 0) {
                    this.agreementText = "";
                }
            },
            continueNext() {
                if(this.showWelcome) {
                    this.isLoading = true;

                    if(!this.isTermsAgreed) {
                        this.agreementText = "Please read and agree to our terms and conditions!";
                        this.isLoading = false;
                        return;
                    }
                    this.showWelcome = false;
                    if(this.isLoggedIn) {
                        this.showRegion = true
                        this.showAdmin = false
                    } else {
                        this.showRegion = false;
                        this.showAdmin = true;
                    }
                    this.adminProgress = 100;

                    this.isLoading = false;

                    return;
                }

                if(this.showAdmin) {
                    if(this.isAdminCreated) {
                        this.login();
                    } else {
                        this.register();
                    }

                    return;
                }

                if(this.showRegion) {
                    this.updateRegionData();
                }
            },
            register() {
                this.isLoading = true;
                axios.post('/api/v1/users/admin', {
                    name: this.registrationData.name,
                    email: this.registrationData.email,
                    password: this.registrationData.password,
                    password_confirmation: this.registrationData.passwordConfirmation,
                }).then(response => {
                    this.loginData.email = this.registrationData.email
                    this.loginData.password = this.registrationData.password
                    this.login();
                }).catch(error => {
                    this.isLoading = false;
                    this.errors = error.response.data.errors;
                });
            },
            login() {
                this.isLoading = true;
                axios.get('/sanctum/csrf-cookie').then(response => {
                    axios.post('/login', {
                        email: this.loginData.email,
                        password: this.loginData.password,

                    }).then(response => {
                        this.isLoading = false;
                        this.showAdmin = false;
                        this.showWelcome = false;
                        this.showRegion = true;

                        this.productProgress = 100;
                    }).catch(error => {
                        this.isLoading = false;
                        this.errors = error.response.data.errors;
                    })
                });
            },
            countrySelected(val) {
                this.country = val
            },
            updateRegionData() {
                if(this.country === '') {
                    this.showToast('Please chose your country!', 'is-warning')
                    return
                }

                this.isLoading = true
                axios.post('/api/v1/region', {country: this.country}).then(response => {
                    window.location.href = '/'
                })
            },
            getProductState() {
                this.isLoading = true;
                axios.get('/api/v1/status').then(response => {
                    this.isAdminCreated = response.data.data.admin_created;
                    this.isLoading = false;
                })
            },
            showToast(message, type = 'is-success') {
                this.$buefy.toast.open({
                    message: message,
                    type: type,
                    position: 'is-top-right'
                })
            }
        },
        mounted() {
            this.getProductState();
        }
    }
</script>

<style scoped>

</style>
