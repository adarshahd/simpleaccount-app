<template>
    <div class="modal" :class="{ 'is-active' : isActive }">
        <div class="modal-background"></div>
        <div class="modal-card">
            <section class="modal-card-body">
                <div class="field">
                    <div class="control">
                        <div class="label">
                            <label for="name">Tax Name</label>
                        </div>
                        <input class="input" type="text" id="name" v-model="tax.name" placeholder="Name *">
                        <span class="has-text-danger" v-if="errors.name">
                            {{ errors.name[0] }}
                        </span>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <div class="label">
                            <label for="tax">Tax Percent</label>
                        </div>
                        <input class="input" type="text" id="tax" v-model="tax.tax" placeholder="Tax percent *">
                        <span class="has-text-danger" v-if="errors.tax">
                            {{ errors.tax[0] }}
                        </span>
                    </div>
                </div>

                <div class="columns is-align-items-end">
                    <div class="column is-6-desktop is-offset-6-desktop">
                        <button class="button" @click="toggleModal">Cancel</button>
                        <button
                            class="button has-icons-left"
                            :class="{'is-success' : tax.id == null, 'is-primary' : tax.id != null, 'is-loading' : isLoading }"
                            @click="handleConfirmClick">
                            <span class="mdi mdi-plus-circle" v-if="tax.id == null"></span>
                            <span class="mdi mdi-check-circle" v-else></span>
                            <span v-if="tax.id == null">&nbsp; Add </span>
                            <span v-else>&nbsp; Update </span>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Tax",
    data () {
        return {
            isActive: false,
            isLoading: false,
            errors: [],
        }
    },
    props: {
        tax: {
            type: Object,
            default: null
        }
    },
    methods: {
        toggleModal() {
            this.isActive = !this.isActive
        },
        handleConfirmClick() {
            if(this.tax.id === null) {
                this.createTax();
            } else {
                this.updateTax()
            }
        },
        createTax() {
            axios.post('/api/v1/taxes', this.tax).then(response => {
                this.isLoading = false;
                this.errors = [];
                this.$emit('loadTax');
                this.showToast("Tax added successfully");
            }).catch(error => {
                this.isLoading = false;
                this.handleError(error);
            });
        },
        updateTax() {
            axios.patch('/api/v1/taxes/' + this.tax.id, this.tax).then(response => {
                this.tax = {};
                this.isLoading = false;
                this.errors = [];
                this.$emit('loadTax');
                this.showToast("Tax updated successfully");
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
