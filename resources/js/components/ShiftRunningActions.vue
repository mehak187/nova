<template>
    <div>
        <div v-if="displayModal" class="fixed top-0 left-0 h-screen w-screen flex items-center justify-center z-50">
            <div class="bg-black top-0 left-0 w-screen h-screen absolute opacity-25" @click="displayModal = false"></div>

            <div class="relative w-full max-w-md bg-white shadow p-6 rounded-lg">
                <div class="flex justify-between mb-4">
                    <span class="font-bold">Terminer</span>

                    <svg class="w-6 h-6 text-gray-200 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" @click="displayModal = false">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>

                <p class="mb-4">Souhaitez-vous quitter cet espace de travail ?</p>

                <div class="flex items-center justify-between">
                    <button class="px-4 py-2 bg-gray-400 text-white rounded" @click="displayModal = false">Non</button>
                    <button class="px-4 py-2 bg-green-500 text-white rounded" @click="close">Oui</button>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-center bg-gray-50 text-sm rounded-b-lg px-4 py-2 font-bold text-center">
            <button class="px-4 py-2 bg-green-500 text-white rounded" @click="displayModal = true">Terminer</button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        shiftId: Number,
    },

    data() {
        return {
            displayModal: false,
        }
    },

    methods: {
        close() {
            axios.post(`/bookings/${this.shiftId}/close`).then(({data}) => {
                if (data.status === 'success') {
                    this.displayModal = false

                    window.location.reload()
                } else if (data.status === 'error') {
                    this.emitter.emit('notify', {
                        title: 'Erreur',
                        message: data.message,
                        color: 'red',
                    })

                    this.displayModal = false

                    if (data.refresh) {
                        setTimeout(() => {
                            document.location.href = '/history'
                        }, 2000)
                    }
                }
            })
        }
    }
}
</script>
