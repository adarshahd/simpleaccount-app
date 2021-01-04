<template>
    <section class="main-content">
        <expense-modal ref="expenseModal" :expense="expenseItem" v-on:loadExpense="loadExpenseList"></expense-modal>
        <expense-category ref="categoryModal" :categoryList="categoryList" :category="categoryItem" v-on:loadCategory="loadCategoryList"></expense-category>
        <div class="columns is-centered">
            <div class="column is-4">
                <div class="card">
                    <div class="card-content">
                        <div class="columns">
                            <div class="column is-8">
                                <h3 class="title is-4">Expense Categories</h3>
                            </div>
                            <div class="column is-4 has-text-centered-mobile has-text-right-desktop">
                                <button class="button is-primary has-icons-left" @click="addNewCategory">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Category</span>
                                </button>
                            </div>
                        </div>
                        <b-table
                            narrowed
                            striped
                            :paginated="totalCategoryPages > 1"
                            backend-pagination
                            :total="totalCategoryItems"
                            :per-page="categoryItemsPerPage"
                            @page-change="onCategoryPageChange"
                            :mobile-cards="false"
                            :data="categoryList"
                            :loading="isCategoryLoading">
                            <b-table-column field="name" label="Name" v-slot="props">
                                {{ props.row.name }}
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" numeric>
                                <span>
                                    <button class="button is-primary is-small" @click="editCategory(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button
                                        class="button is-danger is-small"
                                        :class="{ 'is-loading' : isDeleteCategoryInProgress }"
                                        @click="deleteCategory(props.row)">
                                        <span class="mdi mdi-delete mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Categories Found</h4>
                                    </div>
                                </div>
                            </template>
                        </b-table>
                    </div>
                </div>
            </div>
            <div class="column is-8">
                <div class="card">
                    <div class="card-content">
                        <div class="columns">
                            <div class="column is-8">
                                <h3 class="title is-4">Expense List</h3>
                            </div>
                            <div class="column is-4 has-text-centered-mobile has-text-right-desktop">
                                <button class="button is-primary has-icons-left" @click="addNewExpense">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Expense</span>
                                </button>
                            </div>
                        </div>
                        <b-table
                            narrowed
                            striped
                            :paginated="totalExpensePages > 1"
                            backend-pagination
                            :total="totalExpenseItems"
                            :per-page="expenseItemsPerPage"
                            @page-change="onExpensePageChange"
                            :mobile-cards="false"
                            :data="expenseList" :loading="isExpenseLoading">
                            <b-table-column field="date" label="Date" v-slot="props">
                                {{ dayjs(props.row.date).format("DD MMM, YYYY") }}
                            </b-table-column>
                            <b-table-column field="category" label="Category" v-slot="props">
                                {{ props.row.category.name }}
                            </b-table-column>
                            <b-table-column field="amount" label="Amount" v-slot="props" numeric>
                                <h6>
                                    ₹{{ props.row.total }}
                                </h6>
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" numeric>
                                <span>
                                    <button class="button is-primary is-small" @click="editExpense(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button
                                        class="button is-danger is-small"
                                        :class="{ 'is-loading' : isDeleteExpenseInProgress }"
                                        @click="deleteExpense(props.row)">
                                        <span class="mdi mdi-delete mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Expenses Found</h4>
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
import ProgressBarIndeterminate from "@/components/ProgressBarIndeterminate";
import dayjs from "dayjs";
import ExpenseModal from "@/views/modals/Expense";
import ExpenseCategory from "@/views/modals/ExpenseCategory";
export default {
    name: "Expenses",
    components: {ExpenseCategory, ExpenseModal, ProgressBarIndeterminate},
    data() {
        return {
            isExpenseLoading: false,
            isCategoryLoading: false,
            isDeleteExpenseInProgress: false,
            isDeleteCategoryInProgress: false,
            expenseList: [],
            expenseItem: {
                id: null,
                total: null,
                expense_category_id: '',
                date: null,
                dateISO: null,
                notes: '',
            },
            categoryList: [],
            categoryItem: {
                id: null,
                name: '',
                description: ''
            },
            totalExpensePages: 1,
            expenseItemsPerPage: 1,
            totalExpenseItems: 1,
            currentExpensePage: 1,
            totalCategoryPages: 1,
            categoryItemsPerPage: 1,
            totalCategoryItems: 1,
            currentCategoryPage: 1,
        }
    },
    computed: {
        dayjs() {
            return dayjs
        }
    },
    methods: {
        loadExpenseList() {
            this.isExpenseLoading = true
            axios.get('/api/v1/expenses?page=' + this.currentExpensePage).then(response => {
                this.expenseList = response.data.data
                this.isExpenseLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        loadCategoryList() {
            this.isCategoryLoading = true
            axios.get('/api/v1/expenses/categories?page=' + this.currentCategoryPage).then(response => {
                this.categoryList = response.data.data
                this.isCategoryLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        onExpensePageChange(val) {
            this.currentExpensePage = val
            this.loadExpenseList()
        },
        onCategoryPageChange(val) {
            this.currentCategoryPage = val
            this.loadCategoryList()
        },
        editExpense(expense) {
            this.expenseItem = expense;
            this.expenseItem.dateISO = dayjs(this.expenseItem.date).toDate()
            this.expenseItem.expense_category_id = expense.category.id
            this.$refs.expenseModal.categoryList = this.categoryList
            this.$refs.expenseModal.toggleModal();
        },
        deleteExpense(expense) {
            this.isDeleteExpenseInProgress = true
            axios.delete('/api/v1/expenses/' + expense.id).then(response => {
                this.isDeleteExpenseInProgress = false
                this.showToast("Expense deleted successfully!")
                this.loadExpenseList()
            }).catch(error => {
                this.isDeleteExpenseInProgress = false
                this.handleError(error)
            })
        },
        addNewExpense() {
            this.expenseItem.expense_category_id = ''
            this.$refs.expenseModal.categoryList = this.categoryList
            this.$refs.expenseModal.toggleModal();
        },
        editCategory(category) {
            this.categoryItem = category;
            this.$refs.categoryModal.toggleModal();
        },
        deleteCategory(category) {
            this.isDeleteCategoryInProgress = true
            axios.delete('/api/v1/expenses/categories/' + category.id).then(response => {
                this.isDeleteCategoryInProgress = false
                this.showToast("Category deleted successfully!")
                this.loadCategoryList()
            }).catch(error => {
                this.isDeleteCategoryInProgress = false
                this.handleError(error)
            })
        },
        addNewCategory() {
            this.$refs.categoryModal.toggleModal();
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
        this.loadCategoryList()
        this.loadExpenseList()
    }
}
</script>

<style scoped>

</style>
