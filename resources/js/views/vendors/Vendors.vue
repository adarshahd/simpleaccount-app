<template>
    <section class="main-content">
        <delete-modal ref="modalDelete" v-on:delete="onDelete"></delete-modal>
        <div class="columns is-centered">
            <div class="column is-12">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-9">
                                <p class="title">Vendors</p>
                            </div>
                            <div class="column is-3 has-text-right">
                                <button class="button is-primary has-icons-left" @click="showAddVendor">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Vendor</span>
                                </button>
                            </div>
                        </div>

                        <hr/>
                        <b-table
                            narrowed
                            striped
                            :paginated="totalVendorPages > 1"
                            backend-pagination
                            :total="totalVendorItems"
                            :per-page="vendorItemsPerPage"
                            @page-change="onVendorPageChange"
                            :mobile-cards="false"
                            :loading="isLoading"
                            :data="vendors">
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
                                    <button class="button is-info is-small" @click="showVendorDetails(props.row)">
                                        <span class="mdi mdi-eye mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-link is-small" @click="showEditVendor(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button
                                        class="button is-danger is-small"
                                        :class="{ 'is-loading' : isDeleteVendorInProgress }"
                                        @click="showDeleteModal(props.row)">
                                        <span class="mdi mdi-delete mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Vendors Found</h4>
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
        name: "vendors",
        components: {DeleteModal, ProgressBarIndeterminate},
        data () {
            return {
                isLoading: false,
                isDeleteVendorInProgress: false,
                vendors: [],
                totalVendorPages: 1,
                vendorItemsPerPage: 1,
                totalVendorItems: 1,
                currentVendorPage: 1,
                deleteItem: null
            }
        },
        methods: {
            loadVendors() {
                this.isLoading = true;
                axios.get('/api/v1/vendors?page=' + this.currentVendorPage).then(response => {
                    this.vendors = response.data.data
                    this.totalVendorPages = response.data.meta.last_page
                    this.vendorItemsPerPage = response.data.meta.per_page
                    this.totalVendorItems = response.data.meta.total
                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error);
                })
            },
            showAddVendor() {
                this.$router.push({
                    name: 'vendors-new',
                    params: {
                        id: null,
                    }
                })
            },
            showVendorDetails(vendor) {
                this.$router.push({
                    name: 'vendor-details',
                    params: {
                        id: vendor.id
                    }
                })
            },
            showEditVendor(vendor) {
                this.$router.push({
                    name: 'vendor-edit',
                    params: {
                        id: vendor.id
                    }
                })
            },
            showDeleteModal(vendor) {
                this.deleteItem = vendor;
                this.$refs.modalDelete.toggleModal()
            },
            onDelete() {
                this.deleteVendor(this.deleteItem)
            },
            deleteVendor(vendor) {
                this.isDeleteVendorInProgress = true;
                axios.delete('/api/v1/vendors/' + vendor.id).then(response => {
                    this.isDeleteVendorInProgress = false;
                    this.showToast('Vendor deleted successfully');
                    this.loadVendors()
                }).catch(error => {
                    this.handleError(error)
                })
            },
            onVendorPageChange(val) {
                this.currentVendorPage = val
                this.loadVendors()
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
            this.loadVendors();
        }
    }
</script>

<style scoped>

</style>
