<template>
    <div>
        <div v-if="loading" class="w-full flex flex-wrap justify-center">
            <svg  class="animate-spin w-24 h-24 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>

            <span class="w-full text-center mt-2 text-gray-800">Chargement de la caméra ...</span>
        </div>

        <qrcode-stream
            v-if="!destroyed"
            :camera="camera"
            @init="onInit"
            @decode="decode"
        >
            <div v-if="!error && showScanConfirmation" class="w-full h-full flex items-center justify-center" style="background-color:rgba(255, 255, 255, 0.5)">
                <svg v-if="!success && !scanError" class="animate-spin w-24 h-24 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>

                <span v-if="success" class="inline-block w-24 h-24 bg-white rounded-full">
                    <svg class="w-24 h-24 text-green-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </span>

                <span v-if="scanError" class="inline-block w-24 h-24 bg-white rounded-full">
                    <svg class="w-24 h-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
        </qrcode-stream>

        <div v-if="scanError" class="text-lg text-center mt-2" v-text="scanError"></div>

        <div v-if="!!error" class="text-center">
            <div class="mb-4 text-lg" v-text="error"></div>

            <button class="px-4 py-2 bg-blue-500 border border-blue-600 rounded text-white" @click="reload()">Recharger la caméra</button>
        </div>
    </div>
</template>

<script>
import { QrcodeStream } from 'vue-qrcode-reader'

export default {
    components: { QrcodeStream },

    data() {
        return {
            loading: true,
            camera: 'auto',
            destroyed: false,
            content: null,
            error: null,
            scanError: null,
            showScanConfirmation: false,
            success: false,
        }
    },

    methods: {
        async decode(content) {
            this.pause()
            this.scanError = null
            this.success = false

            await this.timeout(100)

            try {
                await axios.post('/scan', { code: content })
                this.success = true
            } catch (error) {
                this.scanError = error.response.data.errors.code[0]
            }

            await this.timeout(5000)
            this.unpause()
        },

        unpause () {
            this.camera = 'auto'
        },

        pause () {
            this.camera = 'off'
        },

        timeout (ms) {
            return new Promise(resolve => {
                window.setTimeout(resolve, ms)
            })
        },

        async reload() {
            this.loading = true
            this.destroyed = true
            this.content = null
            this.error = null
            this.scanError = null

            await this.$nextTick()

            this.destroyed = false
        },

        async onInit(promise) {
            try {
                await promise
            } catch (error) {
                if (error.name === 'NotAllowedError') {
                    this.error = "L'accès à la caméra est refusé"
                } else if (error.name === 'NotFoundError') {
                    this.error = "Aucune caméra détectée"
                } else if (error.name === 'NotSupportedError') {
                    this.error = "HTTPS requis"
                } else if (error.name === 'NotReadableError') {
                    this.error = "La caméra est déjà en cours d'utilisation"
                } else if (error.name === 'OverconstrainedError') {
                    this.error = "Une erreur s'est produite avec la caméra"
                } else if (error.name === 'StreamApiNotSupportedError') {
                    this.error = "Votre navigateur n'est pas compabtible"
                } else if (error.name === 'InsecureContextError') {
                    this.error = "Veuillez accéder à l\'app en HTTPS";
                } else {
                    this.error = `ERROR: ${error.name}`;
                }
            } finally {
                this.showScanConfirmation = this.camera === "off"
                this.loading = false
            }
        }
    },
}
</script>
