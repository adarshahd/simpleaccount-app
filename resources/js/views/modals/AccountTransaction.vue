<template>
    <div class="modal is-rounded" :class="{ 'is-active' : isActive }">
        <div class="modal-background"></div>
        <div class="modal-card">
            <section class="modal-card-body has-rounded-corner">
                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label for="type">Transaction Type</label>
                                </div>
                                <div class="select is-fullwidth">
                                    <select v-model="transaction.type" id="type">
                                        <option selected disabled value="">Select Transaction Type</option>
                                        <option value="credit">Credit</option>
                                        <option value="debit">Debit</option>
                                    </select>
                                    <span class="has-text-danger" v-if="errors.type">
                                            {{ errors.type[0] }}
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label for="total">Transaction Total</label>
                                </div>
                                <input class="input" type="text" id="total" v-model="transaction.total" placeholder="Total *">
                                <span class="has-text-danger" v-if="errors.total">
                                    {{ errors.total[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label for="method">Transaction Method</label>
                                </div>
                                <div class="select is-fullwidth">
                                    <select v-model="transaction.method" id="method">
                                        <option selected disabled value="">Select Transaction Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="online">Online Transfer</option>
                                    </select>
                                    <span class="has-text-danger" v-if="errors.method">
                                        {{ errors.method[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label for="reference">Transaction Reference</label>
                                </div>
                                <input class="input" type="text" id="reference" v-model="transaction.reference" placeholder="Reference">
                                <span class="has-text-danger" v-if="errors.reference">
                                    {{ errors.reference[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label>Date</label>
                                </div>
                                <b-datepicker v-model="transaction.dateISO" placeholder="Transaction Date"></b-datepicker>
                            </div>
                            <span class="has-text-danger" v-if="errors.date">
                                {{ errors.date[0] }}
                            </span>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label for="notes">Notes</label>
                                </div>
                                <textarea class="textarea" v-model="transaction.notes" id="notes"></textarea>
                            </div>
                            <span class="has-text-danger" v-if="errors.notes">
                                {{ errors.notes[0] }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="columns is-align-items-end">
                    <div class="column is-6-desktop is-offset-6-desktop">
                        <button class="button" @click="toggleModal">Cancel</button>
                        <button
                            class="button has-icons-left"
                            :class="{'is-success' : transaction.id == null, 'is-primary' : transaction.id != null, 'is-loading' : isLoading }"
                            @click="handleConfirmClick">
                            <span class="mdi mdi-plus-circle" v-if="transaction.id == null"></span>
                            <span class="mdi mdi-check-circle" v-else></span>
                            <span v-if="transaction.id == null">&nbsp; Add </span>
                            <span v-else>&nbsp; Update </span>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import axios from "axios"
import dayjs from 'dayjs'

export default {
    name: "AccountTransactionModal",
    data () {
        return {
            isActive: false,
            isLoading: false,
            errors: [],
        }
    },
    props: {
        transaction: {
            type: Object,
            default: null
        }
    },
    computed: {
        dayjs() {
            return dayjs
        }
    },
    methods: {
        toggleModal() {
            this.isActive = !this.isActive
        },
        handleConfirmClick() {
            if(this.transaction.id === null) {
                this.createTransaction();
            } else {
                this.updateTransaction()
            }
        },
        createTransaction() {
            this.isLoading = true;
            this.transaction.date = dayjs(this.transaction.dateISO).format("YYYY-MM-DD")
            axios.post('/api/v1/accounts/' + this.transaction.account_id + '/transactions', this.transaction).then(response => {
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadTransaction');
                this.showToast("Transaction added successfully");
            }).catch(error => {
                this.isLoading = false;
                this.handleError(error);
            });
        },
        updateTransaction() {
            this.isLoading = true;
            this.transaction.date = dayjs(this.transaction.dateISO).format("YYYY-MM-DD")
            axios.patch('/api/v1/accounts/' + this.transaction.account_id + '/transactions/' + this.transaction.id, this.transaction).then(response => {
                this.transaction = {};
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadTransaction');
                this.showToast("Transaction updated successfully");
            }).catch(error => {
                this.isLoading = false;
                this.handleError(error);
            });
        },
        handleError(error) {
            this.errors = error.response.data.errors;
            if (error.response.status === 401) {
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
        },
    }
}
</script>

<style scoped>

</style>
