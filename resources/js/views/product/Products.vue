<template>
    <div>
        <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
        <section v-else class="main-content">
            <div class="columns is-centered">
                <div class="column is-10">
                    <div class="card">
                        <div class="card-content">
                            <div class="columns is-centered">
                                <div class="column is-10">
                                    <p class="title">Products</p>
                                </div>
                                <div class="column is-2 is-right">
                                    <button class="button is-primary has-icons-left" @click="showAddProduct">
                                        <span class="mdi mdi-plus-circle"></span>
                                        <span>&nbsp;Add Product</span>
                                    </button>
                                </div>
                            </div>

                            <hr/>
                            <b-table
                                striped
                                :data="products">
                                <b-table-column field="name" label="Name" v-slot="props">
                                    {{ props.row.name }}
                                </b-table-column>
                                <b-table-column field="type" label="Type" v-slot="props">
                                    {{ props.row.product_type.name }}
                                </b-table-column>
                                <b-table-column field="tax" label="Tax" v-slot="props">
                                    {{ props.row.tax.tax }}%
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
                    tax_id: 0,
                    product_type_id: 0
                }
            }
        },
        methods: {
            loadProducts() {
                this.isLoading = true;
                axios.get('/api/v1/products').then(response => {
                    this.products = response.data.data;
                    this.isLoading = false;
                }).catch(error => {
                    //console.log(error.response.status);
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
