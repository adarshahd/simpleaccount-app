<template>
    <div>
        <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
        <div class="columns main-content m-3" v-else>
            <div class="column is-5">
                <div class="card has-background-light">
                    <div class="card-content">
                        <figure class="image is-128x128">
                            <img src="/images/business-shop.png" alt="" v-if="vendorDetails.vendor.image === null">
                            <img v-else :src="vendorDetails.vendor.image" alt="vendor-image">
                        </figure>
                        <h4 class="title is-4 mt-3">{{ vendorDetails.vendor.name }}</h4>
                        <h6 class="subtitle is-6">{{ vendorDetails.vendor.city + ', ' + vendorDetails.vendor.state }}</h6>
                        <h5 class="subtitle is-5">{{ vendorDetails.id_type + ' ' + vendorDetails.vendor.identification }}</h5>
                        <hr/>
                        <div class="columns">
                            <div class="column is-8">
                                <h4 class="title is-4">Contact</h4>
                                <h6 class="title is-6"> {{ vendorDetails.vendor.contact_name }}</h6>
                                <h6 class="subtitle is-6">{{ vendorDetails.vendor.contact_phone }}</h6>
                                <h6 class="is-6" v-if="vendorDetails.vendor.contact_email !== ''">{{ vendorDetails.vendor.contact_email }}</h6>
                            </div>
                            <div class="column is-4">
                                <div class="buttons">
                                    <button class="button is-primary is-fullwidth" @click="showEditVendorView">
                                        <span class="icon"><i class="mdi mdi-account-edit"></i></span>
                                        <span>Edit</span>
                                    </button>
                                    <button class="button is-link is-fullwidth" @click="showVendorPurchases">
                                        <span class="icon"><i class="mdi mdi-eye"></i></span>
                                        <span>View Purchases</span>
                                    </button>
                                    <button class="button is-link is-fullwidth" @click="showNewPurchaseView">
                                        <span class="icon"><i class="mdi mdi-voucher"></i></span>
                                        <span>Add Purchase</span>
                                    </button>
                                    <button class="button is-success is-fullwidth" @click="showNewVoucherView">
                                        <span class="icon"><i class="mdi mdi-currency-inr"></i></span>
                                        <span>Add Voucher</span>
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
                                    <h5 class="title is-5">Total purchases</h5>
                                    <h5 class="subtitle is-5">{{ vendorDetails.total_purchases }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="column is-half">
                            <div class="card has-background-light">
                                <div class="card-content">
                                    <h5 class="title is-5">Total transaction</h5>
                                    <h5 class="subtitle is-5">₹ {{ vendorDetails.total_purchase_amount }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-half">
                            <div class="card has-background-light">
                                <div class="card-content">
                                    <h5 class="title is-5">Amount paid</h5>
                                    <h5 class="subtitle is-5">₹ {{ vendorDetails.amount_paid }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="column is-half">
                            <div class="card has-background-light">
                                <div class="card-content">
                                    <h5 class="title is-5">Balance</h5>
                                    <h5 class="subtitle is-5"  :class="{'has-text-danger' : vendorDetails.balance > 0, 'has-text-success' : vendorDetails.balance < 1}">₹ {{ vendorDetails.balance }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5 class="title is-5 has-text-centered no-margin">Recent Purchases</h5>
                    <div v-if="vendorDetails.total_purchases === 0">
                        <h6 class="subtitle is-6 has-text-centered">
                            No recent purchases found
                        </h6>
                    </div>
                    <div class="vendor-container has-background-grey-lighter" v-for="recentPurchase in vendorDetails.recent_purchases">
                        <div class="columns">
                            <div class="column is-10">
                                <div class="columns is-mobile">
                                    <div class="column is-half">
                                        <h5 class="title is-5">{{ recentPurchase.bill_number }}</h5>
                                        {{ dayjs(recentPurchase.bill_date).format("DD MMM, YYYY") }}
                                    </div>
                                    <div class="column is-half has-text-centered">
                                        <h5 class="title is-5"> ₹ {{ recentPurchase.total }}</h5>
                                        {{ recentPurchase.items }} item(s)
                                    </div>
                                </div>
                            </div>
                            <div class="column is-2 has-text-centered-mobile">
                                <button class="button is-link" @click="showPurchase(recentPurchase)">
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
        name: "Vendor",
        components: {ProgressBarIndeterminate},
        data () {
            return {
                isLoading: true,
                isIdTypeLoading: true,
                vendorId: null,
                vendorDetails: {},
                errors: [],
            }
        },
        computed: {
            dayjs() {
                return dayjs;
            }
        },
        methods: {
            getVendorDetails() {
                this.isLoading = true;
                axios.get('/api/v1/vendors/' + this.vendorId + '/details').then(response => {
                    this.isLoading = false;
                    this.vendorDetails = response.data.data;
                }).catch(error => {
                    this.isLoading = false;
                    this.handleError(error);
                })
            },
            showPurchase(purchase) {
                this.$router.push({
                    name: 'purchase',
                    params: {id: purchase.id}
                });
            },
            showEditVendorView() {
                this.$router.push({
                    name: 'vendor-edit',
                    params: {id: this.vendorId }
                });
            },
            showVendorPurchases() {

            },
            showNewPurchaseView() {

            },
            showNewVoucherView() {

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
            this.vendorId = this.$route.params.id
            this.getVendorDetails()
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

    .vendor-container {
        padding: 20px;
        margin: 8px;
    }

    .no-margin {
        margin-bottom: .25rem;
    }
</style>
