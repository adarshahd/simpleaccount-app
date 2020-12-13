<template>
    <section class="hero is-fullheight has-background-primary">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column" :class="{'is-three-fifths' : showWelcome || showAdmin, 'is-full' : showProduct}">
                        <div class="box">
                            <welcome v-if="showWelcome"></welcome>
                            <admin
                                v-if="showAdmin"
                                :is-admin-created="isAdminCreated"
                                :login-data="loginData"
                                :registration-data="registrationData"
                                :errors="errors">
                            </admin>
                            <product v-if="showProduct" :errors="errors" :product-owner-data="productOwnerData"></product>
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
                                    <progress class="progress is-primary is-small" :value="productProgress"></progress>
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
    import product from './Product'
    import axios from "axios";
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";

    export default {
        name: "Container",
        components: {
            ProgressBarIndeterminate,
            welcome,
            admin,
            product
        },
        data () {
            return {
                isLoading: true,
                showWelcome: true,
                showAdmin: false,
                showProduct: false,
                isTermsAgreed: false,
                agreementText: "",
                errors: [],
                adminProgress: 0,
                productProgress: 0,
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
                },
                productOwnerData: {
                    name: '',
                    idTypeId: '',
                    identification: '',
                    addressLine1: '',
                    addressLine2: '',
                    city: '',
                    state: '',
                    country: '',
                    pin: '',
                    contactName: '',
                    contactPhone: '',
                    contactEmail: '',
                    website: '',
                    logo: null
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
                    this.showProduct = false;
                    this.showAdmin = true;
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

                if(this.showProduct) {
                    this.createProductOwner();
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
                        this.showProduct = true;

                        this.productProgress = 100;
                    }).catch(error => {
                        this.isLoading = false;
                        this.errors = error.response.data.errors;
                    })
                });
            },
            createProductOwner() {
                this.isLoading = true;
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                let formData = new FormData()
                formData.append('name', this.productOwnerData.name)
                formData.append('identification', this.productOwnerData.identification)
                formData.append('address_line_1', this.productOwnerData.addressLine1)
                formData.append('address_line_2', this.productOwnerData.addressLine2)
                formData.append('city', this.productOwnerData.city)
                formData.append('state', this.productOwnerData.state)
                formData.append('country', this.productOwnerData.country)
                formData.append('pin', this.productOwnerData.pin)
                formData.append('contact_name', this.productOwnerData.contactName)
                formData.append('contact_email', this.productOwnerData.contactEmail)
                formData.append('contact_phone', this.productOwnerData.contactPhone)
                formData.append('website', this.productOwnerData.website)
                formData.append('id_type_id', this.productOwnerData.idTypeId)
                if(this.productOwnerData.logo != null) {
                    formData.append('logo', this.productOwnerData.logo)
                }
                axios.post('/api/v1/product-owner', formData, config).then(response => {
                    this.isLoading = false;
                    window.location.href = '/'
                }).catch(error => {
                    this.isLoading = false;
                    this.errors = error.response.data.errors;
                });
            },
            getProductState() {
                this.isLoading = true;
                axios.get('/api/v1/status').then(response => {
                    this.isAdminCreated = response.data.data.admin_created;
                    this.isLoading = false;
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
