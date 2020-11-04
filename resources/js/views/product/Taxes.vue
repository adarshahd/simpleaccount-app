<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section v-else class="main-content">
        <tax ref="taxModal" :tax="taxItem" v-on:loadTax="loadTaxes"></tax>
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-10">
                                <p class="title">Taxes</p>
                            </div>
                            <div class="column is-2 is-right">
                                <button class="button is-primary has-icons-left" @click="showAddTaxModal">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Tax</span>
                                </button>
                            </div>
                        </div>
                        <hr/>
                        <b-table
                            striped
                            :data="taxes">
                            <b-table-column field="name" label="Tax Name" v-slot="props">
                                {{ props.row.name }}
                            </b-table-column>
                            <b-table-column field="tax" label="Tax Percent" v-slot="props">
                                {{ props.row.tax }} %
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" numeric>
                                <span>
                                    <button class="button is-link is-small" @click="showEditTaxModal(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button
                                        class="button is-danger is-small"
                                        :class="{ 'is-loading' : isDeleteTaxInProgress }"
                                        @click="deleteTax(props.row)">
                                        <span class="mdi mdi-delete mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Taxes Found</h4>
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
    import Tax from "../modals/Tax";

    export default {
        name: "Taxes",
        components: {ProgressBarIndeterminate, Tax},
        data() {
            return {
                isLoading: true,
                isDeleteTaxInProgress: false,
                taxItem: {
                    id: null,
                    name: '',
                    tax: 0,
                },
                taxes: null
            }
        },
        mounted() {
            this.loadTaxes();
        },
        methods: {
            loadTaxes() {
                this.isLoading = true;
                axios.get('/api/v1/taxes').then(response => {
                    this.taxes = response.data.data;
                    this.isLoading = false;
                }).catch(error => {
                    this.isLoading = false;
                });
            },
            showAddTaxModal() {
                this.taxItem.id = null;
                this.taxItem.name = '';
                this.taxItem.tax = '';

                this.$refs.taxModal.toggleModal();
            },
            showEditTaxModal(tax) {
                this.taxItem.id = tax.id;
                this.taxItem.name = tax.name;
                this.taxItem.tax = tax.tax;

                this.$refs.taxModal.toggleModal();
            },
            deleteTax(tax) {
                this.isDeleteTaxInProgress = true;
                axios.delete('/api/v1/taxes/' + tax.id).then(response => {
                    this.isDeleteTaxInProgress = false;
                    this.showToast("Tax deleted successfully")
                    this.loadTaxes()
                }).catch(error => {
                    this.isDeleteTaxInProgress = false;
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
