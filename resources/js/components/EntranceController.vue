<template>
    <div class="text-center relative"
        style="width:200px;height:200px"
        @touchstart="startTouching"
        @mousedown="startTouching"
        @touchend="endTouching"
        @mouseup="endTouching"
    >
        <svg v-if="!sent" class="inline-block w-24 h-24" :class="!! timeout ? 'text-black' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
        </svg>

        <svg v-else class="inline-block w-24 h-24 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
        </svg>

        <svg class="absolute top-0 transform -rotate-90" style="margin-top:-25px" id="svg" width="200" height="200" viewPort="0 0 100 100" version="1.1" xmlns="http://www.w3.org/2000/svg">
            <circle ref="circle" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
            <circle ref="progress" id="bar" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="565.48"></circle>
        </svg>

        <p v-if="!sent" class="text-xs text-gray-500 disable-select">Touchez et maintenez<br>pour ouvrir</p>
        <p v-else class="text-xs text-gray-500 disable-select">Vous pouvez ouvrir la porte</p>
        <p v-if="displayCode" class="text-sm mt-12 text-gray-500 disable-select leading-tight">Si la porte ne s'ouvre pas, vous pouvez utiliser le code suivant: <strong class="block text-xl">F{{ code }}E</strong></p>
    </div>
</template>

<script>
export default {
    props: {
        code: String,
    },

    data() {
        return {
            timeout: null,
            sent: false,
            displayCode: false,
        }
    },

    methods: {
        startTouching() {
            if (this.sent) {
                return
            }

            this.sent = false
            this.$refs.progress.style.transition = 'stroke-dashoffset 1.5s ease-in';
            this.$refs.circle.style.transition = 'stroke-dashoffset 1.5s ease-in';
            this.$refs.progress.style.strokeDashoffset = 0;

            this.timeout = setTimeout(() => {
                this.submit()
            }, 1500)
        },

        endTouching() {
            this.$refs.progress.style.transition = 'none';
            this.$refs.circle.style.transition = 'none';
            this.$refs.progress.style.strokeDashoffset = 565.48;
            clearTimeout(this.timeout)
            this.timeout = null
        },

        submit() {
            axios.post('/entrance/open').then(() => {
                this.endTouching()

                this.sent = true
                this.displayCode = true

                setTimeout(() => {
                    this.sent = false
                }, 5000)

                setTimeout(() => {
                    this.displayCode = false
                }, 15000)
            }).catch(error => {
                this.endTouching()
                this.sent = false
            })
        }
    }
}
</script>

<style>
#svg circle {
    stroke-width: 1em;
}
#svg #bar {
    stroke: #fbce07;
}
</style>
