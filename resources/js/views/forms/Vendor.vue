<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <div v-else class="columns is-centered main-content">
        <id-type-modal ref="idTypeModal" :idType="idTypeItem" v-on:loadIdType="getIdTypes"></id-type-modal>
        <div class="column is-10-desktop mt-2">
            <div class="box has-background-white">
                <div class="columns is-centered">
                    <div class="column is-6">
                        <h5 class="title is-5">Vendor Info</h5>
                        <div class="columns is-vcentered">
                            <div class="column is-two-fifths-desktop">
                                <figure class="image is-128x128">
                                    <img src="/images/business-shop.png" alt="" v-if="imageList === null">
                                    <img v-else :src="imageList" alt="vendor-image">
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
                                <input class="input" type="text" v-model="vendor.name"
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
                                                <span v-if="vendor.id_type_id  === ''">Select ID Type</span>
                                                <span v-else>{{ vendor.id_type_name }}</span>
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
                                        <input class="input" v-model="vendor.identification"
                                               placeholder="Identification" autofocus="">
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
                                        <input class="input" type="text" v-model="vendor.website"
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
                                        <input class="input" type="text" v-model="vendor.contact_email"
                                               placeholder="Email" autofocus="">
                                        <span class="has-text-danger" v-if="errors.contact_email">
                                            {{ errors.contact_email[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="title is-5">Contact Info</h5>
                        <div class="columns">
                            <div class="column">
                                <div class="columns">
                                    <div class="column is-half">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" type="text" v-model="vendor.contact_name"
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
                                                <input class="input" type="text" v-model="vendor.contact_phone"
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
                    </div>
                    <div class="column is-6">
                        <h5 class="title is-5">Address</h5>
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="vendor.address_line_1"
                                       placeholder="Address line 1" autofocus="">
                                <span class="has-text-danger" v-if="errors.address_line_1">
                                    {{ errors.address_line_1[0] }}
                                </span>
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="vendor.address_line_2"
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
                                        <input class="input" type="text" v-model="vendor.city"
                                               placeholder="City" autofocus="">
                                        <span class="has-text-danger" v-if="errors.city">
                                            {{ errors.city[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-half">
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" v-model="vendor.state"
                                               placeholder="State" autofocus="">
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
                                        <input class="input" type="text" v-model="vendor.country"
                                               placeholder="Country" autofocus="">
                                        <span class="has-text-danger" v-if="errors.country">
                                            {{ errors.country[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-half">
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" v-model="vendor.pin"
                                               placeholder="Pin" autofocus="">
                                        <span class="has-text-danger" v-if="errors.pin">
                                            {{ errors.pin[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="title is-5">Bank Details</h5>
                        <div class="columns">
                            <div class="column">
                                <div class="columns">
                                    <div class="column is-6">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" type="text" v-model="vendor.bank_name"
                                                       placeholder="Bank Name" autofocus="">
                                                <span class="has-text-danger" v-if="errors.bank_name">
                                            {{ errors.bank_name[0] }}
                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-6">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" type="text" v-model="vendor.account_name"
                                                       placeholder="Account Name" autofocus="">
                                                <span class="has-text-danger" v-if="errors.account_name">
                                            {{ errors.account_name[0] }}
                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column is-6">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" type="text" v-model="vendor.account_number"
                                                       placeholder="Account Number" autofocus="">
                                                <span class="has-text-danger" v-if="errors.account_number">
                                            {{ errors.account_number[0] }}
                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-6">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" type="text" v-model="vendor.ifsc_code"
                                                       placeholder="IFSC Code" autofocus="">
                                                <span class="has-text-danger" v-if="errors.ifsc_code">
                                            {{ errors.ifsc_code[0] }}
                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column is-6">
                                        <div class="field">
                                            <div class="control">
                                                <input class="input" type="text" v-model="vendor.vpa"
                                                       placeholder="UPI VPA" autofocus="">
                                                <span class="has-text-danger" v-if="errors.vpa">
                                            {{ errors.vpa[0] }}
                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-6"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns">

                </div>
                <div class="has-text-centered">
                    <button
                        class="button"
                        @click="handleSubmitClick"
                        :class="{'is-loading' : isUpdating, 'is-primary' : this.vendor.id != null, 'is-success' : this.vendor.id == null}">
                        <span class="icon"><i class="mdi mdi-check-circle"></i> </span>
                        <span v-if="this.vendor.id == null">&nbsp; Create</span>
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
    import IdTypeModal from "../modals/IdType";

    export default {
        name: "NewVendor",
        components: {IdTypeModal, ProgressBarIndeterminate},
        data () {
            return {
                isLoading: false,
                isUpdating: false,
                isIdTypeLoading: true,
                vendor: {
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
                    id_type_id: '',
                    id_type_name: null,
                    image: null,
                    bank_name: '',
                    account_name: '',
                    account_number: '',
                    ifsc_code: '',
                    vpa: '',
                },
                vendorImage: null,
                idTypes: [],
                idTypeItem: {
                    id: null,
                    name: '',
                    description: '',
                },
                errors: []
            }
        },
        computed: {
            imageList : {
                get() {
                    return this.vendorImage
                }
            },
        },
        methods: {
            loadVendor() {
                this.isLoading = true
                axios.get('/api/v1/vendors/' + this.vendor.id).then(response => {
                    this.vendor = response.data.data
                    this.vendorImage = response.data.data.image
                    this.vendor.image = null

                    this.vendor.id_type_id = this.vendor.id_type != null ? this.vendor.id_type.id : ''
                    this.vendor.id_type_name = this.vendor.id_type != null ? this.vendor.id_type.name : ''
                    this.vendor.identification = this.vendor.identification == null ? '' : this.vendor.identification
                    this.vendor.address_line_1 = this.vendor.address_line_1 == null ? '' : this.vendor.address_line_1
                    this.vendor.address_line_2 = this.vendor.address_line_2 == null ? '' : this.vendor.address_line_2
                    this.vendor.city = this.vendor.city == null ? '' : this.vendor.city
                    this.vendor.state = this.vendor.state == null ? '' : this.vendor.state
                    this.vendor.country = this.vendor.country == null ? '' : this.vendor.country
                    this.vendor.pin = this.vendor.pin == null ? '' : this.vendor.pin
                    this.vendor.website = this.vendor.website == null ? '' : this.vendor.website
                    this.vendor.contact_email = this.vendor.contact_email == null ? '' : this.vendor.contact_email
                    this.vendor.bank_name = this.vendor.bank_name == null ? '' : this.vendor.bank_name
                    this.vendor.account_name = this.vendor.account_name == null ? '' : this.vendor.account_name
                    this.vendor.account_number = this.vendor.account_number == null ? '' : this.vendor.account_number
                    this.vendor.ifsc_code = this.vendor.ifsc_code == null ? '' : this.vendor.ifsc_code
                    this.vendor.vpa = this.vendor.vpa == null ? '' : this.vendor.vpa

                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error)
                })
            },
            getIdTypes () {
                axios.get('/api/v1/id-types').then(response => {
                    this.idTypes = response.data.data;
                    this.isIdTypeLoading = false;
                }).catch(error => {
                    this.isIdTypeLoading = false;
                });
            },
            showAddNewIdType() {
                this.$refs.idTypeModal.toggleModal()
            },
            idTypeSelected(idType) {
                this.vendor.id_type_id = idType.id
                this.vendor.id_type_name = idType.name
            },
            handleSubmitClick() {
                if(this.vendor.id == null) {
                    this.createVendor()
                } else {
                    this.updateVendor()
                }
            },
            createVendor() {
                this.isUpdating = true;
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                let formData = new FormData()
                formData.append('name', this.vendor.name)
                formData.append('identification', this.vendor.identification)
                formData.append('address_line_1', this.vendor.address_line_1)
                formData.append('address_line_2', this.vendor.address_line_2)
                formData.append('city', this.vendor.city)
                formData.append('state', this.vendor.state)
                formData.append('country', this.vendor.country)
                formData.append('pin', this.vendor.pin)
                formData.append('contact_name', this.vendor.contact_name)
                formData.append('contact_email', this.vendor.contact_email)
                formData.append('contact_phone', this.vendor.contact_phone)
                formData.append('website', this.vendor.website)
                formData.append('id_type_id', this.vendor.id_type_id)
                formData.append('bank_name', this.vendor.bank_name)
                formData.append('account_name', this.vendor.account_name)
                formData.append('account_number', this.vendor.account_number)
                formData.append('ifsc_code', this.vendor.ifsc_code)
                formData.append('vpa', this.vendor.vpa)
                if(this.vendor.image != null) {
                    formData.append('image', this.vendor.image)
                }

                axios.post('/api/v1/vendors', formData, config).then(response => {
                    this.isUpdating = false;
                    this.vendor = {}
                    this.showToast("Vendor created successfully!")
                    this.$router.push({ name: 'vendors'})
                }).catch(errors => {
                    this.isUpdating = false;
                    this.handleError(errors);
                })
            },
            updateVendor() {
                this.isUpdating = true;
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                let formData = new FormData()
                formData.append('name', this.vendor.name)
                formData.append('identification', this.vendor.identification)
                formData.append('address_line_1', this.vendor.address_line_1)
                formData.append('address_line_2', this.vendor.address_line_2)
                formData.append('city', this.vendor.city)
                formData.append('state', this.vendor.state)
                formData.append('country', this.vendor.country)
                formData.append('pin', this.vendor.pin)
                formData.append('contact_name', this.vendor.contact_name)
                formData.append('contact_email', this.vendor.contact_email)
                formData.append('contact_phone', this.vendor.contact_phone)
                formData.append('website', this.vendor.website)
                formData.append('id_type_id', this.vendor.id_type_id)
                formData.append('bank_name', this.vendor.bank_name)
                formData.append('account_name', this.vendor.account_name)
                formData.append('account_number', this.vendor.account_number)
                formData.append('ifsc_code', this.vendor.ifsc_code)
                formData.append('vpa', this.vendor.vpa)
                if(this.vendor.image != null) {
                    formData.append('image', this.vendor.image)
                }

                axios.post('/api/v1/vendors/' + this.vendor.id + '?_method=PATCH', formData, config).then(response => {
                    this.isUpdating = false;
                    this.vendor = {}
                    this.showToast("Vendor updated successfully!")
                    this.$router.push({ name: 'vendors'})
                }).catch(errors => {
                    this.isUpdating = false;
                    this.handleError(errors);
                })
            },
            handleFileChange(event) {
                const file = event.target.files[0];
                this.vendor.image = file
                this.vendorImage = URL.createObjectURL(file)
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
            this.vendor.id = this.$route.params.id
            if(this.vendor.id != null) {
                this.loadVendor()
            }
            this.getIdTypes();
        }
    }
</script>

<style scoped>

</style>
