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
                                        <label class="label">Customer</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <v-select class="has-background-white-ter"
                                                          @search="searchCustomer"
                                                          @input="onCustomerSelected"
                                                          placeholder="Search Customer"
                                                          :options="customers"
                                                          :reduce="customer => customer.id"
                                                          label="name"
                                                          v-model="receipt.customer_id">
                                                </v-select>
                                            </p>
                                            <span class="has-text-danger" v-if="errors.customer_id">
                                                {{ errors.customer_id[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Receipt date</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <b-datepicker
                                                    :mobile-native="false"
                                                    :date-formatter=dateFormat
                                                    placeholder="Receipt date"
                                                    icon="calendar"
                                                    v-model="receipt.bill_date"
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
                                                <input class="input" placeholder="Amount *" type="number" v-model="receipt.total">
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
                                            <p class="control">
                                                <input class="input" placeholder="Payment Method *" type="text" v-model="receipt.payment_method">
                                            </p>
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
                                                <input class="input" placeholder="Payment Reference" type="text" v-model="receipt.payment_reference">
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
                                                <textarea class="textarea" v-model="receipt.notes"></textarea>
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
                                    :class="{ 'is-loading': isReceiptCompleting, 'is-primary' : this.receipt.id != null, 'is-success' : this.receipt.id == null }">
                                        <span class="icon">
                                            <i class="mdi mdi-check-circle"></i>
                                        </span>
                                        <span v-if="this.receipt.id == null">Submit</span>
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
    name: "Receipt",
    components: {ProgressBarIndeterminate, vSelect},
    data() {
        return {
            isLoading: false,
            isReceiptCompleting: false,
            receipt: {
                id: null,
                customer_id: null,
                bill_number: '',
                bill_date: dayjs().toDate(),
                total: null,
                payment_method: null,
                payment_reference: null,
                notes: ''
            },
            customers: [],
            errors: []
        }
    },
    computed: {
        dayjs() {
            return dayjs
        }
    },
    methods: {
        loadReceipt() {
            this.isLoading = true
            axios.get('/api/v1/receipts/' + this.receipt.id).then(response => {
                this.receipt = response.data.data
                this.receipt.bill_date = dayjs(this.receipt.bill_date).toDate()
                this.receipt.customer_id = this.receipt.customer.id
                this.getCustomer()
                this.isLoading = false
            })
        },
        searchCustomer(search, loading) {
            if (search.length < 2) {
                return
            }
            loading(true)
            axios.get('/api/v1/customers/search?name=' + search).then(response => {
                loading(false)
                this.customers = response.data.data
            }).catch(error => {
                loading(false)
                this.handleError(error)
            })
        },
        onCustomerSelected(val) {
            this.receipt.customer_id = val
        },
        getCustomer() {
            axios.get('/api/v1/customers/' + this.receipt.customer_id).then(response => {
                this.customers.push(response.data.data)
            }).catch(error => {
                this.handleError(error)
            })
        },
        addReceipt() {
            this.receipt.bill_date = dayjs(this.receipt.bill_date).format('YYYY-MM-DD')
            this.isReceiptCompleting = true
            axios.post('/api/v1/receipts', this.receipt).then(response => {
                this.isReceiptCompleting = false
                this.showToast("Receipt added successfully")
                this.$router.back()
            }).catch(error => {
                this.isReceiptCompleting = false
                this.handleError(error)
            })
        },
        updateReceipt() {
            this.receipt.bill_date = dayjs(this.receipt.bill_date).format('YYYY-MM-DD')
            this.isReceiptCompleting = true
            axios.patch('/api/v1/receipts/' + this.receipt.id, this.receipt).then(response => {
                this.isReceiptCompleting = false
                this.showToast("Receipt updated successfully")
                this.$router.back()
            }).catch(error => {
                this.isReceiptCompleting = false
                this.handleError(error)
            })
        },
        handleSubmitClick() {
            if(this.receipt.id == null) {
                this.addReceipt()
            } else {
                this.updateReceipt()
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
        this.receipt.id = this.$route.params.id
        if(this.receipt.id != null) {
            this.loadReceipt()
        }

        if(this.$route.params.customer_id != null) {
            this.receipt.customer_id = parseInt(this.$route.params.customer_id)
            this.getCustomer()
        }
    }
}
</script>

<style scoped>

</style>
