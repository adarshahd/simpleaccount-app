<template>
    <section class="main-content">
        <tax ref="taxModal" :tax="taxItem" v-on:loadTax="loadTaxes"></tax>
        <delete-modal ref="modalDelete" v-on:delete="onDelete"></delete-modal>
        <div class="columns is-centered">
            <div class="column is-12">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-9">
                                <p class="title">Taxes</p>
                            </div>
                            <div class="column is-3 has-text-right">
                                <button class="button is-primary has-icons-left" @click="showAddTaxModal">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Tax</span>
                                </button>
                            </div>
                        </div>
                        <hr/>
                        <b-table
                            narrowed
                            striped
                            :paginated="totalTaxPages > 1"
                            backend-pagination
                            :total="totalTaxItems"
                            :per-page="taxItemsPerPage"
                            @page-change="onTaxPageChange"
                            :mobile-cards="false"
                            :loading="isLoading"
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
                                        @click="showDeleteModal(props.row)">
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
    import DeleteModal from "../modals/Delete";

    export default {
        name: "Taxes",
        components: {DeleteModal, ProgressBarIndeterminate, Tax},
        data() {
            return {
                isLoading: true,
                isDeleteTaxInProgress: false,
                taxItem: {
                    id: null,
                    name: '',
                    tax: 0,
                },
                taxes: [],
                totalTaxPages: 1,
                taxItemsPerPage: 1,
                totalTaxItems: 1,
                currentTaxPage: 1,
                deleteItem: null
            }
        },
        mounted() {
            this.loadTaxes();
        },
        methods: {
            loadTaxes() {
                this.isLoading = true;
                axios.get('/api/v1/taxes?page=' + this.currentTaxPage).then(response => {
                    this.taxes = response.data.data
                    this.totalTaxPages = response.data.meta.last_page
                    this.taxItemsPerPage = response.data.meta.per_page
                    this.totalTaxItems = response.data.meta.total
                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error)
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
            showDeleteModal(tax) {
                this.deleteItem = tax;
                this.$refs.modalDelete.toggleModal()
            },
            onDelete() {
                this.deleteTax(this.deleteItem)
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
            onTaxPageChange(val) {
                this.currentTaxPage = val
                this.loadTaxes()
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
