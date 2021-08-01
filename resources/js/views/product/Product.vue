<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section v-else class="main-content">
        <div class="columns is-centered">
            <div class="column is-4">
                <div class="box">
                    <div class="columns">
                        <div class="column is-2">
                            <figure class="image is-48x48">
                                <img src="/images/business-shop.png" alt="" v-if="product.images.length < 1">
                                <img v-else :src="product.images[0]" alt="product-image">
                            </figure>
                        </div>
                        <div class="column is-10">
                            <h4 class="title is-4">{{ product.name }}</h4>
                            <h5 class="subtitle is-5">{{ product.product_type.name }}</h5>
                            <h6 class="subtitle is-6">{{product.tax.name}}</h6>
                            <div class="has-text-center">
                                <button class="button is-primary has-icons-left" @click="editProduct">
                                    <span class="mdi mdi-pencil"></span>
                                    <span>&nbsp;Edit Product</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-8">
                <div class="box">
                    <h3 class="title is-3 has-text-centered">Product Stock</h3>
                    <b-table
                        narrowed
                        striped
                        :paginated="stockPages > 1"
                        backend-pagination
                        :total="totalStockItems"
                        :per-page="stockPerPage"
                        @page-change="onPageChange"
                        :mobile-cards="false"
                        :data="stock"
                        :loading="isStockLoading">
                        <b-table-column field="hsn" label="HSN" v-slot="props">
                            {{ props.row.product.hsn }}
                        </b-table-column>
                        <b-table-column field="batch" label="Batch" v-slot="props">
                            {{ props.row.batch }}
                        </b-table-column>
                        <b-table-column field="expiry" label="Expiry" v-slot="props">
                            {{ dayjs(props.row.expiry).format("MMM YYYY") }}
                        </b-table-column>
                        <b-table-column field="mrp" label="MRP" v-slot="props" numeric>
                            {{ currencySymbol }}{{ props.row.mrp }}
                        </b-table-column>
                        <b-table-column field="price" label="Price" v-slot="props" numeric>
                            {{ currencySymbol }}{{ props.row.price + props.row.tax }}
                        </b-table-column>
                        <b-table-column field="stock" label="Current Stock" v-slot="props" centered>
                            {{ props.row.stock }}
                        </b-table-column>
                        <b-table-column field="total_stock" label="Total Stock" v-slot="props" centered>
                            {{ props.row.total_stock }}
                        </b-table-column>

                        <template slot="empty">
                            <div class="columns is-centered">
                                <div class="column has-text-centered is-spaced">
                                    <h5 class="subtitle m-6">No Stock Found</h5>
                                </div>
                            </div>
                        </template>
                    </b-table>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import axios from 'axios'
    import dayjs from 'dayjs'
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";

    export default {
        name: "Product",
        components: {ProgressBarIndeterminate},
        data () {
            return {
                isLoading: false,
                isStockLoading: false,
                stockPages: 1,
                stockPerPage: 1,
                stockCurrentPage: 1,
                totalStockItems: 1,
                product: {
                    id: null,
                },
                stock: [],
                errors: [],
                currencySymbol: this.$store.state.regionData.currencySymbol
            }
        },
        computed: {
            dayjs() {
                return dayjs
            }
        },
        methods: {
            loadProduct() {
                this.isLoading = true;
                axios.get('/api/v1/products/' + this.product.id).then(response => {
                    this.product = response.data.data;
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                    this.handleError(error);
                })
            },
            loadProductStock() {
                this.isStockLoading = true;
                axios.get('/api/v1/products/' + this.product.id + '/stock/search?all_stock=true&page=' + this.stockCurrentPage).then(response => {
                    this.stock = response.data.data
                    this.isStockLoading = false
                    this.errors = []
                }).catch(error => {
                    this.isStockLoading = false;
                    this.handleError(error)
                })
            },
            onPageChange(val) {
                this.stockCurrentPage = val
                this.loadProductStock()
            },
            editProduct() {
                this.$router.push({
                    name: 'product-edit',
                    params: {
                        id: this.product.id
                    }
                })
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
            this.product.id = this.$route.params.id
            this.loadProduct()
            this.loadProductStock()
        }
    }
</script>

<style scoped>

</style>
