<template>
    <nav class="navbar is-primary has-shadow is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <div class="navbar-item is-expanded">
                <div class="columns is-vcentered is-gapless is-hidden-mobile" style="margin-bottom: 0 !important">
                    <div class="column is-2">
                        <figure class="figure">
                            <img class="image" src="/images/logo-dark.png" alt="logo">
                        </figure>
                    </div>
                    <div class="column is-10 ml-2">
                        <h4 class="title is-4">SimpleAccount</h4>
                    </div>
                </div>
                <figure class="figure is-hidden-desktop is-hidden-fullhd is-fullwidth">
                    <img class="image" src="/images/logo-mobile-dark.png" width="60" height="50" alt="logo">
                </figure>
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
                <div class="navbar-item is-expanded">
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
