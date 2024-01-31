<template>
    <card class="min-h-screen">
        <!-- <booking-calendar-view v-if="!card.v2" :clients="card.clients"></booking-calendar-view> -->
        <BookingCalendarV2
            :clients="card.clients"
            :clientsOnAlert="card.clientsOnAlert"
        ></BookingCalendarV2>
    </card>
</template>

<script>
import BookingCalendarV2 from './BookingCalendarV2.vue'

export default {
    components: {
        BookingCalendarV2,
    },

    props: [
        'card',

        // The following props are only available on resource detail cards...
        // 'resource',
        // 'resourceId',
        // 'resourceName',
    ],

    mounted() {
        //
    },
}
</script>
