<template>
    <div class="shadow rounded-lg overflow-hidden w-full bg-white mb-4">
        <div class="flex items-center justify-start bg-gray-50 rounded-t-lg p-4">
            <div class="flex-1">
                <span class="block font-medium text-sm leading-tight mb-1">
                    <span v-if="!isEditing" class="mr-2">{{ newName }}</span>

                    <input v-if="isEditing" type="text" ref="input" class="border rounded p-2 w-full mr-2" v-model="newName"/>
                </span>

                <div class="flex text-gray-500 text-sm">
                    <span>{{ lastUpdated }}</span>
                </div>
            </div>

            <div class="ml-auto flex items-center">
                <span v-if="!isEditing" class="flex flex-wrap justify-center" @click="isEditing = true, $nextTick(() => $refs.input.focus())">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>

                    <span class="mt-2 text-xs text-gray-500 w-full text-center">Renommer</span>
                </span>

                <span v-if="isEditing" class="flex flex-wrap justify-center" @click="rename">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>

                    <span class="mt-2 text-xs text-gray-500 w-full text-center">Enregistrer</span>
                </span>

                <a :href="`/documents/${id}/download`" class="flex flex-wrap justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>

                    <span class="mt-2 text-xs text-gray-500 w-full text-center">{{ humanReadableSize }}</span>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        id: Number,
        name: String,
        lastUpdated: String,
        humanReadableSize: String,
    },

    data() {
        return {
            isEditing: false,
            newName: this.name,
        }
    },

    methods: {
        rename() {
            axios.put(`/documents/${this.id}/rename`, {
                name: this.newName,
            }).then(({data}) => {
                if (data.status === 'ok') {
                    document.location.reload()
                }
            }).catch(error => {
                this.emitter.emit('notify', {
                    type: 'error',
                    message: error.response.data.message,
                })
            }).finally(() => {
                this.isEditing = false
            })
        },
    },
}
</script>
