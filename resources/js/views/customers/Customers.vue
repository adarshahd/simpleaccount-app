<template>
    <section class="main-content">
        <delete-modal ref="modalDelete" v-on:delete="onDelete"></delete-modal>
        <div class="columns is-centered">
            <div class="column is-12">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-9">
                                <p class="title">Customers</p>
                            </div>
                            <div class="column is-3 has-text-right">
                                <button class="button is-primary has-icons-left" @click="showAddCustomer">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Customer</span>
                                </button>
                            </div>
                        </div>

                        <hr/>
                        <b-table
                            narrowed
                            striped
                            :paginated="totalCustomerPages > 1"
                            backend-pagination
                            :total="totalCustomerItems"
                            :per-page="customerItemsPerPage"
                            @page-change="onCustomerPageChange"
                            :mobile-cards="false"
                            :loading="isLoading"
                            :data="customers">
                            <b-table-column field="name" label="Name" v-slot="props">
                                {{ props.row.name }}
                            </b-table-column>
                            <b-table-column field="type" label="Address" v-slot="props">
                                {{ props.row.address_line_1 }}<br/>
                                {{ props.row.city }}
                            </b-table-column>
                            <b-table-column field="tax" label="Phone" v-slot="props">
                                {{ props.row.contact_phone }}
                            </b-table-column>
                            <b-table-column field="stock" label="Sales" v-slot="props">
                                {{ props.row.total_sales }}
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" numeric>
                                <span>
                                    <button class="button is-info is-small" @click="showCustomerDetails(props.row)">
                                        <span class="mdi mdi-eye mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-link is-small" @click="showEditCustomer(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button
                                        class="button is-danger is-small"
                                        :class="{ 'is-loading' : isDeleteCustomerInProgress }"
                                        @click="showDeleteModal(props.row)">
                                        <span class="mdi mdi-delete mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Customers Found</h4>
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
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";
    import axios from 'axios';
    import DeleteModal from "../modals/Delete";

    export default {
        name: "customers",
        components: {DeleteModal, ProgressBarIndeterminate},
        data () {
            return {
                isLoading: false,
                isDeleteCustomerInProgress: false,
                customers: [],
                totalCustomerPages: 1,
                customerItemsPerPage: 1,
                totalCustomerItems: 1,
                currentCustomerPage: 1,
                deleteItem: null
            }
        },
        methods: {
            loadCustomers() {
                this.isLoading = true;
                axios.get('/api/v1/customers?page=' + this.currentCustomerPage).then(response => {
                    this.customers = response.data.data
                    this.totalCustomerPages = response.data.meta.last_page
                    this.customerItemsPerPage = response.data.meta.per_page
                    this.totalCustomerItems = response.data.meta.total
                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error);
                })
            },
            showAddCustomer() {
                this.$router.push({
                    name: 'customers-new',
                    params: {
                        id: null,
                    }
                })
            },
            showCustomerDetails(customer) {
                this.$router.push({
                    name: 'customer-details',
                    params: {
                        id: customer.id
                    }
                })
            },
            showEditCustomer(customer) {
                this.$router.push({
                    name: 'customer-edit',
                    params: {
                        id: customer.id
                    }
                })
            },
            showDeleteModal(customer) {
                this.deleteItem = customer;
                this.$refs.modalDelete.toggleModal()
            },
            onDelete() {
                this.deleteCustomer(this.deleteItem)
            },
            deleteCustomer(customer) {
                this.isDeleteCustomerInProgress = true;
                axios.delete('/api/v1/customers/' + customer.id).then(response => {
                    this.isDeleteCustomerInProgress = false;
                    this.showToast('Customer deleted successfully');
                    this.loadCustomers()
                }).catch(error => {
                    this.handleError(error)
                })
            },
            onCustomerPageChange(val) {
                this.currentCustomerPage = val
                this.loadCustomers()
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
                    type: type
                });
            }
        },
        mounted() {
            this.loadCustomers();
        }
    }
</script>

<style scoped>

</style>
