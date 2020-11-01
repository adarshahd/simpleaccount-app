<template>
    <div class="modal" :class="{ 'is-active' : isActive }">
        <div class="modal-background"></div>
        <div class="modal-card">
            <section class="modal-card-body has-rounded-corner">
                <div class="field">
                    <div class="control">
                        <div class="label">
                            <label for="name">Product Type</label>
                        </div>
                        <input class="input" type="text" id="name" v-model="productType.name" placeholder="Name *">
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
                        <textarea class="input" type="text" id="tax" v-model="productType.description" placeholder="Description *">

                        </textarea>
                        <span class="has-text-danger" v-if="errors.description">
                            {{ errors.description[0] }}
                        </span>
                    </div>
                </div>

                <div class="columns is-align-items-end">
                    <div class="column is-6-desktop is-offset-6-desktop">
                        <button class="button" @click="toggleModal">Cancel</button>
                        <button
                            class="button has-icons-left"
                            :class="{'is-success' : productType.id == null, 'is-primary' : productType.id != null, 'is-loading' : isLoading }"
                            @click="handleConfirmClick">
                            <span class="mdi mdi-plus-circle" v-if="productType.id == null"></span>
                            <span class="mdi mdi-check-circle" v-else></span>
                            <span v-if="productType.id == null">&nbsp; Add </span>
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
        productType: {
            type: Object,
            default: null
        }
    },
    methods: {
        toggleModal() {
            this.isActive = !this.isActive
        },
        handleConfirmClick() {
            if(this.productType.id === null) {
                this.createProductType();
            } else {
                this.updateProductType()
            }
        },
        createProductType() {
            this.isLoading = true;
            axios.post('/api/v1/product-types', this.productType).then(response => {
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadProductType');
                this.showToast("Product Type added successfully");
            }).catch(error => {
                this.isLoading = false;
                this.handleError(error);
            });
        },
        updateProductType() {
            this.isLoading = true;
            axios.patch('/api/v1/product-types/' + this.productType.id, this.productType).then(response => {
                this.tax = {};
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadProductType');
                this.showToast("Product Type updated successfully");
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
