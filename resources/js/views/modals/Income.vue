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
                                    <label for="total">Income Total</label>
                                </div>
                                <input class="input" type="text" id="total" v-model="income.total" placeholder="Total *">
                                <span class="has-text-danger" v-if="errors.total">
                                    {{ errors.total[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label>Date</label>
                                </div>
                                <b-datepicker v-model="income.dateISO" placeholder="Income Date"></b-datepicker>
                            </div>
                            <span class="has-text-danger" v-if="errors.date">
                                {{ errors.date[0] }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-6">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label for="method">Income Method</label>
                                </div>
                                <div class="select is-fullwidth">
                                    <select v-model="income.payment_method" id="method">
                                        <option selected disabled value="">Select Income Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="online">Online Transfer</option>
                                    </select>
                                    <span class="has-text-danger" v-if="errors.payment_method">
                                        {{ errors.payment_method[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label for="reference">Income Reference</label>
                                </div>
                                <input class="input" type="text" id="reference" v-model="income.payment_reference" placeholder="Reference">
                                <span class="has-text-danger" v-if="errors.payment_reference">
                                    {{ errors.payment_reference[0] }}
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
                                    <label for="notes">Notes</label>
                                </div>
                                <textarea class="textarea" v-model="income.notes" id="notes"></textarea>
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
                            :class="{'is-success' : income.id == null, 'is-primary' : income.id != null, 'is-loading' : isLoading }"
                            @click="handleConfirmClick">
                            <span class="mdi mdi-plus-circle" v-if="income.id == null"></span>
                            <span class="mdi mdi-check-circle" v-else></span>
                            <span v-if="income.id == null">&nbsp; Add </span>
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
    name: "IncomeModal",
    data () {
        return {
            isActive: false,
            isLoading: false,
            errors: [],
        }
    },
    props: {
        income: {
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
            if(this.income.id === null) {
                this.createIncome();
            } else {
                this.updateIncome()
            }
        },
        createIncome() {
            this.isLoading = true;
            this.income.date = dayjs(this.income.dateISO).format("YYYY-MM-DD")
            axios.post('/api/v1/incomes', this.income).then(response => {
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadIncome');
                this.showToast("Income added successfully");
            }).catch(error => {
                this.isLoading = false;
                this.handleError(error);
            });
        },
        updateIncome() {
            this.isLoading = true;
            this.income.date = dayjs(this.income.dateISO).format("YYYY-MM-DD")
            axios.patch('/api/v1/incomes/' + this.income.id, this.income).then(response => {
                this.income = {};
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadIncome');
                this.showToast("Income updated successfully");
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
