<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <account-transaction-modal ref="transactionModal" :transaction="transactionItem" v-on:loadTransaction="loadAccounts"></account-transaction-modal>
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-9">
                                <p class="title">Bank Accounts</p>
                            </div>
                            <div class="column is-3 has-text-right">
                                <button class="button is-primary has-icons-left" @click="addAccount">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Account</span>
                                </button>
                            </div>
                        </div>
                        <hr/>
                        <b-table
                            striped
                            :data="accounts">
                            <b-table-column field="bank" label="Bank" v-slot="props">
                                {{ props.row.bank_name }}<br/>
                                {{ props.row.bank_branch }}
                            </b-table-column>
                            <b-table-column field="account_number" label="Account Number" v-slot="props">
                                {{ props.row.account_number }}
                            </b-table-column>
                            <b-table-column field="balance" label="Account Balance" v-slot="props">
                                {{ props.row.balance }}
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" numeric>
                                <span>
                                    <button class="button is-success is-small" @click="addTransaction(props.row)">
                                        <span class="mdi mdi-cash-plus mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-link is-small" @click="viewAccount(props.row)">
                                        <span class="mdi mdi-eye mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-primary is-small" @click="editAccount(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button
                                        class="button is-danger is-small"
                                        :class="{ 'is-loading' : isDeleteAccountInProgress }"
                                        @click="deleteAccount(props.row)">
                                        <span class="mdi mdi-delete mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Accounts Found</h4>
                                    </div>
                                </div>
                            </template>
                        </b-table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";
import axios from 'axios'
import dayjs from 'dayjs'
import AccountTransactionModal from "../modals/AccountTransaction";

export default {
    name: "Accounts",
    components: {AccountTransactionModal, ProgressBarIndeterminate},
    data() {
        return {
            isLoading: false,
            isDeleteAccountInProgress: false,
            accounts: [],
            transactionItem: {
                id: null,
                type: "",
                total: 0,
                method: "",
                reference: "",
                date: null,
                notes: '',
                account_id: null,
            }
        }
    },
    methods: {
        loadAccounts() {
            this.isLoading = true
            axios.get('/api/v1/accounts').then(response => {
                this.accounts = response.data.data
                this.isLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        addAccount() {
            this.$router.push({
                name: 'accounts-new',
                params: {
                    id: null
                }
            })
        },
        addTransaction(account) {
            this.transactionItem.account_id = account.id;
            this.$refs.transactionModal.toggleModal();
        },
        viewAccount(account) {
            this.$router.push({
                name: 'account-details',
                params: {
                    id: account.id
                }
            })
        },
        editAccount(account) {
            this.$router.push({
                name: 'account-edit',
                params: {
                    id: account.id
                }
            })
        },
        deleteAccount(account) {
            this.isDeleteAccountInProgress = true
            axios.delete('/api/v1/accounts/' + account.id).then(response => {
                this.isDeleteAccountInProgress = false
                this.showToast("Account deleted successfully!")
                this.loadAccounts()
            }).catch(error => {
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
        this.loadAccounts()
    }
}
</script>

<style scoped>

</style>
