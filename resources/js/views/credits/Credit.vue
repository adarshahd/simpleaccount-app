<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <div class="columns">
            <div class="column is-6 is-offset-6 has-text-right-desktop has-text-centered-mobile">
                <button class="button is-link has-icon-left" @click="downloadInvoice">
                    <span class="mdi mdi-download"></span>
                    <span>Download Invoice</span>
                </button>
                <button class="button is-primary has-icon-left" @click="editCredit">
                    <span class="mdi mdi-pencil"></span>
                    <span>Edit Credit</span>
                </button>
            </div>
        </div>
        <div class="box">
            <div class="columns">
                <div class="column is-8">
                    <div class="has-text-dark">
                        <h5 class="title is-5">
                            {{ credit.customer.name }}
                        </h5>
                        <h6 class="subtitle is-6">
                            {{ credit.customer.address_line_1 }} <br/>
                            {{ credit.customer.city }} <br/>
                            {{ credit.customer.contact_phone }}
                        </h6>
                        <h6 class="subtitle is-6 has-text-weight-bold">
                            {{ credit.customer.id_type.name + '# ' + credit.customer.identification }}
                        </h6>
                    </div>
                </div>
                <div class="column is-4">
                    <h5 class="subtitle is-5 is-pulled-right">
                        Invoice Number: {{ credit.bill_number }}<br/>
                        Invoice Date: {{ dayjs(credit.bill_date).format("DD-MM-YYYY") }}
                    </h5>
                </div>
            </div>
            <b-table :data="credit.items" bordered>
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
                    {{ props.row.product_stock.hsn }}
                </b-table-column>
                <b-table-column field="batch" label="Batch" v-slot="props">
                    {{ props.row.product_stock.batch }}
                </b-table-column>
                <b-table-column field="expiry" label="Expiry" v-slot="props">
                    {{ dayjs(props.row.product_stock.expiry).format("MMM YYYY") }}
                </b-table-column>
                <b-table-column field="mrp" label="MRP" v-slot="props" numeric>
                    ₹{{ props.row.product_stock.mrp }}
                </b-table-column>
                <b-table-column field="price" label="Price" v-slot="props" numeric>
                    ₹{{ props.row.price }}
                </b-table-column>
                <b-table-column field="tax" label="Tax" v-slot="props" numeric>
                    ₹{{ props.row.tax }}
                </b-table-column>
                <b-table-column field="discount" label="Discount" v-slot="props" numeric>
                    ₹{{ props.row.discount }}
                </b-table-column>
                <b-table-column field="subtotal" label="Subtotal" v-slot="props" numeric>
                    ₹{{ props.row.sub_total }}
                </b-table-column>
                <b-table-column field="total" label="Item Total" v-slot="props" numeric>
                    ₹{{ props.row.total }}
                </b-table-column>
            </b-table>
            <div class="columns">
                <div class="column is-3 is-offset-9">
                    <b-table bordered :data="creditData" :columns="creditDataColumns" :show-header="false" narrowed></b-table>
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
        name: "Credit",
        components: {ProgressBarIndeterminate},
        data() {
            return {
                credit: {},
                creditData: [],
                creditDataColumns: [
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
            }
        },
        computed: {
            dayjs() {
                return dayjs;
            }
        },
        methods: {
            getCredit() {
                this.isLoading = true
                axios.get('/api/v1/credits/' + this.credit.id).then(response => {
                    this.credit = response.data.data
                    this.creditData = [
                        {
                            name: "SubTotal",
                            value: '₹' + this.credit.sub_total
                        },
                        {
                            name: "Discount",
                            value: '₹' + this.credit.discount
                        },
                        {
                            name: "Tax",
                            value: '₹' + this.credit.tax
                        },
                        {
                            name: "Round Off",
                            value: '₹' + (this.credit.total - Math.floor(this.credit.total)).toFixed(2)
                        },
                        {
                            name: "Total",
                            value: '₹' + Math.round(this.credit.total)
                        }
                    ]
                    this.isLoading = false
                }).catch(error => {
                    this.handleError(error)
                })
            },
            editCredit() {
                this.$router.push({
                    name: 'credit-edit',
                    params: {
                        id: this.credit.id,
                    }
                })
            },
            downloadInvoice() {
                window.open('/api/v1/credits/' + this.credit.id + '/invoice', '_blank')
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
            this.getCredit()
        }
    }
</script>

<style scoped>

</style>
