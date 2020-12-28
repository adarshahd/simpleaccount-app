<template>

</template>

<script>
import axios from "axios";

export default {
    name: "Logout",
    mounted() {
        this.doLogout();
    },
    methods: {
        doLogout() {
            axios.post("/logout").then(response => {
                this.$router.go({
                    name: "home",
                    force: true
                });
            }).catch(error => {
                alert("Could not log you out!")
            })
        }
    }
}
</script>

<style scoped>

</style>
