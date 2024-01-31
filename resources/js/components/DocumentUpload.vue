<template>
    <form>
        <label class="flex items-center justify-center bg-gray-50 rounded-lg shadow p-4 mb-4">
            <input type="file" class="hidden" ref="file" @change="upload" />

            <div class="mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>

            <div>
                <div class="font-medium">Ajouter un document</div>
                <div class="text-gray-500 text-sm">Formats acceptés: PDF, PNG, JPG, DOCX</div>
            </div>
        </label>

        <div v-if="!! progress" class="w-full mt-6">
            <div class="h-1 mb-4 flex rounded bg-gray-200">
                <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500" :style="`width:${progress}%`"></div>
            </div>
        </div>
    </form>
</template>

<script>
export default {
    data() {
        return {
            progress: 0
        }
    },

    methods: {
        upload() {
            const files = this.$refs.file.files

            if (files.length === 0) {
                return
            }

            const formData = new FormData()

            files.forEach(file => {
                formData.append('files[]', file)
            })

            axios.post(`/documents`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: progressEvent => {
                    this.progress = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100));
                }
            }).then(({data}) => {
                if (data.status === 'ok') {
                    document.location.reload()
                }
            }).catch(error => {
                this.emitter.emit('notify', {
                    title: 'Erreur',
                    message: error.response.data.errors.test[0],
                    color: 'red',
                })
            }).finally(() => {
                this.progress = 0
            })
        }
    }
}
</script>
