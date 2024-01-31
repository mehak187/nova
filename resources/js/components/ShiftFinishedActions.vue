<template>
    <div>
        <div class="flex items-center justify-between bg-gray-50 text-sm rounded-b-lg p-4 font-bold text-center">
            <div v-text="`Montant: CHF ${amount.toFixed(2)}`"></div>

            <div v-if="paidAt">
                <svg class="w-6 h-6 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <div v-else>
                <a :href="`/shifts/${shiftId}/pay`" class="inline-block px-4 py-2 bg-green-500 text-white rounded font-semibold">Payer</a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        shiftId: Number,
        amount: Number,
        paidAt: String,
    },
}
</script>
