<template>
    <!-- Purge whitelist
        border-green-200 bg-green-50 bg-green-50 text-green-800 text-green-400 text-green-500 hover:bg-green-100 hover:bg-green-100 focus:ring-offset-green-50 focus:ring-green-600
        border-red-200 bg-red-50 bg-red-50 text-red-800 text-red-400 text-red-500 hover:bg-red-100 hover:bg-red-100 focus:ring-offset-red-50 focus:ring-red-600
    -->
    <div style="z-index:100" class="absolute w-full md:max-w-sm mb-16 bottom-0 md:bottom-auto md:top-0 right-0 p-4 pointer-events-none">
        <div v-for="(notification, i) in notifications" :key="i">
            <div
                class="border rounded-md p-4 mb-2"
                :class="`border-${notification.color}-200 bg-${notification.color}-50`"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg :class="`h-5 w-5 text-${notification.color}-400`" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <div class="ml-3">
                        <p :class="`text-sm font-medium text-${notification.color}-800`" v-text="notification.message"></p>
                    </div>

                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button :class="`inline-flex bg-${notification.color}-50 rounded-md p-1.5 text-${notification.color}-500 hover:bg-${notification.color}-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-${notification.color}-50 focus:ring-${notification.color}-600`">
                                <span class="sr-only">Dismiss</span>

                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            notifications: [],
        }
    },

    created() {
        this.emitter.on('notify', n => {
            let notification = {
                title: n.title,
                message: n.message,
                color: n.color,
            }

            let timeout = setTimeout(() => {
                this.notifications.shift()

                clearTimeout(timeout)
            }, 4000)

            this.notifications.push(notification)
        })
    },
}
</script>
