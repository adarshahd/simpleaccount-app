<template>
    <div class="modal is-rounded" :class="{ 'is-active' : isActive }">
        <div class="modal-background"></div>
        <div class="modal-card">
            <section class="modal-card-body has-rounded-corner">
                <div class="field">
                    <div class="control">
                        <div class="label">
                            <label for="name">Id Name</label>
                        </div>
                        <input class="input" type="text" id="name" v-model="idType.name" placeholder="Name *">
                        <span class="has-text-danger" v-if="errors.name">
                            {{ errors.name[0] }}
                        </span>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <div class="label">
                            <label for="idType">Description</label>
                        </div>
                        <textarea class="input" id="idType" v-model="idType.idType" placeholder="Description"></textarea>
                        <span class="has-text-danger" v-if="errors.idType">
                            {{ errors.idType[0] }}
                        </span>
                    </div>
                </div>

                <div class="columns is-align-items-end">
                    <div class="column is-6-desktop is-offset-6-desktop">
                        <button class="button" @click="toggleModal">Cancel</button>
                        <button
                            class="button has-icons-left"
                            :class="{'is-success' : idType.id == null, 'is-primary' : idType.id != null, 'is-loading' : isLoading }"
                            @click="handleConfirmClick">
                            <span class="mdi mdi-plus-circle" v-if="idType.id == null"></span>
                            <span class="mdi mdi-check-circle" v-else></span>
                            <span v-if="idType.id == null">&nbsp; Add </span>
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
    name: "IdTypeModal",
    data () {
        return {
            isActive: false,
            isLoading: false,
            errors: [],
        }
    },
    props: {
        idType: {
            type: Object,
            default: null
        }
    },
    methods: {
        toggleModal() {
            this.isActive = !this.isActive
        },
        handleConfirmClick() {
            if(this.idType.id === null) {
                this.createIdType();
            } else {
                this.updateIdType()
            }
        },
        createIdType() {
            this.isLoading = true;
            axios.post('/api/v1/id-types', this.idType).then(response => {
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadIdType');
                this.showToast("Id Type added successfully");
            }).catch(error => {
                this.isLoading = false;
                this.handleError(error);
            });
        },
        updateIdType() {
            this.isLoading = true;
            axios.patch('/api/v1/id-types/' + this.idType.id, this.idType).then(response => {
                this.idType = {};
                this.isLoading = false;
                this.errors = [];
                this.toggleModal()
                this.$emit('loadIdType');
                this.showToast("Id Type updated successfully");
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
