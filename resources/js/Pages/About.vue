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
            <h1 class="text-3xl font-bold mb-4">Lorem ipsum</h1>
            <p class="text-gray-700">
                Lorem ipsum, dolor sit amet consectetur adipisicing elit. Harum sit itaque dicta facere nisi alias in
                commodi cum asperiores, quae, autem deserunt possimus omnis obcaecati accusamus vel magni natus
                praesentium, non rerum veritatis molestias totam. Eveniet deleniti magnam impedit culpa minima quia quo
                quam maiores sequi. Reprehenderit exercitationem placeat nulla eveniet doloribus, incidunt reiciendis
                magni quia eius, excepturi velit iste numquam, labore eum voluptate quos neque quisquam ut? Cumque fugit
                velit rerum aspernatur et amet eligendi ad inventore voluptatibus, aliquam sed totam ullam possimus
                iusto eius officiis eaque, voluptates impedit qui repudiandae laboriosam quaerat molestiae animi.
                Pariatur nam porro expedita.
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
