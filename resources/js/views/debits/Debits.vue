<template>
    <section class="main-content">
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-9">
                                <p class="title">Debits</p>
                            </div>
                            <div class="column is-3 has-text-right">
                                <button class="button is-primary has-icons-left" @click="showAddDebit">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Debit</span>
                                </button>
                            </div>
                        </div>

                        <hr/>
                        <b-table
                            narrowed
                            striped
                            :paginated="totalDebitPages > 1"
                            backend-pagination
                            :total="totalDebitItems"
                            :per-page="debitItemsPerPage"
                            @page-change="onDebitPageChange"
                            :mobile-cards="false"
                            :loading="isLoading"
                            :data="debits">
                            <b-table-column field="id" label="ID" v-slot="props">
                                {{ props.row.bill_number }}
                            </b-table-column>
                            <b-table-column field="vendor" label="Vendor" v-slot="props">
                                {{ props.row.vendor.name }}<br/>
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
                                    <button class="button is-info is-small" @click="showDebitDetails(props.row)">
                                        <span class="mdi mdi-eye mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-link is-small" @click="downloadInvoice(props.row)">
                                        <span class="mdi mdi-download mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-primary is-small" @click="editDebit(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Debits Found</h4>
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
        name: "Debits",
        components: {
            ProgressBarIndeterminate
        },
        data() {
            return {
                isLoading: true,
                debits: [],
                totalDebitPages: 1,
                debitItemsPerPage: 1,
                totalDebitItems: 1,
                currentDebitPage: 1,
            }
        },
        computed: {
            dayjs() {
                return dayjs
            }
        },
        methods: {
            getDebits() {
                this.isLoading = true
                axios.get('/api/v1/debits?page=' + this.currentDebitPage).then(response => {
                    this.debits = response.data.data
                    this.totalDebitPages = response.data.meta.last_page
                    this.debitItemsPerPage = response.data.meta.per_page
                    this.totalDebitItems = response.data.meta.total
                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error)
                })
            },
            showAddDebit() {
                this.$router.push({
                    name: 'debits-new',
                    params: {
                        id: null,
                    }
                })
            },
            downloadInvoice(debit) {
                window.open('/api/v1/debits/' + debit.id + '/invoice', '_blank')
            },
            showDebitDetails(debit) {
                this.$router.push({
                    name: 'debit-details',
                    params: {
                        id: debit.id,
                    }
                })
            },
            editDebit(debit) {
                this.$router.push({
                    name: 'debit-edit',
                    params: {
                        id: debit.id,
                    }
                })
            },
            onDebitPageChange(val) {
                this.currentDebitPage = val
                this.getDebits()
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
            this.getDebits()
        }
    }
</script>

<style scoped>

</style>
