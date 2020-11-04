<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section v-else class="main-content">
        <div class="columns is-centered">
            <div class="column is-centered">
                <div class="box has-background-white">
                    <h3 class="title is-3 has-text-centered">Product Information</h3>
                    <hr/>
                    <div class="columns">
                        <div class="column is-2">
                            <div class="columns">
                                <div class="column">
                                    <label class="label">Select Product Image</label>
                                    <figure class="image is-128x128" style="border: 3px #dbdbdb solid">
                                        <img :src="companyLogo" alt="product-image">
                                    </figure>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="file">
                                        <label class="file-label">
                                            <input class="file-input" type="file" @change="handleFileChange">
                                            <span class="file-cta">
                                                <span class="file-icon">
                                                    <i class="mdi mdi-upload"></i>
                                                </span>
                                                <span class="file-label">
                                                    Choose image…
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="column is-5">
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label" for="name">Product Name</label>
                                        <div class="control">
                                            <input class="input" id="name" type="text" placeholder="Product Name *"
                                                   v-model="product.name">
                                            <span class="has-text-danger" v-if="errors.name">
                                                {{ errors.name[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label" for="description">Product Description</label>
                                        <div class="control">
                                    <textarea class="textarea" id="description"
                                              placeholder="Product description"
                                              v-model="product.description"></textarea>
                                            <span class="has-text-danger" v-if="errors.description">
                                                {{ errors.description[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="column is-5">
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label" for="productType">Select Product Type</label>
                                        <div class="control">
                                            <div class="select is-fullwidth"
                                                 :class="{'is-loading' : isSelectProductTypeLoading}">
                                                <select id="productType" v-model="product.product_types_id">
                                                    <option disabled value="">Select Product Type</option>
                                                    <option v-for="productType in productTypes" :key="productType.id"
                                                            v-bind:value="productType.id">
                                                        {{ productType.type }}
                                                    </option>
                                                </select>
                                                <span class="has-text-danger" v-if="errors.product_types_id">
                                            {{ errors.product_types_id[0] }}
                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label" for="productTax">Select Tax</label>
                                        <div class="control">
                                            <div class="select is-fullwidth"
                                                 :class="{'is-loading' : isSelectTaxLoading}">
                                                <select id="productTax" v-model="product.tax_id">
                                                    <option disabled value="">Select Tax</option>
                                                    <option v-for="tax in taxes" :key="tax.id" v-bind:value="tax.id">
                                                        {{ tax.name }}
                                                    </option>
                                                </select>
                                                <span class="has-text-danger" v-if="errors.tax_id">
                                            {{ errors.tax_id[0] }}
                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <hr/>
                    <div class="columns">
                        <div class="column">
                            <div class="has-text-centered">
                                <button class="button is-primary" @click="updateProduct"
                                        :class="{'is-loading' : isLoading}">
                                    <span class="icon"><i class="mdi mdi-check-circle"></i> </span>
                                    <span>Update</span>
                                </button>
                            </div>
                        </div>
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
        name: "Product",
        components: {ProgressBarIndeterminate, PerfectScrollbar},
        data () {
            return {
                isLoading: true,
                isUpdating: false,
                product: null,
                productTypes: [],
                taxes: [],
                isSelectProductTypeLoading: true,
                isSelectTaxLoading: true,
                logoFile : null,
                errors: []
            }
        },
        computed: {
            currentProductId: {
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
            loadProduct() {
                this.isLoading = true;
                axios.get('/api/v1/products/' + this.currentProductId).then(response => {
                    this.isLoading = false;
                    this.isSelectProductTypeLoading = false;
                    this.isSelectTaxLoading = false;
                    this.product = response.data.data;
                    console.log(response.data.data);
                }).catch(error => {
                    this.isLoading = false;
                    this.handleError(error);
                })
            },
            updateProduct() {
                this.isUpdating = true;
                axios.patch('/api/v1/products/' + this.currentProductId,
                    this.product).then(response => {
                    this.isUpdating = false
                    this.errors = []
                    this.showToast("Updated successfully")
                }).catch(error => {
                    this.isUpdating = false;
                    this.handleError(error)
                })
            },
            loadTax() {
                axios.get('/api/v1/taxes').then(response => {
                    this.taxes = response.data.data;
                    this.isSelectTaxLoading = false;
                }).catch(response => {
                    this.isSelectTaxLoading = false;
                });
            },
            loadProductType() {
                axios.get('/api/v1/producttypes').then(response => {
                    this.productTypes = response.data.data;
                    this.isSelectProductTypeLoading = false;
                }).catch(response => {
                    this.isSelectProductTypeLoading = false;
                });
            },
            handleFileChange(event) {
                let file = event.target.files[0];
                let reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = e => {
                    this.logoFile = e.target.result
                }
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
            this.loadProduct();
            this.loadProductType();
            this.loadTax();
        }
    }
</script>

<style scoped>

</style>
