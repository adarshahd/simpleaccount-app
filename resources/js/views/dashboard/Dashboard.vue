<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <div class="main-content" v-else>
        <div class="columns mt-2">
            <div class="column is-4">
                <h5 class="title is-5">Hey {{ currentUser.name }},</h5>
                Welcome Back!
            </div>
            <div class="column is-2 is-offset-4-desktop">
                <div class="select is-fullwidth">
                    <select @change="filterData" v-model="currentFilter">
                        <option value="0">Last 7 Days</option>
                        <option value="1">Last 30 Days</option>
                        <option value="2">This Year</option>
                        <option value="3">Last Year</option>
                        <option value="4">Custom</option>
                    </select>
                </div>
            </div>
            <div class="column is-2">
                <div class="field">
                    <b-datepicker
                        :disabled="currentFilter !== '4'"
                        :date-formatter=dateFormat
                        :mobile-native="false"
                        class="is-fullwidth"
                        v-model="dateRange"
                        :append-to-body="true"
                        position="is-bottom-left"
                        @input="loadDashboard"
                        range>
                    </b-datepicker>
                </div>
            </div>
        </div>
        <div class="columns is-gapless">
            <div class="column is-3">
                <div class="box p-5 m-1 has-text-centered">
                    <h4 class="title is-4">{{ currencySymbol }}{{ dashboardData.revenue.toFixed(2) }}</h4>
                    <h6 class="subtitle is-6">Revenue</h6>
                </div>
            </div>
            <div class="column is-2">
                <div class="box p-5 m-1 has-text-centered">
                    <h4 class="title is-4">{{ dashboardData.sales }} ({{ currencySymbol }}{{ dashboardData.salesTotal.toFixed(2) }})</h4>
                    <h6 class="subtitle is-6">Sales</h6>
                </div>
            </div>
            <div class="column is-2">
                <div class="box p-5 m-1 has-text-centered">
                    <h4 class="title is-4">{{ dashboardData.purchases }} ({{ currencySymbol }}{{ dashboardData.purchasesTotal.toFixed(2) }})</h4>
                    <h6 class="subtitle is-6">Purchases</h6>
                </div>
            </div>
            <div class="column is-2">
                <div class="box p-5 m-1 has-text-centered">
                    <h4 class="title is-4">{{ currencySymbol }}{{ dashboardData.expenseTotal.toFixed(2) }}</h4>
                    <h6 class="subtitle is-6">Expenses</h6>
                </div>
            </div>
            <div class="column is-3">
                <div class="box p-5 m-1 has-text-centered">
                    <h4 class="title is-4">{{ dashboardData.stock }} items</h4>
                    <h6 class="subtitle is-6">Total Stock</h6>
                </div>
            </div>
        </div>
        <div class="columns mt-4">
            <div class="column is-12">
                <div class="chart" id="chart"></div>
                <h5 class="subtitle is-5 has-text-centered">Sales Chart</h5>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import dayjs from 'dayjs'
    import tooltip from 'chartist-plugin-tooltips'
    import chart from 'chartist'
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";
    export default {
        name: "Dashboard",
        components: {ProgressBarIndeterminate},
        data() {
            return {
                isLoading: true,
                currentUser: this.$store.state.currentUser,
                currentFilter: 0,
                dateRange: [dayjs().subtract(7, 'day').toDate(), dayjs().toDate()],
                dashboardData: {},
                currencySymbol: this.$store.state.regionData.currencySymbol
            }
        },
        computed: {
            dayjs() {
                return dayjs
            },
            chart() {
                return chart
            }
        },
        methods: {
            loadDashboard() {
                this.isLoading = true
                let startDate = dayjs(this.dateRange[0]).format("YYYY-MM-DD")
                let endDate = dayjs(this.dateRange[1]).format("YYYY-MM-DD")
                axios.get('/api/v1/dashboard?filter=' + this.currentFilter + '&startDate=' + startDate + '&endDate=' + endDate).then(response => {
                    this.dashboardData = response.data.data
                    this.isLoading = false
                    setTimeout(this.showChart, 500);
                    if(this.dashboardData.productOwnerUpdateRequired) {
                        this.$buefy.snackbar.open({
                            message: 'Please update your product information before using application',
                            type: 'is-info',
                            position: 'is-top',
                            actionText: 'Update',
                            indefinite: true,
                            onAction: () => {
                                this.$router.push({
                                    name: 'business'
                                })
                            }
                        });
                    }
                }).catch(error => {
                    this.handleError(error)
                })
            },
            showChart() {
                new chart.Line(
                    '#chart',
                    this.dashboardData.salesChartData,
                    {
                        plugins: [
                            chart.plugins.tooltip({
                                appendToBody: true
                            })
                        ]
                    }
                )
            },
            filterData(filter) {
                switch (this.currentFilter) {
                    case '0':
                        this.dateRange = [dayjs().subtract(7, 'day').toDate(), dayjs().toDate()]
                        break;
                    case '1':
                        this.dateRange = [dayjs().subtract(30, 'day').toDate(), dayjs().toDate()]
                        break;
                    case '2':
                        this.dateRange = [dayjs().startOf('year').toDate(), dayjs().toDate()]
                        break;
                    case '3':
                        this.dateRange = [
                            dayjs().subtract(1, 'year').startOf('year').toDate(),
                            dayjs().subtract(1, 'year').endOf('year').toDate()
                        ]
                        break;
                    case '4':
                        break;
                }
                if(this.currentFilter !== '4') {
                    this.loadDashboard()
                }
            },
            dateFormat(d) {
                let startDate = dayjs(d[0]).format("DD-MM-YYYY")
                let endDate = dayjs(d[1]).format("DD-MM-YYYY")
                return startDate + ' to ' + endDate
            },

            handleError(error) {
                this.errors = error.response.data.errors
                if(error.response.status === 401) {
                    this.$router.go({
                        path: '/login',
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
            //new chart.Line('#chart', this.data)
            this.loadDashboard()
        }
    }
</script>

<style scoped>

</style>
