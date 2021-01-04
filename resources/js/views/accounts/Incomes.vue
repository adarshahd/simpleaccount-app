<template>
    <section class="main-content">
        <income-modal ref="incomeModal" :income="incomeItem" v-on:loadIncome="loadIncomeList"></income-modal>
        <income-category ref="categoryModal" :category="categoryItem" v-on:loadCategory="loadCategoryList"></income-category>
        <div class="columns is-centered">
            <div class="column is-4">
                <div class="card">
                    <div class="card-content">
                        <div class="columns">
                            <div class="column is-8">
                                <h3 class="title is-4">Income Categories</h3>
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
                            :paginated="totalIncomePages > 1"
                            backend-pagination
                            :total="totalIncomeItems"
                            :per-page="incomeItemsPerPage"
                            @page-change="onIncomePageChange"
                            :mobile-cards="false"
                            :data="incomeList" :loading="isIncomeLoading">
                            <b-table-column field="date" label="Date" v-slot="props">
                                {{ dayjs(props.row.date).format("DD MMM, YYYY") }}
                            </b-table-column>
                            <b-table-column field="category" label="Category" v-slot="props">
                                {{ props.row.category.name }}
                            </b-table-column>
                            <b-table-column field="amount" label="Amount" v-slot="props" numeric>
                                <h6 class="has-text-success">
                                    ₹{{ props.row.total }}
                                </h6>
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" numeric>
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
import ProgressBarIndeterminate from "@/components/ProgressBarIndeterminate";
import dayjs from "dayjs";
import IncomeModal from "@/views/modals/Income";
import IncomeCategory from "@/views/modals/IncomeCategory";
export default {
    name: "Incomes",
    components: {IncomeCategory, IncomeModal, ProgressBarIndeterminate},
    data() {
        return {
            isIncomeLoading: false,
            isCategoryLoading: false,
            isDeleteIncomeInProgress: false,
            isDeleteCategoryInProgress: false,
            incomeList: [],
            incomeItem: {
                id: null,
                total: null,
                income_category_id: null,
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
            totalIncomePages: 1,
            incomeItemsPerPage: 1,
            totalIncomeItems: 1,
            currentIncomePage: 1,
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
        loadIncomeList() {
            this.isIncomeLoading = true
            axios.get('/api/v1/incomes?page=' + this.currentIncomePage).then(response => {
                this.incomeList = response.data.data
                this.isIncomeLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        loadCategoryList() {
            this.isCategoryLoading = true
            axios.get('/api/v1/incomes/categories?page=' + this.currentCategoryPage).then(response => {
                this.categoryList = response.data.data
                this.isCategoryLoading = false
            }).catch(error => {
                this.handleError(error)
            })
        },
        onIncomePageChange(val) {
            this.currentIncomePage = val
            this.loadIncomeList()
        },
        onCategoryPageChange(val) {
            this.currentCategoryPage = val
            this.loadCategoryList()
        },
        editIncome(income) {
            this.incomeItem = income;
            this.incomeItem.dateISO = dayjs(this.incomeItem.date).toDate()
            this.incomeItem.income_category_id = income.category.id
            this.$refs.incomeModal.categoryList = this.categoryList
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
            this.incomeItem.income_category_id = ''
            this.$refs.incomeModal.categoryList = this.categoryList
            this.$refs.incomeModal.toggleModal();
        },
        editCategory(category) {
            this.categoryItem = category;
            this.$refs.categoryModal.toggleModal();
        },
        deleteCategory(category) {
            this.isDeleteCategoryInProgress = true
            axios.delete('/api/v1/incomes/categories/' + category.id).then(response => {
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
        this.loadIncomeList()
    }
}
</script>

<style scoped>

</style>
