<template>
    <div class="main-content">
        <progress-bar-indeterminate v-if="isLoading"></progress-bar-indeterminate>
        <section v-else>
            <div class="columns">
                <div class="column is-one-third">
                    <div class="card has-background-light">
                        <div class="card-content">
                            <div class="columns">
                                <div class="column">
                                    <i class="mdi mdi-office-building mdi-48px has-text-primary"></i>
                                </div>
                                <div class="column is-10">
                                    <h3 class="title is-3">Manufacturers</h3>
                                    <p class="subtitle is-6">{{ manufacturers.length }} manufacturer(s)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-right-desktop">
                    <div class="has-text-right-desktop has-text-centered-mobile">
                        <button class="button is-primary" @click="showNewManufacturerView">
                        <span class="icon">
                            <i  class="mdi mdi-plus-circle"></i>
                        </span>
                            <span>Add</span>
                        </button>
                    </div>
                </div>
            </div>

            <hr/>

            <div v-if="manufacturers.length === 0">
                <div class="columns">
                    <div class="column is-centered">
                        <h4 class="title is-4 has-text-centered">No manufacturers yet!</h4>
                    </div>
                </div>
            </div>
            <div class="columns" v-else v-for="manufacturerChunk in chunk(manufacturers, 3)">
                <div class="column is-one-third" v-for="manufacturer in manufacturerChunk">
                    <div class="card has-background-light">
                        <div class="card-content">
                            <div class="columns is-vcentered">
                                <div class="column">
                                    <h6 class="title is-6">{{ manufacturer.name }}</h6>
                                    <p>{{ manufacturer.product_count }} Product(s)</p>
                                </div>
                                <div class="column is-3 is-right">
                                    <div class="has-text-right-desktop has-text-centered-mobile">
                                        <button class="button is-link" @click="showManufacturer(manufacturer)">View</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import axios from 'axios'
    import chunk from "lodash/core";
    import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";
    export default {
        name: "Manufacturers",
        components: {ProgressBarIndeterminate},
        data() {
            return {
                isLoading : false,
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
            showManufacturer(manufacturer) {
                this.$router.push({ name: 'manufacturer', params: { id: manufacturer.id }})
            },
            showNewManufacturerView() {
                this.$router.push({ name: 'manufacturers-new' })
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
                    type: type
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
