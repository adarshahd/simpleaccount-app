<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <div class="main-content" v-else>
        <id-type-modal ref="idTypeModal" :idType="idTypeItem" v-on:loadIdType="getIdTypes"></id-type-modal>
        <div class="columns is-centered mt-4">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-6">
                                <h5 class="title is-5">Company Info</h5>
                                <div class="columns is-vcentered">
                                    <div class="column is-two-fifths-desktop">
                                        <figure class="image is-128x128">
                                            <img class="is-rounded" :src="logoFile" v-if="logoFile != null">
                                            <img class="is-rounded" src="/images/business-shop.png" v-else>
                                        </figure>
                                    </div>
                                    <div class="column">
                                        <div class="file">
                                            <label class="file-label">
                                                <input class="file-input" type="file" @change="handleFileChange">
                                                <span class="file-cta">
                                                    <span class="file-icon">
                                                        <i class="mdi mdi-upload"></i>
                                                    </span>
                                                    <span class="file-label">Choose logo…</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" v-model="productOwnerData.name"
                                               placeholder="Company Name *" autofocus="">
                                        <span class="has-text-danger" v-if="errors.name">
                                            {{ errors.name[0] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column is-4">
                                        <div class="field">
                                            <div class="control">
                                                <b-dropdown
                                                    id="idType"
                                                    class="select"
                                                    animation="slide"
                                                    expanded>
                                                    <button class="button has-background-white-ter is-fullwidth has-text-left" slot="trigger" slot-scope="{ active }">
                                                        <span v-if="productOwnerData.id_type_id === ''">Select ID Type</span>
                                                        <span v-else>{{ productOwnerData.id_type_name }}</span>
                                                    </button>

                                                    <b-dropdown-item @click="showAddNewIdType">Add ID Type</b-dropdown-item>
                                                    <b-dropdown-item v-for="item in idTypes" @click="idTypeSelected(item)" :key="item.id">{{ item.name }}</b-dropdown-item>
                                                </b-dropdown>
                                                <span class="has-text-danger" v-if="errors.id_type_id">
                                                    {{ errors.id_type_id[0] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-expanded">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" v-model="productOwnerData.identification"
                                                       placeholder="Identification *" autofocus="">
                                                <span class="has-text-danger" v-if="errors.identification">
                                                    {{ errors.identification[0] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column is-half">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" type="text" v-model="productOwnerData.website"
                                                       placeholder="Website" autofocus="">
                                                <span class="has-text-danger" v-if="errors.website">
                                                    {{ errors.website[0] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-half">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" type="text" v-model="productOwnerData.contact_email"
                                                       placeholder="Email" autofocus="">
                                                <span class="has-text-danger" v-if="errors.contact_email">
                                                    {{ errors.contact_email[0] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <h5 class="title is-5">Address</h5>
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" v-model="productOwnerData.address_line_1"
                                               placeholder="Address line 1 *" autofocus="">
                                        <span class="has-text-danger" v-if="errors.address_line_1">
                                            {{ errors.address_line_1[0] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" v-model="productOwnerData.address_line_2"
                                               placeholder="Address line 2" autofocus="">
                                        <span class="has-text-danger" v-if="errors.address_line_2">
                                            {{ errors.address_line_2[0] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column is-half">
                                        <div class="field">
                                            <div class="control">
                                                <div class="select is-fullwidth" :class="{'is-loading' : isCountriesLoading}">
                                                    <select id="country" v-model="productOwnerData.country" @change="onCountrySelected">
                                                        <option value="" disabled selected>Select Country</option>
                                                        <option v-for="country in countries" :key="country">
                                                            {{ country }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <span class="has-text-danger" v-if="errors.country">
                                                    {{ errors.country[0] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-half">
                                        <div class="field">
                                            <div class="control">
                                                <div class="select is-fullwidth" :class="{'is-loading' : isStatesLoading}">
                                                    <select id="state" v-model="productOwnerData.state" @change="onStateSelected">
                                                        <option value="" disabled selected>Select State</option>
                                                        <option v-for="state in states" :key="state">
                                                            {{ state }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <span class="has-text-danger" v-if="errors.state">
                                                    {{ errors.state[0] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column is-half">
                                        <div class="field">
                                            <div class="control">
                                                <div class="select is-fullwidth" :class="{'is-loading' : isCitiesLoading}">
                                                    <select id="city" v-model="productOwnerData.city">
                                                        <option value="" disabled selected>Select City</option>
                                                        <option v-for="city in cities" :key="city">
                                                            {{ city }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <span class="has-text-danger" v-if="errors.city">
                                                    {{ errors.city[0] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-half">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" type="text" v-model="productOwnerData.pin"
                                                       placeholder="Pin *" autofocus="">
                                                <span class="has-text-danger" v-if="errors.pin">
                                                    {{ errors.pin[0] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="title is-5">Contact Info</h5>
                                <div class="columns">
                                    <div class="column is-half">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" type="text" v-model="productOwnerData.contact_name"
                                                       placeholder="Contact Person *" autofocus="">
                                                <span class="has-text-danger" v-if="errors.contact_name">
                                                    {{ errors.contact_name[0] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-half">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" type="text" v-model="productOwnerData.contact_phone"
                                                       placeholder="Contact Phone *" autofocus="">
                                                <span class="has-text-danger" v-if="errors.contact_phone">
                                                    {{ errors.contact_phone[0] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="columns is-centered">
                            <div class="column is-2">
                                <button
                                    class="button is-primary"
                                    @click="updateProductOwner"
                                    :class="{ 'is-loading': isUpdating}">
                                        <span class="icon">
                                            <i class="mdi mdi-check-circle"></i>
                                        </span>
                                        <span>Update</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import IdTypeModal from "../modals/IdType";
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";

    export default {
        name: "Product",
        components: {ProgressBarIndeterminate, IdTypeModal},
        data() {
            return {
                isLoading: false,
                isUpdating: false,
                productOwnerData: {
                    name: '',
                    id_type_id: '',
                    id_type_name: '',
                    identification: '',
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    country: '',
                    pin: '',
                    contact_name: '',
                    contact_phone: '',
                    contact_email: '',
                    website: '',
                    logo: null
                },
                errors: [],
                idTypes : [],
                idTypeItem: {
                    id: null,
                    name: '',
                    description: '',
                },
                logoFile : null,
                isIdTypeLoading : true,
                isCountriesLoading: true,
                isStatesLoading: false,
                isCitiesLoading: false,
                countries: [],
                states: [],
                cities: []
            }
        },
        methods: {
            handleFileChange(event) {
                const file = event.target.files[0]
                this.logoFile = URL.createObjectURL(file)
                this.productOwnerData.logo = file
            },
            getProductOwnerData() {
                this.isLoading = true
                axios.get('/api/v1/product-owner').then(response => {
                    this.productOwnerData = response.data.data
                    if(this.productOwnerData.country !== '') {
                        this.getStates()
                    }
                    if(this.productOwnerData.state !== '') {
                        this.getCities()
                    }
                    this.logoFile = this.productOwnerData.logo
                    let idType = this.getIdType(parseInt(this.productOwnerData.id_type_id))
                    if(idType != null) {
                        this.productOwnerData.id_type_name = idType.name
                    }
                    this.productOwnerData.logo = null
                    this.productOwnerData.website = this.productOwnerData.website == null ? '' : this.productOwnerData.website
                    this.productOwnerData.contact_email = this.productOwnerData.contact_email == null ? '' : this.productOwnerData.contact_email
                    this.isLoading = false
                })
            },
            updateProductOwner() {
                this.isUpdating = true;
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                let formData = new FormData()
                formData.append('name', this.productOwnerData.name)
                formData.append('identification', this.productOwnerData.identification)
                formData.append('address_line_1', this.productOwnerData.address_line_1)
                formData.append('address_line_2', this.productOwnerData.address_line_2)
                formData.append('city', this.productOwnerData.city)
                formData.append('state', this.productOwnerData.state)
                formData.append('country', this.productOwnerData.country)
                formData.append('pin', this.productOwnerData.pin)
                formData.append('contact_name', this.productOwnerData.contact_name)
                formData.append('contact_email', this.productOwnerData.contact_email)
                formData.append('contact_phone', this.productOwnerData.contact_phone)
                formData.append('website', this.productOwnerData.website)
                formData.append('id_type_id', this.productOwnerData.id_type_id)
                if(this.productOwnerData.logo != null) {
                    formData.append('logo', this.productOwnerData.logo)
                }
                axios.post('/api/v1/product-owner', formData, config).then(response => {
                    this.isUpdating = false;
                    this.showToast('Updated product information!')
                }).catch(error => {
                    this.isUpdating = false;
                    this.errors = error.response.data.errors;
                });
            },
            getIdTypes() {
                this.isIdTypeLoading = true;
                axios.get('/api/v1/id-types').then(response => {
                    this.idTypes = response.data.data;
                    this.isIdTypeLoading = false;
                }).catch(response => {
                    this.isIdTypeLoading = false;
                });
            },
            getIdType(id) {
                for(let i=0; i < this.idTypes.length; ++i) {
                    if(id === this.idTypes[i].id) {
                        return this.idTypes[i]
                    }
                }
                return null
            },
            getCountries() {
                this.isCountriesLoading = true;
                axios.get('/api/v1/countries').then(response => {
                    this.countries = response.data
                    this.isCountriesLoading = false
                })
            },
            getStates() {
                this.isStatesLoading = true;
                axios.get('/api/v1/states?country=' + this.productOwnerData.country).then(response => {
                    this.states = response.data
                    this.isStatesLoading = false
                })
            },
            getCities() {
                this.isCitiesLoading = true;
                axios.get('/api/v1/cities?country=' + this.productOwnerData.country + '&state=' + this.productOwnerData.state).then(response => {
                    this.cities = response.data
                    this.isCitiesLoading = false
                })
            },
            onCountrySelected() {
                this.states = []
                this.cities = []
                this.getStates()
            },
            onStateSelected() {
                this.cities = []
                this.getCities()
            },
            showAddNewIdType() {
                this.$refs.idTypeModal.toggleModal()
            },
            idTypeSelected(idType) {
                this.productOwnerData.id_type_id = idType.id
                this.productOwnerData.id_type_name = idType.name
            },

            handleError(error) {
                this.errors = error.response.data.errors
                if(error.response.status === 401) {
                    this.$router.go({
                        path: '/login',
                        force: true
                    });
                } else {
                    this.showToast(error.response.data.message, 'is-danger');
                }
            },
            showToast(message, type = 'is-success') {
                this.$buefy.toast.open({
                    message: message,
                    type: type
                });
            }
        },
        mounted() {
            this.getIdTypes()
            this.getCountries()
            this.getProductOwnerData()
        }
    }
</script>

<style scoped>

</style>
