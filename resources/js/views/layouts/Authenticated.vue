<template>
    <div :class="{ 'sidebar-open' : isSideBarOpen }">
        <div v-if="isSideBarOpen" class="sidebar-overlay" @click="overlayClicked"></div>
        <navBar ref="navBar" v-on:toggleSideBar="toggleSideBar"></navBar>
        <sideBar :menu="sideBarItems"></sideBar>
        <div class="is-hidden-desktop global-search">
            <b-field>
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
        <transition name="fade" mode="out-in">
            <router-view></router-view>
        </transition>
    </div>
</template>

<script>
import debounce from 'debounce'
import axios from 'axios'
import navBar from '@/components/NavBar'
import sideBar from '@/components/SideBar'
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
    }
}
</script>

<style scoped>

</style>
