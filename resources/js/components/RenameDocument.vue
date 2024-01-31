<template>
    <span class="flex items-center font-medium text-sm leading-tight mb-1">
        <span v-if="!isEditing" class="mr-2">{{ newName }}</span>

        <input v-if="isEditing" type="text" ref="input" class="border rounded p-2 form-input w-32 mr-2" v-model="newName"/>

        <svg v-if="!isEditing" @click="isEditing = true; $nextTick(() => $refs.input.focus())" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
        </svg>

        <svg v-if="isEditing" @click="save" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
    </span>
</template>

<script>
export default {
    props: {
        id: Number,
        name: String,
    },

    data() {
        return {
            isEditing: false,
            newName: this.name,
        }
    },

    methods: {
        save() {
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
