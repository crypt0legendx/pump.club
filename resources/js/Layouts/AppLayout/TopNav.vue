<script setup>
import { computed, ref } from "vue";

import { Link } from "@inertiajs/vue3";
import { BookOpenText, Plus } from "lucide-vue-next";
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
                <div class="hidden lg:flex lg:items-center lg:space-x-4">
                    <!-- Action Buttons -->
                    <PrimaryButton link href="/launch"
                        :class="[
                            'mr-2 rounded-full px-5 py-4 text-white hover:bg-transparent flex items-center gap-1 border border-white/10 text-sm',
                            isConnected ? 'bg-primary' : 'bg-transparent'
                        ]"
                    >
                        Create Coin
                        <Plus class="w-4 h-4" />
                    </PrimaryButton>
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
                   <PrimaryButton link href="/launch"
                            class="!rounded-none !rounded-l-full !bg-primary !border-0 grow justify-center text-white px-5 py-4">
                            Create Coin
                        </PrimaryButton>
                <button
                    @click="showHowItworks = !showHowItworks"
                    class="block px-3 py-2 rounded text-base font-medium text-gray-300 hover:text-primary hover:bg-gray-700"
                >
                    {{ $t("How it works") }}
                </button>
            </div>
            <!-- Mobile Social Links and Actions -->
            <div class="pt-4 pb-3 border-t border-gray-700">
                <div class="px-2 space-y-1">
                    <!-- Social Links -->
                    <div class="flex items-center space-x-2 px-3 py-2">
                        <PrimaryButton
                            size="sm"
                            outlined
                            iconMode
                        >
                            <XIcon class="w-4 h-4" />
                        </PrimaryButton>
                        <PrimaryButton
                            size="sm"
                            outlined
                            iconMode
                        >
                            <BookOpenText class="w-5 h-5" />
                        </PrimaryButton>
                        <PrimaryButton
                            size="sm"
                            outlined
                            iconMode
                        >
                            <TelegramIcon class="w-4 h-4" />
                        </PrimaryButton>
                    </div>
                    <!-- Action Buttons -->
                    <div class="space-y-2 px-3">
                        <PrimaryButton
                            size="sm"
                            link
                            href="/launch"
                            class="w-full justify-center"
                        >
                            {{ $t("Launch Meme") }}
                        </PrimaryButton>

                        <!-- Action Buttons -->
                        <Web3Auth
                            size="sm"
                            class="flex-col gap-2"
                            full
                        />
                        <AuthLink full />
                    </div>
                </div>
            </div>
        </div>
        <HowItWorksModal v-model:show="showHowItworks" />
    </nav>
</template>
