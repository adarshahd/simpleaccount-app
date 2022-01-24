<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns">
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Vendor</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <v-select class="has-background-white-ter"
                                                          @search="searchVendor"
                                                          @input="onVendorSelected"
                                                          placeholder="Search Vendor"
                                                          :options="vendors"
                                                          :reduce="vendor => vendor.id"
                                                          label="name"
                                                          v-model="voucher.vendor_id">
                                                </v-select>
                                            </p>
                                            <span class="has-text-danger" v-if="errors.vendor_id">
                                                {{ errors.vendor_id[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Voucher date</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <b-datepicker
                                                    :mobile-native="false"
                                                    :date-formatter=dateFormat
                                                    placeholder="Voucher date"
                                                    icon="calendar"
                                                    v-model="voucher.bill_date"
                                                />
                                            </p>
                                            <span class="has-text-danger" v-if="errors.bill_date">
                                                {{ errors.bill_date[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Amount</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <input class="input" placeholder="Amount *" type="number" v-model="voucher.total">
                                            </p>
                                            <span class="has-text-danger" v-if="errors.total">
                                                {{ errors.total[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Method</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <div class="select is-fullwidth">
                                                <select v-model="voucher.payment_method" id="method">
                                                    <option selected disabled value="">Select Payment Method *</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="cheque">Cheque</option>
                                                    <option value="online">Online Transfer</option>
                                                </select>
                                            </div>
                                            <span class="has-text-danger" v-if="errors.payment_method">
                                                {{ errors.payment_method[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Reference</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <input class="input" placeholder="Payment Reference" type="text" v-model="voucher.payment_reference">
                                            </p>
                                            <span class="has-text-danger" v-if="errors.payment_reference">
                                                {{ errors.payment_reference[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Notes</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <textarea class="textarea" v-model="voucher.notes"></textarea>
                                            </p>
                                            <span class="has-text-danger" v-if="errors.notes">
                                                {{ errors.notes[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column"></div>
                        </div>

                        <div class="columns is-centered">
                            <div class="column is-2">
                                <button
                                    class="button"
                                    @click="handleSubmitClick"
                                    :class="{ 'is-loading': isVoucherCompleting, 'is-primary' : this.voucher.id != null, 'is-success' : this.voucher.id == null }">
                                        <span class="icon">
                                            <i class="mdi mdi-check-circle"></i>
                                        </span>
                                        <span v-if="this.voucher.id == null">Submit</span>
                                        <span v-else>Update</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate"
import vSelect from 'vue-select'
import axios from 'axios'
import dayjs from 'dayjs'

export default {
    name: "Voucher",
    components: {ProgressBarIndeterminate, vSelect},
    data() {
        return {
            isLoading: false,
            isVoucherCompleting: false,
            voucher: {
                id: null,
                vendor_id: null,
                bill_number: '',
                bill_date: dayjs().toDate(),
                total: null,
                payment_method: "",
                payment_reference: null,
                notes: ''
            },
            vendors: [],
            errors: []
        }
    },
    computed: {
        dayjs() {
            return dayjs
        }
    },
    methods: {
        loadVoucher() {
            this.isLoading = true
            axios.get('/api/v1/vouchers/' + this.voucher.id).then(response => {
                this.voucher = response.data.data
                this.voucher.bill_date = dayjs(this.voucher.bill_date).toDate()
                this.voucher.vendor_id = this.voucher.vendor.id
                this.getVendor()
                this.isLoading = false
            })
        },
        searchVendor(search, loading) {
            if (search.length < 2) {
                return
            }
            loading(true)
            axios.get('/api/v1/vendors/search?name=' + search).then(response => {
                loading(false)
                this.vendors = response.data.data
            }).catch(error => {
                loading(false)
                this.handleError(error)
            })
        },
        onVendorSelected(val) {
            this.voucher.vendor_id = val
        },
        getVendor() {
            axios.get('/api/v1/vendors/' + this.voucher.vendor_id).then(response => {
                this.vendors.push(response.data.data)
            }).catch(error => {
                this.handleError(error)
            })
        },
        addVoucher() {
            this.voucher.bill_date = dayjs(this.voucher.bill_date).format('YYYY-MM-DD')
            this.isVoucherCompleting = true
            axios.post('/api/v1/vouchers', this.voucher).then(response => {
                this.isVoucherCompleting = false
                this.showToast("Voucher added successfully")
                this.$router.back()
            }).catch(error => {
                this.isVoucherCompleting = false
                this.handleError(error)
            })
        },
        updateVoucher() {
            this.voucher.bill_date = dayjs(this.voucher.bill_date).format('YYYY-MM-DD')
            this.isVoucherCompleting = true
            axios.patch('/api/v1/vouchers/' + this.voucher.id, this.voucher).then(response => {
                this.isVoucherCompleting = false
                this.showToast("Voucher updated successfully")
                this.$router.back()
            }).catch(error => {
                this.isVoucherCompleting = false
                this.handleError(error)
            })
        },
        handleSubmitClick() {
            if(this.voucher.id == null) {
                this.addVoucher()
            } else {
                this.updateVoucher()
            }
        },
        dateFormat(d) {
            return dayjs(d).format('DD-MM-YYYY')
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
        this.voucher.id = this.$route.params.id
        if(this.voucher.id != null) {
            this.loadVoucher()
        }

        if(this.$route.params.vendor_id != null) {
            this.voucher.vendor_id = parseInt(this.$route.params.vendor_id)
            this.getVendor()
        }
    }
}
</script>

<style scoped>

</style>
