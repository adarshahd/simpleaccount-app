<template>
    <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
    <section v-else class="main-content">
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <div class="columns is-centered">
                            <div class="column is-9">
                                <p class="title">Manufacturers</p>
                            </div>
                            <div class="column is-3 has-text-right">
                                <button class="button is-primary has-icons-left" @click="showNewManufacturerView">
                                    <span class="mdi mdi-plus-circle"></span>
                                    <span>&nbsp;Add Manufacturer</span>
                                </button>
                            </div>
                        </div>

                        <hr/>
                        <b-table
                            striped
                            :data="manufacturers">
                            <b-table-column field="name" label="Name" v-slot="props">
                                {{ props.row.name }}
                            </b-table-column>
                            <b-table-column field="type" label="Short Name" v-slot="props">
                                {{ props.row.short_name }}
                            </b-table-column>
                            <b-table-column label="Actions" v-slot="props" centered>
                                <span>
                                    <button class="button is-info is-small" @click="showManufacturerDetails(props.row)">
                                        <span class="mdi mdi-eye mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button class="button is-link is-small" @click="showEditManufacturer(props.row)">
                                        <span class="mdi mdi-pencil mdi-18px"></span>
                                    </button>
                                </span>
                                <span>
                                    <button
                                        class="button is-danger is-small"
                                        :class="{ 'is-loading' : isDeleteManufacturerInProgress }"
                                        @click="deleteProduct(props.row)">
                                        <span class="mdi mdi-delete mdi-18px"></span>
                                    </button>
                                </span>
                            </b-table-column>
                            <template slot="empty">
                                <div class="columns is-centered">
                                    <div class="column has-text-centered is-spaced">
                                        <h4 class="title m-6">No Products Found</h4>
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
    export default {
        name: "Manufacturers",
        components: {ProgressBarIndeterminate},
        data() {
            return {
                isLoading : false,
                isDeleteManufacturerInProgress: false,
                manufacturers : [],
                errors: []
            }
        },
        computed: {
            chunk() {
                return chunk;
            }
        },
        methods: {
            loadManufacturers() {
                this.isLoading = true;
                axios.get('/api/v1/manufacturers').then(response => {
                    this.manufacturers = response.data.data;
                    this.isLoading = false;
                }).catch(error => {
                    this.handleError(error);
                })
            },
            showManufacturerDetails(manufacturer) {
                this.$router.push({
                    name: 'manufacturer-details',
                    params: {
                        id: manufacturer.id
                    }
                })
            },
            showEditManufacturer(manufacturer) {
                this.$router.push({
                    name: 'manufacturer-edit',
                    params: {
                        id: manufacturer.id
                    }
                })
            },
            showNewManufacturerView() {
                this.$router.push({
                    name: 'manufacturers-new',
                    params: {
                        id: null
                    }
                })
            },
            handleError(error) {
                this.errors = error.response.data.errors
                if(error.response.status === 401) {
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
                    type: type,
                    position: 'is-top-right'
                });
            }
        },
        mounted() {
            this.loadManufacturers();
        }
    }
</script>

<style scoped>

</style>
