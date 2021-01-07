<template>
    <div>
        <h1 class="has-text-centered title is-size-1 has-text-primary">SimpleAccount</h1>
        <div class="has-text-centered" >
            <img class="is-centered" src="/images/world.png">
        </div>
        <div class="has-text-centered">
            <h4 class="title is-4">Your Region Information</h4>
        </div>
        <region-component v-on:country-selected="countrySelected"></region-component>
    </div>

</template>

<script>
import RegionComponent from "../../components/Region";
export default {
    name: "Region",
    components: {RegionComponent},
    methods: {
        countrySelected(country) {
            this.$emit('country-selected', country)
        }
    }
}
</script>

<style scoped>

</style>
