<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <div class="main-content" v-else>
        <div class="columns mt-2">
            <div class="column is-4">
                <h5 class="title is-5">Hey {{ currentUser.name }}!!</h5>
                Welcome back
            </div>
            <div class="column is-3 is-offset-5">
                <div class="select is-fullwidth">
                    <select @change="filterData" v-model="currentFilter">
                        <option value="0">Last 7 Days</option>
                        <option value="1">Last 30 Days</option>
                        <option value="2">This Year</option>
                        <option value="3">Last Year</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="columns is-gapless">
            <div class="column is-3">
                <div class="box p-5 m-1">
                    <h4 class="title is-4">₹{{ dashboardData.revenue.toFixed(2) }}</h4>
                    <h6 class="subtitle is-6">Revenue</h6>
                </div>
            </div>
            <div class="column is-3">
                <div class="box p-5 m-1">
                    <h4 class="title is-4">{{ dashboardData.sales }} (₹{{ dashboardData.salesTotal.toFixed(2) }})</h4>
                    <h6 class="subtitle is-6">Sales</h6>
                </div>
            </div>
            <div class="column is-3">
                <div class="box p-5 m-1">
                    <h4 class="title is-4">{{ dashboardData.purchases }} (₹{{ dashboardData.purchasesTotal.toFixed(2) }})</h4>
                    <h6 class="subtitle is-6">Purchases</h6>
                </div>
            </div>
            <div class="column is-3">
                <div class="box p-5 m-1">
                    <h4 class="title is-4">{{ dashboardData.stock }} </h4>
                    <h6 class="subtitle is-6">Total Stock</h6>
                </div>
            </div>
        </div>
        <div class="columns">
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
                dateRange: [dayjs().subtract(7, 'day').format("YYYY-MM-DD"), dayjs().format("YYYY-MM-DD")],
                dashboardData: {}
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
                axios.get('/api/v1/dashboard?filter=' + this.currentFilter).then(response => {
                    this.dashboardData = response.data.data
                    this.isLoading = false
                    setTimeout(this.showChart, 500);
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
                this.loadDashboard()
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
