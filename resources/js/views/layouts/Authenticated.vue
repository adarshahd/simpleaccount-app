<template>
    <div :class="{ 'sidebar-open' : isSideBarOpen }">
        <div v-if="isSideBarOpen" class="sidebar-overlay"></div>
        <navBar v-on:toggleSideBar="toggleSideBar"></navBar>
        <sideBar :menu="sideBarItems"></sideBar>
        <div class="is-hidden-desktop global-search">
            <b-field>
                <b-autocomplete
                    icon-right="mdi mdi-search-web mdi-24px has-text-primary has-text-weight-bold"
                    rounded
                    placeholder="Search Everything"
                >

                </b-autocomplete>
            </b-field>
        </div>
        <transition name="fade" mode="out-in">
            <router-view></router-view>
        </transition>
    </div>
</template>

<script>
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
            searchData: []
        }
    },
    methods: {
        toggleSideBar() {
            this.isSideBarOpen = !this.isSideBarOpen
        }
    }
}
</script>

<style scoped>

</style>
