<template>
    <div class="modal is-rounded" :class="{ 'is-active' : isActive }">
        <div class="modal-background"></div>
        <div class="modal-card">
            <section class="modal-card-body has-rounded-corner">
                <h4 class="subtitle is-4 m-6">Are you sure want to delete?</h4>
                <div class="columns">
                    <div class="column has-text-right">
                        <button class="button is-small" @click="toggleModal">Cancel</button>
                        <button class="button is-danger is-small" @click="onDelete">Delete</button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
export default {
    name: "DeleteModal",
    data() {
        return {
            isActive: false,
        }
    },
    methods: {
        toggleModal() {
            this.isActive = !this.isActive;
        },
        onDelete() {
            this.$emit('delete')
            this.toggleModal()
        }
    }
}
</script>

<style scoped>

</style>
