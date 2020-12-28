<template>
    <div class="sidebar-left card">
        <aside class="menu is-gapless">
            <div class="mt-4">
                <b-collapse
                    class="m-2"
                    animation="slide"
                    v-for="(item, index) of menu"
                    :key="index"
                    :open="isOpen === index"
                    @open="isOpen = index">
                    <div
                        slot="trigger"
                        slot-scope="props"
                        class="columns is-mobile ml-2 mr-2"
                        role="button">
                        <div class="column is-10">
                            <h5 class="subtitle is-5"> {{ item.label }}</h5>
                        </div>
                        <div class="column is-2">
                            <a>
                                <b-icon
                                    :icon="props.open ? 'menu-up' : 'menu-down'">
                                </b-icon>
                            </a>
                        </div>
                    </div>
                    <div class="ml-4 mr-3 mt-1 mb-1">
                        <side-bar-list :menu="item.children"></side-bar-list>
                    </div>
                </b-collapse>
            </div>
        </aside>
    </div>
</template>

<script>
    import SideBarItem from "./SideBarItem";
    import SideBarList from "./SideBarList";

    export default {
        name: "SideBar",
        components: {SideBarList, SideBarItem},
        data() {
            return {
                isOpen: 0
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
