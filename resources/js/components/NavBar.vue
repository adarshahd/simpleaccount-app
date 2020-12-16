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
                    <b-field class="global-search-desktop">
                        <b-autocomplete
                            icon="magnify has-text-link"
                            rounded
                            open-on-focus
                            v-model="searchQuery"
                            :loading="isSearching"
                            @input="search"
                            @select="searchItemSelected"
                            placeholder="Search Everything"
                            group-field="type"
                            group-options="items"
                            :data="searchData">
                            <div slot="empty">
                                <h6 class="subtitle is-6" v-if="searchQuery.length >= 2 && isSearching === false">Couldn't find anything!</h6>
                                <h6 class="subtitle is-6" v-else>Start typing</h6>
                            </div>

                            <template slot="group" slot-scope="type">
                                <h5 class="subtitle is-5">
                                    {{ type.group }}
                                </h5>
                            </template>

                            <template slot="default" slot-scope="props">
                                <p v-if="props.option.name"> {{ props.option.name }}</p>
                                <p v-if="props.option.bill_number"> {{ props.option.bill_number }}</p>
                            </template>

                        </b-autocomplete>
                    </b-field>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    import debounce from 'debounce'
    import axios from 'axios'

    export default {
        name: "NavBar",
        data() {
            return {
                isActive : false,
                isSearching: false,
                searchData: [],
                searchQuery: '',
            }
        },
        computed: {
            debounce() {
                return debounce
            }
        },
        methods: {
            toggleSideBar() {
                this.isActive = !this.isActive
                this.$emit('toggleSideBar')
            },
            search(val) {
                if(!this.searchQuery.length || this.searchQuery.length < 2) {
                    this.searchData = []
                    return
                }

                this.isSearching = true

                debounce(this.doSearch, 500)()
            },
            doSearch() {
                axios.get('/api/v1/search?query=' + this.searchQuery).then(response => {
                    this.searchData = response.data.data
                    this.isSearching = false
                }).catch(error => {
                    console.log(error.response.data)
                    this.isSearching = false
                })
            },
            searchItemSelected(option) {
                switch (option.target) {
                    case 'customer':
                        this.searchQuery = ''
                        this.searchData = []
                        this.$router.push({
                            name: 'customer-details',
                            params: {
                                id: option.id
                            }
                        });
                        break;
                    case 'vendor':
                        this.searchQuery = ''
                        this.searchData = []
                        this.$router.push({
                            name: 'vendor-details',
                            params: {
                                id: option.id
                            }
                        });
                        break;
                    case 'sale':
                        this.searchQuery = ''
                        this.searchData = []
                        this.$router.push({
                            name: 'sale-details',
                            params: {
                                id: option.id
                            }
                        });
                        break;
                    case 'purchase':
                        this.searchQuery = ''
                        this.searchData = []
                        this.$router.push({
                            name: 'purchase-details',
                            params: {
                                id: option.id
                            }
                        });
                        break;
                }
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
