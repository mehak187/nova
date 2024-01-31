<template>
    <div>
        <div class="flex items-center justify-end bg-gray-50 text-sm rounded-b-lg font-bold text-center pt-2">
            <button class="text-center px-4 py-2 bg-red-500 text-white rounded font-bold" @click="cancel()">Annuler</button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        shiftId: Number,
    },

    methods: {
        cancel() {
            axios.delete(`/bookings/${this.shiftId}`).then(({data}) => {
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
            }).catch(error => {
                document.location.href = '/history'
            })
        }
    }
}
</script>
