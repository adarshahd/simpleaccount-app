<template>
    <div class="box purchase-item-container mt-1">
        <div class="columns is-vcentered">
            <div class="column is-11">
                <div class="columns">
                    <div class="column is-4">
                        <vSelect
                            class="style-chooser"
                            @search="searchProduct"
                            @input="productSelected"
                            placeholder="Search Product"
                            :options="products"
                            :reduce="product => product.id"
                            label="name"
                            v-model="purchaseItem.product_id">
                        </vSelect>
                        <span class="has-text-danger" v-if="errors.product">{{ errors.product }}</span>
                    </div>
                    <div class="column is-3">
                        <vSelect
                            class="style-chooser"
                            @search="searchManufacturers"
                            placeholder="Search Manufacturers"
                            :options="manufacturers"
                            :reduce="manufacturer => manufacturer.id"
                            label="name"
                            v-model="purchaseItem.manufacturer_id">
                        </vSelect>
                    </div>
                    <div class="column is-2">
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label">Qty</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <p class="control">
                                        <input
                                            class="input"
                                            placeholder="Quantity"
                                            type="number"
                                            v-model="purchaseItem.quantity">
                                    </p>
                                </div>
                            </div>
                            <span class="has-text-danger" v-if="errors.quantity">
                                {{ errors.quantity }}
                            </span>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label">MRP</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <p class="control">
                                        <input
                                            class="input"
                                            placeholder="MRP"
                                            type="number"
                                            v-model="purchaseItem.mrp">
                                    </p>
                                </div>
                            </div>
                            <span class="has-text-danger" v-if="errors.mrp">
                                {{ errors.mrp }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="columns is-vcentered">
                    <div class="column is-2">
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label">Price</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <p class="control">
                                        <input class="input" placeholder="Price" type="number" v-model="purchaseItem.price" value="0">
                                    </p>
                                </div>
                            </div>
                            <span class="has-text-danger" v-if="errors.price">
                                {{ errors.price }}
                            </span>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label">Batch</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <p class="control">
                                        <input
                                            class="input"
                                            placeholder="Batch"
                                            type="text"
                                            v-model="purchaseItem.batch">
                                    </p>
                                </div>
                            </div>
                            <span class="has-text-danger" v-if="errors.batch">
                                {{ errors.batch }}
                            </span>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label">Expiry</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <p class="control">
                                        <b-datepicker type="month" v-model="purchaseItem.expiry" :mobile-native="false" trap-focus locale="en-IN"></b-datepicker>
                                    </p>
                                </div>
                            </div>
                            <span class="has-text-danger" v-if="errors.expiry">
                                {{ errors.expiry }}
                            </span>
                        </div>
                    </div>
                    <div class="column is-2">
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label">HSN</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <p class="control">
                                        <input
                                            class="input"
                                            placeholder="HSN"
                                            type="text"
                                            v-model="purchaseItem.hsn">
                                    </p>
                                </div>
                            </div>
                            <span class="has-text-danger" v-if="errors.hsn">
                                {{ errors.hsn }}
                            </span>
                        </div>
                    </div>
                    <div class="column is-3">
                        <h6 class="title is-6 has-text-right">
                            Item Total: ₹{{ getItemTotal }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="column is-1 has-text-right-desktop has-text-centered-mobile">
                <button class="delete has-background-danger" @click="deleteComponent"></button>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import vSelect from 'vue-select'
    import dayjs from 'dayjs'

    export default {
        name: "PurchaseItem",
        components: {
            vSelect
        },
        data () {
            return {
                isManufacturersLoading: false,
                manufacturers: [],
                products: [],
                errors: [],
                currentProduct: null,
            }
        },
        props: {
            purchaseItem: {
                type: Object,
                default: null
            }
        },
        computed: {
            dayjs() {
                return dayjs
            },
            getItemTotal() {
                let itemTotal = Number((this.purchaseItem.price * this.purchaseItem.quantity));
                return itemTotal.toFixed(2);
            }
        },
        methods: {
            searchProduct(search, loading) {
                if(search.length < 2) {
                    return;
                }
                loading(true);
                axios.get('/api/v1/products/search?name=' + search).then(response => {
                    loading(false);
                    this.products = response.data.data;
                }).catch(errors => {
                    this.products = [];
                    this.handleError(errors)
                })
            },
            searchManufacturers(search, loading) {
                if(search.length < 2) {
                    return;
                }
                loading(true);
                axios.get('/api/v1/manufacturers/search?name=' + search).then(response => {
                    loading(false);
                    this.manufacturers = response.data.data;
                }).catch(error => {
                    this.manufacturers = [];
                    this.handleError(error)
                })
            },
            fetchProduct() {
                axios.get('/api/v1/products/' + this.purchaseItem.product_stock.product.id).then(response => {
                    this.products.push(response.data.data)
                    this.currentProduct = this.getProduct(this.purchaseItem.product_id);
                }).catch(error => {
                    this.handleError(error)
                })
            },
            fetchManufacturer() {
                axios.get('/api/v1/manufacturers/' + this.purchaseItem.product_stock.manufacturer_id).then(response => {
                    this.manufacturers.push(response.data.data)
                }).catch(error => {
                    this.handleError(error)
                })
            },
            productSelected(val) {
                this.currentProduct = this.getProduct(val)
                this.purchaseItem.tax_percent = this.currentProduct.tax.tax
            },
            getProduct(id) {
                for(let i=0; i < this.products.length; ++i) {
                    if(this.products[i].id === id) {
                        return this.products[i];
                    }
                }
            },
            deleteComponent() {
                this.$emit('on-delete', this.purchaseItem.id);
            },
            onProductCleared() {
                this.purchaseItem.manufacturers = []
                this.purchaseItem.manufacturer_id = ""
                this.purchaseItem.product_stock_id = ""
                this.purchaseItem.price = 0
                this.purchaseItem.quantity = 0
                this.purchaseItem.mrp = 0
                this.purchaseItem.tax = 0
                this.purchaseItem.batch = ''
                this.purchaseItem.expiry = null
                this.purchaseItem.hsn = ''
            },
            buildPurchaseItem() {
                this.fetchProduct()
                this.fetchManufacturer()

                this.purchaseItem.manufacturer_id = this.purchaseItem.product_stock.manufacturer_id
                this.purchaseItem.product_id = this.purchaseItem.product_stock.product.id
                this.purchaseItem.price = this.purchaseItem.product_stock.price + this.purchaseItem.tax
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
            if(this.purchaseItem.purchaseId != null) {
                this.buildPurchaseItem()
            }
        }
    }
</script>

<style scoped>
    .purchase-item-container {
        margin: 2px;
        padding: 8px;
    }

    .style-chooser {
        background: #EBEBEB !important;
    }

    .field-label {
        margin-right: 0.5rem !important;
    }
</style>
