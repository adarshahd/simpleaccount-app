<template>
    <div class="modal is-rounded" :class="{ 'is-active' : isActive }">
        <div class="modal-background"></div>
        <div class="modal-card">
            <section class="modal-card-body has-rounded-corner">
                <div class="has-text-centered" >
                    <img class="is-centered" src="/images/world.png">
                </div>
                <div class="has-text-centered">
                    <h4 class="title is-4">Your Region Information</h4>
                </div>
                <region-component ref="regionComponent" v-on:country-selected="countrySelected"></region-component>
                <div class="columns is-centered">
                    <div class="column has-text-centered">
                        <button class="button" @click="toggleModal">Cancel</button>
                        <button class="button is-primary has-icons-right" @click="updateRegion" :class="{'is-loading' : isLoading}">
                            <span class="icon"><i class="mdi mdi-check-circle"></i></span>
                            <span>Update</span>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import RegionComponent from "@/components/Region";
import axios from 'axios'
export default {
    name: "RegionModal",
    components: {RegionComponent},
    data () {
        return {
            isLoading: false,
            isActive: false,
            country: this.$store.state.regionData.country,
            newCountry: ''
        }
    },
    methods: {
        toggleModal() {
            this.isActive = !this.isActive
        },
        countrySelected(country) {
            this.newCountry = country
        },
        updateRegion() {
            if(this.newCountry === '' || this.newCountry === this.country) {
                this.toggleModal()
                return
            }
            this.isLoading = true
            axios.post('/api/v1/region', {country: this.newCountry}).then(response => {
                this.$store.dispatch('getRegionData').then(() => {this.$emit('country-selected')})
                this.isLoading = false
                this.toggleModal()
            })
        }
    },
    mounted() {
        this.$refs.regionComponent.country = this.country
    }
}
</script>

<style scoped>

</style>
