<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <div class="box">
            <div class="columns is-vcentered">
                <div class="column is-3">
                    <vSelect
                        class="has-background-white-ter"
                        @search="searchCustomer"
                        @input="onCustomerSelected"
                        placeholder="Search Customer"
                        :options="customers"
                        :reduce="customer => customer.id"
                        label="name"
                        v-model="credit.customer_id"
                    >
                    </vSelect>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <b-datepicker
                            :mobile-native="false"
                            :date-formatter=dateFormat
                            placeholder="Credit date"
                            icon="calendar"
                            v-model="credit.bill_date"
                        />
                    </div>
                </div>
                <div class="column is-4 has-text-right">
                    <h5>
                        Subtotal: {{ currencySymbol }} {{ creditSubTotal.toFixed(2) }}
                    </h5>
                    <h5>
                        Tax: {{ currencySymbol }} {{ creditTax.toFixed(2) }}
                    </h5>
                    <h4 class="title is-5">Total: {{ currencySymbol }} {{ creditTotal.toFixed(2) }}</h4>
                </div>
                <div class="column is-2 has-text-right-desktop has-text-centered-mobile">
                    <button class="button is-link" @click="addCreditItem">
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
            <credit-item
                v-for="creditItem in credit.items"
                :key="creditItem.id"
                v-on:on-delete="removeCreditItem"
                :credit-item="creditItem"/>
        </div>
        <div class="submit">
            <button
                class="button"
                @click="handleSubmitClick"
                :class="{ 'is-loading': isCreditCompleting, 'is-primary' : this.credit.id != null, 'is-success' : this.credit.id == null }">
                    <span class="icon">
                        <i class="mdi mdi-check-circle"></i>
                    </span>
                <span v-if="this.credit.id == null">Submit</span>
                <span v-else>Update</span>
            </button>
        </div>
    </section>
</template>

<script>
import vSelect from 'vue-select'
import axios from 'axios'
import dayjs from 'dayjs'
import CreditItem from '@/views/credits/CreditItem'
import ProgressBarIndeterminate from "@/components/ProgressBarIndeterminate";

export default {
    name: 'NewCredit',
    components: {ProgressBarIndeterminate, CreditItem, vSelect },
    data() {
        return {
            isLoading: false,
            isCreditCompleting: false,
            credit: {
                id: null,
                customer_id: null,
                bill_date: null,
                discount: 0,
                items: []
            },
            creditEdit: null,
            customers: [],
            itemId: 0,
            creditDate: dayjs().toDate(),
            currencySymbol: this.$store.state.regionData.currencySymbol
        }
    },
    computed: {
        dayjs() {
            return dayjs
        },
        creditSubTotal() {
            let subTotal = 0
            for (let i = 0; i < this.credit.items.length; ++i) {
                let creditItem = this.credit.items[i]
                let taxExcludedPrice = (creditItem.price / (( creditItem.tax_percent / 100 ) + 1 ));
                subTotal = subTotal + taxExcludedPrice * creditItem.quantity
            }

            return  Number(subTotal)
        },
        creditTax() {
            let tax = 0
            for (let i = 0; i < this.credit.items.length; ++i) {
                let creditItem = this.credit.items[i]
                let taxExcludedPrice = (creditItem.price / (( creditItem.tax_percent / 100 ) + 1 ));
                tax = tax + (taxExcludedPrice * creditItem.quantity * creditItem.tax_percent) / 100
            }
            return  Number(tax)
        },
        creditTotal() {
            return  Number(this.creditSubTotal) + Number(this.creditTax)
        }
    },
    methods: {
        dateFormat(d) {
            return dayjs(d).format('DD-MM-YYYY')
        },
        getCredit() {
            this.isLoading = true
            axios.get('/api/v1/credits/' + this.credit.id).then(response => {
                this.creditEdit = response.data.data
                this.credit.customer_id = this.creditEdit.customer.id
                this.credit.bill_date = dayjs(this.creditEdit.bill_date).toDate()
                this.getCustomer()
                this.addCreditItems()
                this.isLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        getCustomer() {
            axios.get('/api/v1/customers/' + this.credit.customer_id).then(response => {
                this.customers.push(response.data.data)
            }).catch(error => {
                this.handleError(error)
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
            this.credit.customer_id = val
        },
        onCustomerCleared() {
            this.credit.items = []
            this.customers = []
        },
        addCreditItem() {
            if (this.credit.customer_id == null) {
                this.showToast('Please select a customer first', 'is-warning')
                return
            }
            let creditItem = {
                id: this.itemId++,
                price: '0',
                quantity: '0',
                tax_percent: 0,
                tax: 0,
                discount: 0,
                product_stock_id: '',
                sale_id: null,
                stockAvailable: 0,
                creditId: null,
                errors: []
            }
            this.credit.items.push(creditItem)
        },
        addCreditItems() {
            let creditItems = []
            for(let i=0; i < this.creditEdit.items.length; ++i) {
                let item = this.creditEdit.items[i]
                let creditItem = {
                    id: this.itemId++,
                    price: item.price,
                    quantity: item.quantity,
                    discount: item.discount,
                    tax_percent: item.tax_percent,
                    tax: item.tax,
                    product_stock_id: item.product_stock.id,
                    sale_id: item.sale_id,
                    product_stock: item.product_stock,
                    stockAvailable: item.product_stock.stock,
                    creditId: item.credit_id,
                    errors: []
                }

                creditItems.push(creditItem)
            }

            this.credit.items = creditItems
        },
        removeCreditItem(id) {
            for(let i=0; i < this.credit.items.length; ++i) {
                if(this.credit.items[i].id === id) {
                    this.credit.items.splice(i, 1)
                    break
                }
            }
        },
        handleSubmitClick() {
            if(this.credit.id == null) {
                this.addCredit()
            } else {
                this.updateCredit()
            }
        },
        addCredit() {
            if (this.credit.customer_id == null) {
                this.showToast('Please select a customer first', 'is-danger')
                return
            }
            if (this.credit.bill_date == null) {
                this.showToast('Please chose credit date', 'is-warning')
                return
            }
            if (this.credit.items.length < 1) {
                this.showToast(
                    'Please add an item before completing credit!',
                    'is-warning'
                )
                return
            }
            for (let i = 0; i < this.credit.items.length; ++i) {
                let creditItem = this.credit.items[i]
                if (
                    creditItem.products_stock_id === '' ||
                    creditItem.quantity === 0
                ) {
                    this.showToast(
                        'Please fill all details for each items',
                        'is-warning'
                    )
                    return
                }
            }

            this.credit.bill_date = dayjs(this.credit.bill_date).format('YYYY-MM-DD')
            this.isCreditCompleting = true
            axios.post('/api/v1/credits', this.credit).then(response => {
                this.isCreditCompleting = false
                this.showToast('Credit added successfully!')
                this.$router.back()
            }).catch(error => {
                this.isCreditCompleting = false
                this.handleError(error)
            })
        },
        updateCredit() {
            this.credit.bill_date = dayjs(this.credit.bill_date).format('YYYY-MM-DD')
            this.isCreditCompleting = true
            axios.patch('/api/v1/credits/' + this.credit.id, this.credit).then(response => {
                this.isCreditCompleting = false
                this.showToast('Credit Updated successfully!')
                this.$router.back()
            }).catch(error => {
                this.isCreditCompleting = false
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
        this.credit.id = this.$route.params.id
        if(this.credit.id != null) {
            this.getCredit()
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
