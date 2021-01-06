<template>
    <progress-bar-indeterminate v-if="isSettingsLoading"></progress-bar-indeterminate>
    <section class="main-content" v-else>
        <div class="columns mt-4 is-centered">
            <div class="column is-5">
                <div class="box">
                    <div class="columns is-mobile">
                        <div class="column is-10">
                            <h4 class="subtitle is-4">Company Information</h4>
                        </div>
                        <div class="column is-2">
                            <button class="button is-primary is-small has-icons-right">
                                <span class="mdi mdi-pencil"></span>
                            </button>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column is-3">
                            <figure class="image is-64x64">
                                <img class="is-rounded" :src="productOwnerData.logo" v-if="productOwnerData.logo !== ''" alt="logo">
                                <img class="is-rounded" src="/images/business-shop.png" v-else alt="logo">
                            </figure>
                        </div>
                        <div class="column is-8 is-offset-1">
                            <h5 class="title is-5">{{ productOwnerData.name }}</h5>
                            <h6 class="title is-6">
                                <span class="mdi mdi-account"></span>
                                {{ productOwnerData.contact_name }}
                            </h6>
                            <h6 class="subtitle is-6">
                                <span class="mdi mdi-phone"></span>
                                {{ productOwnerData.contact_phone }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-5">
                <div class="box">
                    <div class="columns is-mobile">
                        <div class="column is-10">
                            <h4 class="subtitle is-4">Regional Settings</h4>
                        </div>
                        <div class="column is-2">
                            <button class="button is-primary is-small has-icons-right">
                                <span class="mdi mdi-pencil"></span>
                            </button>
                        </div>
                    </div>

                    <h5 class="title is-5">
                        <span class="mdi mdi-earth"></span>
                        {{ regionalData.country }}
                    </h5>
                    <h6 class="title is-6">
                        {{ regionalData.currency }} ({{ regionalData.symbol }})
                    </h6>
                    <h6 class="subtitle is-6">
                        <span class="mdi mdi-translate"></span>
                        English
                    </h6>
                </div>
            </div>
        </div>
        <div class="columns mt-2 is-centered">
            <div class="column is-3">
                <div class="box">
                    <div class="columns is-mobile">
                        <div class="column is-9">
                            <h4 class="subtitle is-4">Invoice Settings</h4>
                        </div>
                        <div class="column is-3 has-text-right">
                            <button class="button is-primary is-small has-icons-right">
                                <span class="mdi mdi-pencil"></span>
                            </button>
                        </div>
                    </div>
                    <h6 class="subtitle is-6">
                        Template, Invoice prefix, Invoice start etc
                    </h6>
                </div>
            </div>
            <div class="column is-3">
                <div class="box">
                    <div class="columns is-mobile">
                        <div class="column is-9">
                            <h4 class="subtitle is-4">Product Settings</h4>
                        </div>
                        <div class="column is-3 has-text-right">
                            <button class="button is-primary is-small has-icons-right">
                                <span class="mdi mdi-pencil"></span>
                            </button>
                        </div>
                    </div>
                    <h6 class="subtitle is-6">
                        Product expiry, hsn etc
                    </h6>
                </div>
            </div>
            <div class="column is-3">
                <div class="box">
                    <div class="columns is-mobile">
                        <div class="column is-9">
                            <h4 class="subtitle is-4">Users & Roles</h4>
                        </div>
                        <div class="column is-3 has-text-right">
                            <button class="button is-primary is-small has-icons-right">
                                <span class="mdi mdi-pencil"></span>
                            </button>
                        </div>
                    </div>
                    <h6 class="subtitle is-6">
                        View, add, update and delete users and roles
                    </h6>
                </div>
            </div>
            <div class="column is-3">
                <div class="box">
                    <div class="columns is-mobile">
                        <div class="column is-9">
                            <h4 class="subtitle is-4">Profile</h4>
                        </div>
                        <div class="column is-3 has-text-right">
                            <button class="button is-primary is-small has-icons-right">
                                <span class="mdi mdi-pencil"></span>
                            </button>
                        </div>
                    </div>
                    <h6 class="subtitle is-6">
                        View, and update your profile
                    </h6>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios'
import ProgressBarIndeterminate from "../../components/ProgressBarIndeterminate";

export default {
    name: "Overview",
    components: {ProgressBarIndeterminate},
    data() {
        return {
            isSettingsLoading: true,
            productOwnerData: null,
            regionalData: null,
        }
    },
    methods: {
        getApplicationData() {
            this.isSettingsLoading = true;
            axios.get('/api/v1/settings/overview').then(response => {
                this.productOwnerData = response.data.data.product_owner_data
                this.regionalData = response.data.data.regional_settings_data
                this.isSettingsLoading = false
            }).catch(error => {
                this.handleError(error)
            })
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
    },
    mounted() {
        this.getApplicationData()
    }
}
</script>

<style scoped>

</style>
