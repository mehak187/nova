<template>
    <div ref="boundary" class="w-full h-full flex flex-col">
        <div class="p-4 pb-0 flex items-center justify-between">
            <input type="date" class="px-4 py-2 border rounded h-12 mr-2" v-model="date">

            <!-- <select class="px-2 py-2 border rounded h-12">
                <option value="">Bureau 4-6 personnes</option>
            </select> -->

            <!-- <button class="ml-auto bg-primary px-3 py-2 rounded text-white">Valider la réservation</button> -->
            <div class="ml-auto inline-flex items-center">
                <span><strong>Code porte:</strong> <span v-text="code.code"></span></span>

                <button class="mx-2" @click="generateNewCode">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </button>
            </div>

            <button class="ml-4 bg-primary-500 font-semibold px-3 py-2 rounded text-white" @click="openDoor">Ouvrir la porte</button>
        </div>

        <div v-if="displayForm" class="fixed h-full w-full flex items-center justify-center z-50 top-0 left-0">
            <div class="bg-black w-full h-full absolute opacity-25 top-0 left-0" @click="displayForm = false"></div>

            <form class="relative w-full max-w-md bg-white shadow p-6 rounded-lg" @submit.prevent="submit()">
                <div class="flex justify-between mb-4">
                    <span class="font-bold">Réservation</span>

                    <svg class="w-6 h-6 text-200 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" @click="displayForm = false">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>

                <div class="mb-4">
                    <label for="clientId" class="text-xs">Client</label>

                    <select name="clientId" id="clientId" class="px-4 py-2 border rounded block w-full" v-model="formData.client_id">
                        <option :value="null">-</option>
                        <option v-for="client in clients" :key="client.id" :value="client.id" v-text="client.full_name_reversed"></option>
                    </select>

                    <span v-if="'client_id' in errors" class="text-xs text-red-500" v-text="errors.client_id[0]"></span>
                </div>

                <div class="flex -mx-2">
                    <div class="px-2 flex-1">
                        <label for="startTime" class="text-xs">Heure de début</label>

                        <select v-if="!formData.started_at || availableSlots.map(slot => slot.value).indexOf(formData.started_at) > -1" name="startTime" id="startTime" class="px-4 py-2 border rounded block w-full" v-model="formData.started_at">
                            <option v-for="slot in availableSlots" :key="slot.value" :value="slot.value" v-text="slot.label" :disabled="slot.disabled"></option>
                        </select>

                        <input v-if="!!formData.started_at && availableSlots.map(slot => slot.value).indexOf(formData.started_at) === -1" type="text" class="px-4 py-2 border rounded block w-full cursor-not-allowed" style="height:2.6rem;" :value="moment(formData.started_at).format('HH:mm')" disabled />

                        <span v-if="'conflict' in errors" class="text-xs text-red-500" v-text="errors.conflict[0]"></span>
                    </div>

                    <div class="px-2 flex-1">
                        <label for="endSlot" class="text-xs">Heure de fin</label>

                        <select v-if="!formData.ended_at || endSlots.map(slot => slot.value).indexOf(formData.ended_at) > -1" name="endSlot" id="endSlot" class="px-4 py-2 border rounded block w-full" v-model="formData.ended_at">
                            <option :value="null">-</option>
                            <option v-for="slot in endSlots" :key="slot.value" :value="slot.value" v-text="slot.label"></option>
                        </select>

                        <input v-if="!!formData.ended_at && endSlots.map(slot => slot.value).indexOf(formData.ended_at) === -1" type="text" class="px-4 py-2 border rounded block w-full cursor-not-allowed" style="height:2.6rem;" :value="moment(formData.ended_at).format('HH:mm')" disabled />
                    </div>
                </div>

                <div class="mt-4 mb-4">
                    <label for="note" class="text-xs">Note interne</label>

                    <textarea name="note" id="note" rows="3" class="p-2 border rounded block w-full" v-model="formData.note"></textarea>
                </div>

                <div class="flex items-center justify-between pt-6">
                    <button v-if="formData.shift_id" type="button" class="py-2 text-red-500" @click="displayCancelConfirmation = true">Annuler la réservation</button>
                    <span v-else ></span>

                    <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded">Valider</button>
                </div>

                <div v-if="displayCancelConfirmation" class="bg-50 mt-4 -mx-6 -mb-6 rounded-b-lg p-6">
                    <span v-if="!formData.is_cancellable" class="text-sm italic">Attention, la réservation commence dans moins de 72h</span>

                    <div class="flex justify-between pt-2 text-center">
                        <button type="button" class="px-4 py-2 text-sm bg-primary-500 rounded text-white" @click="cancelBooking()">Annuler en tant que client</button>
                        <button type="button" class="px-4 py-2 text-sm bg-primary-500 rounded text-white" @click="forceCancelBooking()">Annuler en tant qu'admin</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="p-4">
            <div class="shadow rounded">
                <div class="flex w-full flex-1">
                    <div class="md:w-48 lg:w-60 pt-8" :class="truncateWorkspaces ? 'w-32' : 'w-48'" @click="truncateWorkspaces = !truncateWorkspaces">
                        <div class="h-8 border-t border-b px-2 font-bold truncate flex items-center bg-gray-100"></div>
                        <div v-for="(workspace, index) in workspaces" :key="index" class="h-8 border-t border-b px-2 font-bold truncate flex items-center bg-gray-100" v-text="workspace.name"></div>
                    </div>

                    <div class="flex-1 bg-white overflow-x-auto">
                        <div class="relative"  style="min-width:112rem">
                            <div class="flex font-bold">
                                <div v-for="i in hours" :key="i" class="border border-l-0 h-8 flex-1 flex items-center justify-center bg-gray-100" v-text="`${i}h`"></div>
                            </div>
                        </div>

                        <div v-for="(workspace, index) in workspaces" :key="index" class="relative" style="min-width:112rem">
                            <div class="border-t border-b border-70 h-8 w-full flex">
                                <div
                                    v-for="count in 56"
                                    :key="count"
                                    class="flex-1 hover:bg-70 border-r cursor-pointer"
                                    @click="selectSlot(workspace, count * 15)"
                                ></div>

                                <div
                                    v-for="(shift, index) in workspace.shifts"
                                    :key="index"
                                    class="absolute h-8"
                                    :style="`left:${shift.offset}%;width:${shift.duration}%`"
                                    :content="shift.client_name"
                                    v-tippy="{ placement: 'top', boundary: $refs.boundary, arrow: true }"
                                    @click="slotClicked(shift)"
                                >
                                    <div
                                        v-if="!shift.note"
                                        class="relative w-full h-full rounded-full -mt-px text-xs inline-flex items-center justify-center truncate text-white border"
                                        :class="
                                            (shift.is_reservation
                                                ? shift.status === 'running' ? 'bg-primary-500 border-red-500 hover:bg-primary-600 cursor-pointer' : 'bg-primary-500 border-primary-500 hover:bg-primary-600 cursor-pointer'
                                                : 'bg-info-500 border-info-500 cursor-not-allowed'
                                            )
                                        "
                                    >
                                        <span class="truncate" v-text="shift.client_name"></span>
                                    </div>

                                    <div
                                        v-if="shift.note"
                                        class="relative w-full h-full rounded-full -mt-px text-xs inline-flex items-center justify-center truncate text-white border"
                                        :class="
                                            (shift.is_reservation
                                                ? shift.status === 'running' ? 'bg-primary-500 border-red-500 hover:bg-primary-600 cursor-pointer' : 'bg-primary-500 border-primary-500 hover:bg-primary-600 cursor-pointer'
                                                : 'bg-info-500 border-info-500 cursor-not-allowed'
                                            )
                                        "
                                        :content="shift.note"
                                        v-tippy="{ placement: 'bottom', boundary: $refs.boundary, arrow: true }"
                                    >
                                        <span class="truncate" v-text="shift.client_name"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4">
            <h2 class="text-xl font-medium mb-3">Clients en alerte</h2>

            <div class="-mx-4">
                <table class="table w-full table-default">
                    <thead>
                        <tr>
                            <th class="bg-gray-100 px-4 py-2 text-left">Client</th>
                            <th class="bg-gray-100 px-4 py-2">Crédits (tables)</th>
                            <th class="bg-gray-100 px-4 py-2">Crédits (bureaux)</th>
                            <th class="bg-gray-100 px-4 py-2">235 (tables)</th>
                            <th class="bg-gray-100 px-4 py-2">235 (bureaux)</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="client in clientsOnAlert" :key="client.id">
                            <td class="px-4 py-2 border-t bg-gray-100">
                                <a :href="`/cp/resources/clients/${client.id}`" class="no-underline block hover:text-primary-500 font-semibold">
                                    <span v-text="`${client.first_name} ${client.last_name}`"></span>
                                </a>
                            </td>
                            <td class="px-4 py-2 border-t text-center" v-text="client.purchased_minutes_table"></td>
                            <td class="px-4 py-2 border-t text-center" v-text="client.purchased_minutes_office"></td>
                            <td class="px-4 py-2 border-t text-center" v-text="client.included_minutes_table"></td>
                            <td class="px-4 py-2 border-t text-center" v-text="client.included_minutes_office"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'

export default {
    props: {
        clients: {},
        clientsOnAlert: {},
    },

    data() {
        return {
            date: null,

            hours: ['07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20'],

            workspaces: [],

            hoverShift: null,

            formData: {},

            truncateWorkspaces: true,

            displayForm: false,

            displayCancelConfirmation: false,

            errors: {},

            code: {},
        }
    },

    watch: {
        date(d) {
            this.loadSchedule()
        },
    },

    computed: {
        availableSlots() {
            let startOfDay = moment(this.date).hour(7).minute(0).second(0)
            let endOfDay = moment(this.date).hour(20).minute(45).second(0)
            let slots = []

            let shifts = this.workspaces.filter(workspace => workspace.id === this.formData.workspace_id)[0].shifts

            if (this.formData.shift_id) {
                shifts = shifts.filter(shift => shift.shift_id !== this.formData.shift_id)
            }

            while (startOfDay.isBefore(endOfDay)) {
                let isDisabled = /*startOfDay.isBefore(now)
                    || */!!shifts.filter(shift => {
                        return moment(shift.started_at).isSameOrBefore(startOfDay)
                            && moment(shift.ended_at).isSameOrAfter(startOfDay)
                    }).length

                slots.push({
                    value: startOfDay.format('YYYY-MM-DD HH:mm:ss'),
                    label: startOfDay.format('HH:mm'),
                    disabled: isDisabled,
                })

                startOfDay.add(15, 'minute')
            }

            return slots
        },

        endSlots() {
            let from = moment(this.formData.started_at)
            let slots = []

            for (let slot of this.availableSlots) {
                if (moment(slot.value).isAfter(from)) {
                    slots.push(slot)

                    if (slot.disabled) {
                        break
                    }
                }
            }

            return slots
        },
    },

    methods: {
        openDoor() {
            Nova.request().get('/nova-vendor/booking-calendar/open-door')
        },

        generateNewCode() {
            Nova.request().get('/nova-vendor/booking-calendar/generate-new-code')
                .then(response => {
                    this.code = response.data ? response.data : {}
                })
        },

        loadAccessCode() {
            Nova.request().get('/nova-vendor/booking-calendar/get-access-code')
                .then(response => {
                    this.code = response.data ? response.data : {}
                })
        },

        moment(value) {
            return moment(value)
        },

        loadSchedule() {
            Nova.request().get(`/nova-vendor/booking-calendar/schedule2?date=${this.date}`)
                .then(({data}) => {
                    this.workspaces = data
                })
        },

        selectSlot(workspace, offset) {
            let start = moment(this.date).hour(7).minute(0).second(0).add(offset - 15, 'minutes')

            this.formData.client_id = null
            this.formData.shift_id = null
            this.formData.workspace_id = workspace.id
            this.formData.started_at = start.format('YYYY-MM-DD HH:mm:ss')
            this.formData.ended_at = null
            this.formData.is_cancellable = null
            this.formData.note = null

            this.displayForm = true
            this.displayCancelConfirmation = false
        },

        resetFormData() {
            this.formData = {
                client_id: null,
                shift_id: null,
                workspace_id: null,
                started_at: null,
                ended_at: null,
                is_cancellable: null,
                note: null,
            }
        },

        submit() {
            Nova.request().post('/nova-vendor/booking-calendar/store', this.formData)
                .then(() => {
                    this.resetFormData()

                    this.loadSchedule()

                    this.displayForm = false
                }).catch(error => {
                    this.errors = error.response.data.errors
                })
        },

        cancelBooking() {
            Nova.request().delete(`/nova-vendor/booking-calendar/shifts/${this.formData.shift_id}/delete`)
                .then(({data}) => {
                    this.resetFormData()

                    this.loadSchedule()

                    this.displayForm = false
                })
        },

        forceCancelBooking() {
            Nova.request().delete(`/nova-vendor/booking-calendar/shifts/${this.formData.shift_id}/force-delete`)
                .then(({data}) => {
                    this.resetFormData()

                    this.loadSchedule()

                    this.displayForm = false
                })
        },

        slotClicked(shift) {
            if (!shift.is_reservation) {
                return
            }

            if (shift.status === 'finished') {
                window.open(`/cp/resources/shifts/${shift.shift_id}`, '_blank').focus();

                return;
            }

            this.formData.client_id  = shift.client_id
            this.formData.shift_id  = shift.shift_id
            this.formData.workspace_id = shift.workspace_id
            this.formData.started_at = shift.started_at
            this.formData.ended_at = shift.ended_at
            this.formData.is_cancellable = shift.is_cancellable
            this.formData.note = shift.note

            this.displayForm = true
            this.displayCancelConfirmation = false
        }
    },

    created() {
        this.resetFormData()

        this.date = moment().format('YYYY-MM-DD')

        setInterval(this.loadSchedule, 5000)

        this.loadAccessCode()
    }
}
</script>

<style>
.bg-info-500 {
    background-color: #3490dc;
}

.border-info-500 {
    border-color: #3490dc;
}
</style>
