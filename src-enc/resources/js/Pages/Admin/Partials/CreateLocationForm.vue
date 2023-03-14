<template>

    <span>
      <span @click="startCreating">
        <slot/>
      </span>
      <jet-dialog-modal :show="creating" modal-id="user" @close="closeModal">
        <template #title> Create Location </template>
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
                    autofocus
                    class="mt-1 block w-full"
                    type="text"
                />
                  <JetInputError :message="form.errors.short_name" class="mt-2"/>
              </div>

              <div class="mt-4">
                <jet-label for="address" value="Address" mandatory="true"/>
                  <GMapAutocomplete
                      placeholder="Search address"
                      v-model="googleLocation"
                      @place_changed="setPlace"
                      v-for="(m, index) in markerOptions"
                      :position="m.position"
                      @click="onchange"
                      class="search"
                      style="width: 100%;height:45px;border:1px solid rgb(212, 212, 216);border-radius: 3px;margin-top:10px;"
                  >
                  </GMapAutocomplete>
                <JetInputError :message="form.errors.address" class="mt-2"/>
              </div>
              <div class="mt-4" style="display: flex; flex-direction: row;">
                      <div style="display: flex; flex-direction: column; width: 50%;">
                              <jet-input
                                  id="postcode"
                                  v-model="form.postcode"
                                  class="mt-1 block w-full"
                                  type="text"
                                  placeholder="Postcode"
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
                  />
                  <JetInputError :message="form.errors.city_town" class="mt-2"/>
              </div>
              </div>
              <div   style="display: flex;flex:1;justify-content:flex-end;margin-top: 10px;">
                  <jet-button
                      class="ml-4 justify-center bg-yellow-700"
                      @click="onLocation"
                      type="button"
                      id="locate_on_map"
                      dusk="locate_on_map"
                      v-if="Location.is_locationVisible"
                  >
                         Locate on map
                  </jet-button>
              </div>
              <div class="mt-4" v-if="Location.is_locationEnable" >
                   <GoogleMap
                              style="width: 100%; height: 150px; border: none !important; border-radius: 3px; margin-top: 10px;"
                              :center="Location.location_center" :zoom="15"
                              @click="mark">
                           <Marker :options="{ position: Location.location_center }"
                                   :clickable="true"
                                   :draggable="true"/>
                   </GoogleMap>
              </div>
              <div class="mt-4" style="display: flex; justify-content: space-around">
                  <jet-input
                      id="location_lat"
                      v-model="form.location_lat"
                      class="mt-1 block w-full"
                      type="hidden"
                      placeholder="Location latitude"
                      v-if="Location.is_latLon"
                  />
                  <JetInputError :message="form.errors.location_lat" class="mt-2"/>
                  <jet-input
                      id="location_lon"
                      v-model="form.location_lon"
                      class="mt-1 block w-full"
                      type="hidden"
                      placeholder="Location longitude"
                      v-if="Location.is_latLon"
                  />
                  <JetInputError :message="form.errors.location_lon" class="mt-2"/>
               </div>

              <div class="mt-4">
                  <JetLabel for="phone" value="Phone number"/>
                    <vue-tel-input v-model="form.phone" name="phone" id="phone" required
                                   v-bind="bindProps" v-on:country-changed="countryChanged"
                                   :preferredCountries="preferredCountries"
                                   style="border-radius: 0px;height:45px;--tw-border-opacity: 1;
                                   border-color: rgb(209 213 219 / var(--tw-border-opacity));"></vue-tel-input>
                  <JetInputError :message="form.errors.phone" class="mt-2"/>

              </div>
                <div class="mt-4">
                     <JetLabel for="timezones" value="Timezones" mandatory="true"/>
                    <jet-drop-down-widget
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm h-11"
                        v-model="form.timezone"
                        :items="timezoneOptions"
                        :selected="tzGuess"
                    />
                  <JetInputError :message="form.errors.timezone" class="mt-2"/>
                </div>
               <div class="mt-4">
                  <JetLabel for="travel_instructions" value="Travel instructionss"/>
                   <input type="file" v-on:change="onFileChange" accept="application/pdf" class="inline-flex items-center px-4 py-2 bg-brand-300 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-700 active:bg-brand-900 focus:outline-none focus:border-brand-900 focus:ring focus:ring-brand-300 disabled:opacity-25 transition w-full"/>
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
              @click="create"
          >
            Add location
          </jet-button>
        </template>
      </jet-dialog-modal>

    </span>
    <jet-dialog-modal :show="Location.confirm_address" modal-id="user" >
        <template #title> Confirm Address </template>
        <template #content>
            <div class="grid grid-cols-1 gap-2 p-6">
                <div class="mt-4">
                    {{Location.address}}
                </div>
            </div>
        </template>
        <template #footer>
            <jet-secondary-button class="ml-4 justify-center" @click="closeConfirmModal">Cancel</jet-secondary-button>
            <jet-button class="ml-4 justify-center" @click="clearConfirmModal">Confirm</jet-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import {defineComponent, onMounted, ref, reactive} from "vue";
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
import  {useLocationStore} from "../../../../../store/location/locationStore"

require('moment-timezone');



export default defineComponent({
    props: {
        timezones: Array,
        preferredCountries: Array,
        apiKey: String,
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
        useLocationStore,
    },
    setup() {
        const creating = ref(false);
        const street = ref()
        const selectedRoles = ref(null);
        const timezoneOptions = reactive([]);
        const center = {lat: 40.689247, lng: -74.044502};
        const markerOptions = [{position: center, label: "L", title: "LADY LIBERTY"}];
        let locationEnable = false
        let created = ref(false);
        let Location = useLocationStore()

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

        const startCreating = () => {
            creating.value = true;
        }

        const countryChanged = (country) => {
            form.country_code = country.iso2
            form.dial_code = country.dialCode
        }

        const onFileChange = (e) => {
            form.file = e.target.files[0];
        }

        const create = (e) => {
            e.preventDefault();

            form.post(route("admin.resources.locations.create"), {
                onSuccess: (res) => {
                    Location?.$patch({confirm_address:false, is_locationVisible: false, is_latLon: false, is_locationEnable:false})
                    closeModal();
                },
            });
        }

        const onLocation = ()=>{
            locationEnable =  true
            return Location.$patch({is_locationEnable:true})

        }

        const clearConfirmModal= ()=>{
            Location?.$patch({confirm_address:false, is_locationVisible: false, is_latLon: false})

        }

        const closeConfirmModal= ()=>{
            Location?.$patch({is_locationEnable:false, location_center:{lat:null,lng:null}, confirm_address:false, address:""})
            form.city_town = " "
            form.postcode = " "
            form.location_lat = " "
            form.location_lon = " "
            document.getElementsByClassName('search pac-target-input')[0].value = " "
        }

        const closeModal = () => {
            form.reset();
            form.clearErrors();
            Location?.$patch({ is_locationVisible: false, is_locationEnable: false})
            usePage().props.value.errors = {};
            creating.value = false;
        }

        const mark = (event) => {
            Location?.$patch({confirm_address:true})

        }

        const setPlace = (P)=> {
            Location?.$patch({location_center:{lat:P?.geometry.location.lat(), lng:P?.geometry.location.lng()}, is_locationVisible: true})
            let searched_address = document.getElementsByClassName('search pac-target-input')[0].value
            Location?.$patch({address:searched_address})
            let postalCode =  P.address_components[P.address_components?.length - 1]
            let city = P.address_components[P.address_components?.length - 4]

            form.postcode =  postalCode["long_name"]
            form.city_town = city["long_name"]
            form.location_lat = P?.geometry.location.lat()
            form.location_lon = P?.geometry.location.lng()
            form.address = P?.formatted_address
        }

        const onchange = () => {
            Location.$patch({is_locationEnable:false})
        }


        onMounted(() => {
            let timeZone = Object.values(usePage().props.value.timezones)
            timeZone.forEach((item) =>
                timezoneOptions.push({
                    name: item,
                    value: item,
                })
            );
            Location?.$patch({ is_locationVisible: false})

        })


        return {
            bindProps: {
                mode: "international",
                placeholder: "Enter a phone number",
                maxLen: 25,
                autoFormat: false,
                validCharactersOnly: true,
            },
            startCreating,
            create,
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
            Location,
            onLocation,
            street,
            setPlace,
            onchange,
            center,
            markerOptions,
            mark,
            locationEnable,
            closeConfirmModal,
            clearConfirmModal,
        }
    },

});
</script>

<style>

.autocomplete{
    width: 100%;
    height:45px;
    border:1px solid rgb(212, 212, 216);
    border-radius: 3px;
    margin-top:10px;
}

.search:focus{
    outline: none !important;
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow) !important;

}
.search{
    text-indent: 10px;
}

</style>
