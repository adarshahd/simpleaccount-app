<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <div class="columns">
            <div class="column is-5">
                <div class="card">
                    <div class="card-content">
                        <figure class="image is-128x128">
                            <img src="/images/business-shop.png" alt="" v-if="customerDetails.customer.image === null">
                            <img v-else :src="customerDetails.customer.image" alt="customer-image">
                        </figure>
                        <h4 class="title is-4 mt-3">{{ customerDetails.customer.name }}</h4>
                        <h6 class="subtitle is-6">{{ customerDetails.customer.city + ', ' + customerDetails.customer.state }}</h6>
                        <h5 class="subtitle is-5">{{ customerDetails.id_type + ' ' + customerDetails.customer.identification }}</h5>
                        <hr/>
                        <div class="columns">
                            <div class="column is-8">
                                <h4 class="title is-4">Contact</h4>
                                <h6 class="title is-6"> {{ customerDetails.customer.contact_name }}</h6>
                                <h6 class="subtitle is-6">{{ customerDetails.customer.contact_phone }}</h6>
                                <h6 class="is-6" v-if="customerDetails.customer.contact_email !== ''">{{ customerDetails.customer.contact_email }}</h6>
                            </div>
                            <div class="column is-4">
                                <div class="buttons">
                                    <button class="button is-primary is-fullwidth" @click="showEditCustomerView">
                                        <span class="icon"><i class="mdi mdi-account-edit"></i></span>
                                        <span>Edit</span>
                                    </button>
                                    <button class="button is-link is-fullwidth" @click="showCustomerSales">
                                        <span class="icon"><i class="mdi mdi-eye"></i></span>
                                        <span>View Sales</span>
                                    </button>
                                    <button class="button is-link is-fullwidth" @click="showNewSaleView">
                                        <span class="icon"><i class="mdi mdi-receipt"></i></span>
                                        <span>Add Sale</span>
                                    </button>
                                    <button class="button is-success is-fullwidth" @click="showNewReceiptView">
                                        <span class="icon"><i class="mdi mdi-currency-inr"></i></span>
                                        <span>Add Receipt</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-7">
                <section>
                    <div class="columns">
                        <div class="column is-half">
                            <div class="card">
                                <div class="card-content">
                                    <h5 class="title is-5">Total sales</h5>
                                    <h5 class="subtitle is-5">{{ customerDetails.total_sales }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="column is-half">
                            <div class="card">
                                <div class="card-content">
                                    <h5 class="title is-5">Total transaction</h5>
                                    <h5 class="subtitle is-5">{{ currencySymbol }} {{ customerDetails.total_sale_amount }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-half">
                            <div class="card">
                                <div class="card-content">
                                    <h5 class="title is-5">Amount paid</h5>
                                    <h5 class="subtitle is-5">{{ currencySymbol }} {{ customerDetails.amount_paid }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="column is-half">
                            <div class="card">
                                <div class="card-content">
                                    <h5 class="title is-5">Balance</h5>
                                    <h5 class="subtitle is-5"  :class="{'has-text-danger' : customerDetails.balance > 0, 'has-text-success' : customerDetails.balance < 1}">{{ currencySymbol }} {{ customerDetails.balance }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5 class="title is-5 has-text-centered no-margin">Customer Activities</h5>
                    <b-table
                        striped
                        narrowed
                        :data="customerDetails.recent_activities">
                        <b-table-column field="bill_number" label="Bill Number" v-slot="props">
                            <div v-if="props.row.type === 'sale'">
                                {{ props.row.bill_number }} ({{ props.row.items }} items)
                            </div>
                            <div v-else>
                                {{ props.row.bill_number }}
                            </div>
                        </b-table-column>
                        <b-table-column field="type" label="Type" v-slot="props">
                            <p v-if="props.row.type === 'sale'">Sale</p>
                            <p v-else>Receipt</p>
                        </b-table-column>
                        <b-table-column field="date" label="Date" v-slot="props">
                            {{ dayjs(props.row.bill_date).format("DD MMM, YYYY") }}
                        </b-table-column>
                        <b-table-column field="total" label="Total" v-slot="props" numeric>
                            {{ currencySymbol }}{{ props.row.total.toFixed(2) }}
                        </b-table-column>
                        <b-table-column field="actions" label="Actions" v-slot="props" centered>
                            <button class="button is-link is-small has-icons-left" @click="viewSale(props.row)">
                                <span class="mdi mdi-eye mdi-18px"></span>
                            </button>
                            <button class="button is-primary is-small has-icons-left" @click="editSale(props.row)">
                                <span class="mdi mdi-pencil mdi-18px"></span>
                            </button>
                        </b-table-column>

                        <template slot="empty">
                            <div class="columns is-centered">
                                <div class="column has-text-centered is-spaced">
                                    <h4 class="title m-6">No Recent Activities Found</h4>
                                </div>
                            </div>
                        </template>
                    </b-table>
                </section>
            </div>
        </div>
    </section>
</template>

<script>
    import axios from "axios";
    import dayjs from 'dayjs'
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";

    export default {
        name: "Customer",
        components: {ProgressBarIndeterminate},
        data () {
            return {
                isLoading: true,
                isIdTypeLoading: true,
                customerId: null,
                customerDetails: {},
                errors: [],
                currencySymbol: this.$store.state.regionData.currencySymbol
            }
        },
        computed: {
            dayjs() {
                return dayjs;
            }
        },
        methods: {
            getCustomerDetails() {
                this.isLoading = true;
                axios.get('/api/v1/customers/' + this.customerId + '/details').then(response => {
                    this.isLoading = false;
                    this.customerDetails = response.data.data;
                }).catch(error => {
                    this.isLoading = false;
                    this.handleError(error);
                })
            },
            viewSale(sale) {
                this.$router.push({
                    name: 'sale-details',
                    params: {
                        id: sale.id
                    }
                });
            },
            editSale(sale) {
                this.$router.push({
                    name: 'sale-edit',
                    params: {
                        id: sale.id
                    }
                });
            },
            showEditCustomerView() {
                this.$router.push({
                    name: 'customer-edit',
                    params: {
                        id: this.customerId
                    }
                });
            },
            showCustomerSales() {

            },
            showNewSaleView() {
                this.$router.push({
                    name: 'sales-new',
                    params: {
                        id: null,
                        customer_id: this.customerId,
                    }
                });
            },
            showNewReceiptView() {
                this.$router.push({
                    name: 'receipts-new',
                    params: {
                        id: null,
                        customer_id: this.customerId,
                    }
                });
            },
            handleError(error) {
                this.errors = error.response.data.errors
                if(error.response.status === 401) {
                    this.$router.go({
                        name: 'login',
                        force: true
                    });
                } else {
                    this.showToast(error.response.data.message, 'is-danger');
                }
            },
            showToast(message, type = 'is-success') {
                this.$buefy.toast.open({
                    message: message,
                    type: type
                });
            }
        },
        mounted() {
            this.customerId = this.$route.params.id
            this.getCustomerDetails()
        }
    }
</script>

<style scoped>
</style>
