<template>
    <div>
        <login v-if="isAdminCreated" :errors="errors" :login-data="loginData"></login>
        <register v-else :errors="errors" :registration-data="registrationData"></register>
    </div>

</template>

<script>
    import login from '../../components/Login'
    import register from '../../components/Register'
    export default {
        name: "Admin",
        components : {
            login,
            register
        },
        data () {
            return {
            }
        },
        props: {
            isAdminCreated : {
                type : Boolean,
                default : false
            },
            errors : {
                type : Array,
                default : []
            },
            registrationData: {
                type: Object,
                default: null
            },
            loginData: {
                type: Object,
                default: null
            }
        }
    }
</script>

<style scoped>

</style>
