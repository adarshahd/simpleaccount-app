<template>
    <li class="ml-3 mt-1">
        <router-link :to="{ name: item.to }" exact-active-class="is-active" class="has-icons-left" @click.native="toggleSideBar">
            <p>
                <span><i :class="item.icon"></i></span>
                <span class="item-label">{{ item.label }}</span>
            </p>
        </router-link>
    </li>
</template>

<script>
    export default {
        name: "SideBarItem",
        data () {
            return {
                isActive: false
            }
        },
        props: {
            item: {
                type: Object,
                default: null
            }
        },
        methods: {
            toggleSideBar() {
                this.$store.dispatch('sideBarItemClicked')
            }
        }
    }
</script>

<style scoped>
    .item-label {
        padding-left: 4px;
    }

</style>
