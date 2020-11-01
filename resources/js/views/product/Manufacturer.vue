<template>
    <progress-bar-indeterminate v-if="isLoading" class="main-content"></progress-bar-indeterminate>
    <section v-else class="main-content">
        <div class="columns">
            <div class="column is-half">
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
                        <button class="button is-primary" @click="updateManufacturer" :class="{'is-loading' : isUpdating}">
                            <span class="icon"><i class="mdi mdi-check-circle"></i> </span>
                            <span>Update</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="column is-half">
                <h3 class="title is-3 has-text-centered">Products</h3>
                <progress-bar-indeterminate v-if="isProductsLoading"></progress-bar-indeterminate>
                <div v-else>
                    <div v-if="manufacturerProducts.length > 0">
                        <perfect-scrollbar ref="scroll" class="scroll">
                            <div v-for="product in manufacturerProducts" class="card-container">
                                <div class="product-container has-background-light">
                                    <div class="columns">
                                        <div class="column is-two-thirds">
                                            <h5 class="title is-5">{{ product.name }}</h5>
                                        </div>
                                        <div class="column has-text-right-desktop has-text-centered-mobile">
                                            <button class="button is-link" @click="showProduct(product)">
                                                View
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </perfect-scrollbar>
                    </div>
                    <div v-else>
                        <h4 class="subtitle is-4 has-text-centered">No product stock found for this manufacturer</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import axios from 'axios'
    import { PerfectScrollbar } from 'vue2-perfect-scrollbar';
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";
    export default {
        name: "Manufacturer",
        components: {ProgressBarIndeterminate, PerfectScrollbar},
        data () {
            return {
                isLoading: true,
                isProductsLoading: false,
                isUpdating: false,
                manufacturer: null,
                manufacturerProducts: [],
                logoFile : null,
                errors: []
            }
        },
        computed: {
            currentManufacturerId: {
                get() {
                    return this.$route.params.id;
                }
            },
            companyLogo : {
                get() {
                    return this.logoFile == null ? "/images/business-shop.png" : this.logoFile;
                },
                set() {

                }
            },
        },
        methods: {
            loadManufacturer() {
                this.isLoading = true;
                axios.get('/api/v1/manufacturers/' + this.currentManufacturerId).then(response => {
                    this.isLoading = false;
                    this.manufacturer = response.data.data;
                }).catch(error => {
                    //this.isLoading = false;
                    this.handleError(error);
                })
            },
            loadManufacturerProducts() {
                this.isProductsLoading = true;
                axios.get('/api/v1/products?manufacturers_id=' + this.currentManufacturerId).then(response =>{
                    this.isProductsLoading = false;
                    this.manufacturerProducts = this.manufacturerProducts.concat(response.data.data);
                }).catch(error => {
                    this.handleError(error);
                })
            },
            updateManufacturer() {
                this.isUpdating = true;
                axios.patch('/api/v1/manufacturers/' + this.currentManufacturerId,
                    this.manufacturer).then(response => {
                        this.isUpdating = false;
                        this.showToast("Updated successfully")
                }).catch(error => {
                    this.isUpdating = false;
                    this.handleError(error)
                })
            },
            showProduct(product){
                this.$router.push({ name: 'products', params: { id: product.id }});
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
        },
        mounted() {
            this.loadManufacturer();
            this.loadManufacturerProducts();
        }
    }
</script>

<style src="vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css"/>
<style scoped>
    .scroll {
        position: relative;
        max-height: 75vh;
        overflow: hidden;
        padding-top: 16px;
    }
    .product-container {
        padding: 20px;
        margin: 8px;
    }
</style>
