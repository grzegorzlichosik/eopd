<template>
    <span>
      <span @click="startUpdating">
        <slot/>
      </span>
        <form>
      <jet-dialog-modal :show="updating" modal-id="user" @close="closeModal">
        <template #title> Edit Flow </template>
        <template #content>
          <div class="grid grid-cols-1 gap-2 p-6">

              <div class="mt-4">
                  <JetLabel for="name" value="Name" mandatory="true"/>
                  <jet-input
                      id="name"
                      v-model="form.name"
                      autocomplete="objective"
                      autofocus
                      name="name"
                      class="mt-1 block w-full"
                      type="text"
                  />
                  <JetInputError :message="form.errors.name" class="mt-2"/>
              </div>
              <div class="mt-4">
                  <JetLabel for="objective" value="Objective" mandatory="true"/>
                  <jet-input
                      id="objective"
                      v-model="form.objective"
                      autocomplete="objective"
                      name="objective"
                      class="mt-1 block w-full"
                      type="text"
                  />
                  <JetInputError :message="form.errors.objective" class="mt-2"/>
              </div>
              <table class="mt-4">
                  <tr class="text-left">
                      <th class="pb-2">Channel type</th>
                      <th class="pb-2">Max. Participants</th>
                      <th class="pb-2">Default</th>
                      <th class="pb-2">Auto Confirm</th>
                  </tr>
                  <tr v-for="(item, index) of form.channels" :key="index" class="pb-2">
                      <td class="pb-2">
                          <jet-checkbox
                              class="mr-1"
                              name="channels"
                              v-model="form.channels[index].is_enabled"
                              :checked="form.channels[index].is_enabled"
                              @click="checkIfEnabled(index)"
                          /> {{ item.name }}
                      </td>
                      <td class="pb-2">
                          <jet-input
                              name="maxParticipants[]"
                              class="block w-16 pr-0 py-1"
                              type="number"
                              min="1"
                              max="99"
                              pattern="[0-9]+"
                              :disabled="!form.channels[index].is_enabled"
                              v-model="form.channels[index].max_participants"
                              :class="{'opacity-25': !form.channels[index].is_enabled}"
                              onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 49 && event.charCode <= 57))"
                          />
                      </td>
                      <td class="pb-2">
                          <jet-checkbox
                              class="mr-1"
                              name="channels"
                              v-model="form.channels[index].is_default"
                              :checked="form.channels[index].is_default"
                              :disabled="!form.channels[index].is_enabled || checkIsDefault(index)"
                              :class="{'opacity-25': !form.channels[index].is_enabled || checkIsDefault(index)} "
                          />
                      </td>
                      <td class="pb-2">
                          <jet-checkbox
                              class="mr-1"
                              name="channels"
                              v-model="form.channels[index].is_auto_confirm"
                              :checked="form.channels[index].is_auto_confirm"
                              :disabled="!form.channels[index].is_enabled"
                              :class="{'opacity-25': !form.channels[index].is_enabled}"
                          />
                      </td>
                  </tr>
              </table>
              <JetInputError :message="form.errors.channels" class="mt-2"/>
          </div>
        </template>
        <template #footer>
          <jet-secondary-button class="ml-4 justify-center" @click="closeModal"
          >Cancel</jet-secondary-button>
          <jet-button
              dusk="edit_flow"
              class="ml-4 justify-center"
              @click="update"
          >
            Update
          </jet-button>
        </template>
      </jet-dialog-modal>
            </form>
    </span>
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
import {useForm, usePage} from "@inertiajs/inertia-vue3";
import JetCheckbox from "@/Components/Checkbox.vue";

export default defineComponent({
    props: {
        flow: Object,
        channelTypes: Array,
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
        JetInputError,
        JetCheckbox,
    },

    setup(props) {
        const updating = ref(false);
        const form = useForm({
            name: null,
            objective: null,
            channels: ref([]),
        });


        const startUpdating = () => {
            updating.value = true;
            form.name = props.flow.name;
            form.objective = props.flow.objective;
            props.channelTypes.forEach((type, index) => {
                let temp = Object.assign({}, props.flow.channels.find(value => value.type.uuid === type.uuid) || {});
                temp['name'] = type.name;
                temp['channelTypeUuid'] = type.uuid;
                temp['is_enabled'] = temp.uuid ? true : false;
                temp['max_participants'] = temp['max_participants'] ? temp['max_participants'] : 0;
                temp['is_auto_confirm'] = temp['is_auto_confirm'] ? true : false;
                temp['is_default'] = temp['is_default'] ? true : false;
                form.channels[index] = temp
            })
        }

        const update = () => {
            form
                .transform((data) => {
                    data.channels = data.channels.map(channel => {
                        if (channel.is_enabled === false) {
                            channel.max_participants = 0;
                        }
                        return channel;
                    })

                    return data;
                })
                .put(route("admin.flows.update", {uuid: props.flow.uuid}), {
                    preserveState: true,
                    onSuccess: (res) => {
                        closeModal();
                    },
                });
        }

        const closeModal = () => {
            form.clearErrors();
            updating.value = false;
        }

        const checkIsDefault = (index) => {
            let isDefault = form.channels.findIndex(e => e.is_default === true)
            return isDefault !== index && isDefault > -1;
        }

        const checkIfEnabled = (index) => {
            let value = form.channels[index].is_enabled
            if (value) {
                form.channels[index].is_default = false;
                form.channels[index].max_participants = 0;
                form.channels[index].is_auto_confirm = false;
            } else {
                form.channels[index].max_participants = 1;
            }
        }

        const checkValue = (index) => {
            if(form.channels[index].max_participants === '0'){
                form.channels[index].max_participants = 1;
            }
        }

        return {
            startUpdating,
            update,
            closeModal,
            checkIsDefault,
            checkIfEnabled,
            checkValue,
            updating,
            form,
        }
    },
});
</script>

<style>
.participants {
    height: 25px;
    width: 6.5rem;
}
</style>
