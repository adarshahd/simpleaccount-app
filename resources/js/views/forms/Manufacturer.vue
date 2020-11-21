<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <div class="columns is-centered">
            <div class="column is-half-desktop is-12-mobile">
                <div class="box">
                    <h3 class="title is-3 has-text-centered">Manufacturer Information</h3>
                    <hr/>
                    <div class="columns is-vcentered">
                        <div class="column is-half has-text-centered">
                            <figure class="image is-128x128">
                                <img src="/images/business-shop.png" alt="" v-if="manufacturerLogo === null">
                                <img v-else :src="manufacturerLogo" alt="manufacturer-image">
                            </figure>
                        </div>
                        <div class="column is-half">
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
                            <label class="field-label" for="name">Manufacturer Name</label>
                            <input class="input" id="name" type="text" placeholder="Company Name *" v-model="manufacturer.name">
                            <span class="has-text-danger" v-if="errors.name">
                                {{ errors.name[0] }}
                            </span>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-5">
                            <div class="field">
                                <div class="control">
                                    <label class="field-label" for="short_name">Short Name</label>
                                    <input class="input" id="short_name" type="text" placeholder="Short Name" v-model="manufacturer.short_name">
                                    <span class="has-text-danger" v-if="errors.short_name">
                                        {{ errors.short_name[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="column is-7">
                            <div class="field">
                                <div class="control">
                                    <label class="field-label" for="short_name">Website</label>
                                    <input class="input" id="website" type="text" placeholder="Website" v-model="manufacturer.website">
                                    <span class="has-text-danger" v-if="errors.website">
                                        {{ errors.website[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="has-text-centered">
                        <button
                            class="button"
                            @click="handleSubmitClick"
                            :class="{'is-loading' : isManufacturerUpdating, 'is-primary' : this.manufacturer.id != null, 'is-success' : this.manufacturer.id == null}">
                            <span class="icon"><i class="mdi mdi-check-circle"></i> </span>
                            <span v-if="this.manufacturer.id == null">&nbsp; Create</span>
                            <span v-else>&nbsp; Update</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import axios from 'axios';
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";

    export default {
        name: "Manufacturer",
        components: {ProgressBarIndeterminate},
        data() {
            return {
                isLoading: false,
                isManufacturerUpdating: false,
                manufacturer: {
                    id: null,
                    name: '',
                    short_name: '',
                    website: '',
                    logo: null
                },
                manufacturerLogo: null,
                errors: []
            }
        },
        methods: {
            loadManufacturer() {
                this.isLoading = true
                axios.get('/api/v1/manufacturers/' + this.manufacturer.id).then(response => {
                    this.manufacturer = response.data.data
                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error)
                })
            },
            handleSubmitClick() {
                if(this.manufacturer.id == null) {
                    this.createManufacturer()
                } else {
                    this.updateManufacturer()
                }
            },
            createManufacturer() {
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                let formData = new FormData()
                formData.append('name', this.manufacturer.name)
                formData.append('short_name', this.manufacturer.short_name)
                formData.append('website', this.manufacturer.website)
                if(this.manufacturer.logo != null) {
                    formData.append('image', this.manufacturer.logo)
                }
                axios.post('/api/v1/manufacturers', formData, config).then(response => {
                    this.showToast("Manufacturer created successfully")
                    this.manufacturer = {}
                    this.$router.back()
                }).catch(error => {
                    this.handleError(error);
                })
            },
            updateManufacturer() {
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                let formData = new FormData()
                formData.append('name', this.manufacturer.name)
                formData.append('short_name', this.manufacturer.short_name)
                formData.append('website', this.manufacturer.website)
                if(this.manufacturer.logo != null) {
                    formData.append('image', this.manufacturer.logo)
                }
                axios.post('/api/v1/manufacturers/' + this.manufacturer.id + '/?_method=PATCH', formData, config).then(response => {
                    this.showToast("Manufacturer updated successfully")
                    this.manufacturer = {}
                    this.$router.back()
                }).catch(error => {
                    this.handleError(error);
                })
            },
            handleFileChange(event) {
                const file = event.target.files[0];
                this.manufacturer.logo = file
                this.manufacturerLogo = URL.createObjectURL(file)
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
                    type: type,
                    position: 'is-top-right'
                });
            }
        },
        mounted() {
            this.manufacturer.id = this.$route.params.id
            if(this.manufacturer.id != null) {
                this.loadManufacturer()
            }
        }
    }
</script>

<style scoped>

</style>
