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
                        v-model="purchase.vendor_id"
                    >
                    </vSelect>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <b-datepicker
                            :mobile-native="false"
                            :date-formatter=dateFormat
                            placeholder="Bill date"
                            icon="calendar"
                            v-model="purchase.bill_date"
                        />
                    </div>
                </div>
                <div class="column is-2">
                    <div class="field">
                        <div class="field-body">
                            <div class="field">
                                <p class="control">
                                    <input
                                        class="input"
                                        placeholder="Bill Number"
                                        type="text"
                                        v-model="purchase.bill_number">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-3 has-text-right">
                    <h5>
                        Subtotal: ₹ {{ purchaseSubTotal.toFixed(2) }}
                    </h5>
                    <h5>
                        Tax: ₹ {{ purchaseTax.toFixed(2) }}
                    </h5>
                    <h4 class="title is-5">Total: ₹ {{ purchaseTotal.toFixed(2) }}</h4>
                </div>
                <div class="column is-2 has-text-right-desktop has-text-centered-mobile">
                    <button class="button is-link" @click="addPurchaseItem">
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
            <purchase-item
                v-for="purchaseItem in purchase.purchase_items"
                :key="purchaseItem.id"
                v-on:on-delete="removePurchaseItem"
                :purchase-item="purchaseItem"/>
        </div>
        <div class="submit">
            <button
                class="button"
                @click="handleSubmitClick"
                :class="{ 'is-loading': isPurchaseCompleting, 'is-primary' : this.purchase.id != null, 'is-success' : this.purchase.id == null }">
                    <span class="icon">
                        <i class="mdi mdi-check-circle"></i>
                    </span>
                <span v-if="this.purchase.id == null">Submit</span>
                <span v-else>Update</span>
            </button>
        </div>
    </section>
</template>

<script>
import vSelect from 'vue-select'
import axios from 'axios'
import dayjs from 'dayjs'
import PurchaseItem from '@/views/purchases/PurchaseItem'
import ProgressBarIndeterminate from "@/components/ProgressBarIndeterminate";

export default {
    name: 'NewPurchase',
    components: {ProgressBarIndeterminate, PurchaseItem, vSelect },
    data() {
        return {
            isLoading: false,
            isPurchaseCompleting: false,
            purchase: {
                id: null,
                vendor_id: null,
                bill_date: null,
                bill_number: null,
                discount: 0,
                purchase_items: []
            },
            purchaseEdit: null,
            vendors: [],
            itemId: 0,
            purchaseDate: dayjs().toDate()
        }
    },
    computed: {
        dayjs() {
            return dayjs
        },
        purchaseSubTotal() {
            let subTotal = 0
            for (let i = 0; i < this.purchase.purchase_items.length; ++i) {
                let purchaseItem = this.purchase.purchase_items[i]
                let taxExcludedPrice = (purchaseItem.price / (( purchaseItem.tax_percent / 100 ) + 1 ));
                subTotal = subTotal + taxExcludedPrice * purchaseItem.quantity
            }

            return  Number(subTotal)
        },
        purchaseTax() {
            let tax = 0
            for (let i = 0; i < this.purchase.purchase_items.length; ++i) {
                let purchaseItem = this.purchase.purchase_items[i]
                let taxExcludedPrice = (purchaseItem.price / (( purchaseItem.tax_percent / 100 ) + 1 ));
                tax = tax + (taxExcludedPrice * purchaseItem.quantity * ( purchaseItem.tax_percent / 100 ))
            }
            return  Number(tax)
        },
        purchaseTotal() {
            return  Number(this.purchaseSubTotal) + Number(this.purchaseTax)
        }
    },
    methods: {
        dateFormat(d) {
            return dayjs(d).format('DD-MM-YYYY')
        },
        getPurchase() {
            this.isLoading = true
            axios.get('/api/v1/purchases/' + this.purchase.id).then(response => {
                this.purchaseEdit = response.data.data
                this.purchase.vendor_id = this.purchaseEdit.vendor.id
                this.purchase.bill_date = dayjs(this.purchaseEdit.bill_date).toDate()
                this.purchase.bill_number = this.purchaseEdit.bill_number
                this.getVendor()
                this.addPurchaseItems()
                this.isLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        getVendor() {
            axios.get('/api/v1/vendors/' + this.purchase.vendor_id).then(response => {
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
            this.purchase.vendor_id = val
        },
        onVendorCleared() {
            this.purchase.purchase_items = []
            this.vendors = []
        },
        addPurchaseItem() {
            if (this.purchase.vendor_id == null) {
                this.showToast('Please select a vendor first', 'is-warning')
                return
            }
            if (this.purchase.bill_date == null) {
                this.showToast('Please chose bill date first', 'is-warning')
                return
            }
            if (this.purchase.bill_number == null) {
                this.showToast('Please enter bill number first', 'is-warning')
                return
            }
            let purchaseItem = {
                id: this.itemId++,
                mrp: '0',
                price: '0',
                quantity: '0',
                tax_percent: 0,
                tax: 0,
                discount: 0,
                batch: '',
                expiry: null,
                expiryISO: null,
                hsn: '',
                errors: []
            }
            this.purchase.purchase_items.push(purchaseItem)
        },
        addPurchaseItems() {
            let purchaseItems = []
            for(let i=0; i < this.purchaseEdit.items.length; ++i) {
                let item = this.purchaseEdit.items[i]
                let purchaseItem = {
                    id: this.itemId++,
                    mrp: item.product_stock.mrp,
                    price: item.product_stock.price,
                    quantity: item.quantity,
                    tax_percent: item.product_stock.product.tax.tax,
                    tax: item.product_stock.tax,
                    discount: item.discount,
                    batch: item.product_stock.batch,
                    expiry: item.product_stock.expiry,
                    expiryISO: dayjs(item.product_stock.expiry).toDate(),
                    hsn: item.product_stock.hsn,
                    purchaseId: item.purchase_id,
                    product_stock: item.product_stock,
                    errors: []
                }

                purchaseItems.push(purchaseItem)
            }

            this.purchase.purchase_items = purchaseItems
        },
        removePurchaseItem(id) {
            for(let i=0; i < this.purchase.purchase_items.length; ++i) {
                if(this.purchase.purchase_items[i].id === id) {
                    this.purchase.purchase_items.splice(i, 1)
                    break
                }
            }
        },
        handleSubmitClick() {
            if(this.purchase.id == null) {
                this.addPurchase()
            } else {
                this.updatePurchase()
            }
        },
        addPurchase() {
            if (this.purchase.vendor_id == null) {
                this.showToast('Please select a vendor first', 'is-danger')
                return
            }
            if (this.purchase.bill_date == null) {
                this.showToast('Please chose purchase date', 'is-warning')
                return
            }
            if (this.purchase.bill_number == null) {
                this.showToast('Please chose purchase date', 'is-warning')
                return
            }
            if (this.purchase.purchase_items.length < 1) {
                this.showToast(
                    'Please add an item before completing purchase!',
                    'is-warning'
                )
                return
            }

            this.purchase.bill_date = dayjs(this.purchase.bill_date).format('YYYY-MM-DD')
            for(let i=0; i < this.purchase.purchase_items.length; ++i) {
                this.purchase.purchase_items[i].expiry = dayjs(this.purchase.purchase_items[i].expiryISO).format('YYYY-MM-DD')
            }

            this.isPurchaseCompleting = true
            axios.post('/api/v1/purchases', this.purchase).then(response => {
                this.isPurchaseCompleting = false
                this.showToast('Purchase added successfully!')
                this.$router.back()
            }).catch(error => {
                this.isPurchaseCompleting = false
                this.handleError(error)
            })
        },
        updatePurchase() {
            this.purchase.bill_date = dayjs(this.purchase.bill_date).format('YYYY-MM-DD')
            for(let i=0; i < this.purchase.purchase_items.length; ++i) {
                this.purchase.purchase_items[i].expiry = dayjs(this.purchase.purchase_items[i].expiryISO).format('YYYY-MM-DD')
            }

            this.isPurchaseCompleting = true
            axios.patch('/api/v1/purchases/' + this.purchase.id, this.purchase).then(response => {
                this.isPurchaseCompleting = false
                this.showToast('Purchase Updated successfully!')
                this.$router.back()
            }).catch(error => {
                this.isPurchaseCompleting = false
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
        this.purchase.id = this.$route.params.id
        if(this.purchase.id != null) {
            this.getPurchase()
        }

        if(this.$route.params.vendor_id != null) {
            this.purchase.vendor_id = parseInt(this.$route.params.vendor_id)
            this.getVendor()
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
