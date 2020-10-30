<template>
    <div>
        <h1 class="has-text-centered title is-size-1 has-text-primary">SimpleAccount</h1>
        <h6 class="has-text-centered is-size-4">Create a product administrator user.</h6>
        <div class="columns is-centered">
            <div class="column is-6">
                <form>
                    <div class="field">
                        <div class="control">
                            <input class="input" type="text" v-model="registrationData.name"
                                   placeholder="Name *" autofocus="">
                            <span class="has-text-danger" v-if="errors.name">
                    {{ errors.name[0] }}
                </span>
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <input class="input" type="email" v-model="registrationData.email"
                                   placeholder="Email *" autofocus="">
                            <span class="has-text-danger" v-if="errors.email">
                    {{ errors.email[0] }}
                </span>
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <input class="input" type="password" v-model="registrationData.password"
                                   placeholder="Password *" autofocus="">
                            <span class="has-text-danger" v-if="errors.password">
                    {{ errors.password[0] }}
                </span>
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <input class="input is-disabled" type="password" v-model="registrationData.passwordConfirmation"
                                   placeholder="Confirm Password *">
                            <span class="has-text-danger" v-if="errors.password_confirmation">
                    {{ errors.password_confirmation[0] }}
                </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "Register",
        data() {
            return {}
        },
        props: {
            errors: {
                type: Array,
                default: null
            },
            registrationData: {
                type: Object,
                default: null
            }
        }
    }
</script>

<style scoped>

</style>
