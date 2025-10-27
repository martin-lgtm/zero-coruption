<template>
    <div class="min-h-screen flex flex-col bg-gradient-to-b from-gray-50 to-gray-100">
        <!-- HEADER -->
        <header class="bg-gray-900 text-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">

                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img src="../../images/multus.png" class="w-12 sm:w-14" alt="Multus Logo">
                    <h1 class="text-lg sm:text-xl font-bold tracking-wide">Multus</h1>
                </div>

                <!-- Hamburger Button -->
                <button @click="isOpen = !isOpen"
                    class="sm:hidden focus:outline-none focus:ring-2 focus:ring-yellow-400 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path v-if="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Desktop Nav -->
                <nav class="hidden sm:flex gap-6 text-sm font-medium">
                    <Link :href="route('map')" class="hover:text-yellow-400 transition">Мапа</Link>
                    <Link :href="route('about')" class="hover:text-yellow-400 transition">За нас</Link>
                    <Link href="/report" class="hover:text-yellow-400 transition">Пријави корупција</Link>
                </nav>
            </div>

            <!-- Mobile Dropdown -->
            <div v-if="isOpen" class="sm:hidden px-4 pb-4 space-y-2">
                <Link :href="route('map')" class="block hover:text-yellow-400 transition">Мапа</Link>
                <Link :href="route('about')" class="block hover:text-yellow-400 transition">За нас</Link>
                <Link href="/report" class="block hover:text-yellow-400 transition">Пријави корупција</Link>
            </div>
        </header>

        <main class="flex-1 max-w-4xl mx-auto py-12 px-6">
            <h1 class="text-3xl font-bold mb-4">Multus</h1>
            <p class="text-gray-700">
                Center for Education Culture and Activism Multus from Gevgelija is a local civil society organization,
                founded on December 3, 2018, from enthusiasts, media workers and artists, in order to act towards
                improvement in the respective fields contained in its name, to rise awareness and boost the critical
                thinking among the local and national targeted audience. The main focus of the activities and projects
                of Center Multus is Media and Digital Literacy, debunking disinformarion and gender perspectives. This
                is the only non-governmental organization in the region that deals mainly with this issue. We work with
                creators and social media influencers, aiming to improve the content the audience consumes through their
                feeds and to use that media space for educational purposes.
            </p>
            <p class="mt-4">
                The mission of the association is aimed at identifying social problems, developing socially innovative
                projects and linking them with individuals and groups. The goal is to encourage cultural and social
                awareness. The organization develops programs and models for strengthening young people and citizens’
                critical thinking, promotes active participation and individual responsibility for the future well-being
                of the whole society. Multus is a platform that connects people committed to transforming social,
                environmental and cultural challenges offering opportunities prosperity for the community.
            </p>
            <p class="mt-4">
                Through initiatives like this project, Multus continues its mission of translating complex media and
                digital literacy issues into accessible, relatable narratives. By addressing the evolving risks of media
                manipulation and digital exposure, the organization remains committed to equipping young people with the
                critical thinking skills necessary to navigate an increasingly digital world.
            </p>
        </main>

        <footer class="bg-gray-900 text-gray-400 mt-8">
            <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row justify-between items-center text-sm">
                <p>© 2025 Multus. All rights reserved.</p>

                <div class="flex gap-4 mt-4 md:mt-0">
                    <a target="_blank" href="https://www.facebook.com/MultusCentar/">
                        <img src="../../images/facebook.png" class="w-5" alt="">
                    </a>
                    <a target="_blank" href="https://www.instagram.com/centar.multus/">
                        <img src="../../images/instagram.png" class="w-5" alt="">
                    </a>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
const isOpen = ref(false);
import { ref } from "vue";
defineProps({
    title: String,
    description: String,
})
</script>
