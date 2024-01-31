<template>
    <div v-if="show" class="fixed bottom-0 right-0 mb-24 px-6 w-full md:w-auto z-40">
        <button class="w-full inline-flex items-center bg-white shadow rounded-lg" @click="addToHomeScreen()">
            <div class="py-4 pl-4 pr-4 bg-blue-500 rounded-l-lg text-white">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2h2m3-4H9a2 2 0 00-2 2v7a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-1m-1 4l-3 3m0 0l-3-3m3 3V3"></path></svg>
            </div>

            <div class="px-4 font-bold">Ajouter l'App à l'écran d'accueil</div>
        </button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            show: false,
        }
    },

    methods: {
        addToHomeScreen() {
            this.show = false

            this.deferredPrompt.prompt();
        }
    },

    created() {
        let addBtn = document.querySelector('.add-button')

        window.addEventListener('beforeinstallprompt', (deferredPrompt) => {
            this.show = true

            deferredPrompt.preventDefault()

            this.deferredPrompt = deferredPrompt
        });
    }
}
</script>
