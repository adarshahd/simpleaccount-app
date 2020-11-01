<template>
    <div class="sidebar-left card">
        <aside class="menu is-gapless">
            <template v-for="(item, index) in menu">
                <p class="menu-label" v-if="typeof item === 'string'">{{ item }}</p>
                <side-bar-list v-else :menu="item"></side-bar-list>
            </template>
        </aside>
    </div>
</template>

<script>
    import SideBarItem from "./SideBarItem";
    import SideBarList from "./SideBarList";
    import { PerfectScrollbar } from 'vue2-perfect-scrollbar';

    export default {
        name: "SideBar",
        components: {SideBarList, SideBarItem, PerfectScrollbar},
        data() {
            return {

            }
        },
        props: {
            menu: {
                type: Array,
                default: () => []
            }
        }
    }
</script>

<style scoped>

.menu-label {
    font-size: 15px;
    padding-left: 5px;
    font-weight: bold;
}
</style>
