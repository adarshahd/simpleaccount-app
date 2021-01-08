<template>
    <div>
        <section class="main-content">
            <div class="columns is-centered">
                <div class="column is-10">
                    <div class="card">
                        <div class="card-content">
                            <div class="columns is-centered">
                                <div class="column is-9">
                                    <p class="title">Products</p>
                                </div>
                                <div class="column is-3 has-text-right">
                                    <button class="button is-primary has-icons-left" @click="showAddProduct">
                                        <span class="mdi mdi-plus-circle"></span>
                                        <span>&nbsp;Add Product</span>
                                    </button>
                                </div>
                            </div>

                            <hr/>
                            <b-table
                                narrowed
                                striped
                                :paginated="totalProductPages > 1"
                                backend-pagination
                                :total="totalProductItems"
                                :per-page="productItemsPerPage"
                                @page-change="onProductPageChange"
                                :mobile-cards="false"
                                :loading="isLoading"
                                :data="products">
                                <b-table-column field="name" label="Name" v-slot="props">
                                    {{ props.row.name }}
                                </b-table-column>
                                <b-table-column field="type" label="Type" v-slot="props">
                                    {{ props.row.product_type.name }}
                                </b-table-column>
                                <b-table-column field="mrp" label="MRP" v-slot="props">
                                    {{ currencySymbol }}{{ props.row.mrp == null ? '-' : props.row.mrp.toFixed(2) }}
                                </b-table-column>
                                <b-table-column field="price" label="Retail Price" v-slot="props">
                                    {{ currencySymbol }}{{ props.row.price == null ? '-' : props.row.price.toFixed(2) }}
                                </b-table-column>
                                <b-table-column field="tax" label="Tax" v-slot="props">
                                    {{ props.row.tax.tax }}%
                                </b-table-column>
                                <b-table-column field="hsn" label="HSN" v-slot="props">
                                    {{ props.row.hsn }}
                                </b-table-column>
                                <b-table-column field="stock" label="Stock" v-slot="props">
                                    {{ props.row.product_stock }}
                                </b-table-column>
                                <b-table-column label="Actions" v-slot="props" numeric>
                                <span>
                                    <button class="button is-info is-small" @click="showProductDetails(props.row)">
                                        <span class="mdi mdi-eye mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-link is-small" @click="showEditProduct(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                                    <span>
                                    <button
                                        class="button is-danger is-small"
                                        :class="{ 'is-loading' : isDeleteProductTypeInProgress }"
                                        @click="deleteProduct(props.row)">
                                        <span class="mdi mdi-delete mdi-18px"></span>
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
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import axios from 'axios'
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";

    export default {
        name: "Products",
        components: {ProgressBarIndeterminate},
        data() {
            return {
                isLoading: false,
                isDeleteProductTypeInProgress: false,
                products: [],
                product: {
                    id: null,
                    name: '',
                    description: '',
                    mrp: '',
                    price: '',
                    tax_id: 0,
                    hsn: '',
                    product_type_id: 0
                },
                totalProductPages: 1,
                productItemsPerPage: 1,
                totalProductItems: 1,
                currentProductPage: 1,
                currencySymbol: this.$store.state.regionData.currencySymbol
            }
        },
        methods: {
            loadProducts() {
                this.isLoading = true;
                axios.get('/api/v1/products?page=' + this.currentProductPage).then(response => {
                    this.products = response.data.data;
                    this.totalProductPages = response.data.meta.last_page
                    this.productItemsPerPage = response.data.meta.per_page
                    this.totalProductItems = response.data.meta.total
                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error)
                })
            },
            showAddProduct() {
                this.$router.push({
                    name: 'products-new',
                    params: {
                        id: null,
                    }
                })
            },
            showProductDetails(product) {
                this.$router.push({
                    name: 'product-details',
                    params: {
                        id: product.id,
                    }
                })
            },
            showEditProduct(product) {
                this.$router.push({
                    name: 'product-edit',
                    params: {
                        id: product.id,
                    }
                })
            },
            deleteProduct(product) {
                this.isDeleteProductTypeInProgress = true;
                axios.delete('/api/v1/products/' + product.id).then(response => {
                    this.isDeleteProductTypeInProgress = false;
                    this.showToast('Product deleted successfully');
                    this.loadProducts()
                }).catch(error => {
                    this.handleError(error)
                })
            },
            onProductPageChange(val) {
                this.currentProductPage = val
                this.loadProducts()
            },


            handleError(error) {
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
        },
        mounted() {
            this.loadProducts();
        }
    }
</script>

<style scoped>

</style>
