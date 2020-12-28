<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <sale-item-modal ref="saleItemModal"
                         v-on:sale-item-added="onSaleItemAdded"
                         v-on:sale-item-edited="onSaleItemEdited">
        </sale-item-modal>
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
                            v-model="saleDate"
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
            <b-table
                narrowed
                striped
                draggable
                @dragstart="dragstart"
                @drop="drop"
                @dragover="dragover"
                @dragleave="dragleave"
                :data="sale.items">
                <b-table-column field="drag" label="">
                    <span class="mdi mdi-drag-vertical"></span>
                </b-table-column>
                <b-table-column field="product" label="Product" v-slot="props">
                    {{ props.row.product_stock.product.name }}
                </b-table-column>
                <b-table-column field="quantity" label="Quantity" v-slot="props">
                    {{ props.row.quantity }}
                </b-table-column>
                <b-table-column field="batch" label="Batch" v-slot="props">
                    {{ props.row.product_stock.batch }}
                </b-table-column>
                <b-table-column field="expiry" label="Expiry" v-slot="props">
                    {{ dayjs(props.row.product_stock.expiry).format("MMM, YYYY") }}
                </b-table-column>
                <b-table-column field="price" label="Price" v-slot="props">
                    ₹{{ parseFloat(props.row.price).toFixed(2) }}
                </b-table-column>
                <b-table-column field="tax" label="Tax" v-slot="props">
                    ₹{{ (props.row.price * props.row.quantity * props.row.tax_percent / 100).toFixed(2) }}
                </b-table-column>
                <b-table-column field="total" label="Item Total" v-slot="props">
                    ₹{{ ((props.row.price * props.row.quantity) + (props.row.price * props.row.quantity * props.row.tax_percent / 100)).toFixed(2) }}
                </b-table-column>

                <b-table-column field="actions" label="Actions" v-slot="props">
                    <button class="button has-icons-right is-primary is-small" @click="editSaleItem(props.row)">
                        <span class="mdi mdi-pencil"></span>
                    </button>
                    <button class="button has-icons-right is-danger is-small" @click="removeSaleItem(props.row.id)">
                        <span class="mdi mdi-delete"></span>
                    </button>
                </b-table-column>

            </b-table>
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
import ProgressBarIndeterminate from "@/components/ProgressBarIndeterminate";
import SaleItemModal from "../modals/SaleItem";

export default {
    name: 'NewSale',
    components: {SaleItemModal, ProgressBarIndeterminate, SaleItem, vSelect },
    data() {
        return {
            isLoading: false,
            isSaleCompleting: false,
            sale: {
                id: null,
                customer_id: null,
                bill_date: null,
                discount: 0,
                items: []
            },
            saleEdit: null,
            customers: [],
            itemId: 0,
            saleDate: dayjs().toDate(),
            draggingRow: null,
            droppedOnRow: null,
        }
    },
    computed: {
        dayjs() {
            return dayjs
        },
        saleSubTotal() {
            let subTotal = 0
            for (let i = 0; i < this.sale.items.length; ++i) {
                let saleItem = this.sale.items[i]
                //let taxExcludedPrice = (saleItem.price / (( saleItem.tax_percent / 100 ) + 1 ));
                subTotal = subTotal + saleItem.price * saleItem.quantity
            }

            return  Number(subTotal)
        },
        saleTax() {
            let tax = 0
            for (let i = 0; i < this.sale.items.length; ++i) {
                let saleItem = this.sale.items[i]
                //let taxExcludedPrice = (saleItem.price / (( saleItem.tax_percent / 100 ) + 1 ));
                tax = tax + (saleItem.price * saleItem.quantity * saleItem.tax_percent) / 100
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
                this.saleDate = dayjs(this.saleEdit.bill_date).toDate()
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
            this.sale.items = []
            this.customers = []
        },
        addSaleItem() {
            if (this.sale.customer_id == null) {
                this.showToast('Please select a customer first', 'is-warning')
                return
            }
            this.$refs.saleItemModal.saleItem = {
                id: this.itemId++,
                price: '0',
                quantity: '0',
                tax_percent: 0,
                tax: 0,
                discount: 0,
                product_stock_id: null,
                stockAvailable: 0,
                saleId: null,
                errors: []
            }

            this.$refs.saleItemModal.isEditing = false
            this.$refs.saleItemModal.toggleModal()
        },
        editSaleItem(item) {
            this.$refs.saleItemModal.saleItem = {
                id: item.id,
                price: item.price,
                quantity: item.quantity,
                tax_percent: item.tax_percent,
                tax: item.tax,
                discount: item.discount,
                product_stock: item.product_stock,
                product_stock_id: item.product_stock_id,
                stockAvailable: item.stockAvailable,
                saleId: item.saleId,
                errors: []
            }
            this.$refs.saleItemModal.isEditing = true
            this.$refs.saleItemModal.toggleModal()
        },
        onSaleItemAdded(item) {
            item.price = (item.price / (( item.tax_percent / 100 ) + 1 ));
            this.sale.items.push(item)
        },
        onSaleItemEdited(item) {
            item.price = (item.price / (( item.tax_percent / 100 ) + 1 ));
            for(let i=0; i < this.sale.items.length; ++i) {
                if(this.sale.items[i].id === item.id) {
                    this.sale.items.splice(i, 1, item)
                    break
                }
            }
        },
        addSaleItems() {
            let saleItems = []
            for(let i=0; i < this.saleEdit.items.length; ++i) {
                let item = this.saleEdit.items[i]
                let saleItem = {
                    id: this.itemId++,
                    price: item.price,
                    quantity: item.quantity,
                    discount: item.discount,
                    tax_percent: item.tax_percent,
                    tax: item.tax,
                    product_stock_id: item.product_stock.id,
                    product_stock: item.product_stock,
                    stockAvailable: item.product_stock.stock,
                    saleId: item.sale_id,
                    errors: []
                }

                saleItems.push(saleItem)
            }

            this.sale.items = saleItems
        },
        removeSaleItem(id) {
            for(let i=0; i < this.sale.items.length; ++i) {
                if(this.sale.items[i].id === id) {
                    this.sale.items.splice(i, 1)
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
            if (this.sale.items.length < 1) {
                this.showToast(
                    'Please add an item before completing sale!',
                    'is-warning'
                )
                return
            }
            for (let i = 0; i < this.sale.items.length; ++i) {
                let saleItem = this.sale.items[i]
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

            this.sale.bill_date = dayjs(this.saleDate).format('YYYY-MM-DD')
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
            this.sale.bill_date = dayjs(this.saleDate).format('YYYY-MM-DD')
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
        dragstart (payload) {
            this.draggingRow = payload.row
            payload.event.dataTransfer.effectAllowed = 'move'
        },
        dragover(payload) {
            /*payload.event.dataTransfer.dropEffect = 'move'
            if(payload.event.target != null) {
                payload.event.target.closest('tr').classList.add('is-selected')
            }*/
            payload.event.preventDefault()
        },
        dragleave(payload) {
            /*if(payload.event.target != null) {
                payload.event.target.closest('tr').classList.remove('is-selected')
            }*/
            payload.event.preventDefault()
        },
        drop(payload) {
            /*if(payload.event.target != null) {
                payload.event.target.closest('tr').classList.remove('is-selected')
            }*/
            this.droppedOnRow = payload.row
            if(this.draggingRow.id !== this.droppedOnRow.id) {
                this.sortSaleItems()
            }
        },
        sortSaleItems() {
            let indexDragStart = this.sale.items.indexOf(this.draggingRow)
            let indexDropEnd = this.sale.items.indexOf(this.droppedOnRow)
            this.sale.items.splice(indexDragStart, 1)
            this.sale.items.splice(indexDropEnd, 0, this.draggingRow)
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

        if(this.$route.params.customer_id != null) {
            this.sale.customer_id = parseInt(this.$route.params.customer_id)
            this.getCustomer()
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
