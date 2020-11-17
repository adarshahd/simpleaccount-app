<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <div class="box">
            <div class="columns is-vcentered">
                <div class="column is-3">
                    <vSelect
                        class="has-background-white-ter"
                        @search="searchVendor"
                        @input="onVendorSelected"
                        placeholder="Search Vendor"
                        :options="vendors"
                        :reduce="vendor => vendor.id"
                        label="name"
                        v-model="debit.vendor_id"
                    >
                    </vSelect>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <b-datepicker
                            :mobile-native="false"
                            :date-formatter=dateFormat
                            placeholder="Debit date"
                            icon="calendar"
                            v-model="debit.bill_date"
                        />
                    </div>
                </div>
                <div class="column is-4 has-text-right">
                    <h5>
                        Subtotal: ₹ {{ debitSubTotal.toFixed(2) }}
                    </h5>
                    <h5>
                        Tax: ₹ {{ debitTax.toFixed(2) }}
                    </h5>
                    <h4 class="title is-5">Total: ₹ {{ debitTotal.toFixed(2) }}</h4>
                </div>
                <div class="column is-2 has-text-right-desktop has-text-centered-mobile">
                    <button class="button is-link" @click="addDebitItem">
                        <span class="icon">
                            <i class="mdi mdi-plus-circle"></i>
                        </span>
                            <span class="label has-text-white">
                            Add Item
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div class="scroll">
            <debit-item
                v-for="debitItem in debit.items"
                :key="debitItem.id"
                v-on:on-delete="removeDebitItem"
                :debit-item="debitItem"/>
        </div>
        <div class="submit">
            <button
                class="button"
                @click="handleSubmitClick"
                :class="{ 'is-loading': isDebitCompleting, 'is-primary' : this.debit.id != null, 'is-success' : this.debit.id == null }">
                    <span class="icon">
                        <i class="mdi mdi-check-circle"></i>
                    </span>
                <span v-if="this.debit.id == null">Submit</span>
                <span v-else>Update</span>
            </button>
        </div>
    </section>
</template>

<script>
import vSelect from 'vue-select'
import axios from 'axios'
import dayjs from 'dayjs'
import DebitItem from '../../components/DebitItem'
import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";

export default {
    name: 'NewDebit',
    components: {ProgressBarIndeterminate, DebitItem, vSelect },
    data() {
        return {
            isLoading: false,
            isDebitCompleting: false,
            debit: {
                id: null,
                vendor_id: null,
                bill_date: null,
                discount: 0,
                items: []
            },
            debitEdit: null,
            vendors: [],
            itemId: 0,
            debitDate: dayjs().toDate()
        }
    },
    computed: {
        dayjs() {
            return dayjs
        },
        debitSubTotal() {
            let subTotal = 0
            for (let i = 0; i < this.debit.items.length; ++i) {
                let debitItem = this.debit.items[i]
                let taxExcludedPrice = (debitItem.price / (( debitItem.tax_percent / 100 ) + 1 ));
                subTotal = subTotal + taxExcludedPrice * debitItem.quantity
            }

            return  Number(subTotal)
        },
        debitTax() {
            let tax = 0
            for (let i = 0; i < this.debit.items.length; ++i) {
                let debitItem = this.debit.items[i]
                let taxExcludedPrice = (debitItem.price / (( debitItem.tax_percent / 100 ) + 1 ));
                tax = tax + (taxExcludedPrice * debitItem.quantity * debitItem.tax_percent) / 100
            }
            return  Number(tax)
        },
        debitTotal() {
            return  Number(this.debitSubTotal) + Number(this.debitTax)
        }
    },
    methods: {
        dateFormat(d) {
            return dayjs(d).format('DD-MM-YYYY')
        },
        getDebit() {
            this.isLoading = true
            axios.get('/api/v1/debits/' + this.debit.id).then(response => {
                this.debitEdit = response.data.data
                this.debit.vendor_id = this.debitEdit.vendor.id
                this.debit.bill_date = dayjs(this.debitEdit.bill_date).toDate()
                this.getVendor()
                this.addDebitItems()
                this.isLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        getVendor() {
            axios.get('/api/v1/vendors/' + this.debit.vendor_id).then(response => {
                this.vendors.push(response.data.data)
            }).catch(error => {
                this.handleError(error)
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
            this.debit.vendor_id = val
        },
        onVendorCleared() {
            this.debit.items = []
            this.vendors = []
        },
        addDebitItem() {
            if (this.debit.vendor_id == null) {
                this.showToast('Please select a vendor first', 'is-warning')
                return
            }
            let debitItem = {
                id: this.itemId++,
                price: '0',
                quantity: '0',
                tax_percent: 0,
                tax: 0,
                discount: 0,
                product_stock_id: '',
                stockAvailable: 0,
                debitId: null,
                errors: []
            }
            this.debit.items.push(debitItem)
        },
        addDebitItems() {
            let debitItems = []
            for(let i=0; i < this.debitEdit.items.length; ++i) {
                let item = this.debitEdit.items[i]
                let debitItem = {
                    id: this.itemId++,
                    price: item.price,
                    quantity: item.quantity,
                    discount: item.discount,
                    tax_percent: item.tax_percent,
                    tax: item.tax,
                    product_stock_id: item.product_stock.id,
                    product_stock: item.product_stock,
                    stockAvailable: item.product_stock.stock,
                    debitId: item.debit_id,
                    errors: []
                }

                debitItems.push(debitItem)
            }

            this.debit.items = debitItems
        },
        removeDebitItem(id) {
            for(let i=0; i < this.debit.items.length; ++i) {
                if(this.debit.items[i].id === id) {
                    this.debit.items.splice(i, 1)
                    break
                }
            }
        },
        handleSubmitClick() {
            if(this.debit.id == null) {
                this.addDebit()
            } else {
                this.updateDebit()
            }
        },
        addDebit() {
            if (this.debit.vendor_id == null) {
                this.showToast('Please select a vendor first', 'is-danger')
                return
            }
            if (this.debit.bill_date == null) {
                this.showToast('Please chose debit date', 'is-warning')
                return
            }
            if (this.debit.items.length < 1) {
                this.showToast(
                    'Please add an item before completing debit!',
                    'is-warning'
                )
                return
            }
            for (let i = 0; i < this.debit.items.length; ++i) {
                let debitItem = this.debit.items[i]
                if (
                    debitItem.products_stock_id === '' ||
                    debitItem.quantity === 0
                ) {
                    this.showToast(
                        'Please fill all details for each items',
                        'is-warning'
                    )
                    return
                }
            }

            this.debit.bill_date = dayjs(this.debit.bill_date).format('YYYY-MM-DD')
            this.isDebitCompleting = true
            axios.post('/api/v1/debits', this.debit).then(response => {
                this.isDebitCompleting = false
                this.showToast('Debit added successfully!')
                this.$router.back()
            }).catch(error => {
                this.isDebitCompleting = false
                this.handleError(error)
            })
        },
        updateDebit() {
            this.debit.bill_date = dayjs(this.debit.bill_date).format('YYYY-MM-DD')
            this.isDebitCompleting = true
            axios.patch('/api/v1/debits/' + this.debit.id, this.debit).then(response => {
                this.isDebitCompleting = false
                this.showToast('Debit Updated successfully!')
                this.$router.back()
            }).catch(error => {
                this.isDebitCompleting = false
                this.handleError(error)
            })
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
        this.debit.id = this.$route.params.id
        if(this.debit.id != null) {
            this.getDebit()
        }
    }
}
</script>

<style scoped>
.scroll {
    overflow-y: auto;
    overflow-x: hidden;
    height: 65vh;
    margin-bottom: 60px;
}
.submit {
    position: fixed;
    right: 10px;
    bottom: 10px;
    padding: 8px;
}

</style>
