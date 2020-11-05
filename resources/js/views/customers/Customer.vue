<template>
    <div>
        <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
        <div class="columns main-content m-3" v-else>
            <div class="column is-5">
                <div class="card has-background-light">
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
                            <div class="card has-background-light">
                                <div class="card-content">
                                    <h5 class="title is-5">Total sales</h5>
                                    <h5 class="subtitle is-5">{{ customerDetails.total_sales }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="column is-half">
                            <div class="card has-background-light">
                                <div class="card-content">
                                    <h5 class="title is-5">Total transaction</h5>
                                    <h5 class="subtitle is-5">₹ {{ customerDetails.total_sale_amount }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-half">
                            <div class="card has-background-light">
                                <div class="card-content">
                                    <h5 class="title is-5">Amount paid</h5>
                                    <h5 class="subtitle is-5">₹ {{ customerDetails.amount_paid }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="column is-half">
                            <div class="card has-background-light">
                                <div class="card-content">
                                    <h5 class="title is-5">Balance</h5>
                                    <h5 class="subtitle is-5"  :class="{'has-text-danger' : customerDetails.balance > 0, 'has-text-success' : customerDetails.balance < 1}">₹ {{ customerDetails.balance }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5 class="title is-5 has-text-centered no-margin">Recent Sales</h5>
                    <div v-if="customerDetails.total_sales === 0">
                        <h6 class="subtitle is-6 has-text-centered">
                            No recent sales found
                        </h6>
                    </div>
                    <div class="customer-container has-background-grey-lighter" v-for="recentSale in customerDetails.recent_sales">
                        <div class="columns">
                            <div class="column is-10">
                                <div class="columns is-mobile">
                                    <div class="column is-half">
                                        <h5 class="title is-5">{{ recentSale.bill_number }}</h5>
                                        {{ dayjs(recentSale.bill_date).format("DD MMM, YYYY") }}
                                    </div>
                                    <div class="column is-half has-text-centered">
                                        <h5 class="title is-5"> ₹ {{ recentSale.total }}</h5>
                                        {{ recentSale.items }} item(s)
                                    </div>
                                </div>
                            </div>
                            <div class="column is-2 has-text-centered-mobile">
                                <button class="button is-link" @click="showSale(recentSale)">
                                    <span class="icon"><i class="mdi mdi-eye"></i></span>
                                    <span>View</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
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
            }
        },
        computed: {
            dayjs() {
                return dayjs;
            }
        },
        methods: {
            getIdTypes () {
                axios.get('/api/v1/idtypes').then(response => {
                    this.idTypes = response.data.data;
                    this.isIdTypeLoading = false;
                }).catch(error => {
                    this.isIdTypeLoading = false;
                });
            },
            getIdType(id) {
                for(let i=0; i<this.idTypes.length; ++i) {
                    let idType = this.idTypes[i];
                    if (idType.id === id) {
                        return idType;
                    }
                }
            },
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
            showSale(sale) {
                this.$router.push({
                    name: 'sale',
                    params: {id: sale.id}
                });
            },
            showEditCustomerView() {
                this.$router.push({
                    name: 'customer-edit',
                    params: {id: this.customerId }
                });
            },
            showCustomerSales() {

            },
            showNewSaleView() {

            },
            showNewReceiptView() {

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
            this.getIdTypes();
        }
    }
</script>

<style scoped>
    .scroll {
        position: relative;
        max-height: 40vh;
        overflow: hidden;
        padding-top: 16px;
    }

    .customer-container {
        padding: 20px;
        margin: 8px;
    }

    .no-margin {
        margin-bottom: .25rem;
    }
</style>
