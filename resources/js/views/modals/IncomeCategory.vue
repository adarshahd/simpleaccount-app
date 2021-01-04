<template>
    <div class="modal is-rounded" :class="{ 'is-active' : isActive }">
        <div class="modal-background"></div>
        <div class="modal-card">
            <section class="modal-card-body has-rounded-corner">
                <div class="columns">
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label for="name">Category Name</label>
                                </div>
                                <input class="input" type="text" id="name" v-model="category.name" placeholder="Name *">
                                <span class="has-text-danger" v-if="errors.name">
                                    {{ errors.name[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <div class="label">
                                    <label for="description">Category Description</label>
                                </div>
                                <textarea class="input" type="text" id="description" v-model="category.description"></textarea>
                                <span class="has-text-danger" v-if="errors.description">
                                    {{ errors.description[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns is-align-items-end">
                    <div class="column is-6-desktop is-offset-6-desktop">
                        <button class="button" @click="toggleModal">Cancel</button>
                        <button
                            class="button has-icons-left"
                            :class="{'is-success' : category.id == null, 'is-primary' : category.id != null, 'is-loading' : isLoading }"
                            @click="handleConfirmClick">
                            <span class="mdi mdi-plus-circle" v-if="category.id == null"></span>
                            <span class="mdi mdi-check-circle" v-else></span>
                            <span v-if="category.id == null">&nbsp; Add </span>
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
    name: "IncomeCategory",
    data () {
        return {
            isActive: false,
            isLoading: false,
            errors: [],
        }
    },
    props: {
        category: {
            type: Object,
            default: null
        }
    },
    methods: {
        toggleModal() {
            this.isActive = !this.isActive

            if(!this.isActive) {
                this.category = {}
            }
        },
        handleConfirmClick() {
            if(this.category.id === null) {
                this.createCategory();
            } else {
                this.updateCategory()
            }
        },
        createCategory() {
            this.isLoading = true;
            axios.post('/api/v1/incomes/categories', this.category).then(response => {
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadCategory');
                this.showToast("Category added successfully");
            }).catch(error => {
                this.isLoading = false;
                this.handleError(error);
            });
        },
        updateCategory() {
            this.isLoading = true;
            axios.patch('/api/v1/incomes/categories/' + this.category.id, this.category).then(response => {
                this.category = {};
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadCategory');
                this.showToast("Category updated successfully");
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
