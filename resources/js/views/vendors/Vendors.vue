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
                                    <p class="title">Vendors</p>
                                </div>
                                <div class="column is-2 is-right">
                                    <button class="button is-primary has-icons-left" @click="showAddVendor">
                                        <span class="mdi mdi-plus-circle"></span>
                                        <span>&nbsp;Add Vendor</span>
                                    </button>
                                </div>
                            </div>

                            <hr/>
                            <b-table
                                striped
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
                                        @click="deleteVendor(props.row)">
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
    </div>
</template>

<script>
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";
    import axios from 'axios';

    export default {
        name: "vendors",
        components: {ProgressBarIndeterminate},
        data () {
            return {
                isLoading: false,
                isDeleteVendorInProgress: false,
                vendors: [],
            }
        },
        computed: {
            chunk() {
                return chunk;
            }
        },
        methods: {
            loadVendors() {
                this.isLoading = true;
                axios.get('/api/v1/vendors').then(response => {
                    this.isLoading = false
                    this.vendors = response.data.data
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
