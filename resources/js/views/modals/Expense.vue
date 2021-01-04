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
                                    <label for="total">Expense Total</label>
                                </div>
                                <input class="input" type="text" id="total" v-model="expense.total" placeholder="Total *">
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
                                <b-datepicker v-model="expense.dateISO" placeholder="Expense Date"></b-datepicker>
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
                                    <label for="category">Expense Category</label>
                                </div>
                                <div class="select is-fullwidth">
                                    <select v-model="expense.expense_category_id" id="category">
                                        <option selected disabled value="">Select Category</option>
                                        <option v-for="category in categoryList" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <span class="has-text-danger" v-if="errors.expense_category_id">
                                        {{ errors.expense_category_id[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-6">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label for="notes">Notes</label>
                                </div>
                                <textarea class="textarea" v-model="expense.notes" id="notes"></textarea>
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
                            :class="{'is-success' : expense.id == null, 'is-primary' : expense.id != null, 'is-loading' : isLoading }"
                            @click="handleConfirmClick">
                            <span class="mdi mdi-plus-circle" v-if="expense.id == null"></span>
                            <span class="mdi mdi-check-circle" v-else></span>
                            <span v-if="expense.id == null">&nbsp; Add </span>
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
    name: "ExpenseModal",
    data () {
        return {
            isActive: false,
            isLoading: false,
            categoryList: [],
            errors: [],
        }
    },
    props: {
        expense: {
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

            if(!this.isActive) {
                this.expense = {};
            }
        },
        handleConfirmClick() {
            if(this.expense.id === null) {
                this.createExpense();
            } else {
                this.updateExpense()
            }
        },
        createExpense() {
            this.isLoading = true;
            this.expense.date = dayjs(this.expense.dateISO).format("YYYY-MM-DD")
            axios.post('/api/v1/expenses', this.expense).then(response => {
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadExpense');
                this.showToast("Expense added successfully");
            }).catch(error => {
                this.isLoading = false;
                this.handleError(error);
            });
        },
        updateExpense() {
            this.isLoading = true;
            this.expense.date = dayjs(this.expense.dateISO).format("YYYY-MM-DD")
            axios.patch('/api/v1/expenses/' + this.expense.id, this.expense).then(response => {
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadExpense');
                this.showToast("Expense updated successfully");
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
