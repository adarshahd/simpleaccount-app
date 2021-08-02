<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <div class="columns">
            <div class="column is-6 is-offset-6 has-text-right-desktop has-text-centered-mobile">
                <button class="button is-link has-icon-left" @click="downloadInvoice">
                    <span class="mdi mdi-download"></span>
                    <span>Download Invoice</span>
                </button>
                <button class="button is-primary has-icon-left" @click="editPurchase">
                    <span class="mdi mdi-pencil"></span>
                    <span>Edit Purchase</span>
                </button>
            </div>
        </div>
        <div class="box">
            <div class="columns">
                <div class="column is-8">
                    <div class="has-text-dark">
                        <h5 class="title is-5">
                            {{ purchase.vendor.name }}
                        </h5>
                        <h6 class="subtitle is-6">
                            {{ purchase.vendor.address_line_1 }} <br/>
                            {{ purchase.vendor.city }} <br/>
                            {{ purchase.vendor.contact_phone }}
                        </h6>
                        <h6 class="subtitle is-6 has-text-weight-bold">
                            {{ purchase.vendor.id_type.name + '# ' + purchase.vendor.identification }}
                        </h6>
                    </div>
                </div>
                <div class="column is-4">
                    <h5 class="subtitle is-5 is-pulled-right">
                        Invoice Number: {{ purchase.bill_number }}<br/>
                        Invoice Date: {{ dayjs(purchase.bill_date).format("DD-MM-YYYY") }}
                    </h5>
                </div>
            </div>
            <b-table :data="purchase.items" bordered>
                <!--<b-table-column field="si" label="SI" v-slot="props">
                    {{ props.row.id }}
                </b-table-column>-->
                <b-table-column field="product" label="Product" v-slot="props">
                    {{ props.row.product_stock.product.name }}
                </b-table-column>
                <b-table-column field="quantity" label="Quantity" v-slot="props">
                    {{ props.row.quantity }}
                </b-table-column>
                <b-table-column field="hsn" label="HSN" v-slot="props">
                    {{ props.row.product_stock.product.hsn }}
                </b-table-column>
                <b-table-column field="batch" label="Batch" v-slot="props">
                    {{ props.row.product_stock.batch }}
                </b-table-column>
                <b-table-column field="expiry" label="Expiry" v-slot="props">
                    {{ dayjs(props.row.product_stock.expiry).format("MMM YYYY") }}
                </b-table-column>
                <b-table-column field="mrp" label="MRP" v-slot="props" numeric>
                    {{ currencySymbol }}{{ props.row.product_stock.mrp.toFixed(2) }}
                </b-table-column>
                <b-table-column field="price" label="Price" v-slot="props" numeric>
                    {{ currencySymbol }}{{ props.row.product_stock.price.toFixed(2) }}
                </b-table-column>
                <b-table-column field="taxable" label="Taxable" v-slot="props" numeric>
                    {{ currencySymbol }}{{ (props.row.product_stock.price * props.row.quantity).toFixed(2) }}
                </b-table-column>
                <b-table-column field="tax" label="Tax" v-slot="props" numeric>
                    {{ currencySymbol }}{{ (props.row.product_stock.tax * props.row.quantity).toFixed(2) }}
                </b-table-column>
                <b-table-column field="discount" label="Discount" v-slot="props" numeric>
                    {{ currencySymbol }}{{ props.row.discount.toFixed(2) }}
                </b-table-column>
                <b-table-column field="subtotal" label="Subtotal" v-slot="props" numeric>
                    {{ currencySymbol }}{{ (props.row.product_stock.price.toFixed(2) * props.row.quantity).toFixed(2) }}
                </b-table-column>
                <b-table-column field="total" label="Item Total" v-slot="props" numeric>
                    {{ currencySymbol }}{{ (props.row.product_stock.price * props.row.quantity + props.row.product_stock.tax * props.row.quantity).toFixed(2) }}
                </b-table-column>
            </b-table>
            <div class="columns">
                <div class="column is-3 is-offset-9">
                    <b-table bordered :data="purchaseData" :columns="purchaseDataColumns" :show-header="false" narrowed></b-table>
                </div>
            </div>
        </div>
    </section>

</template>

<script>
    import ProgressBarIndeterminate from "@/components/ProgressBarIndeterminate";
    import axios from "axios";
    import dayjs from "dayjs";
    export default {
        name: "Purchase",
        components: {ProgressBarIndeterminate},
        data() {
            return {
                purchase: {},
                purchaseData: [],
                purchaseDataColumns: [
                    {
                        field: "name",
                        width: "60%",
                    },
                    {
                        field: "value",
                        width: "40%",
                        numeric: true
                    }
                ],
                isLoading: true,
                currencySymbol: this.$store.state.regionData.currencySymbol
            }
        },
        computed: {
            dayjs() {
                return dayjs;
            }
        },
        methods: {
            getPurchase() {
                this.isLoading = true
                axios.get('/api/v1/purchases/' + this.purchase.id).then(response => {
                    this.purchase = response.data.data
                    this.purchaseData = [
                        {
                            name: "SubTotal",
                            value: '{{ currencySymbol }}' + this.purchase.sub_total.toFixed(2)
                        },
                        {
                            name: "Discount",
                            value: '{{ currencySymbol }}' + this.purchase.discount.toFixed(2)
                        },
                        {
                            name: "Tax",
                            value: '{{ currencySymbol }}' + this.purchase.tax.toFixed(2)
                        },
                        {
                            name: "Round Off",
                            value: '{{ currencySymbol }}' + (this.purchase.total - Math.floor(this.purchase.total)).toFixed(2)
                        },
                        {
                            name: "Total",
                            value: '{{ currencySymbol }}' + Math.round(this.purchase.total).toFixed(2)
                        }
                    ]
                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error)
                })
            },
            editPurchase() {
                this.$router.push({
                    name: 'purchase-edit',
                    params: {
                        id: this.purchase.id,
                    }
                })
            },
            downloadInvoice() {
                window.open('/api/v1/purchases/' + this.purchase.id + '/invoice', '_blank')
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
            this.getPurchase()
        }
    }
</script>

<style scoped>

</style>
