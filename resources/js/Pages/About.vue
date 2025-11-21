<template>
    <div class="min-h-screen flex flex-col bg-gradient-to-b bg-white">
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
                    <Link href="/report" class="hover:text-yellow-400 transition">Сподели приказна</Link>
                </nav>
            </div>

            <!-- Mobile Dropdown -->
            <div v-if="isOpen" class="sm:hidden px-4 pb-4 space-y-2">
                <Link :href="route('map')" class="block hover:text-yellow-400 transition">Мапа</Link>
                <Link :href="route('about')" class="block hover:text-yellow-400 transition">За нас</Link>
                <Link href="/report" class="block hover:text-yellow-400 transition">Сподели приказна</Link>
            </div>
        </header>

        <main class="flex-1 max-w-4xl mx-auto py-12 px-6">
            <h1 class="text-3xl font-bold mb-4">За Мултус</h1>
            <p class="text-gray-700">
                Центар за едукација, култура и активизам „Мултус“ од Гевгелија е локална
                граѓанска организација, основана на 3 декември 2018 година, од ентузијасти,
                медиумски работници и уметници, со цел да дејствува кон подобрување во
                соодветните области содржани во неговото име, да ја подигне свеста и да го
                поттикне критичкото размислување кај локалната и националната целна публика.
                Главниот фокус на активностите и проектите на Центарот „Мултус“ се
                медиумската и дигиталната писменост, разоткривањето на дезинформациите и
                родовите перспективи. Ова е единствената невладина организација во регионот
                која се занимава главно со ова прашање. Работиме со креатори и инфлуенсери на
                социјалните медиуми, со цел да ја подобриме содржината што публиката ја
                консумира преку своите канали и да го користиме тој медиумски простор за
                образовни цели.
            </p>

            <p class="text-gray-700 mt-4">
                Преку иницијативи како овој проект, Мултус ја продолжува својата мисија за
                преведување на сложени поими и дигитална писменост во достапни, релативни
                наративи. Организацијата останува посветена на опремување на младите луѓе со
                вештини за критичко размислување потребни за да се снајдат во сè повеќе
                дигиталниот свет.
            </p>
            <!-- <p class="mt-4">
                Мисијата на здружението е насочена кон идентификување на социјалните проблеми, развивање на социјално
                иновативни проекти и нивно поврзување со поединци и групи. Целта е да се поттикне културна и социјална
                свест. Организацијата развива програми и модели за зајакнување на критичкото размислување на младите
                луѓе и граѓаните, промовира активно учество и индивидуална одговорност за идната благосостојба на целото
                општество. „Мултус“ е платформа која ги поврзува луѓето посветени на трансформирање на социјалните,
                еколошките и културните предизвици, нудејќи можности за просперитет за заедницата.
            </p>
            <p class="mt-4">
                Преку иницијативи како овој проект, Мултус ја продолжува својата мисија за преведување на сложени
                медиумски и дигитална писменост во достапни, релативни наративи. Со справување со еволуирачките ризици
                од медиумска манипулација и дигитална изложеност, организацијата останува посветена на опремување на
                младите луѓе со вештини за критичко размислување потребни за да се снајдат во сè повеќе дигиталниот
                свет.
            </p> -->
            <h1 class="text-3xl font-bold my-4">За проектот</h1>
            <p class="text-gray-700 my-4">
                Проектот „Под маса: Претпрдажба на интегритетот“ има за цел да ја подигне
                свеста за корупцијата и штетата од ситниот поткуп. Како дел од овој проект, Мултус
                реализираше кампања со Диверзија со 6 хумористични видеа за подигање на
                свеста од штетите од ситниот поткуп, а оваа активност – демо веб страна за
                сподеување лични искуства со корупција во Југоисточниот микрорегион е
                активност за мобилизација. Ова е дигитална архива на приказни, а методот се
                вика Банка на приказни. Банка на приказни е алатка за собирање и складирање
                лични искуства на одредена тема, во случајот корупција, за да им даде колективна
                сила, глас и приказ.
                Погледете го краткото објаснувачко видео за тоа што е Банка на приказни.
            </p>

            <div class="w-full max-w-3xl mx-auto mt-6 rounded-xl overflow-hidden shadow-lg">
                <iframe class="w-full h-64 sm:h-80 md:h-96" src="https://www.youtube.com/embed/5O9N9boA-GY"
                    title="YouTube video player" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>

            <p class="text-gray-700 my-10">
                А овде може да го погледнете краткото објаснувачко видео за како да се користи
                токму оваа банка на приказни, за искуства со корупција во југоисточен
                микрорегион.
                Мапата е интерактивна и комуницира со формуларот.
                Веб страната е во демо верзија, но, споделените искуства се реални и покажуваат
                критични жаришта на корупција во општините каде се собирале приказните и
                искуствата.
            </p>


            <div class="w-full max-w-3xl mx-auto mt-6 rounded-xl overflow-hidden shadow-lg">
                <iframe class="w-full h-64 sm:h-80 md:h-96" src="https://www.youtube.com/embed/6fPiix_Vmng?si=ERpkU1P7C2cPvRK5"
                    title="YouTube video player" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>

        </main>


        <footer class="bg-gray-900 text-gray-400 mt-8">
            <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row justify-between items-center text-sm">
                <p>Оваа активност се спроведува во рамки на проектот „Под маса: претпродба на
                    интегритетот“ кој го спроведува Центар за едукација, култура и активизам Мултус.
                    Проектот е дел од програмата за мали грантови на СЕЛДИ која се спроведува во
                    рамки на проектот „Граѓанско општество за добро владеење и антикорупција во
                    Југоисточна Европа: Градење на капацитети за застапување врз база на докази,
                    влијание врз политики и граѓански ангажман (СЕЛДИ.нет)“, финансиран од
                    Европската Унија. Оваа содржина е единствена одговорност на Центар за
                    едукација, култура и активизам Мултус и не нужно ги одразува ставовите на
                    Евопската Унија и СЕЛДИ.</p>


            </div>
            <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row justify-between items-center">
                <img class="w-72" src="../../images/Funded_by_EU-removebg-preview.png" alt="">
                <img class="w-[120px]" src="../../images/eu with you copy.png" alt="">
                <img class="w-[190px]" src="../../images/Seldi_Logo-removebg-preview.png" alt="">
                <img class="w-[240px]" src="../../images/MULTUS_final_Artboard_11_copy_2-removebg-preview.png" alt="">
            </div>
            <div class="max-w-7xl mx-auto px-4 py-4 border-t border-gray-700 text-center text-sm">
                <p>© 2025 Multus. All rights reserved.</p>
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
