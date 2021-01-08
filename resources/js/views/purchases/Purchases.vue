<template>
    <section class="main-content">
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-9">
                                <p class="title">Purchases</p>
                            </div>
                            <div class="column is-3 has-text-right">
                                <button class="button is-primary has-icons-left" @click="showAddPurchase">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Purchase</span>
                                </button>
                            </div>
                        </div>

                        <hr/>
                        <b-table
                            narrowed
                            striped
                            :paginated="totalPurchasePages > 1"
                            backend-pagination
                            :total="totalPurchaseItems"
                            :per-page="purchaseItemsPerPage"
                            @page-change="onPurchasePageChange"
                            :mobile-cards="false"
                            :loading="isLoading"
                            :data="purchases">
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
                                {{ currencySymbol }}{{ props.row.sub_total }}
                            </b-table-column>
                            <b-table-column field="discount" label="Discount" v-slot="props">
                                {{ currencySymbol }}{{ props.row.discount }}
                            </b-table-column>
                            <b-table-column field="tax" label="Tax" v-slot="props">
                                {{ currencySymbol }}{{ props.row.tax }}
                            </b-table-column>
                            <b-table-column field="total" label="Total" v-slot="props">
                                {{ currencySymbol }}{{ props.row.total }}
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" numeric>
                                <span>
                                    <button class="button is-info is-small" @click="showPurchaseDetails(props.row)">
                                        <span class="mdi mdi-eye mdi-18px"></span>
                                    </button>
                                </span>
                                <!--<span>
                                    <button class="button is-link is-small" @click="downloadInvoice(props.row)">
                                        <span class="mdi mdi-download mdi-18px"></span>
                                    </button>
                                </span>-->
                                <span>
                                    <button class="button is-primary is-small" @click="editPurchase(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Purchases Found</h4>
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
        name: "Purchases",
        components: {
            ProgressBarIndeterminate
        },
        data() {
            return {
                isLoading: true,
                purchases: [],
                totalPurchasePages: 1,
                purchaseItemsPerPage: 1,
                totalPurchaseItems: 1,
                currentPurchasePage: 1,
                currencySymbol: this.$store.state.regionData.currencySymbol
            }
        },
        computed: {
            dayjs() {
                return dayjs
            }
        },
        methods: {
            getPurchases() {
                this.isLoading = true
                axios.get('/api/v1/purchases?page=' + this.currentPurchasePage).then(response => {
                    this.purchases = response.data.data
                    this.totalPurchasePages = response.data.meta.last_page
                    this.purchaseItemsPerPage = response.data.meta.per_page
                    this.totalPurchaseItems = response.data.meta.total
                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error)
                })
            },
            showAddPurchase() {
                this.$router.push({
                    name: 'purchases-new',
                    params: {
                        id: null,
                    }
                })
            },
            downloadInvoice(purchase) {

            },
            showPurchaseDetails(purchase) {
                this.$router.push({
                    name: 'purchase-details',
                    params: {
                        id: purchase.id,
                    }
                })
            },
            editPurchase(purchase) {
                this.$router.push({
                    name: 'purchase-edit',
                    params: {
                        id: purchase.id,
                    }
                })
            },
            onPurchasePageChange(val) {
                this.currentPurchasePage = val
                this.getPurchases()
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
            this.getPurchases()
        }
    }
</script>

<style scoped>

</style>
