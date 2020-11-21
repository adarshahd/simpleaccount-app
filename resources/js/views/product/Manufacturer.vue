<template>
    <progress-bar-indeterminate v-if="isLoading" class="main-content"></progress-bar-indeterminate>
    <section v-else class="main-content">
        <div class="columns">
            <div class="column is-4">
                <div class="box has-background-white">
                    <div class="columns">
                        <div class="column is-4">
                            <figure class="image is-128x128">
                                <img src="/images/business-shop.png" alt="" v-if="manufacturer.logo !== null || manufacturer.logo !== ''">
                                <img v-else :src="manufacturer.logo" alt="manufacturer-logo">
                            </figure>
                        </div>
                        <div class="column is-8">
                            <h4 class="title is-4">{{ manufacturer.name }}</h4>
                            <h5 class="subtitle is-5">{{ manufacturer.short_name }}</h5>
                            <h6 class="subtitle is-5">{{ manufacturer.website }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-8">
                <h3 class="title is-3 has-text-centered">Products</h3>
                <b-table
                    narrowed
                    striped
                    :paginated="totalProductPages > 1"
                    backend-pagination
                    :total="totalProducts"
                    :per-page="productsPerPage"
                    @page-change="onPageChange"
                    :mobile-cards="false"
                    :data="products"
                    :loading="isProductsLoading">
                    <b-table-column field="name" label="Name" v-slot="props">
                        {{ props.row.name }}
                    </b-table-column>
                    <b-table-column field="type" label="Type" v-slot="props">
                        {{ props.row.product_type.name }}
                    </b-table-column>
                    <b-table-column field="stock" label="Stock" v-slot="props">
                        {{ props.row.product_stock }}
                    </b-table-column>
                    <b-table-column label="Actions" v-slot="props" centered>
                        <span>
                            <button class="button is-link is-small" @click="viewProduct(props.row)">
                                <span class="mdi mdi-eye mdi-18px"></span>
                            </button>
                        </span>
                    </b-table-column>
                    <template slot="empty">
                        <div class="columns is-centered">
                            <div class="column has-text-centered is-spaced">
                                <h4 class="title m-6">No Products Found</h4>
                            </div>
                        </div>
                    </template>
                </b-table>
            </div>
        </div>
    </section>
</template>

<script>
    import axios from 'axios'
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";
    export default {
        name: "Manufacturer",
        components: {ProgressBarIndeterminate},
        data () {
            return {
                isLoading: true,
                isProductsLoading: false,
                isUpdating: false,
                manufacturer: {
                    id: null,
                    name: '',
                    short_name: '',
                    website: ''
                },
                products: [],
                currentProductPage: 1,
                totalProducts: 1,
                productsPerPage: 1,
                totalProductPages: 1,
                logoFile : null,
                errors: []
            }
        },
        methods: {
            loadManufacturer() {
                this.isLoading = true;
                axios.get('/api/v1/manufacturers/' + this.manufacturer.id).then(response => {
                    this.isLoading = false;
                    this.manufacturer = response.data.data;
                }).catch(error => {
                    this.handleError(error);
                })
            },
            loadProducts() {
                this.isProductsLoading = true;
                axios.get('/api/v1/products?manufacturer_id=' + this.manufacturer.id + '&page=' + this.currentProductPage).then(response =>{
                    this.products = response.data.data;
                    this.totalProductPages = response.data.meta.last_page
                    this.productsPerPage = response.data.meta.per_page
                    this.totalProducts = response.data.meta.total
                    this.isProductsLoading = false;
                }).catch(error => {
                    this.handleError(error);
                })
            },
            onPageChange(val) {
                this.currentProductPage = val
                this.loadProducts()
            },
            viewProduct(product){
                this.$router.push({
                    name: 'product-details',
                    params: {
                        id: product.id
                    }
                });
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
            this.loadManufacturer();
            this.loadProducts();
        }
    }
</script>

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
