<template>
    <div :class="{ 'sidebar-open' : isSideBarOpen }">
        <div v-if="isSideBarOpen" class="sidebar-overlay" @click="overlayClicked"></div>
        <navBar
            ref="navBar"
            :searchData="searchData"
            :searchQuery="searchQuery"
            :isSearching="isSearching"
            v-on:search="search"
            v-on:searchItemSelected="searchItemSelected"
            v-on:toggleSideBar="toggleSideBar">
        </navBar>
        <sideBar :menu="sideBarItems"></sideBar>
        <div class="is-hidden-desktop global-search">
            <b-field>
                <b-autocomplete
                    icon="magnify has-text-link"
                    rounded
                    open-on-focus
                    :loading="isSearching"
                    @input="search"
                    @select="searchItemSelected"
                    placeholder="Search Everything"
                    group-field="type"
                    group-options="items"
                    :data="searchData">
                    <div slot="empty">
                        <h6 class="subtitle is-6" v-if="searchQuery && searchQuery.length >= 2 && isSearching === false">Couldn't find anything!</h6>
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
        <transition name="fade" mode="out-in">
            <router-view></router-view>
        </transition>
    </div>
</template>

<script>
import debounce from 'debounce'
import axios from 'axios'
import navBar from '@/components/NavBar'
import sideBar from '@/components/sidebar/SideBar'
import menuItems from "@/data/menu";

export default {
    name: "Authenticated",
    components: {
        navBar,
        sideBar,
    },
    data () {
        return {
            sideBarItems : menuItems,
            isSideBarOpen: false,
            isSearching: false,
            searchQuery: '',
            searchData: []
        }
    },
    computed: {
        debounce() {
            return debounce
        }
    },
    methods: {
        toggleSideBar() {
            this.isSideBarOpen = !this.isSideBarOpen
        },
        overlayClicked() {
            this.$refs.navBar.toggleSideBar()
        },
        search(query) {
            this.searchQuery = query

            debounce(this.doSearch, 500)(query)
        },
        doSearch(query) {
            if(!query) {
                return
            }

            if(!query.length || query.length < 2) {
                this.searchData = []
                return
            }

            this.isSearching = true

            axios.get('/api/v1/search?query=' + query).then(response => {
                this.searchData = response.data.data
                this.isSearching = false
            }).catch(error => {
                console.log(error.response.data)
                this.isSearching = false
            })
        },
        searchItemSelected(option) {
            this.searchQuery = ''
            this.searchData = []
            this.isSearching = false

            switch (option.target) {
                case 'customer':
                    this.$router.push({
                        name: 'customer-details',
                        params: {
                            id: option.id
                        }
                    });
                    break;
                case 'vendor':
                    this.$router.push({
                        name: 'vendor-details',
                        params: {
                            id: option.id
                        }
                    });
                    break;
                case 'manufacturer':
                    this.$router.push({
                        name: 'manufacturer-details',
                        params: {
                            id: option.id
                        }
                    });
                    break;
                case 'product':
                    this.$router.push({
                        name: 'product-details',
                        params: {
                            id: option.id
                        }
                    });
                    break;
                case 'sale':
                    this.$router.push({
                        name: 'sale-details',
                        params: {
                            id: option.id
                        }
                    });
                    break;
                case 'purchase':
                    this.$router.push({
                        name: 'purchase-details',
                        params: {
                            id: option.id
                        }
                    });
                    break;
                default:
                    break;
            }
        }
    }
}
</script>

<style scoped>

</style>
