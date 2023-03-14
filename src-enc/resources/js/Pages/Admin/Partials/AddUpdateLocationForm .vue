<template>
    <span>
        <span @click="startCreating">
            <slot/>
        </span>
        <jet-dialog-modal :show="creating" modal-id="user" @close="closeModal">
            <template #title>{{ !location ? 'Create Location' : 'Edit Location' }}</template>
            <template #content>
                <div class="grid grid-cols-1 gap-2 p-6">
                    <form>
                        <div>
                            <jet-label for="name" value="Name" mandatory="true"/>
                            <jet-input
                                id="name"
                                v-model="form.name"
                                autocomplete="name"
                                autofocus
                                class="mt-1 block w-full"
                                type="text"
                            />
                            <JetInputError :message="form.errors.name" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="short_name" value="Short name" mandatory="true"/>
                            <jet-input
                                id="short_name"
                                v-model="form.short_name"
                                autocomplete="name"
                                class="mt-1 block w-full"
                                type="text"
                            />
                            <JetInputError :message="form.errors.short_name" class="mt-2"/>
                        </div>
                        <div class="mt-4">
                            <div class="flex flex-row justify-between">
                                <jet-label for="address" value="Address" mandatory="true"/>
                                <jet-button class="bg-green-500 hover:bg-green-800 text-white order-last"
                                            type="button"
                                            @click="addressEditor"
                                            v-if="!showAutocomplete"
                                            title="Click here to change address"
                                >
                                    <i class="fa fa-edit"></i>
                                </jet-button>
                            </div>
                            <GMapAutocomplete
                                ref="addressAutocomplete"
                                placeholder="Search address"
                                @place_changed="setPlace"
                                v-for="(m, index) in markerOptions"
                                :position="m.position"
                                :keypress="onChange"
                                class="search"
                                id="search_field"
                                style="width: 100%;height:45px;border:1px solid rgb(212, 212, 216);border-radius: 3px;margin-top:10px;"
                                v-show="showAutocomplete"
                                :options="{fields: ['address_components', 'geometry']}"
                            >
                            </GMapAutocomplete>
                            <div v-show="!showAutocomplete">
                                <jet-input
                                    id="address"
                                    v-model="form.address"
                                    class="mt-1 block w-full"
                                    type="text"
                                    placeholder="Address"
                                    disabled
                                />
                            </div>
                            <JetInputError :message="form.errors.address" class="mt-2"/>
                        </div>
                        <div class="mt-2" style="display: flex; flex-direction: row;">
                            <div style="display: flex; flex-direction: column; width: 50%;">
                                <jet-input
                                    id="postcode"
                                    v-model="form.postcode"
                                    class="mt-1 block w-full"
                                    type="text"
                                    placeholder="Postcode"
                                    :disabled="!showAutocomplete"
                                />
                                <JetInputError :message="form.errors.postcode" class="mt-2"/>
                            </div>
                            <div style="display: flex; flex-direction: column;width: 50%;">
                                <jet-input
                                    id="city_town"
                                    v-model="form.city_town"
                                    class="mt-1 block w-full"
                                    type="text"
                                    placeholder="City / Town"
                                    :disabled="!showAutocomplete"
                                />
                                <JetInputError :message="form.errors.city_town" class="mt-2"/>
                            </div>
                        </div>
                        <div style="display: flex;flex:1;justify-content:flex-end;margin-top: 10px;">
                            <jet-button
                                class="ml-4 justify-center bg-yellow-700"
                                @click="onLocation"
                                type="button"
                                id="locate_on_map"
                                dusk="locate_on_map"
                                v-if="locateOnMap && showAutocomplete"
                            >
                                Locate on map
                            </jet-button>
                        </div>
                        <div class="mt-4" v-if="showOnMap && showAutocomplete">
                            <GoogleMap
                                ref="googleMapRef"
                                style="width: 100%; height: 150px; border: none !important; border-radius: 3px; margin-top: 10px;"
                                :center="{lat: parseFloat(form.location_lat), lng: parseFloat(form.location_lon)}"
                                :zoom="15"
                                :api-key="mapKey"
                                @click="mark">
                                <Marker
                                    ref="markerRef"
                                    :options="{ position: {lat: parseFloat(form.location_lat), lng: parseFloat(form.location_lon)}}"
                                />
                            </GoogleMap>
                            <JetInputError :message="geocoderError" class="mt-2"/>
                        </div>
                        <div class="mt-4">
                            <JetLabel for="phone" value="Phone number"/>
                            <vue-tel-input v-model="form.phone" name="phone" id="phone" required
                                           v-bind="bindProps" v-on:country-changed="countryChanged"
                                           :preferredCountries="preferredCountries"
                                           style="border-radius: 0px;height:45px;--tw-border-opacity: 1; border-color: rgb(209 213 219 / var(--tw-border-opacity));"></vue-tel-input>
                            <JetInputError :message="form.errors.phone" class="mt-2"/>
                        </div>
                        <div class="mt-4">
                            <JetLabel for="timezones" value="Timezones" mandatory="true"/>
                            <jet-drop-down-widget
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-300
                                focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm h-11"
                                v-model="form.timezone"
                                :items="timezoneOptions"
                                :selected="tzGuess"
                            />
                            <JetInputError :message="form.errors.timezone" class="mt-2"/>
                        </div>
                        <div class="mt-4" v-if="!location">
                            <JetLabel for="travel_instructions" value="Travel instructionss"/>
                            <input type="file" v-on:change="onFileChange" accept="application/pdf"
                                   class="inline-flex items-center px-4 py-2 bg-brand-300 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-700 active:bg-brand-900 focus:outline-none focus:border-brand-900 focus:ring focus:ring-brand-300 disabled:opacity-25 transition w-full"/>
                            <JetInputError :message="form.errors.file" class="mt-2"/>
                        </div>
                    </form>
                </div>
            </template>
            <template #footer>
                <jet-secondary-button class="ml-4 justify-center" @click="closeModal"
                >Cancel</jet-secondary-button>
                <jet-button
                    dusk="create_location"
                    class="ml-4 justify-center"
                    id="location"
                    @click="update"
                >
                    <p>{{ location === null ? 'Add Location' : 'Update' }}</p>
                </jet-button>
            </template>
        </jet-dialog-modal>
    </span>
    <jet-dialog-modal :show="showConfirmationModal">
        <template #title> Confirm Address</template>
        <template #content>
            <div class="grid grid-cols-1 gap-2 p-6">
                <div class="mt-4">
                    {{ temporaryAddress.address }}
                </div>
                <div class="mt-4">
                    {{ temporaryAddress.postcode + ' ' + temporaryAddress.city_town }}
                </div>
            </div>
        </template>
        <template #footer>
            <jet-secondary-button class="ml-4 justify-center" @click="showConfirmationModal = false">Cancel
            </jet-secondary-button>
            <jet-button class="ml-4 justify-center" @click="confirmModal">Confirm</jet-button>
        </template>
    </jet-dialog-modal>
</template>


<script>
import {defineComponent, onMounted, ref, reactive, watch} from "vue";
import JetButton from "@/Components/Button.vue";
import JetDialogModal from "@/Components/DialogModal.vue";
import JetSecondaryButton from "@/Components/SecondaryButton.vue";
import JetLabel from "@/Components/Label.vue";
import JetDropDownWidget from "@/Components/DropDownWidget";
import JetInput from "@/Components/Input.vue";
import JetValidationErrors from "@/Components/ValidationErrors.vue";
import JetTextarea from '@/Components/Textarea.vue';
import JetInputError from '@/Components/InputError.vue';
import MultiSelect from 'primevue/multiselect';
import {useForm, usePage} from "@inertiajs/inertia-vue3";
import {GoogleMap, Marker} from "vue3-google-map";
import moment from 'moment';

require('moment-timezone');


export default defineComponent({
    props: {
        timezones: Array,
        preferredCountries: Array,
        apiKey: String,
        location: {
            type: Object,
            default: null,
        },

    },
    computed: {
        tzGuess() {
            return moment.tz.guess(true);
        }
    },

    components: {
        JetButton,
        JetDialogModal,
        JetSecondaryButton,
        JetLabel,
        JetInput,
        JetValidationErrors,
        JetDropDownWidget,
        JetTextarea,
        MultiSelect,
        JetInputError,
        GoogleMap,
        Marker,
    },
    setup(props) {
        const creating = ref(false);
        const street = ref()
        const selectedRoles = ref(null);
        const timezoneOptions = reactive([]);
        const center = {lat: null, lng: null};
        const markerOptions = [{position: center, label: "L", title: "LADY LIBERTY"}];
        let locationEnable = false
        let created = ref(false);
        const showAutocomplete = ref(!props.location);
        const addressAutocomplete = ref(null);
        const markerRef = ref(null);
        const googleMapRef = ref(null);

        const showConfirmationModal = ref(false);

        const locateOnMap = ref(false);
        const showOnMap = ref(false);
        const geocoderError = ref(null);
        const form = useForm({
            name: "",
            short_name: "",
            country_code: "",
            phone: "",
            dial_code: "",
            address: "",
            postcode: "",
            city_town: "",
            timezone: "",
            location_lon: "",
            location_lat: "",
            file: "",

        })

        const temporaryAddress = ref({
            address: '',
            postcode: '',
            city_town: '',
            location_lon: '',
            location_lat: ''
        });

        const mapKey = window.document.getElementsByTagName('meta')['google-api-key'].content;

        const startCreating = () => {
            creating.value = true;
            if (props.location) {
                form.name = props.location?.name
                form.address = props.location?.address_1
                form.short_name = props.location?.short_name
                form.city_town = props.location?.city_town
                form.postcode = props.location?.postcode
                form.phone = props.location?.phone
                form.location_lon = props.location?.location_lon
                form.location_lat = props.location?.location_lat
                form.timezone = props.location?.timezone
                form.file = props.location?.file?.name
            }
        }

        const mark = (event) => {
            markerRef.value.marker.setPosition(event.latLng);
            googleMapRef.value.map.setCenter(event.latLng);

            const geocoder = new googleMapRef.value.api.Geocoder();
            geocoder
                .geocode({location: event.latLng})
                .then((response) => {
                    let P = response.results[0];

                    temporaryAddress.value.address = P.formatted_address;

                    let postalCode = P.address_components.filter(f => JSON.stringify(f.types) === JSON.stringify(['postal_code']))
                    if (postalCode.length) {
                        temporaryAddress.value.postcode = postalCode[0].short_name;
                    } else {
                        temporaryAddress.value.postcode = '';
                    }
                    let cityTown = P.address_components.filter(f => JSON.stringify(f.types) === JSON.stringify(['locality', 'political']))
                    if (cityTown.length) {
                        temporaryAddress.value.city_town = cityTown[0].short_name;
                    } else {
                        cityTown = P.address_components.filter(f => JSON.stringify(f.types) === JSON.stringify(['postal_town']))
                        if (cityTown.length) {
                            temporaryAddress.value.city_town = cityTown[0].short_name;
                        } else {
                            temporaryAddress.value.city_town = '';
                        }
                    }

                    temporaryAddress.value.location_lon = event.latLng.lng();
                    temporaryAddress.value.location_lat = event.latLng.lat();

                    showConfirmationModal.value = true;
                })
                .catch((e) => {
                    geocoderError.value = "Geocoder failed due to: " + e
                });

        }

        const confirmModal = (event) => {
            showConfirmationModal.value = false;
            showAutocomplete.value = false;
            form.address = temporaryAddress.value.address;
            form.postcode = temporaryAddress.value.postcode;
            form.city_town = temporaryAddress.value.city_town
            form.location_lat = temporaryAddress.value.location_lon;
            form.location_lat = temporaryAddress.value.location_lat;

        }


        const countryChanged = (country) => {
            form.country_code = country.iso2
            form.dial_code = country.dialCode
        }

        const onFileChange = (e) => {
            form.file = e.target.files[0];
        }

        const update = (e) => {

            if (props.location) {
                form.put(route("admin.resources.locations.update", {'uuid': props.location.uuid}), {
                    onSuccess: (res) => {
                        closeModal();
                    },
                });
            } else {
                form.post(route("admin.resources.locations.create"), {
                    onSuccess: (res) => {
                        closeModal();
                    },
                });
            }
        }
        //
        const onLocation = () => {
            showOnMap.value = true
        }

        const addressEditor = () => {
            document.getElementById('search_field').value = form.address;
            showAutocomplete.value = true;
            showOnMap.value = false;
        }

        const closeModal = () => {
            form.reset();
            form.clearErrors();
            props.errors = {};
            creating.value = false;

            showAutocomplete.value = true;
            locateOnMap.value = false;
            showOnMap.value = false;
            props.errors = null;

            if (props.location) {
                showAutocomplete.value = false;
            }

        }

        const setPlace = (P) => {
            let postalCode = P.address_components.filter(f => JSON.stringify(f.types) === JSON.stringify(['postal_code']))
            form.postcode = '';
            if (postalCode.length) {
                form.postcode = postalCode[0].short_name;
            }

            form.city_town = '';
            let cityTown = P.address_components.filter(f => JSON.stringify(f.types) === JSON.stringify(['locality', 'political']))
            if (cityTown.length) {
                form.city_town = cityTown[0].short_name;
            } else {
                cityTown = P.address_components.filter(f => JSON.stringify(f.types) === JSON.stringify(['postal_town']))
                if (cityTown.length) {
                    form.city_town = cityTown[0].short_name;
                }
            }
            form.location_lat = P?.geometry.location.lat()
            form.location_lon = P?.geometry.location.lng()
            form.address = document.getElementById('search_field').value
            locateOnMap.value = true;
        }

        const onChange = () => {
            form.address = document.getElementById('search_field').value

        }

        onMounted(() => {
            props.timezones.forEach((item) =>
                timezoneOptions.push({
                    name: item,
                    value: item,
                })
            );
        })

        return {
            bindProps: {
                mode: "international",
                placeholder: "Enter a phone number",
                maxLen: 25,
                autoFormat: true,
                validCharactersOnly: true,
            },
            startCreating,
            update,
            closeModal,
            creating,
            selectedRoles,
            form,
            file: '',
            timezoneOptions,
            country: null,
            countryChanged,
            onFileChange,
            created,
            onLocation,
            street,
            setPlace,
            onChange,
            center,
            markerOptions,
            locationEnable,
            addressEditor,
            mapKey,
            locateOnMap,
            showOnMap,
            showAutocomplete,
            mark,
            googleMapRef,
            markerRef,
            showConfirmationModal,
            confirmModal,
            temporaryAddress,
            addressAutocomplete,
            geocoderError
        }
    },

});
</script>

<style>

.autocomplete {
    width: 100%;
    height: 45px;
    border: 1px solid rgb(212, 212, 216);
    border-radius: 3px;
    margin-top: 10px;
}

.search:focus {
    outline: none !important;
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow) !important;

}

.search {
    text-indent: 10px;
}

</style>
