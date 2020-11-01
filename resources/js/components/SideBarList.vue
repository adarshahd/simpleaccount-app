<template>
    <ul class="menu-list">
        <side-bar-item v-for="(item, index) in menu" :item="item" :key="index"></side-bar-item>
    </ul>
</template>

<script>
    import SideBarItem from "./SideBarItem";
    export default {
        name: "SideBarList",
        props: {
            menu: {
                type: Array,
                default: () => []
            }
        },
        components: {SideBarItem}
    }
</script>

<style scoped>

</style>
