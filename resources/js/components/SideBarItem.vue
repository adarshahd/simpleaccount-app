<template>
    <li>
        <router-link :to="{ name: item.to }" exact-active-class="is-active" class="has-icons-left">
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
        }
    }
</script>

<style scoped>
    .item-label {
        padding-left: 4px;
    }

</style>
