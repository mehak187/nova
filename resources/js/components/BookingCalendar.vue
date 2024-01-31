<template>
    <div class="w-full h-full flex flex-col">
        <div class="p-4 pb-0 flex items-center">
            <input type="date" id="datePicker" class="px-4 py-2 border rounded h-12 mr-2" v-model="date" @input.prevent="checkSunday()">

            <span v-if="sundayAlert">Vous ne pouvez pas réserver de salles le dimanche.</span>

            <!-- <select class="px-2 py-2 border rounded h-12">
                <option value="">Bureau 4-6 personnes</option>
            </select> -->

            <!-- <button class="ml-auto bg-blue-500 px-3 py-2 rounded text-white">Valider la réservation</button> -->
        </div>

        <div v-if="displayForm" class="fixed top-0 left-0 h-screen w-screen flex items-center justify-center" style="z-index:101">
            <div class="bg-black top-0 left-0 w-screen h-screen absolute opacity-25" @click="displayForm = false"></div>

            <form v-if="formData.status === null || formData.status === 'booked'" class="relative w-full max-w-md bg-white shadow p-6 rounded-lg" @submit.prevent="submit()">
                <div class="flex justify-between mb-4">
                    <span class="font-bold">Réservation</span>

                    <svg class="w-6 h-6 text-gray-200 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" @click="displayForm = false">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>

                <p v-if="minuteFactor !== 1" class="text-xs text-gray-500 mb-4" v-text="`
                    Le calcul du temps pour cet espace de travail est multiplié par un facteur ${minuteFactor}.
                    Exemple pour une réservation de 60 minutes: 60 x ${minuteFactor} = ${minuteFactor * 60} minutes
                `"></p>

                <p v-if="isEvening" class="text-xs text-gray-500 mb-4" v-text="`
                    Le calcul du temps pour les réservations après 18h est multiplié par un facteur 1.25.
                    Exemple pour une réservation de 60 minutes: 60 x 1.25 = 75 minutes
                `"></p>

                <p v-if="isSaturday" class="text-xs text-gray-500 mb-4" v-text="`
                    Le calcul du temps pour les réservations le samedi est multiplié par un facteur 1.5.
                    Exemple pour une réservation de 60 minutes: 60 x 1.5 = 90 minutes
                `"></p>

                <div class="flex -mx-2">
                    <div class="px-2 flex-1">
                        <label for="startTime" class="text-xs font-medium">Heure de début</label>

                        <select :disabled="!!formData.shift_id && !formData.is_cancellable" name="startTime" id="startTime" class="px-4 py-2 border border-70 rounded block w-full" v-model="formData.started_at" required>
                            <option v-for="slot in availableSlots" :key="slot.value" :value="slot.value" v-text="slot.label" :disabled="slot.disabled"></option>
                        </select>
                    </div>

                    <div class="px-2 flex-1">
                        <label for="endSlot" class="text-xs font-medium">Heure de fin</label>

                        <select :disabled="!!formData.shift_id && !formData.is_cancellable" name="endSlot" id="endSlot" class="px-4 py-2 border border-70 rounded block w-full" v-model="formData.ended_at" required>
                            <option :value="null">-</option>
                            <option v-for="slot in endSlots" :key="slot.value" :value="slot.value" v-text="slot.label"></option>
                        </select>
                    </div>
                </div>

                <span class="block text-xs italic text-gray-500 mt-1" v-if="!!formData.shift_id && !formData.is_cancellable">Les modifications ne sont plus possibles dans les 72h, merci d’annuler et de réserver un nouveau créneaux</span>

                <div class="flex items-center justify-between pt-6">
                    <button v-if="formData.shift_id" type="button" :disabled="loading" class="py-2 text-red-500" @click="displayCancelConfirmation = true">Annuler la réservation</button>
                    <span v-else ></span>

                    <button v-if="!formData.shift_id || formData.is_cancellable" :disabled="loading" type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Valider</button>
                </div>

                <div v-if="displayCancelConfirmation" class="bg-gray-50 mt-4 -mx-6 -mb-6 rounded-b-lg p-6">
                    <span v-if="formData.is_cancellable" class="text-sm italic">L'annulation ne comporte aucun frais jusqu'à 72h avant le début. Souhaitez-vous vraiment annuler cette réservation ?</span>
                    <span v-if="!formData.is_cancellable" class="text-sm italic">La réservation commence dans moins de 72h, son annulation vous sera facturée 80%. Sans annulation de votre part, vous serez facturé 100%</span>

                    <div class="pt-2 text-center">
                        <button type="button" :disabled="loading" class="px-2 py-1 text-sm bg-red-500 rounded text-white" @click="cancelBooking()">Oui, annuler</button>
                    </div>
                </div>
            </form>

            <div v-if="formData.status === 'running'" class="relative w-full max-w-md bg-white shadow p-6 rounded-lg">
                <div class="flex justify-between mb-4">
                    <span class="font-bold">En cours</span>

                    <svg class="w-6 h-6 text-gray-200 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" @click="displayForm = false">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>

                <p>Vous utilisez actuellement cet espace de travail. Souhaitez-vous le quitter ?</p>

                <div class="flex items-center justify-center pt-6">
                    <button type="submit" :disabled="loading" class="px-4 py-2 bg-blue-500 text-white rounded" @click="closeShift(formData.shift_id)">Oui, terminer</button>
                </div>
            </div>
        </div>

        <div v-if="!sundayAlert" class="p-4">
            <div class="shadow rounded overflow-hidden">
                <div class="flex w-full flex-1">
                    <div class="md:w-48 lg:w-60 bg-white" :class="truncateWorkspaces ? 'w-32' : 'w-48'" @click="truncateWorkspaces = !truncateWorkspaces">
                        <div class="h-8 border-t border-b border-r"></div>
                        <div v-for="(workspace, index) in workspaces" :key="index" class="h-8 border-t border-b border-r px-2 font-semibold text-xs leading-8 truncate" v-text="workspace.name"></div>
                    </div>

                    <div class="flex-1 bg-white overflow-x-auto">
                        <div class="relative">
                            <div class="flex font-bold">
                                <div v-for="i in hours" :key="i" class="border border-l-0 h-8 w-32 flex-1 text-center" v-text="`${i}h`" style="min-width: 8rem"></div>
                            </div>
                        </div>

                        <div v-for="(workspace, index) in workspaces" :key="index" class="relative" style="min-width:112rem">
                            <div class="border-t border-b border-70 h-8 w-full flex">
                                <div
                                    v-for="count in 56"
                                    :key="count"
                                    class="flex-1 hover:bg-70 border-r border-70 cursor-pointer"
                                    :class="isPast(count * 15) ? 'bg-gray-100 pointer-events-none' : ''"
                                    @click="selectSlot(workspace, count * 15)"
                                ></div>

                                <div
                                    v-for="(shift, shiftIndex) in workspace.shifts"
                                    :key="`${shiftIndex}-shiftIndex`"
                                    class="absolute h-8"
                                    :style="`left:${shift.offset}%;width:${shift.duration}%`"
                                    @click="slotClicked(shift)"
                                >
                                    <div
                                        class="relative w-full h-full rounded-full -mt-px text-xs inline-flex items-center justify-center"
                                        :class="{
                                            'bg-blue-500 border border-blue-600 hover:bg-blue-600 cursor-pointer': shift.is_me && shift.is_reservation && shift.status !== 'finished',
                                            'bg-yellow-400 border border-yellow-600 cursor-not-allowed': shift.is_me && !shift.is_reservation && shift.status !== 'finished',
                                            'bg-blue-400 border border-blue-500 cursor-not-allowed': shift.is_me && shift.status === 'finished',
                                            'bg-gray-200 border border-gray-300 cursor-not-allowed': !shift.is_me,
                                        }"
                                    >
                                        <span v-text="shift.status_text"></span>
                                    </div>
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
    data() {
        return {
            date: null,
            hours: ['07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20'],
            workspaces: [],
            hoverShift: null,
            sundayAlert: false,
            formData: {},
            truncateWorkspaces: true,
            displayForm: false,
            displayCancelConfirmation: false,
            minuteFactor: 1,
            loading: false,
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
            let now = moment()
            let slots = []

            let shifts = this.workspaces.filter(workspace => workspace.id === this.formData.workspace_id)[0].shifts

            if (this.formData.shift_id) {
                shifts = shifts.filter(shift => shift.shift_id !== this.formData.shift_id)
            }

            while (startOfDay.isBefore(endOfDay)) {
                let isDisabled = startOfDay.isBefore(now)
                    || !!shifts.filter(shift => {
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

        isEvening() {
            if (this.isSaturday) {
                return false
            }

            return moment(this.formData.started_at).isSameOrAfter(moment(this.date).hour(18).minute(0).second(0))
                || moment(this.formData.ended_at).isSameOrAfter(moment(this.date).hour(18).minute(0).second(0))
        },

        isSaturday() {
            return moment(this.date).day() === 6
        },
    },

    methods: {
        resetFormData() {
            this.formData = {
                shift_id: null,
                workspace_id: null,
                started_at: null,
                ended_at: null,
                is_cancellable: null,
                status: null,
            }
        },

        isPast(offset) {
            return moment(this.date).hour(7).minute(0).second(0)
                .add(offset, 'minute')
                .isSameOrBefore(moment().add(15, 'minute'))
        },

        loadSchedule() {
            if (!this.sundayAlert) {
                axios.get(`/bookings/schedule?date=${this.date}`)
                    .then(({data}) => {
                        this.workspaces = data
                    })
            }
        },

        selectSlot(workspace, offset) {
            let start = moment(this.date).hour(7).minute(0).second(0).add(offset - 15, 'minutes')

            this.formData.shift_id = null
            this.formData.workspace_id = workspace.id
            this.formData.started_at = start.format('YYYY-MM-DD HH:mm:ss')
            this.formData.ended_at = null
            this.formData.is_cancellable = null
            this.formData.status = null

            this.displayForm = true
            this.displayCancelConfirmation = false
            this.minuteFactor = Number(workspace.minute_factor)
        },

        submit() {
            this.loading = true;

            axios.post('/bookings', {
                shift_id: this.formData.shift_id,
                workspace_id: this.formData.workspace_id,
                started_at: this.formData.started_at,
                ended_at: this.formData.ended_at,
            }).then(() => {
                this.loading = false;

                this.emitter.emit('notify', {
                    title: 'Réservation',
                    message: 'Votre réservation a bien été prise en compte',
                    color: 'green',
                })

                this.resetFormData()

                this.loadSchedule()

                this.displayForm = false
            }).catch(error => {
                this.loading = false;

                this.emitter.emit('notify', {
                    title: 'Réservation',
                    message: error.response.data.message,
                    color: 'red',
                })

                this.resetFormData()
                this.loadSchedule()

                this.displayForm = false
                this.displayCancelConfirmation = false
            })
        },

        cancelBooking() {
            this.loading = true;

            axios.delete(`/bookings/${this.formData.shift_id}`)
                .then(({data}) => {
                    this.loading = false;

                    this.emitter.emit('notify', {
                        title: 'Annulation',
                        message: data.message,
                        color: 'green',
                    })

                    this.resetFormData()

                    this.loadSchedule()

                    this.displayForm = false
                    this.displayCancelConfirmation = false
                }).catch(error => {
                    this.loading = false;

                    this.emitter.emit('notify', {
                        title: 'Annulation',
                        message: error.response.data.message,
                        color: 'red',
                    })

                    this.loadSchedule()

                    this.displayForm = false
                    this.displayCancelConfirmation = false
                })
        },

        closeShift(shiftId) {
            this.loading = true;

            axios.post(`/bookings/${shiftId}/close`).then(({data}) => {
                this.loading = false;

                this.emitter.emit('notify', {
                    title: 'Confirmation',
                    message: data.message,
                    color: 'green',
                })

                this.resetFormData()

                this.displayForm = false
                this.displayCancelConfirmation = false
            })
        },

        slotClicked(shift) {
            if (!shift.is_me || shift.status === 'finished') {
                console.log(shift)
                return;
            }

            if (moment(shift.started_at).isBefore(moment())) {
                this.emitter.emit('notify', {
                    title: 'Réservation',
                    message: 'Cette réservation a déjà commencé, vous ne pouvez plus la changer.',
                    color: 'red',
                })
                return;
            }

            this.formData.shift_id  = shift.shift_id
            this.formData.workspace_id = shift.workspace_id
            this.formData.started_at = shift.started_at
            this.formData.ended_at = shift.ended_at
            this.formData.is_cancellable = shift.is_cancellable
            this.formData.status = shift.status

            this.displayForm = true
            this.displayCancelConfirmation = false
            this.minuteFactor = Number(shift.minute_factor)
        },

        checkSunday() {
            this.sundayAlert = moment(this.date).day() === 0
        }
    },

    created() {
        this.resetFormData()

        this.date = moment().format('YYYY-MM-DD')

        this.checkSunday()
    },
}
</script>
