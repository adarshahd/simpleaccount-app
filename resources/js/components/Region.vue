<template>
    <section class="section">
        <div class="field">
            <label class="label" for="country">Your Country</label>
            <div class="control">
                <div class="select is-fullwidth" :class="{'is-loading' : isCountriesLoading}">
                    <select id="country" v-model="country" @change="countrySelected">
                        <option value="" disabled selected>Select Country</option>
                        <option v-for="country in countries" :key="country">
                            {{ country }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios'

export default {
    name: "RegionComponent",
    data() {
        return {
            isCountriesLoading: false,
            countries: [],
            country: '',
        }
    },
    methods: {
        getCountries() {
            this.isCountriesLoading = true
            axios.get('/api/v1/countries').then(response => {
                this.countries = response.data
                this.isCountriesLoading = false
            })
        },
        countrySelected() {
            this.$emit('country-selected', this.country)
        }
    },
    mounted() {
        this.getCountries()
    }
}
</script>

<style scoped>

</style>
