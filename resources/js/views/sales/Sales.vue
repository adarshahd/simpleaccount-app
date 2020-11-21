<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section v-else class="main-content">
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-9">
                                <p class="title">Sales</p>
                            </div>
                            <div class="column is-3 has-text-right">
                                <button class="button is-primary has-icons-left" @click="showAddSale">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Sale</span>
                                </button>
                            </div>
                        </div>

                        <hr/>
                        <b-table
                            striped
                            :data="sales">
                            <b-table-column field="id" label="ID" v-slot="props">
                                {{ props.row.bill_number }}
                            </b-table-column>
                            <b-table-column field="customer" label="Customer" v-slot="props">
                                {{ props.row.customer.name }}<br/>
                            </b-table-column>
                            <b-table-column field="date" label="Date" v-slot="props">
                                {{ dayjs(props.row.bill_date).format("DD MMM, YYYY") }}
                            </b-table-column>
                            <b-table-column field="subtotal" label="Subtotal" v-slot="props">
                                ₹{{ props.row.sub_total }}
                            </b-table-column>
                            <b-table-column field="discount" label="Discount" v-slot="props">
                                ₹{{ props.row.discount }}
                            </b-table-column>
                            <b-table-column field="tax" label="Tax" v-slot="props">
                                ₹{{ props.row.tax }}
                            </b-table-column>
                            <b-table-column field="total" label="Total" v-slot="props">
                                ₹{{ props.row.total }}
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" numeric>
                                <span>
                                    <button class="button is-info is-small" @click="showSaleDetails(props.row)">
                                        <span class="mdi mdi-eye mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-link is-small" @click="downloadInvoice(props.row)">
                                        <span class="mdi mdi-download mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-primary is-small" @click="editSale(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Sales Found</h4>
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
    import axios from 'axios'
    import dayjs from 'dayjs'
    import ProgressBarIndeterminate from "@/components/ProgressBarIndeterminate";

    export default {
        name: "Sales",
        components: {
            ProgressBarIndeterminate
        },
        data() {
            return {
                isLoading: true,
                sales: []
            }
        },
        computed: {
            dayjs() {
                return dayjs
            }
        },
        methods: {
            getSales() {
                this.isLoading = true
                axios.get('/api/v1/sales').then(response => {
                    this.sales = response.data.data
                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error)
                })
            },
            showAddSale() {
                this.$router.push({
                    name: 'sales-new',
                    params: {
                        id: null,
                    }
                })
            },
            downloadInvoice(sale) {
                window.open('/api/v1/sales/' + sale.id + '/invoice', '_blank')
            },
            showSaleDetails(sale) {
                this.$router.push({
                    name: 'sale-details',
                    params: {
                        id: sale.id,
                    }
                })
            },
            editSale(sale) {
                this.$router.push({
                    name: 'sale-edit',
                    params: {
                        id: sale.id,
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
                    type: type
                });
            }
        },
        mounted() {
            this.getSales()
        }
    }
</script>

<style scoped>

</style>
