<template>
    <div>
        <h1 class="has-text-centered title is-size-1 has-text-primary">SimpleAccount</h1>
        <h6 class="has-text-centered is-size-4">Please login with email and password</h6>
        <br/>
        <div class="columns is-centered">
            <div class="column is-6">
                <form>
                    <div class="field">
                        <div class="control">
                            <input class="input" type="email" id="email" name="email" v-model="loginData.email"
                                   placeholder="Email *" autofocus="">
                            <span class="has-text-danger" v-if="errors.email">
                                {{ errors.email[0] }}
                            </span>
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <input class="input" type="password" id="password" name="password" v-model="loginData.password"
                                   placeholder="Password *" autofocus="" autocomplete="">
                            <span class="has-text-danger" v-if="errors.password">
                                {{ errors.password[0] }}
                            </span>
                        </div>
                    </div>

                    <span class="has-text-danger" v-if="errors.error">
                        {{ errors.error[0] }}
                    </span>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Login",
        data() {
            return {}
        },
        props: {
            errors: {
                type: Array,
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
