<template>
    <div>
        <div class="fixed top-0 mx-6 md:mx-0 md:inset-x-1/3 z-50 mt-12 bg-white shadow rounded p-4 text-center flex items-center" v-if="display">
            <svg class="w-8 h-8 mr-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

            <div>L'un de vos shifts se termine dans moins de 15 minutes</div>

            <svg class="w-6 h-6 ml-auto text-gray-500 cursor-pointer" @click="hide()" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            display: false,

            interval: null,
        }
    },

    methods: {
        hide() {
            this.display = false

            clearInterval(this.interval)
        }
    },

    created() {
        axios.get('/shifts/alert')
                .then(({data}) => {
                    this.display = !!data.count
                })

        this.interval = setInterval(() => {
            axios.get('/shifts/alert')
                .then(({data}) => {
                    this.display = !!data.count
                })
        }, 30000);
    }
}
</script>
