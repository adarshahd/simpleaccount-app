<template>
    <section class="hero is-fullheight has-background-primary">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-three-fifths">
                        <div class="box">
                            <login :errors="errors" :login-data="loginData"></login>
                            <hr/>
                            <div class="has-text-centered">
                                <button class="button is-link is-medium is-rounded" @click="doLogin" :class="{'is-loading' : isLoading}">
                                    Login
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import login from '@/components/auth/Login'
    import axios from 'axios'

    export default {
        name: "LoginView",
        components: {
            login,
        },
        data () {
            return {
                isLoading : false,
                loginData: {
                    email: '',
                    password: ''
                },
                errors: []
            }
        },
        methods: {
            doLogin() {
                this.isLoading = true;
                axios.get('/sanctum/csrf-cookie').then(response => {
                    axios.post('/login', {
                        email: this.loginData.email,
                        password: this.loginData.password,
                    }, {
                        headers: {
                            'Accept': "application/json",
                        }
                    }).then(response => {
                        this.isLoading = false;
                        this.$store.dispatch('getCurrentUser')

                        this.$router.go({
                            name: 'dashboard'
                        })
                    }).catch(error => {
                        this.isLoading = false;
                        this.errors = error.response.data.errors;
                    })
                });
            }
        },
        mounted() {
            if(this.$store.state.currentUser.id != null) {
                this.$router.go(-1);
            }
        }
    }
</script>

<style scoped>

</style>
