<template>
    <section class="main-content">
        <delete-modal ref="modalDelete" v-on:delete="onDelete"></delete-modal>
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-9">
                                <p class="title">Vouchers</p>
                            </div>
                            <div class="column is-3 has-text-right">
                                <button class="button is-primary has-icons-left" @click="addVoucher">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Voucher</span>
                                </button>
                            </div>
                        </div>
                        <hr/>
                        <b-table
                            narrowed
                            striped
                            :paginated="totalVoucherPages > 1"
                            backend-pagination
                            :total="totalVoucherItems"
                            :per-page="voucherItemsPerPage"
                            @page-change="onVoucherPageChange"
                            :mobile-cards="false"
                            :loading="isLoading"
                            :data="vouchers">
                            <b-table-column field="id" label="Bill Id" v-slot="props">
                                {{ props.row.bill_number }}
                            </b-table-column>
                            <b-table-column field="date" label="Date" v-slot="props">
                                {{ dayjs(props.row.bill_date).format('DD MMM, YYYY') }}
                            </b-table-column>
                            <b-table-column field="vendor" label="Vendor" v-slot="props">
                                {{ props.row.vendor.name }}
                            </b-table-column>
                            <b-table-column field="total" label="Total" v-slot="props">
                                {{ props.row.total }}
                            </b-table-column>
                            <b-table-column field="mode" label="Payment Mode" v-slot="props">
                                {{ props.row.payment_method }}
                            </b-table-column>
                            <b-table-column field="reference" label="Reference" v-slot="props">
                                {{ props.row.payment_reference }}
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" numeric>
                                <span>
                                    <button class="button is-link is-small" @click="downloadVoucher(props.row)">
                                        <span class="mdi mdi-download mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-link is-small" @click="editVoucher(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button
                                        class="button is-danger is-small"
                                        :class="{ 'is-loading' : isDeleteVoucherInProgress }"
                                        @click="showDeleteModal(props.row)">
                                        <span class="mdi mdi-delete mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Vouchers Found</h4>
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
    name: "Vouchers",
    components: {DeleteModal, ProgressBarIndeterminate},
    data() {
        return {
            isLoading: false,
            isDeleteVoucherInProgress: false,
            vouchers: [],
            totalVoucherPages: 1,
            voucherItemsPerPage: 1,
            totalVoucherItems: 1,
            currentVoucherPage: 1,
            deleteItem: null
        }
    },
    computed: {
        dayjs() {
            return dayjs
        }
    },
    methods: {
        getVouchers() {
            this.isLoading = true
            axios.get('/api/v1/vouchers?page=' + this.currentVoucherPage).then(response => {
                this.vouchers = response.data.data
                this.totalVoucherPages = response.data.meta.last_page
                this.voucherItemsPerPage = response.data.meta.per_page
                this.totalVoucherItems = response.data.meta.total
                this.isLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        addVoucher() {
            this.$router.push({
                name: 'vouchers-new',
                params: {
                    id: null,
                }
            })
        },
        editVoucher(voucher) {
            this.$router.push({
                name: 'voucher-edit',
                params: {
                    id: voucher.id,
                }
            })
        },
        downloadVoucher(voucher) {
            window.open('/api/v1/vouchers/' + voucher.id + '/invoice', '_blank')
        },
        showDeleteModal(voucher) {
            this.deleteItem = voucher;
            this.$refs.modalDelete.toggleModal()
        },
        onDelete() {
            this.deleteVoucher(this.deleteItem)
        },
        deleteVoucher(voucher) {
            this.isDeleteVoucherInProgress = true;
            axios.delete('/api/v1/vouchers/' + voucher.id).then(response => {
                this.isDeleteVoucherInProgress = false;
                this.getVouchers()
            })
        },
        onVoucherPageChange(val) {
            this.currentVoucherPage = val
            this.getVouchers()
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
        this.getVouchers()
    }
}
</script>

<style scoped>

</style>
