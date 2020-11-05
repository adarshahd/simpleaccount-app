<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <div v-else class="columns is-centered main-content">
        <div class="column is-10-desktop mt-2">
            <div class="box has-background-white">
                <div class="columns is-centered">
                    <div class="column is-6">
                        <h5 class="title is-5">Customer Info</h5>
                        <div class="columns is-vcentered">
                            <div class="column is-two-fifths-desktop">
                                <figure class="image is-128x128">
                                    <img src="/images/business-shop.png" alt="" v-if="imageList === null">
                                    <img v-else :src="imageList" alt="customer-image">
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
                                  <span class="file-label">
                                    Choose logo…
                                  </span>
                            </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="customer.name"
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
                                        <div class="select is-fullwidth" :class="{'is-loading' : isIdTypeLoading}">
                                            <select v-model="customer.id_type_id">
                                                <option disabled value="">Select ID</option>
                                                <option v-for="idType in idTypes" :key="idType.id" v-bind:value="idType.id">{{ idType.name }}</option>
                                            </select>
                                            <span class="has-text-danger" v-if="errors.id_type_id">
                                                {{ errors.id_type_id[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-expanded">
                                <div class="field">
                                    <div class="control">
                                        <input class="input" v-model="customer.identification"
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
                                        <input class="input" type="text" v-model="customer.website"
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
                                        <input class="input" type="text" v-model="customer.contact_email"
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
                                <input class="input" type="text" v-model="customer.address_line_1"
                                       placeholder="Address line 1 *" autofocus="">
                                <span class="has-text-danger" v-if="errors.address_line_1">
                                    {{ errors.address_line_1[0] }}
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="customer.address_line_2"
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
                                        <input class="input" type="text" v-model="customer.city"
                                               placeholder="City *" autofocus="">
                                        <span class="has-text-danger" v-if="errors.city">
                                            {{ errors.city[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-half">
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" v-model="customer.state"
                                               placeholder="State *" autofocus="">
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
                                        <input class="input" type="text" v-model="customer.country"
                                               placeholder="Country *" autofocus="">
                                        <span class="has-text-danger" v-if="errors.country">
                                            {{ errors.country[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-half">
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" v-model="customer.pin"
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
                                        <input class="input" type="text" v-model="customer.contact_name"
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
                                        <input class="input" type="text" v-model="customer.contact_phone"
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
                <div class="has-text-centered">
                    <button
                        class="button"
                        @click="handleSubmitClick"
                        :class="{'is-loading' : isUpdating, 'is-primary' : this.customer.id != null, 'is-success' : this.customer.id == null}">
                        <span class="icon"><i class="mdi mdi-check-circle"></i> </span>
                        <span v-if="this.customer.id == null">&nbsp; Create</span>
                        <span v-else>&nbsp; Update</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";

    export default {
        name: "NewCustomer",
        components: {ProgressBarIndeterminate},
        data () {
            return {
                isLoading: false,
                isUpdating: false,
                isIdTypeLoading: true,
                customer: {
                    name: '',
                    identification: '',
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    country: '',
                    pin: '',
                    contact_name: '',
                    contact_email: '',
                    contact_phone: '',
                    website: '',
                    id_type_id: null,
                    image: null,
                },
                customerImage: null,
                idTypes: [],
                errors: []
            }
        },
        computed: {
            imageList : {
                get() {
                    return this.customerImage
                }
            },
        },
        methods: {
            getIdTypes () {
                axios.get('/api/v1/id-types').then(response => {
                    this.idTypes = response.data.data;
                    this.isIdTypeLoading = false;
                }).catch(error => {
                    this.isIdTypeLoading = false;
                });
            },
            loadCustomer() {
                this.isLoading = true
                axios.get('/api/v1/customers/' + this.customer.id).then(response => {
                    this.customer = response.data.data
                    this.customerImage = response.data.data.image

                    this.customer.image = null
                    this.customer.website = response.data.data.website == null ? '' : response.data.data.website
                    this.customer.contact_email = response.data.data.contact_email == null ? '' : response.data.data.contact_email
                    this.customer.address_line_2 = response.data.data.address_line_2 == null ? '' : response.data.data.address_line_2
                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error)
                })
            },
            handleSubmitClick() {
                if(this.customer.id == null) {
                    this.createCustomer()
                } else {
                    this.updateCustomer()
                }
            },
            createCustomer() {
                this.isUpdating = true;
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                let formData = new FormData()
                formData.append('name', this.customer.name)
                formData.append('identification', this.customer.identification)
                formData.append('address_line_1', this.customer.address_line_1)
                formData.append('address_line_2', this.customer.address_line_2)
                formData.append('city', this.customer.city)
                formData.append('state', this.customer.state)
                formData.append('country', this.customer.country)
                formData.append('pin', this.customer.pin)
                formData.append('contact_name', this.customer.contact_name)
                formData.append('contact_email', this.customer.contact_email)
                formData.append('contact_phone', this.customer.contact_phone)
                formData.append('website', this.customer.website)
                formData.append('id_type_id', this.customer.id_type_id)
                if(this.customer.image != null) {
                    formData.append('image', this.customer.image)
                }

                axios.post('/api/v1/customers', formData, config).then(response => {
                    this.isUpdating = false;
                    this.customer = {}
                    this.showToast("Customer created successfully!")
                    this.$router.push({ name: 'customers'})
                }).catch(errors => {
                    this.isUpdating = false;
                    this.handleError(errors);
                })
            },
            updateCustomer() {
                this.isUpdating = true;
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                let formData = new FormData()
                formData.append('name', this.customer.name)
                formData.append('identification', this.customer.identification)
                formData.append('address_line_1', this.customer.address_line_1)
                formData.append('address_line_2', this.customer.address_line_2)
                formData.append('city', this.customer.city)
                formData.append('state', this.customer.state)
                formData.append('country', this.customer.country)
                formData.append('pin', this.customer.pin)
                formData.append('contact_name', this.customer.contact_name)
                formData.append('contact_email', this.customer.contact_email)
                formData.append('contact_phone', this.customer.contact_phone)
                formData.append('website', this.customer.website)
                formData.append('id_type_id', this.customer.id_type_id)
                if(this.customer.image != null) {
                    formData.append('image', this.customer.image)
                }

                axios.post('/api/v1/customers/' + this.customer.id + '/?_method=PATCH', formData, config).then(response => {
                    this.isUpdating = false;
                    this.customer = {}
                    this.showToast("Customer updated successfully!")
                    this.$router.push({ name: 'customers'})
                }).catch(errors => {
                    this.isUpdating = false;
                    this.handleError(errors);
                })
            },
            handleFileChange(event) {
                const file = event.target.files[0];
                this.customer.image = file
                this.customerImage = URL.createObjectURL(file)
            },
            handleError(error) {
                this.errors = error.response.data.errors
                if(error.response.status === 401) {
                    this.$router.go({
                        name: 'login',
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
            this.customer.id = this.$route.params.id
            if(this.customer.id != null) {
                this.loadCustomer()
            }
            this.getIdTypes();
        }
    }
</script>

<style scoped>

</style>
