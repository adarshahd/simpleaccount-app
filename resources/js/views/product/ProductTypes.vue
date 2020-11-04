<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section v-else class="main-content">
        <product-type ref="productTypeModal" :product-type="productTypeItem" v-on:loadProductType="loadProductTypes"></product-type>
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-10">
                                <p class="title">Product Types</p>
                            </div>
                            <div class="column is-2 is-right">
                                <button class="button is-primary has-icons-left" @click="showAddProductTypeModal">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Product Type</span>
                                </button>
                            </div>
                        </div>

                        <hr/>
                        <b-table
                            striped
                            :data="productTypes">
                            <b-table-column field="name" label="Product Type" v-slot="props">
                                {{ props.row.name }}
                            </b-table-column>
                            <b-table-column field="description" label="Description" v-slot="props">
                                {{ props.row.description }}
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" numeric>
                                <span>
                                    <button class="button is-link is-small" @click="showEditProductTypeModal(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button
                                        class="button is-danger is-small"
                                        :class="{ 'is-loading' : isDeleteProductTypeInProgress }"
                                        @click="deleteProductType(props.row)">
                                        <span class="mdi mdi-delete mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Product Types Found</h4>
                                    </div>
                                </div>
                            </template>
                        </b-table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import axios from 'axios';
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";
    import ProductType from "../modals/ProductType";

    export default {
        name: "ProductTypes",
        components: {ProgressBarIndeterminate, ProductType},
        data() {
            return {
                isLoading: true,
                isDeleteProductTypeInProgress: false,
                productTypeItem: {
                    id: null,
                    name: '',
                    description: '',
                },
                productTypes: null
            }
        },
        mounted() {
            this.loadProductTypes();
        },
        methods: {
            loadProductTypes() {
                this.isLoading = true
                axios.get('/api/v1/product-types').then(response => {
                    this.productTypes = response.data.data;
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                });
            },
            showAddProductTypeModal() {
                this.productTypeItem.id = null;
                this.productTypeItem.name = '';
                this.productTypeItem.description = '';

                this.$refs.productTypeModal.toggleModal();
            },
            showEditProductTypeModal(productType) {
                this.productTypeItem.id = productType.id;
                this.productTypeItem.name = productType.name;
                this.productTypeItem.description = productType.description;

                this.$refs.productTypeModal.toggleModal();
            },
            deleteProductType(productType) {
                this.isDeleteProductTypeInProgress = true;
                axios.delete('/api/v1/product-types/' + productType.id).then(response => {
                    this.isDeleteProductTypeInProgress = false;
                    this.showToast("Product Type deleted successfully")
                    this.loadProductTypes()
                }).catch(error => {
                    this.isDeleteProductTypeInProgress = false;
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
