<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <tax ref="taxModal" :tax="taxItem" v-on:loadTax="loadTax"></tax>
        <product-type ref="productTypeModal" :product-type="productTypeItem" v-on:loadProductType="loadProductType"></product-type>
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
                                        <img src="/images/business-shop.png" alt="" v-if="imageList.length === 0">
                                        <img v-else :src="imageList[0]" alt="product-image">
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

                        <div class="column is-10">
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

                                <div class="column">
                                    <div class="field">
                                        <label class="label" for="productType">Product Type</label>
                                        <div class="control">
                                            <b-dropdown
                                                id="productType"
                                                class="select"
                                                animation="slide"
                                                expanded>
                                                <button class="button has-background-white-ter is-fullwidth has-text-left" slot="trigger" slot-scope="{ active }">
                                                    <span v-if="product.product_type_id == null">Select Product Type</span>
                                                    <span v-else>{{ product.product_type_name }}</span>
                                                </button>

                                                <b-dropdown-item @click="showAddNewProductType">Add Product Type</b-dropdown-item>
                                                <b-dropdown-item v-for="item in productTypes" @click="productTypeSelected(item)" :key="item.id">{{ item.name }}</b-dropdown-item>
                                            </b-dropdown>
                                            <span class="has-text-danger" v-if="errors.product_type_id">
                                                {{ errors.product_type_id[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label" for="mrp">MRP</label>
                                        <div class="control">
                                            <input class="input" id="mrp" type="number" placeholder="MRP"
                                                   v-model="product.mrp">
                                            <span class="has-text-danger" v-if="errors.mrp">
                                                {{ errors.mrp[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="column">
                                    <div class="field">
                                        <label class="label" for="price">Retail Price</label>
                                        <div class="control">
                                            <input class="input" id="price" type="number" placeholder="Retail Price"
                                                   v-model="product.price">
                                            <span class="has-text-danger" v-if="errors.price">
                                                {{ errors.price[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label" for="productTax">Tax</label>
                                        <div class="control">
                                            <b-dropdown
                                                id="productTax"
                                                class="select"
                                                animation="slide"
                                                expanded>
                                                <button class="button has-background-white-ter is-fullwidth" slot="trigger" slot-scope="{ active }">
                                                    <span v-if="product.tax_id == null">Select Tax</span>
                                                    <span v-else>{{ product.tax_name }}</span>
                                                </button>

                                                <b-dropdown-item @click="showAddNewTax">Add Tax</b-dropdown-item>
                                                <b-dropdown-item v-for="item in taxes" @click="taxSelected(item)" :key="item.id">{{ item.name }}</b-dropdown-item>
                                            </b-dropdown>
                                            <span class="has-text-danger" v-if="errors.tax_id">
                                                {{ errors.tax_id[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label" for="hsn">HSN</label>
                                        <div class="control">
                                            <input class="input" id="hsn" type="text" placeholder="HSN *"
                                                   v-model="product.hsn">
                                            <span class="has-text-danger" v-if="errors.hsn">
                                                {{ errors.hsn[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-5 is-offset-2-desktop">
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
                    <hr/>
                    <div class="columns">
                        <div class="column">
                            <div class="has-text-centered">
                                <button
                                    class="button"
                                    @click="handleSubmitClick"
                                    :class="{'is-loading' : isLoading, 'is-primary' : this.product.id != null, 'is-success' : this.product.id == null}">
                                    <span class="icon"><i class="mdi mdi-check-circle"></i> </span>
                                    <span v-if="this.product.id == null">&nbsp; Create</span>
                                    <span v-else>&nbsp; Update</span>
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
    import axios from 'axios';
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";
    import Tax from "../modals/Tax";
    import ProductType from "../modals/ProductType";

    export default {
        name: "NewProduct",
        components: {ProductType, Tax, ProgressBarIndeterminate},
        data() {
            return {
                isLoading: false,
                product: {
                    id: null,
                    name: '',
                    description: '',
                    mrp: '',
                    price: '',
                    hsn: '',
                    tax_id: null,
                    tax_name: "",
                    product_type_id: null,
                    images: null,
                },
                productImages: [],
                productTypes: [],
                productTypeItem: {
                    id: null,
                    name: '',
                    description: '',
                },
                taxes: [],
                taxItem: {
                    id: null,
                    name: '',
                    tax: 0,
                },
                isSelectProductTypeLoading: true,
                isSelectTaxLoading: true,
                errors: []
            }
        },
        computed: {
            imageList: {
                get() {
                    return this.productImages
                }
            }
        },
        mounted() {
            this.product.id = this.$route.params.id
            if(this.product.id != null) {
                this.loadProduct()
            }
            this.loadProductType();
            this.loadTax();
        },
        methods: {
            loadProduct() {
                this.isLoading = true
                axios.get('/api/v1/products/' + this.product.id).then(response => {
                    let productResponse = response.data.data
                    this.product.name = productResponse.name
                    this.product.description = productResponse.description == null ? '' : productResponse.description
                    this.product.mrp = productResponse.mrp
                    this.product.price = productResponse.price
                    this.product.tax_id = productResponse.tax.id
                    this.product.tax_name = productResponse.tax.name
                    this.product.hsn = productResponse.hsn
                    this.product.product_type_id = productResponse.product_type.id
                    this.product.product_type_name = productResponse.product_type.name
                    this.productImages = productResponse.images

                    this.isLoading = false
                }).catch(error => {
                    this.isLoading = false
                    this.handleError(error)
                })
            },
            loadTax() {
                axios.get('/api/v1/taxes').then(response => {
                    this.taxes = response.data.data;
                    this.isSelectTaxLoading = false;
                }).catch(error => {
                    this.handleError(error);
                    this.isSelectTaxLoading = false;
                });
            },
            showAddNewTax() {
                this.$refs.taxModal.toggleModal()
            },
            taxSelected(tax) {
                this.product.tax_id = tax.id
                this.product.tax_name = tax.name
            },
            loadProductType() {
                axios.get('/api/v1/product-types').then(response => {
                    this.productTypes = response.data.data;
                    this.isSelectProductTypeLoading = false;
                }).catch(error => {
                    this.handleError(error);
                    this.isSelectProductTypeLoading = false;
                });
            },
            showAddNewProductType() {
                this.$refs.productTypeModal.toggleModal()
            },
            productTypeSelected(productType) {
                this.product.product_type_id = productType.id
                this.product.product_type_name = productType.name
            },
            handleSubmitClick() {
                if(this.product.id == null) {
                    this.createProduct()
                } else {
                    this.updateProduct()
                }
            },
            createProduct() {
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                let formData = new FormData()
                formData.append('name', this.product.name)
                formData.append('description', this.product.description)
                formData.append('mrp', this.product.mrp)
                formData.append('price', this.product.price)
                formData.append('tax_id', this.product.tax_id)
                formData.append('hsn', this.product.hsn)
                formData.append('product_type_id', this.product.product_type_id)
                if(this.product.images != null) {
                    formData.append('images', this.product.images)
                }
                axios.post('/api/v1/products', formData, config).then(response => {
                    this.showToast("Product created successfully")
                    this.product = {}
                    this.errors = []
                    this.$router.push({ name: 'products'})
                }).catch(error => {
                    this.handleError(error);
                })
            },
            updateProduct() {
                const config = { headers: { 'Content-Type': 'multipart/form-data' } }
                let formData = new FormData()
                formData.append('name', this.product.name)
                formData.append('description', this.product.description)
                formData.append('mrp', this.product.mrp)
                formData.append('price', this.product.price)
                formData.append('tax_id', this.product.tax_id)
                formData.append('hsn', this.product.hsn)
                formData.append('product_type_id', this.product.product_type_id)
                if(this.product.images != null) {
                    formData.append('images', this.product.images)
                }
                axios.post('/api/v1/products/' + this.product.id + '?_method=PATCH', formData, config).then(response => {
                    this.showToast("Product updated successfully")
                    this.product = {}
                    this.errors = []
                    this.$router.push({ name: 'products'})
                }).catch(error => {
                    this.handleError(error);
                })
            },
            handleFileChange(event) {
                const file = event.target.files[0];
                this.product.images = file
                this.productImages = []
                this.productImages.push(URL.createObjectURL(file))
            },
            handleError(error) {
                this.errors = error.response.data.errors
                if (error.response.status === 401) {
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
            },
        }
    }
</script>

<style scoped>

</style>
