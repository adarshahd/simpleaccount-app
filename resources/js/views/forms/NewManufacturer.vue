<template>
    <section class="main-content">
        <div class="columns">
            <div class="column is-half is-centered">
                <div class="box has-background-white">
                    <h3 class="title is-3 has-text-centered">Manufacturer Information</h3>
                    <hr/>
                    <div class="columns is-vcentered">
                        <div class="column is-half has-text-centered">
                            <figure class="image is-128x128">
                                <img :src="companyLogo">
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
                        <button class="button is-primary" @click="createManufacturer" :class="{'is-loading' : isLoading}">
                            <span class="icon"><i class="mdi mdi-check-circle"></i> </span>
                            <span>Create</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import axios from 'axios';

    export default {
        name: "NewManufacturer",
        data() {
            return {
                isLoading: false,
                logoFile : null,
                manufacturer: {},
                errors: []
            }
        },
        computed: {
            companyLogo : {
                get() {
                    return this.logoFile == null ? "/images/business-shop.png" : this.logoFile;
                },
                set(val) {
                    this.companyLogo = val;
                }
            },
        },
        methods: {
            createManufacturer() {
                axios.post('/api/v1/manufacturers', this.manufacturer).then(response => {
                    this.showToast("Manufacturer created successfully")
                    this.manufacturer = {}
                }).catch(error => {
                    this.handleError(error);
                })
            },
            handleFileChange(event) {
                this.logoFile = event.target.files[0];
                this.companyLogo = event.target.result;
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
        }
    }
</script>

<style scoped>

</style>
