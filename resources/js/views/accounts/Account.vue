<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <account-transaction-modal ref="transactionModal" :transaction="transactionItem" v-on:loadTransaction="loadAccount"></account-transaction-modal>
        <div class="columns is-centered">
            <div class="column is-10-desktop is-12-mobile">
                <div class="columns">
                    <div class="column is-4">
                        <div class="card">
                            <div class="card-content">
                                <h4 class="title is-4">{{ account.bank_name }}</h4>
                                <h6 class="subtitle is-6">{{ account.bank_branch }}</h6><br/>
                                <h5 class="title is-5">{{ account.account_number }}</h5>
                                <h6 class="subtitle is-6">{{ account.account_name }}</h6><br/>

                                <h5 class="title is-5">Balance: ₹{{ account.balance }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="column is-8">
                        <div class="card">
                            <div class="card-content">
                                <div class="columns">
                                    <div class="column is-8">
                                        <h4 class="title is-4 has-text-centered">Transactions</h4>
                                    </div>
                                    <div class="column is-4 has-text-centered-mobile has-text-right-desktop">
                                        <button class="button is-primary has-icons-left" @click="addNewTransaction">
                                            <span class="mdi mdi-plus-circle"></span>
                                            <span>&nbsp;Transaction</span>
                                        </button>
                                    </div>
                                </div>
                                <b-table
                                    narrowed
                                    striped
                                    :paginated="totalTransactionPages > 1"
                                    backend-pagination
                                    :total="totalTransactionItems"
                                    :per-page="transactionItemsPerPage"
                                    @page-change="onTransactionPageChange"
                                    :mobile-cards="false"
                                    :data="transactions"
                                    :loading="isTransactionsLoading">
                                    <b-table-column field="date" label="Date" v-slot="props">
                                        {{ dayjs(props.row.date).format("DD MMM, YYYY") }}
                                    </b-table-column>
                                    <b-table-column field="method" label="Method" v-slot="props">
                                        {{ props.row.method }}
                                    </b-table-column>
                                    <b-table-column field="reference" label="Reference" v-slot="props">
                                        {{ props.row.reference }}
                                    </b-table-column>
                                    <b-table-column field="amount" label="Amount" v-slot="props">
                                        <h6 class="has-text-success" v-if="props.row.type === 'credit'">
                                            + ₹{{ props.row.total }}
                                        </h6>
                                        <h6 class="has-text-danger" v-else>
                                            - ₹{{ props.row.total }}
                                        </h6>
                                    </b-table-column>
                                    <b-table-column label="Actions" v-slot="props" centered>
                                        <span>
                                            <button class="button is-primary is-small" @click="editTransaction(props.row)">
                                                <span class="mdi mdi-pencil mdi-18px"></span>
                                            </button>
                                        </span>
                                        <span>
                                            <button
                                                class="button is-danger is-small"
                                                :class="{ 'is-loading' : isDeleteTransactionInProgress }"
                                                @click="deleteTransaction(props.row)">
                                                <span class="mdi mdi-delete mdi-18px"></span>
                                            </button>
                                        </span>
                                    </b-table-column>
                                    <template slot="empty">
                                        <div class="columns is-centered">
                                            <div class="column has-text-centered is-spaced">
                                                <h4 class="title m-6">No Transactions Found</h4>
                                            </div>
                                        </div>
                                    </template>
                                </b-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";
import AccountTransactionModal from "../modals/AccountTransaction";
import axios from 'axios'
import dayjs from 'dayjs'

export default {
    name: "Account",
    components: {AccountTransactionModal, ProgressBarIndeterminate},
    data() {
        return {
            isLoading: false,
            isTransactionsLoading: false,
            isDeleteTransactionInProgress: false,
            transactionItem: {
                id: null,
                type: "",
                total: 0,
                method: "",
                reference: "",
                date: null,
                dateISO: null,
                notes: '',
                account_id: null,
            },
            transactions: [],
            totalTransactionPages: 1,
            transactionItemsPerPage: 1,
            totalTransactionItems: 1,
            currentTransactionPage: 1,
            account: {}
        }
    },
    computed: {
        dayjs() {
            return dayjs;
        }
    },
    methods: {
        loadAccount() {
            this.isLoading = true
            axios.get('/api/v1/accounts/' + this.account.id).then(response => {
                this.account = response.data.data;
                this.isLoading = false
                this.loadTransactions()
            }).catch(error => {
                this.handleError(error)
            })
        },
        loadTransactions() {
            this.isTransactionsLoading = true
            axios.get('/api/v1/accounts/' + this.account.id + '/transactions/?page=' + this.currentTransactionPage).then(response => {
                this.transactions = response.data.data
                this.totalTransactionPages = response.data.meta.last_page
                this.transactionItemsPerPage = response.data.meta.per_page
                this.totalTransactionItems = response.data.meta.total
                this.isTransactionsLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        onTransactionPageChange(val) {
            this.currentTransactionPage = val
            this.loadTransactions()
        },
        addNewTransaction() {
            this.transactionItem.account_id = this.account.id;
            this.$refs.transactionModal.toggleModal();
        },
        editTransaction(transaction) {
            this.transactionItem = transaction;
            this.transactionItem.dateISO = dayjs(this.transactionItem.date).toDate()
            this.$refs.transactionModal.toggleModal();
        },
        deleteTransaction(transaction) {
            this.isDeleteTransactionInProgress = true
            axios.delete('/api/v1/accounts/' + this.account.id + '/transactions/' + transaction.id).then(response => {
                this.isDeleteTransactionInProgress = false
                this.showToast("Transaction deleted successfully!")
                this.loadAccount()
            }).catch(error => {
                this.isDeleteTransactionInProgress = false
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
        this.account.id = this.$route.params.id
        this.loadAccount()
    }
}
</script>

<style scoped>

</style>
