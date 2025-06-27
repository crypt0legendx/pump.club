<script setup>
import { computed, ref } from "vue";

import { Link, usePage } from "@inertiajs/vue3";
import { useAccount } from "@wagmi/vue";
import {
    BookOpenText,
    ChartPie,
    EllipsisVertical,
    House,
    MessageSquareMore,
    Plus,
    Video,
} from "lucide-vue-next";
import { TelegramIcon, XIcon } from "vue3-simple-icons";

import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import BaseButton from "@/Components/BaseButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import HowItWorksModal from "@/Layouts/AppLayout/HowItWorksModal.vue";
import Web3Auth from "@/Pages/Auth/Web3Auth.vue";

const isMobileMenuOpen = ref(false);
const showHowItworks = ref(false);
const { isConnected } = useAccount();

// Check if user is authenticated
const isAuthenticated = computed(() => !!usePage().props.auth.user);
</script>
<template>
    <nav class="py-5 md:py-5 px-2 md:px-10 !relative bg-black/30">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-full">
                <!-- Logo and Brand -->
                <Link class="flex items-center" href="/">
                <ApplicationLogo class="block h-full w-auto font-semibold" />
                </Link>
                <div class="flex-shrink-0 flex items-center lg:space-x-8">
                    <!-- Navigation Links - Desktop -->
                    <div class="hidden xl:flex lg:items-center lg:space-x-4">
                        <Link href="/"
                            class="text-gray-300 rounded-full hover:bg-white/10 px-5 py-4 text-sm font-medium flex items-center gap-2 bg-white/10">
                        <House class="w-4 h-4" />
                        {{ $t("Home") }}
                        </Link>
                        <Link href="/"
                            class="text-gray-300 rounded-full hover:bg-white/10 px-5 py-4 text-sm font-medium flex items-center gap-2">
                        <ChartPie class="w-4 h-4" />
                        {{ $t("Watchlist") }}
                        </Link>
                        <Link href="/"
                            class="text-gray-300 rounded-full hover:bg-white/10 px-5 py-4 text-sm font-medium flex items-center gap-2">
                        <Video class="w-4 h-4" />
                        {{ $t("Launch") }}
                        </Link>
                        <Link href="/"
                            class="text-gray-300 rounded-full hover:bg-white/10 px-5 py-4 text-sm font-medium flex items-center gap-2">
                        <MessageSquareMore class="w-4 h-4" />
                        {{ $t("Support") }}
                        </Link>
                    </div>
                </div>

                <!-- Right Side Actions -->
                <div class="hidden xl:flex lg:items-center lg:space-x-4">
                    <!-- Action Buttons - Only show when not authenticated -->
                    <Web3Auth />
                </div>

                <!-- Mobile menu button and actions -->
                <div class="flex xl:hidden items-center gap-2">
                    <div v-if="isConnected"
                        class="flex items-stretch rounded-full overflow-hidden border border-white/20 grow">
                        <Web3Auth class="grow" full />
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div v-show="isMobileMenuOpen" class="lg:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <Link href="/"
                    class="block px-5 py-4 rounded text-base font-medium text-gray-300 hover:text-primary hover:bg-gray-700">
                {{ $t("Home") }}
                </Link>
                <Link href="/"
                    class="block px-5 py-4 rounded text-base font-medium text-gray-300 hover:text-primary hover:bg-gray-700">
                {{ $t("Watchlist") }}
                </Link>
                <Link href="/"
                    class="block px-5 py-4 rounded text-base font-medium text-gray-300 hover:text-primary hover:bg-gray-700">
                {{ $t("Launch") }}
                </Link>
                <Link href="/"
                    class="block px-5 py-4 rounded text-base font-medium text-gray-300 hover:text-primary hover:bg-gray-700">
                {{ $t("Support") }}
                </Link>
            </div>
        </div>
        <HowItWorksModal v-model:show="showHowItworks" />
    </nav>
</template>
