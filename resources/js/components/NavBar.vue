<template>
    <nav class="navbar is-primary has-shadow is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <div class="navbar-item">
                <h1 class="title">SimpleAccount</h1>
            </div>

            <a
                role="button"
                class="navbar-burger burger"
                :class="{ 'is-active' : isActive }"
                aria-label="menu"
                aria-expanded="false"
                @click="toggleSideBar">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
            </a>
        </div>

        <div class="navbar-menu">
            <div class="navbar-start">
                <div class="navbar-item">
                    <b-field>
                        <b-autocomplete
                            icon-right="mdi mdi-search-web mdi-24px has-text-primary has-text-weight-bold"
                            rounded
                            placeholder="Search Everything"
                        >

                        </b-autocomplete>
                    </b-field>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    export default {
        name: "NavBar",
        data() {
            return {
                isActive : false
            }
        },
        methods: {
            toggleSideBar() {
                this.isActive = !this.isActive
                this.$emit('toggleSideBar')
            }
        },
        mounted() {
            this.$store.subscribe((mutation, state) => {
                if(mutation.type === 'sideBarItemClicked') {
                    this.toggleSideBar()
                }
            })
        }
    }
</script>

<style scoped>

</style>
