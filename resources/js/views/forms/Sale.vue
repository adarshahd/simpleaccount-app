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
                        v-model="sale.customer_id"
                    >
                    </vSelect>
                </div>
                <div class="column is-3">
                    <div class="field">
                        <b-datepicker
                            :mobile-native="false"
                            :date-formatter=dateFormat
                            placeholder="Sale date"
                            icon="calendar"
                            v-model="sale.bill_date"
                        />
                    </div>
                </div>
                <div class="column is-4 has-text-right">
                    <h5>
                        Subtotal: ₹ {{ saleSubTotal.toFixed(2) }}
                    </h5>
                    <h5>
                        Tax: ₹ {{ saleTax.toFixed(2) }}
                    </h5>
                    <h4 class="title is-5">Total: ₹ {{ saleTotal.toFixed(2) }}</h4>
                </div>
                <div class="column is-2 has-text-right-desktop has-text-centered-mobile">
                    <button class="button is-link" @click="addSaleItem">
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
            <sale-item
                v-for="saleItem in sale.sale_items"
                :key="saleItem.id"
                v-on:on-delete="removeSaleItem"
                :sale-item="saleItem"/>
        </div>
        <div class="submit">
            <button
                class="button"
                @click="handleSubmitClick"
                :class="{ 'is-loading': isSaleCompleting, 'is-primary' : this.sale.id != null, 'is-success' : this.sale.id == null }">
                    <span class="icon">
                        <i class="mdi mdi-check-circle"></i>
                    </span>
                <span v-if="this.sale.id == null">Submit</span>
                <span v-else>Update</span>
            </button>
        </div>
    </section>
</template>

<script>
import vSelect from 'vue-select'
import axios from 'axios'
import dayjs from 'dayjs'
import SaleItem from '../../components/SaleItem'
import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";

export default {
    name: 'NewSale',
    components: {ProgressBarIndeterminate, SaleItem, vSelect },
    data() {
        return {
            isLoading: false,
            isSaleCompleting: false,
            sale: {
                id: null,
                customer_id: null,
                bill_date: null,
                discount: 0,
                sale_items: []
            },
            saleEdit: null,
            customers: [],
            itemId: 0,
            saleDate: dayjs().toDate()
        }
    },
    computed: {
        dayjs() {
            return dayjs
        },
        saleSubTotal() {
            let subTotal = 0
            for (let i = 0; i < this.sale.sale_items.length; ++i) {
                let saleItem = this.sale.sale_items[i]
                subTotal = subTotal + saleItem.price * saleItem.quantity
            }

            return  Number(subTotal)
        },
        saleTax() {
            let tax = 0
            for (let i = 0; i < this.sale.sale_items.length; ++i) {
                let saleItem = this.sale.sale_items[i]
                tax = tax + (saleItem.price * saleItem.quantity * saleItem.tax) / 100
            }
            return  Number(tax)
        },
        saleTotal() {
            return  Number(this.saleSubTotal) + Number(this.saleTax)
        }
    },
    methods: {
        dateFormat(d) {
            return dayjs(d).format('DD-MM-YYYY')
        },
        getSale() {
            this.isLoading = true
            axios.get('/api/v1/sales/' + this.sale.id).then(response => {
                this.saleEdit = response.data.data
                this.sale.customer_id = this.saleEdit.customer.id
                this.sale.bill_date = dayjs(this.saleEdit.bill_date).toDate()
                this.getCustomer()
                this.addSaleItems()
                this.isLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        getCustomer() {
            axios.get('/api/v1/customers/' + this.sale.customer_id).then(response => {
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
            this.sale.customer_id = val
        },
        onCustomerCleared() {
            this.sale.sale_items = []
            this.customers = []
        },
        addSaleItem() {
            if (this.sale.customer_id == null) {
                this.showToast('Please select a customer first', 'is-warning')
                return
            }
            let saleItem = {
                id: this.itemId++,
                price: '0',
                quantity: '0',
                tax: 0,
                discount: 0,
                product_stock_id: '',
                stockAvailable: 0,
                saleId: null,
                errors: []
            }
            this.sale.sale_items.push(saleItem)
        },
        addSaleItems() {
            let saleItems = []
            for(let i=0; i < this.saleEdit.items.length; ++i) {
                let item = this.saleEdit.items[i]
                let saleItem = {
                    id: this.itemId++,
                    price: item.product_stock.price,
                    quantity: item.quantity,
                    discount: item.discount,
                    tax: item.product_stock.tax,
                    product_stock_id: item.product_stock.id,
                    product_stock: item.product_stock,
                    stockAvailable: item.product_stock.stock,
                    saleId: item.sale_id,
                    errors: []
                }

                saleItems.push(saleItem)
            }

            this.sale.sale_items = saleItems
        },
        removeSaleItem(id) {
            for(let i=0; i < this.sale.sale_items.length; ++i) {
                if(this.sale.sale_items[i].id === id) {
                    this.sale.sale_items.splice(i, 1)
                    break
                }
            }
        },
        handleSubmitClick() {
            if(this.sale.id == null) {
                this.addSale()
            } else {
                this.updateSale()
            }
        },
        addSale() {
            if (this.sale.customer_id == null) {
                this.showToast('Please select a customer first', 'is-danger')
                return
            }
            if (this.sale.bill_date == null) {
                this.showToast('Please chose sale date', 'is-warning')
                return
            }
            if (this.sale.sale_items.length < 1) {
                this.showToast(
                    'Please add an item before completing sale!',
                    'is-warning'
                )
                return
            }
            for (let i = 0; i < this.sale.sale_items.length; ++i) {
                let saleItem = this.sale.sale_items[i]
                if (
                    saleItem.products_stock_id === '' ||
                    saleItem.quantity === 0
                ) {
                    this.showToast(
                        'Please fill all details for each items',
                        'is-warning'
                    )
                    return
                }
                if (saleItem.quantity > saleItem.stockAvailable) {
                    this.showToast(
                        'Not enough stock available for ' + saleItem.name,
                        'is-warning'
                    )
                    return
                }
            }

            this.isSaleCompleting = true
            axios.post('/api/v1/sales', this.sale).then(response => {
                this.isSaleCompleting = false
                this.showToast('Sale added successfully!')
                this.$router.back()
            }).catch(error => {
                this.isSaleCompleting = false
                this.handleError(error)
            })
        },
        updateSale() {
            this.isSaleCompleting = true
            axios.patch('/api/v1/sales/' + this.sale.id, this.sale).then(response => {
                this.isSaleCompleting = false
                this.showToast('Sale Updated successfully!')
                this.$router.back()
            }).catch(error => {
                this.isSaleCompleting = false
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
        this.sale.id = this.$route.params.id
        if(this.sale.id != null) {
            this.getSale()
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
