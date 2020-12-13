<template>
    <div>
        <h1 class="has-text-centered title is-size-1 has-text-primary">SimpleAccount</h1>
        <h6 class="has-text-centered is-size-4">Please provide your company information</h6>
        <br/>
        <div class="columns is-centered">
            <div class="column is-6">
                <h5 class="title is-5">Company Info</h5>
                <div class="columns is-vcentered">
                    <div class="column is-two-fifths-desktop">
                        <figure class="image is-128x128">
                            <img class="is-rounded" :src="logoFile" v-if="productOwnerData.logo != null">
                            <img class="is-rounded" src="/images/business-shop.png" v-else>
                        </figure>
                    </div>
                    <div class="column">
                        <div class="file">
                            <label class="file-label">
                                <input class="file-input" type="file" @change="handleFileChange">
                                <span class="file-cta">
                                  <span class="file-icon">
                                    <i class="mdi mdi-upload"></i>
                                  </span>
                                  <span class="file-label">
                                    Choose logo…
                                  </span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <input class="input" type="text" v-model="productOwnerData.name"
                               placeholder="Company Name *" autofocus="">
                        <span class="has-text-danger" v-if="errors.name">
                            {{ errors.name[0] }}
                        </span>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-4">
                        <div class="field">
                            <div class="control">
                                <div class="select is-fullwidth" :class="{'is-loading' : isIdTypeLoading}">
                                    <select v-model="productOwnerData.idTypeId">
                                        <option disabled value="">Select ID</option>
                                        <option v-for="idType in idTypes" :key="idType.id" v-bind:value="idType.id">{{ idType.name }}</option>
                                    </select>
                                    <span class="has-text-danger" v-if="errors.id_type_id">
                                        {{ errors.id_type_id[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-expanded">
                        <div class="field">
                            <div class="control">
                                <input class="input" v-model="productOwnerData.identification"
                                       placeholder="Identification *" autofocus="">
                                <span class="has-text-danger" v-if="errors.identification">
                                    {{ errors.identification[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-half">
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="productOwnerData.website"
                                       placeholder="Website" autofocus="">
                                <span class="has-text-danger" v-if="errors.website">
                                    {{ errors.website[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="column is-half">
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="productOwnerData.contactEmail"
                                       placeholder="Email" autofocus="">
                                <span class="has-text-danger" v-if="errors.contact_email">
                                    {{ errors.contact_email[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-6">
                <h5 class="title is-5">Address</h5>
                <div class="field">
                    <div class="control">
                        <input class="input" type="text" v-model="productOwnerData.addressLine1"
                               placeholder="Address line 1 *" autofocus="">
                        <span class="has-text-danger" v-if="errors.address_line_1">
                            {{ errors.address_line_1[0] }}
                        </span>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <input class="input" type="text" v-model="productOwnerData.addressLine2"
                               placeholder="Address line 2" autofocus="">
                        <span class="has-text-danger" v-if="errors.address_line_2">
                            {{ errors.address_line_2[0] }}
                        </span>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-half">
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="productOwnerData.city"
                                       placeholder="City *" autofocus="">
                                <span class="has-text-danger" v-if="errors.city">
                                    {{ errors.city[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="column is-half">
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="productOwnerData.state"
                                       placeholder="State *" autofocus="">
                                <span class="has-text-danger" v-if="errors.state">
                                    {{ errors.state[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-half">
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="productOwnerData.country"
                                       placeholder="Country *" autofocus="">
                                <span class="has-text-danger" v-if="errors.country">
                                    {{ errors.country[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="column is-half">
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="productOwnerData.pin"
                                       placeholder="Pin *" autofocus="">
                                <span class="has-text-danger" v-if="errors.pin">
                                    {{ errors.pin[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <h5 class="title is-5">Contact Info</h5>
                <div class="columns">
                    <div class="column is-half">
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="productOwnerData.contactName"
                                       placeholder="Contact Person *" autofocus="">
                                <span class="has-text-danger" v-if="errors.contact_name">
                                    {{ errors.contact_person[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="column is-half">
                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" v-model="productOwnerData.contactPhone"
                                       placeholder="Contact Phone *" autofocus="">
                                <span class="has-text-danger" v-if="errors.contact_phone">
                                    {{ errors.contact_phone[0] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: "Product",
        data() {
            return {
                idTypes : [],
                logoFile : null,
                isIdTypeLoading : true,
            }
        },
        props: {
            errors : {
                type : Array,
                default : []
            },
            productOwnerData: {
                type: Object,
                default: null
            }
        },
        methods: {
            handleFileChange(event) {
                const file = event.target.files[0]
                this.logoFile = URL.createObjectURL(file)
                this.productOwnerData.logo = file
            }
        },
        mounted() {
            axios.get('/api/v1/id-types').then(response => {
                this.idTypes = response.data.data;
                this.isIdTypeLoading = false;
            }).catch(response => {
                this.isIdTypeLoading = false;
            });
        }
    }
</script>

<style scoped>

</style>
