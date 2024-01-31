<template>
    <div class="relative">
        <div class="relative inline-flex" @click="open = !open">
            <svg v-if="includedMinutesAlert || purchasedMinutesAlert" class="w-6 h-6 text-red-500 -mr-2 -mt-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            <svg class="w-6 h-6 text-gray-700 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>

        <transition name="slide-fade">
            <div
                v-show="open"
                class="fixed h-screen w-screen top-0 left-0 overflow-hidden"
            >
                <div class="absolute w-full h-full bg-black opacity-10" @click="open = false"></div>

                <div class="fixed h-screen w-screen max-w-xs top-0 left-0 bg-white shadow flex flex-col overflow-y-auto">
                    <div class="flex items-center h-16 border-b bg-gray-100">
                        <span class="w-full block font-medium px-4" v-text="username"></span>
                        <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" @click="open = false"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>

                    <div class="mt-4 px-4">
                        <span class="block text-sm font-medium mb-2">Minutes incluses</span>

                        <div class="flex border rounded-lg">
                            <div class="w-1/2 p-2 text-center border-r">
                                <span class="block text-xs text-gray-600">Bureaux</span>
                                <span v-text="includedMinutesOffice"></span>
                            </div>

                            <div class="w-1/2 p-2 text-center">
                                <span class="block text-xs text-gray-600">Tables</span>
                                <span v-text="includedMinutesTable"></span>
                            </div>
                        </div>

                        <span v-if="includedMinutesAlert" class="flex items-center text-xs italic text-red-500 mt-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Il vous reste moins de 5h
                        </span>

                        <span class="block text-sm font-medium mt-6 mb-2">Minutes achetées</span>

                        <div class="flex border rounded-lg">
                            <div class="w-1/2 p-2 text-center border-r">
                                <span class="block text-xs text-gray-600">Bureaux</span>
                                <span v-text="purchasedMinutesOffice"></span>
                            </div>

                            <div class="w-1/2 p-2 text-center">
                                <span class="block text-xs text-gray-600">Tables</span>
                                <span v-text="purchasedMinutesTable"></span>
                            </div>
                        </div>

                        <span v-if="purchasedMinutesAlert" class="flex items-center text-xs italic text-red-500 mt-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Il vous reste moins de 10h
                        </span>
                    </div>

                    <ul class="mt-auto mb-6">
                        <li v-if="hasDoorAccess">
                            <a class="flex items-center px-5 py-2 hover:bg-gray-100" href="/entrance">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                Porte d'entrée
                            </a>
                        </li>

                        <li>
                            <a class="flex items-center px-5 py-2 hover:bg-gray-100" href="/documents">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>

                                Documents
                            </a>
                        </li>

                        <li v-if="displayNotifications">
                            <a class="flex items-center px-5 py-2 hover:bg-gray-100" href="/mail-notifications">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                                Notifications courrier
                                <span v-if="hasUnreadNotifications" class="ml-auto rounded-full bg-red-500 w-5 h-5 inline-flex items-center justify-center text-sm text-white">1</span>
                            </a>
                        </li>

                        <li>
                            <a class="flex items-center px-5 py-2 hover:bg-gray-100" href="/settings">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Paramètres
                            </a>
                        </li>

                        <li>
                            <a class="flex items-center px-5 py-2 hover:bg-gray-100" href="/support">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 011.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 00-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 010 9.424m-4.138-5.976a3.736 3.736 0 00-.88-1.388 3.737 3.737 0 00-1.388-.88m2.268 2.268a3.765 3.765 0 010 2.528m-2.268-4.796a3.765 3.765 0 00-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 01-1.388.88m2.268-2.268l4.138 3.448m0 0a9.027 9.027 0 01-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0l-3.448-4.138m3.448 4.138a9.014 9.014 0 01-9.424 0m5.976-4.138a3.765 3.765 0 01-2.528 0m0 0a3.736 3.736 0 01-1.388-.88 3.737 3.737 0 01-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 01-1.652-1.306 9.027 9.027 0 01-1.306-1.652m0 0l4.138-3.448M4.33 16.712a9.014 9.014 0 010-9.424m4.138 5.976a3.765 3.765 0 010-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 011.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 00-1.652 1.306A9.025 9.025 0 004.33 7.288" />
                                </svg>

                                Support
                            </a>
                        </li>

                        <li>
                            <form action="/logout" method="POST" class="inline">
                                <input type="hidden" name="_token" v-model="_csrf">
                                <button class="flex items-center w-full text-left px-5 py-2 hover:bg-gray-100" type="submit">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
export default {
    props: {
        csrf: String,
        username: String,
        purchasedMinutesTable: Number,
        purchasedMinutesOffice: Number,
        includedMinutesTable: Number,
        includedMinutesOffice: Number,
        hasDoorAccess: Boolean,
        displayNotifications: Boolean,
        hasUnreadNotifications: Boolean,
    },

    data() {
        return {
            open: false,
            _csrf: this.csrf,
        }
    },

    computed: {
        totalPurchased() {
            return this.purchasedMinutesOffice + this.purchasedMinutesTable
        },

        totalIncluded() {
            return this.includedMinutesOffice + this.includedMinutesTable
        },

        purchasedMinutesAlert() {
            return this.totalPurchased > 0 && this.totalPurchased < 600
        },

        includedMinutesAlert() {
            return this.totalIncluded > 0 && this.totalIncluded < 300
        },
    },

    methods: {
        toggle() {
            this.open = !this.open;
        }
    }
}
</script>

<style>
.slide-fade-enter-active {
  transition: all .3s ease;
}
.slide-fade-leave-active {
  transition: all .15s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to {
  transform: translateX(-10px);
  opacity: 0;
}
</style>
