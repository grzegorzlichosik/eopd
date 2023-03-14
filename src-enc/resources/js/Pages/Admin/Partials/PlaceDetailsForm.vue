<template>
  <span>
    <span @click="startUpdating">
      <slot/>
    </span>
    <jet-dialog-modal :show="updating" modal-id="user" @close="closeModal">
      <template #title>Details of place: {{ place.uuid }} </template>
      <template #content>
           <div class="flex flex-row mb-3">
               <div class="basis-1/3 font-bold">Name</div>
               <div class="basis-2/3">{{ place.name }}</div>
           </div>
           <div class="flex flex-row mb-3" v-if="place.email">
               <div class="basis-1/3 font-bold">Email</div>
               <div class="basis-2/3">{{ place.email }}</div>
           </div>
           <div class="flex flex-row mb-3">
               <div class="basis-1/3 font-bold">Description</div>
               <div class="basis-2/3">{{ place.description }}</div>
           </div>
           <div class="flex flex-row mb-3">
               <div class="basis-1/3 font-bold">Status</div>
               <div class="basis-2/3">
                   <div v-if="place.tsm_current_state === 'Active'">
                       <span
                           class="text-white text-xs font-semibold px-1.5 py-1 rounded bg-green-500 w-fit">Active</span>
                   </div>
                   <div v-else>
                       <span
                           class="text-white text-xs font-semibold px-1.5 py-1 rounded bg-red-600 w-fit">Inactive</span>
                   </div>
               </div>
           </div>
           <div class="flex flex-row mb-3">
               <div class="basis-1/3 font-bold">Place type</div>
               <div class="basis-2/3">{{ place.place_type_name }}</div>
           </div>
           <div class="flex flex-row mb-3">
               <div class="basis-1/3 font-bold">Location name</div>
               <div class="basis-2/3">{{ place.location?.name }}
                   <span v-if="place.location?.short_name">({{ place.location?.short_name}})</span></div>
           </div>
           <div class="flex flex-row mb-3">
               <div class="basis-1/3 font-bold">Location Address</div>
               <div class="basis-2/3">
                   {{ place.location?.address_1 }}<br>
                   {{ place.location?.postcode }}<br>
                   {{ place.location?.city_town }}
               </div>
           </div>
           <div class="flex flex-row mb-3" v-if="place.metadata">
               <div class="basis-1/3 font-bold"> Other Details:</div>
           </div>
           <div class="flex mb-3">
                <div class="basis-2/3">{{ place.metadata}}</div>
           </div>

      </template>
      <template #footer>
        <jet-button class="ml-4 justify-center" @click="closeModal"
        >Close</jet-button>
      </template>
    </jet-dialog-modal>
  </span>
</template>

<script>
import {defineComponent, ref} from "vue";
import JetButton from "@/Components/Button.vue";
import JetDialogModal from "@/Components/DialogModal.vue";
import JetLabel from "@/Components/Label.vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default defineComponent({
    props: {
        place: Object
    },

    components: {
        JetButton,
        JetDialogModal,
        JetLabel,
    },

    setup(props) {
        const updating = ref(false);

        const startUpdating = () => {
            updating.value = true;
        }

        const closeModal = () => {
            usePage().props.value.errors = {};
            updating.value = false;
        }

        return {
            startUpdating,
            closeModal,
            updating,
        }
    },
});
</script>
