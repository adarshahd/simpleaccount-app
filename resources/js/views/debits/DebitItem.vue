<template>
    <div class="box debit-item-container mt-1">
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
                            v-model="product_id">
                        </vSelect>
                        <span class="has-text-danger" v-if="errors.product">{{ errors.product }}</span>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <div class="control">
                                <div class="select is-fullwidth" :class="{'is-loading' : isManufacturersLoading}">
                                    <select label="Select Manufacturer" v-model="manufacturer_id" @change="onManufacturerSelected">
                                        <option value="" disabled selected>Select Manufacturer</option>
                                        <option v-for="manufacturer in manufacturers" :key="manufacturer.id" v-bind:value="manufacturer.id">
                                            {{ manufacturer.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-3">
                        <div class="field">
                            <div class="control">
                                <div class="select is-fullwidth" :class="{'is-loading' : isStockLoading}">
                                    <select label="Select Batch" v-model="debitItem.product_stock_id" @change="onStockSelected">
                                        <option value="" disabled selected>Select Batch</option>
                                        <option v-for="stock in stockList" :key="stock.id" v-bind:value="stock.id">
                                            {{ stock.batch + ', ' + dayjs(stock.expiry).format("MMM-YYYY") + " (In stock: " + stock.stock + ")" }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-2 has-text-right-desktop has-text-centered-mobile" v-if="stockSelected">
                        MRP: {{ currencySymbol }}{{ currentStock.mrp }}
                    </div>
                </div>
                <div class="columns is-vcentered" v-if="stockSelected">
                    <div class="column is-3">
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label">Qty</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <p class="control">
                                        <input
                                            class="input"
                                            :class="{'is-danger' : !isStockAvailable && debitItem.debitId == null}"
                                            placeholder="Quantity"
                                            type="number"
                                            v-model="debitItem.quantity">
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
                                <label class="label">Price</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <p class="control">
                                        <input class="input" placeholder="Price" type="number" v-model="debitItem.price" value="0">
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
                                <label class="label">Purchase ID</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <p class="control">
                                        <input class="input" placeholder="Purchase Number" type="text" v-model="debitItem.purchase_id" value="0">
                                    </p>
                                </div>
                            </div>
                        </div>
                        <span class="has-text-danger" v-if="errors.purchase_id">
                            {{ errors.purchase_id[0] }}
                        </span>
                    </div>
                    <div class="column is-1">
                        <h6>Tax: {{ currencySymbol }}{{ getTax }}</h6>
                    </div>
                    <div class="column is-2">
                        <h6 class="title is-6 has-text-right-desktop has-text-centered-mobile">Item Total: {{ currencySymbol }}{{getItemTotal}}</h6>
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
        name: "DebitItem",
        components: {
            vSelect
        },
        data () {
            return {
                isManufacturersLoading: false,
                isStockLoading: false,
                manufacturers: [],
                products: [],
                stockList: [],
                errors: [],
                stockSelected: false,
                product_id: null,
                manufacturer_id: "",
                currentStock: null,
                currencySymbol: this.$store.state.regionData.currencySymbol
            }
        },
        props: {
            debitItem: {
                type: Object,
                default: null
            }
        },
        computed: {
            dayjs() {
                return dayjs
            },
            getTax() {
                let taxExcludedPrice = (this.debitItem.price / (( this.debitItem.tax_percent / 100 ) + 1 ));
                let tax = Number(this.currentStock.tax_percent * taxExcludedPrice * this.debitItem.quantity / 100);
                return tax.toFixed(2);
            },
            isStockAvailable() {
                return this.currentStock.stock >= this.debitItem.quantity;
            },
            getItemTotal() {
                let taxExcludedPrice = (this.debitItem.price / (( this.debitItem.tax_percent / 100 ) + 1 ));
                let itemTotal = Number((taxExcludedPrice * this.debitItem.quantity) + Number(this.getTax));
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
            fetchProduct() {
                axios.get('/api/v1/products/' + this.debitItem.product_stock.product.id).then(response => {
                    this.products.push(response.data.data)
                }).catch(error => {
                    this.handleError(error)
                })
            },
            fetchManufacturer() {
                axios.get('/api/v1/manufacturers/' + this.debitItem.product_stock.manufacturer_id).then(response => {
                    this.manufacturers.push(response.data.data)
                }).catch(error => {
                    this.handleError(error)
                })
            },
            productSelected(val) {
                if(val == null) {
                    this.onProductCleared();
                    return;
                }
                this.product_id = val
                this.isManufacturersLoading = true;
                this.manufacturers = []
                this.manufacturer_id = ""
                axios.get('/api/v1/manufacturers/search?product_id=' + this.product_id).then(response => {
                    this.isManufacturersLoading = false;
                    this.manufacturers = response.data.data;
                }).catch(error => {
                    this.handleError(error)
                })
            },
            onManufacturerSelected() {
                this.isStockLoading = true
                this.stockList = []
                this.currentStock = null
                axios.get(
                    '/api/v1/products/' +
                    this.product_id +
                    '/stock/search?manufacturer_id=' + this.manufacturer_id).then(response => {
                        this.isStockLoading = false;
                        this.stockList = response.data.data;
                }).catch(error => {
                    this.handleError(error)
                })
            },
            onStockSelected() {
                this.stockSelected = true;
                this.currentStock = this.getProductStock(this.debitItem.product_stock_id)
                this.debitItem.quantity = 1;
                this.debitItem.price = this.currentStock.price
                this.debitItem.stockAvailable = this.currentStock.stock
                this.debitItem.tax_percent = this.currentStock.tax_percent;
                this.debitItem.tax = this.currentStock.price * this.currentStock.tax_percent / 100;
                this.debitItem.price += this.debitItem.tax;
                this.debitItem.name = this.getProduct(this.product_id).name
            },
            getProduct(id) {
                for(let i=0; i < this.products.length; ++i) {
                    if(this.products[i].id === id) {
                        return this.products[i];
                    }
                }
            },
            getProductStock(id) {
                for(let i=0; i < this.stockList.length; ++i) {
                    if(this.stockList[i].id === id) {
                        return this.stockList[i];
                    }
                }
            },
            deleteComponent() {
                this.$emit('on-delete', this.debitItem.id);
            },
            onProductCleared() {
                this.manufacturers = []
                this.stockList = []
                this.manufacturer_id = ""
                this.debitItem.product_stock_id = ""
                this.currentStock = null
                this.debitItem.price = 0
                this.debitItem.quantity = 0
                this.debitItem.mrp = 0
                this.debitItem.tax = 0
                this.stockSelected = false
            },
            buildDebitItem() {
                this.fetchProduct()
                this.fetchManufacturer()

                this.manufacturer_id = this.debitItem.product_stock.manufacturer_id
                this.product_id = this.debitItem.product_stock.product.id
                this.stockList.push(this.debitItem.product_stock)
                this.currentStock = this.debitItem.product_stock
                this.debitItem.price += (this.debitItem.tax / this.debitItem.quantity)
                this.isStockLoading = false;
                this.stockSelected = true;
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
            if(this.debitItem.debitId != null) {
                this.buildDebitItem()
            }
        }
    }
</script>

<style scoped>
    .debit-item-container {
        margin: 2px;
        padding: 8px;
    }

    .style-chooser {
        background: #EBEBEB !important;
    }
</style>
