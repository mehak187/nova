<template>
    <div class="bg-white p-4 rounded-lg shadow" style="width:33.333333%">
        <h2 class="flex-auto truncate text-90 font-normal text-sm mb-4">Total à payer</h2>
        <p class="flex items-start justify-start text-4xl text-80">
            <span class="inline-block text-xs">CHF</span>
            <span v-text="total"></span>

            <button
                class="self-end ml-auto text-xs text-white py-2 px-4 rounded bg-primary-500 leading-tight"
                @click="pay"
            >
                Marquer comme payé
            </button>
        </p>
    </div>
</template>

<script>
export default {
    props: ['resourceName', 'resourceId', 'panel'],

    data() {
        return {
            total: '-',
        }
    },

    methods: {
        load() {
            Nova.request().get(`/nova-vendor/open-invoices/${this.resourceId}/total`)
                .then(({data}) => {
                    this.total = data;
                })
        },

        pay() {
            if (confirm('Confirmer ?')) {
                Nova.request().post(`/nova-vendor/open-invoices/${this.resourceId}/pay`)
                    .then(() => {
                        this.load()
                    })
            }
        }
    },

    created() {
        this.load()
    }
}
</script>
