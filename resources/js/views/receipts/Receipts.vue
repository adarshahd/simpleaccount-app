<template>
    <section class="main-content">
        <delete-modal ref="modalDelete" v-on:delete="onDelete"></delete-modal>
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-9">
                                <p class="title">Receipts</p>
                            </div>
                            <div class="column is-3 has-text-right">
                                <button class="button is-primary has-icons-left" @click="addReceipt">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Receipt</span>
                                </button>
                            </div>
                        </div>
                        <hr/>
                        <b-table
                            narrowed
                            striped
                            :paginated="totalReceiptPages > 1"
                            backend-pagination
                            :total="totalReceiptItems"
                            :per-page="receiptItemsPerPage"
                            @page-change="onReceiptPageChange"
                            :mobile-cards="false"
                            :loading="isLoading"
                            :data="receipts">
                            <b-table-column field="id" label="Bill Id" v-slot="props">
                                {{ props.row.bill_number }}
                            </b-table-column>
                            <b-table-column field="date" label="Date" v-slot="props">
                                {{ dayjs(props.row.bill_date).format('DD MMM, YYYY') }}
                            </b-table-column>
                            <b-table-column field="customer" label="Customer" v-slot="props">
                                {{ props.row.customer.name }}
                            </b-table-column>
                            <b-table-column field="total" label="Total" v-slot="props">
                                ₹{{ props.row.total.toFixed(2) }}
                            </b-table-column>
                            <b-table-column field="mode" label="Payment Mode" v-slot="props">
                                {{ props.row.payment_method }}
                            </b-table-column>
                            <b-table-column field="reference" label="Reference" v-slot="props">
                                {{ props.row.payment_reference }}
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" numeric>
                                <span>
                                    <button class="button is-link is-small" @click="downloadReceipt(props.row)">
                                        <span class="mdi mdi-download mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-link is-small" @click="editReceipt(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button
                                        class="button is-danger is-small"
                                        :class="{ 'is-loading' : isDeleteReceiptInProgress }"
                                        @click="showDeleteModal(props.row)">
                                        <span class="mdi mdi-delete mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Receipts Found</h4>
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
import ProgressBarIndeterminate from "@/components/ProgressBarIndeterminate";
import axios from 'axios'
import dayjs from 'dayjs'
import DeleteModal from "../modals/Delete";

export default {
    name: "Receipts",
    components: {DeleteModal, ProgressBarIndeterminate},
    data() {
        return {
            isLoading: false,
            isDeleteReceiptInProgress: false,
            receipts: [],
            totalReceiptPages: 1,
            receiptItemsPerPage: 1,
            totalReceiptItems: 1,
            currentReceiptPage: 1,
            deleteItem: null
        }
    },
    computed: {
        dayjs() {
            return dayjs
        }
    },
    methods: {
        getReceipts() {
            this.isLoading = true
            axios.get('/api/v1/receipts?page=' + this.currentReceiptPage).then(response => {
                this.receipts = response.data.data
                this.totalReceiptPages = response.data.meta.last_page
                this.receiptItemsPerPage = response.data.meta.per_page
                this.totalReceiptItems = response.data.meta.total
                this.isLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        addReceipt() {
            this.$router.push({
                name: 'receipts-new',
                params: {
                    id: null,
                }
            })
        },
        editReceipt(receipt) {
            this.$router.push({
                name: 'receipt-edit',
                params: {
                    id: receipt.id,
                }
            })
        },
        downloadReceipt(receipt) {
            window.open('/api/v1/receipts/' + receipt.id + '/invoice', '_blank')
        },
        showDeleteModal(receipt) {
            this.deleteItem = receipt;
            this.$refs.modalDelete.toggleModal()
        },
        onDelete() {
            this.deleteReceipt(this.deleteItem)
        },
        deleteReceipt(receipt) {
            this.isDeleteReceiptInProgress = true;
            axios.delete('/api/v1/receipts/' + receipt.id).then(response => {
                this.isDeleteReceiptInProgress = false;
                this.getReceipts()
            })
        },
        onReceiptPageChange(val) {
            this.currentReceiptPage = val
            this.getReceipts()
        },

        handleError(error) {
            this.isLoading = false
            this.errors = error.response.data.errors
            if (error.response.status === 401) {
                this.$router.go({
                    path: '/login',
                    force: true
                })
            } else {
                this.showToast(error.response.data.message, 'is-danger')
            }
        },
        showToast(message, type = 'is-success') {
            this.$buefy.toast.open({
                message: message,
                type: type,
                position: 'is-top-right'
            })
        }
    },
    mounted() {
        this.getReceipts()
    }
}
</script>

<style scoped>

</style>
