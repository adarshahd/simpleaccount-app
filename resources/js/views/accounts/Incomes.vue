<template>
    <section class="main-content">
        <income-modal ref="incomeModal" :income="incomeItem" v-on:loadIncome="loadIncomeList"></income-modal>
        <div class="columns is-centered">
            <div class="column is-10-desktop is-12-mobile">
                <div class="card">
                    <div class="card-content">
                        <div class="columns">
                            <div class="column is-8">
                                <h3 class="title is-4">Income List</h3>
                            </div>
                            <div class="column is-4 has-text-centered-mobile has-text-right-desktop">
                                <button class="button is-primary has-icons-left" @click="addNewIncome">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Income</span>
                                </button>
                            </div>
                        </div>
                        <b-table
                            narrowed
                            striped
                            :paginated="totalPages > 1"
                            backend-pagination
                            :total="totalItems"
                            :per-page="ItemsPerPage"
                            @page-change="onPageChange"
                            :mobile-cards="false"
                            :data="incomeList" :loading="isLoading">
                            <b-table-column field="date" label="Date" v-slot="props">
                                {{ dayjs(props.row.date).format("DD MMM, YYYY") }}
                            </b-table-column>
                            <b-table-column field="method" label="Method" v-slot="props">
                                {{ props.row.payment_method }}
                            </b-table-column>
                            <b-table-column field="reference" label="Reference" v-slot="props">
                                {{ props.row.payment_reference }}
                            </b-table-column>
                            <b-table-column field="amount" label="Amount" v-slot="props">
                                <h6 class="has-text-success">
                                    + ₹{{ props.row.total }}
                                </h6>
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" centered>
                                        <span>
                                            <button class="button is-primary is-small" @click="editIncome(props.row)">
                                                <span class="mdi mdi-pencil mdi-18px"></span>
                                            </button>
                                        </span>
                                <span>
                                            <button
                                                class="button is-danger is-small"
                                                :class="{ 'is-loading' : isDeleteIncomeInProgress }"
                                                @click="deleteIncome(props.row)">
                                                <span class="mdi mdi-delete mdi-18px"></span>
                                            </button>
                                        </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Incomes Found</h4>
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
import axios from 'axios'
import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";
import dayjs from "dayjs";
import IncomeModal from "../modals/Income";
export default {
    name: "Incomes",
    components: {IncomeModal, ProgressBarIndeterminate},
    data() {
        return {
            isLoading: false,
            isDeleteIncomeInProgress: false,
            incomeList: [],
            incomeItem: {
                id: null,
                total: null,
                payment_method: "",
                payment_reference: "",
                date: null,
                dateISO: null,
                notes: '',
            },
            totalPages: 1,
            ItemsPerPage: 1,
            totalItems: 1,
            currentPage: 1,
        }
    },
    computed: {
        dayjs() {
            return dayjs
        }
    },
    methods: {
        loadIncomeList() {
            this.isLoading = true
            axios.get('/api/v1/incomes').then(response => {
                this.incomeList = response.data.data
                this.isLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        onPageChange(val) {
            this.currentPage = val
        },
        editIncome(income) {
            this.incomeItem = income;
            this.incomeItem.dateISO = dayjs(this.incomeItem.date).toDate()
            this.$refs.incomeModal.toggleModal();
        },
        deleteIncome(income) {
            this.isDeleteIncomeInProgress = true
            axios.delete('/api/v1/incomes/' + income.id).then(response => {
                this.isDeleteIncomeInProgress = false
                this.showToast("Income deleted successfully!")
                this.loadIncomeList()
            }).catch(error => {
                this.isDeleteIncomeInProgress = false
                this.handleError(error)
            })
        },
        addNewIncome() {
            this.$refs.incomeModal.toggleModal();
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
        this.loadIncomeList()
    }
}
</script>

<style scoped>

</style>
