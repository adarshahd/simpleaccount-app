<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns">
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Bank Name</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <input class="input" placeholder="Bank Name *" type="text" v-model="account.bank_name">
                                            </p>
                                            <span class="has-text-danger" v-if="errors.bank_name">
                                                {{ errors.bank_name[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Bank Branch</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <input class="input" placeholder="Bank Branch" type="text" v-model="account.bank_branch">
                                            </p>
                                            <span class="has-text-danger" v-if="errors.bank_branch">
                                                {{ errors.bank_branch[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Account Name</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <input class="input" placeholder="Account Name *" type="text" v-model="account.account_name">
                                            </p>
                                            <span class="has-text-danger" v-if="errors.account_name">
                                                {{ errors.account_name[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Account Number</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <input class="input" placeholder="Account Number *" type="text" v-model="account.account_number">
                                            </p>
                                            <span class="has-text-danger" v-if="errors.account_number">
                                                {{ errors.account_number[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Account Type</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <div class="control">
                                                <div class="select is-fullwidth">
                                                    <select v-model="account.account_type">
                                                        <option selected disabled value="">Select Account Type</option>
                                                        <option value="savings">Savings</option>
                                                        <option value="current">Current</option>
                                                        <option value="fixed_deposit">Fixed Deposit</option>
                                                        <option value="recurring_deposit">Recurring Deposit</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <span class="has-text-danger" v-if="errors.account_type">
                                                {{ errors.account_type[0] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">IFSC</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <input class="input" placeholder="IFSC Code" type="text" v-model="account.ifsc">
                                            </p>
                                            <span class="has-text-danger" v-if="errors.ifsc">
                                            {{ errors.ifsc[0] }}
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="columns is-centered">
                            <div class="column is-2">
                                <button
                                    class="button"
                                    @click="handleSubmitClick"
                                    :class="{ 'is-loading': isAccountCompleting, 'is-primary' : this.account.id != null, 'is-success' : this.account.id == null }">
                                        <span class="icon">
                                            <i class="mdi mdi-check-circle"></i>
                                        </span>
                                    <span v-if="this.account.id == null">Submit</span>
                                    <span v-else>Update</span>
                                </button>
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
import axios from 'axios'

export default {
    name: "Account",
    components: {ProgressBarIndeterminate},
    data() {
        return {
            isLoading: false,
            isAccountCompleting: false,
            account: {
                id: null,
                account_type: ""
            },
            errors: []
        }
    },
    methods: {
        loadAccount() {
            this.isLoading = true
            axios.get('/api/v1/accounts/' + this.account.id).then(response => {
                this.account = response.data.data
                this.isLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        handleSubmitClick() {
            if(this.account.id == null) {
                this.createAccount()
            } else {
                this.updateAccount()
            }
        },
        createAccount() {
            this.isAccountCompleting = true
            axios.post('/api/v1/accounts', this.account).then(response => {
                this.showToast("Account created successfully!")
                this.isAccountCompleting = false
                this.$router.back()
            }).catch(error => {
                this.isAccountCompleting = false
                this.handleError(error)
            })
        },
        updateAccount() {
            this.isAccountCompleting = true
            axios.patch('/api/v1/accounts/' + this.account.id, this.account).then(response => {
                this.showToast("Account updated successfully!")
                this.isAccountCompleting = false
                this.$router.back()
            }).catch(error => {
                this.isAccountCompleting = false
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
        if(this.account.id != null) {
            this.loadAccount()
        }
    }
}
</script>

<style scoped>

</style>
