<template>
    <div ref="boundary" class="w-full h-full flex flex-col">
        <div class="p-4 pb-0 flex items-center">
            <input type="date" class="px-4 py-2 border border-70 rounded h-12 mr-2" v-model="date">

            <!-- <select class="px-2 py-2 border rounded h-12">
                <option value="">Bureau 4-6 personnes</option>
            </select> -->

            <!-- <button class="ml-auto bg-primary px-3 py-2 rounded text-white">Valider la réservation</button> -->
        </div>

        <div v-if="formData.startSlot" class="fixed h-screen w-screen flex items-center justify-center z-50" style="top:0;left:0">
            <div class="bg-black w-screen h-screen absolute opacity-25" style="top:0;left:0" @click="formData.startSlot = null"></div>

            <form class="relative w-full max-w-md bg-white shadow p-6 rounded-lg" @submit.prevent="submit()">
                <div class="flex justify-between mb-4">
                    <span class="font-bold">Réservation</span>

                    <svg class="w-6 h-6 text-200 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" @click="formData.startSlot = null">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>

                <div class="mb-4">
                    <label for="clientId" class="text-xs">Client</label>

                    <select name="clientId" id="clientId" class="px-4 py-2 border border-70 rounded block w-full" v-model="formData.client_id">
                        <option :value="null">-</option>
                        <option v-for="client in clients" :key="client.id" :value="client.id" v-text="client.full_name_reversed"></option>
                    </select>

                    <span v-if="'client_id' in errors" class="text-xs text-danger" v-text="errors.client_id[0]"></span>
                </div>

                <div class="flex -mx-2">
                    <div class="px-2 flex-1">
                        <label for="startTime" class="text-xs">Heure de début</label>

                        <select name="startTime" id="startTime" class="px-4 py-2 border border-70 rounded block w-full" v-model="formData.startSlot">
                            <option v-for="slot in formData.slots" :key="slot.key" :value="slot.key" v-text="slot.key" :disabled="slot.disabled"></option>
                        </select>

                        <span v-if="'conflict' in errors" class="text-xs text-danger" v-text="errors.conflict[0]"></span>
                    </div>

                    <div class="px-2 flex-1">
                        <label for="endSlot" class="text-xs">Heure de fin</label>

                        <select name="endSlot" id="endSlot" class="px-4 py-2 border border-70 rounded block w-full" v-model="formData.endSlot">
                            <option :value="null">-</option>
                            <option v-for="slot in endSlots" :key="slot.key" :value="slot.key" v-text="slot.key"></option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 mb-4">
                    <label for="note" class="text-xs">Note interne</label>

                    <textarea name="note" id="note" rows="3" class="p-2 border border-70 rounded block w-full" v-model="formData.note"></textarea>
                </div>

                <div class="flex items-center justify-between pt-6">
                    <button v-if="formData.shift_id" type="button" class="py-2 text-danger" @click="displayCancelConfirmation = true">Annuler la réservation</button>
                    <span v-else ></span>

                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Valider</button>
                </div>

                <div v-if="displayCancelConfirmation" class="bg-50 mt-4 -mx-6 -mb-6 rounded-b-lg p-6">
                    <span v-if="!formData.is_cancellable" class="text-sm italic">Attention, la réservation commence dans moins de 72h</span>

                    <div class="flex justify-between pt-2 text-center">
                        <button type="button" class="px-4 py-2 text-sm bg-primary rounded text-white" @click="cancelBooking()">Annuler en tant que client</button>
                        <button type="button" class="px-4 py-2 text-sm bg-primary rounded text-white" @click="forceCancelBooking()">Annuler en tant qu'admin</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="p-4">
            <div class="shadow rounded">
                <div class="flex w-full flex-1">
                    <div class="md:w-48 lg:w-60 bg-40 pt-8" :class="truncateWorkspaces ? 'w-32' : 'w-48'" @click="truncateWorkspaces = !truncateWorkspaces">
                        <div v-for="(workspace, index) in workspaces" :key="index" class="h-8 border-t border-b border-70 px-2 font-bold truncate flex items-center" v-text="workspace.name"></div>
                    </div>

                    <div class="flex-1 bg-white overflow-x-auto">
                        <div class="relative">
                            <div class="flex font-bold">
                                <div v-for="i in hours" :key="i" class="border border-l-0 border-70 h-8 w-32 flex-1 flex items-center justify-center" v-text="`${i}h`" style="min-width: 8rem"></div>
                            </div>
                        </div>

                        <div v-for="(workspace, index) in workspaces" :key="index" class="relative">
                            <div class="z-10 flex">
                                <div
                                    v-for="slot in workspace.slots.slice(0, -1)"
                                    :key="slot.key"
                                    class="flex-1 h-8 border border-70 w-8"
                                    style="min-width: 2rem"
                                    :data-shift-id="slot.shift_id ? slot.shift_id : null"
                                    @mouseover="hoverShift = slot.shift_id"
                                    @mouseout="hoverShift = null"
                                >
                                    <div
                                        v-if="slot.disabled"
                                        :name="`slot-${slot.shift_id}`"
                                        class="h-full -mx-px"
                                        :class="{
                                            'bg-primary': hoverShift !== slot.shift_id,
                                            'bg-primary-dark cursor-pointer' : hoverShift === slot.shift_id
                                        }"
                                        :content="slot.client_name"
                                        v-tippy="{ placement: 'top', boundary: $refs.boundary, arrow: true }"
                                        @click="slotClicked(workspace, slot)"
                                    >
                                        <div v-if="slot.note" class="w-full h-full" :content="slot.note" v-tippy="{ placement: 'bottom', boundary: $refs.boundary, arrow: true }"></div>
                                    </div>

                                    <div class="h-full w-full hover:bg-primary-50% cursor-pointer" @click="selectSlot(workspace, slot.key)"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'

export default {
    props: {
        clients: {}
    },

    data() {
        return {
            date: null,

            hours: ['08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19'],

            workspaces: [],

            hoverShift: null,

            formData: {
                client_id: null,
                shift_id: null,
                workspace_id: null,
                slots: [],
                startSlot: null,
                endSlot: null,
                is_cancellable: null,
                note: null,
            },

            truncateWorkspaces: true,

            displayCancelConfirmation: false,

            errors: {},
        }
    },

    watch: {
        date(d) {
            this.loadSchedule()
        },
    },

    computed: {
        endSlots() {
            let pos = this.formData.slots.findIndex(slot => slot.key === this.formData.startSlot) + 1

            let after = this.formData.slots.map(slot => {
                return {
                    shift_id: slot.shift_id,
                    date_time: slot.date_time,
                    disabled: slot.shift_id === this.formData.shift_id ? false : slot.disabled,
                    key: slot.key,
                }
            }).slice(pos)

            let latest = after.findIndex(slot => slot.disabled) + 1

            if (latest === 0) {
                latest = after.length
            }

            return after.slice(0, latest)
        }
    },

    methods: {
        loadSchedule() {
            Nova.request().get(`/nova-vendor/booking-calendar/schedule?date=${this.date}`)
                .then(({data}) => {
                    this.workspaces = data
                })
        },

        selectSlot(workspace, slotKey) {
            this.formData.client_id = null
            this.formData.shift_id = null
            this.formData.workspace_id = workspace.id
            this.formData.slots = workspace.slots
            this.formData.startSlot = slotKey
            this.formData.endSlot = null
            this.formData.is_cancellable = null
            this.formData.note = null
            this.displayCancelConfirmation = false
        },

        submit() {
            let startSlot = this.formData.slots.find(slot => slot.key === this.formData.startSlot)
            let endSlot = this.formData.slots.find(slot => slot.key === this.formData.endSlot)

            Nova.request().post('/nova-vendor/booking-calendar/store', {
                client_id: this.formData.client_id,
                shift_id: this.formData.shift_id,
                workspace_id: this.formData.workspace_id,
                start_date: startSlot.date_time,
                end_date: endSlot.date_time,
                note: this.formData.note,
            }).then(() => {
                this.formData = {
                    shift_id: null,
                    workspace_id: null,
                    slots: [],
                    startSlot: null,
                    endSlot: null,
                    note: null,
                }

                this.loadSchedule()
            }).catch(error => {
                this.errors = error.response.data.errors
            })
        },

        cancelBooking() {
            Nova.request().delete(`/nova-vendor/booking-calendar/shifts/${this.formData.shift_id}/delete`)
                .then(({data}) => {
                    this.formData = {
                        shift_id: null,
                        workspace_id: null,
                        slots: [],
                        startSlot: null,
                        endSlot: null,
                        note: null,
                    }

                    this.loadSchedule()
                })
        },

        forceCancelBooking() {
            Nova.request().delete(`/nova-vendor/booking-calendar/shifts/${this.formData.shift_id}/force-delete`)
                .then(({data}) => {
                    this.formData = {
                        shift_id: null,
                        workspace_id: null,
                        slots: [],
                        startSlot: null,
                        endSlot: null,
                        note: null,
                    }

                    this.loadSchedule()
                })
        },

        slotClicked(workspace, slot) {
            // get the first slot
            let firstIndex = workspace.slots.findIndex(s => s.shift_id === slot.shift_id)
            let lastIndex = workspace.slots.length - workspace.slots.slice().reverse().findIndex(s => s.shift_id === slot.shift_id)

            this.formData.client_id  = slot.client_id
            this.formData.shift_id  = slot.shift_id
            this.formData.workspace_id = workspace.id
            this.formData.slots = workspace.slots
            this.formData.startSlot = workspace.slots[firstIndex].key
            this.formData.endSlot = workspace.slots[lastIndex].key
            this.formData.is_cancellable = slot.is_cancellable
            this.formData.note = slot.note
            this.displayCancelConfirmation = false
        }
    },

    created() {
        this.date = moment().format('YYYY-MM-DD')

        setInterval(this.loadSchedule, 5000)
    }
}
</script>
