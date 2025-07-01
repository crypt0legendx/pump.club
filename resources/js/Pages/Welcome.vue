<!-- eslint-disable import/order -->
<script setup>
import { computed, ref } from "vue";

import { router } from "@inertiajs/vue3";
import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
import {
    LoaderCircle,
    PencilLine,
    Plus,
    Search,
    Star,
    Trash2,
    TrendingUp,
    ArrowUp,
    Zap,
    Clock,
    CheckCircle,
} from "lucide-vue-next";

import BaseButton from "@/Components/BaseButton.vue";
import { Checkbox } from "@/Components/ui/checkbox";
import FormInput from "@/Components/FormInput.vue";
import Pagination from "@/Components/Pagination.vue";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from "@/Components/ui/carousel";
import { useLaunchpadsData } from "@/hooks/useLaunchpadsData";
import AppLayout from "@/Layouts/AppLayout.vue";
import HowItWorksModal from "@/Layouts/AppLayout/HowItWorksModal.vue";
import TrendingCard from "@/Components/TrendingCard.vue";
import BarButton from "@/Pages/Launchpads/BarButton.vue";
import IndexCard from "@/Pages/Launchpads/IndexCard.vue";
import { useChainId } from "@wagmi/vue";

const props = defineProps({
    launchpads: [Array, Object],
    top: Array,
    usdRates: [Array, Object],
    type: String,
});
const launchpadsList = computed(() => props.launchpads.data);
const launchpadsInfo = useLaunchpadsData(launchpadsList, props.usdRates);
const showHowItWorks = ref(false);
const filters = [
    { id: "trending", label: "Trending", icon: TrendingUp },
    { id: "top", label: "Top", icon: ArrowUp },
    { id: "rising", label: "Rising", icon: Zap },
    { id: "new", label: "New", icon: Clock },
    { id: "finalized", label: "Finalized", icon: CheckCircle },
];

const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");
const chainId = useChainId();

debouncedWatch(
    [search],
    ([search]) => {
        router.get(
            window.route("launchpads.index"),
            { search },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    },
    {
        maxWait: 700,
    },
);
const animate = ref(true);

const staticLaunchpads = ref(Array.from({ length: 11 }, (_, i) => ({
    id: i,
    contract: `0x${[...Array(40)].map(() => Math.floor(Math.random() * 16).toString(16)).join('')}`,
    name: 'Verse World',
    symbol: 'Verse',
    description: 'The artist formerly known as Kanye West has modified his name once more—this time to "Ye Ye," according to California business filings obtained by Page Six.',
    logo: i % 2 === 0 ? '/indexcard.png' : '/indexcard.png',
    market_cap_formatted: '$599.3M',
    msg_count: 746,
    createdAgo: new Date().toISOString(),
    profile_photo_url: '/indexcard.png',
    status: 'bonding'
})));

const staticTrending = ref(Array.from({ length: 10 }, (_, i) => ({
    id: i,
    contract: `0x${[...Array(40)].map(() => Math.floor(Math.random() * 16).toString(16)).join('')}`,
    name: 'Verse World',
    symbol: 'Verse',
    image: i % 2 === 0 ? '/trendingcard.png' : '/trendingcard.png',
    market_cap_formatted: '$599.3M',
    msg_count: 746,
})));

const sortOptions = [
    { value: 'featured', label: 'Featured' },
    { value: 'last_trade', label: 'Last trade' },
    { value: 'creation_time', label: 'Creation time' },
    { value: 'last_reply', label: 'Last reply' },
    { value: 'currently_live', label: 'Currently live' },
    { value: 'market_cap', label: 'Market cap' },
];

const categories = ref([
    { emoji: '🐱', label: 'Animal' },
    { emoji: '🇺🇸', label: 'Trump Musk' },
    { emoji: '🐶', label: 'Viral Pets' },
    { emoji: '🌙', label: 'Moon Madness' },
    { emoji: '😸', label: 'Captain Meowrica' },
    { emoji: '🐱', label: 'Animal Vlogger' },
    { emoji: '🇺🇸', label: 'Trump Musk' },
    { emoji: '🌙', label: 'Moon Madness' },
    { emoji: '😸', label: 'Captain Meowrica' },
    { emoji: '🐱', label: 'Animal Vlogger' },
    { emoji: '🇺🇸', label: 'Trump Musk' },
    { emoji: '🌙', label: 'Moon Madness' },
    { emoji: '😸', label: 'Captain Meowrica' },
    { emoji: '🐱', label: 'Animal Vlogger' },
    { emoji: '🇺🇸', label: 'Trump Musk' },
    { emoji: '🌙', label: 'Moon Madness' },
    { emoji: '😸', label: 'Captain Meowrica' },
]);

const selectedTab = ref('all');
const showAddModal = ref(false);

// Modal state for managing lists
const newListName = ref('');
const lists = ref([
  { name: 'Main list', default: true },
  { name: 'List #1', default: false },
  { name: 'List #2', default: false },
]);

function addList() {
  if (newListName.value.trim()) {
    lists.value.push({ name: newListName.value.trim(), default: false });
    newListName.value = '';
  }
}
function deleteList(idx) {
  if (!lists.value[idx].default) lists.value.splice(idx, 1);
}
function editList(idx) {
  // Placeholder for edit logic
  const newName = prompt('Rename list', lists.value[idx].name);
  if (newName) lists.value[idx].name = newName;
}

const sortedTrendings = computed(() => {
    return launchpadsInfo.launchpads.value.slice().sort((a, b) => Number(b.marketCap) - Number(a.marketCap));
});

const selectedFilter = ref(props.type || 'trending');
</script>

<template>
    <AppLayout compact>
        <template #header>
            <div class="hidden md:flex items-center w-full bg-black h-12 relative overflow-x-hidden bg-black/10">
                <div class="flex w-full items-center overflow-x-auto [scrollbar-width:none]">
                    <div class="flex w-full items-center">
                        <BarButton v-for="(launch, i) in top" :key="launch.id" :launch="launch" :active="i === 0" />
                    </div>
                </div>
                <div
                    class="h-12 w-20 absolute right-0 pointer-events-none bg-gradient-to-r from-transparent via-gray-850/50 to-gray-850">
                </div>
            </div>
        </template>
        <div class="grid my-4 lg:my-8 mx-5 lg:mx-10">
            <div v-if="type === 'mine'" class="flex flex-col justify-center">
                <h3 class="flex items-center">
                    <LoaderCircle v-if="launchpadsInfo.loading.value" class="w-6 h-6 mr-2 animate-spin" />
                    {{ $t("My Launchpads") }}
                </h3>
                <div class="flex items-center mt-4 gap-4">
                    <BaseButton link href="/launch" outlined>
                        {{ $t("Launch a new token") }}
                    </BaseButton>
                </div>
            </div>
            <template v-else>
                <div class="flex flex-col gap-4 justify-center items-center mt-10">
                    <h1 class="text-5xl lg:text-6xl font-normal text-white text-center">
                        {{ $t("Find a meme. Pump it. Enjoy") }}
                    </h1>
                    <div class="flex flex-col gap-4 w-full md:w-1/2 lg:w-1/3">
                        <FormInput v-model="search" class="ml-auto mr-auto w-full" size="md"
                            placeholder="Search for meme">
                            <template #lead>
                                <Search class="w-4 h-4 ml-1 text-gray-400" />
                            </template>
                            <template #trail>
                                <PrimaryButton size="xs"
                                    class="rounded-full text-white hover:bg-transparent px-4 py-1.5 cursor-pointer"
                                    style="background: linear-gradient(to right, #6C2801 0%, #DA5200 34%, #E97C02 100%);">
                                    {{ $t("Search") }}
                                </PrimaryButton>
                            </template>
                        </FormInput>
                    </div>
                </div>
                <div class="my-8 overflow-hidden">
                    <Carousel class="w-full" :opts="{
                        align: 'start',
                    }">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-3xl font-semibold text-white">
                                Now trending
                            </h2>
                            <div class="flex items-center gap-2">
                                <CarouselPrevious
                                    class="relative top-auto left-auto right-auto bottom-auto translate-x-0 translate-y-0 text-white " />
                                <CarouselNext
                                    class="relative top-auto left-auto right-auto bottom-auto translate-x-0 translate-y-0 text-white" />
                            </div>
                        </div>
                        <CarouselContent>
                            <CarouselItem v-for="item in sortedTrendings" :key="item.id" class="!basis-auto">
                                <TrendingCard :launchpad="item" />
                            </CarouselItem>
                        </CarouselContent>
                    </Carousel>
                </div>
                <div class="my-8 overflow-hidden">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-3xl font-semibold text-white">
                            Explore
                        </h2>
                    </div>
                    <div class="flex justify-between items-center mt-4 pt-4 pb-4 border-b border-b-white/10">
                        <div class="flex items-center w-full md:w-auto justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <button
                                    :class="[
                                        'px-4 md:px-8 py-2 md:py-4 text-sm font-medium rounded-full',
                                        selectedTab === 'all' ? 'text-white bg-gray-800/50 border border-zinc-700/50' : 'text-gray-500 bg-gray-800/50 border border-zinc-700/50'
                                    ]"
                                    @click="selectedTab = 'all'"
                                >
                                    All
                                </button>
                                <button
                                    :class="[
                                        'px-4 md:px-8 py-2 md:py-4 text-sm font-medium rounded-full flex items-center gap-1',
                                        selectedTab === 'watchlist' ? 'text-white bg-gray-800/50 border border-zinc-700/50' : 'text-gray-500 bg-gray-800/50 border border-zinc-700/50'
                                    ]"
                                >
                                    <Star class="w-4 h-4" />
                                    Watchlist
                                </button>
                            </div>
                                <div class="text-sm text-gray-500 flex items-center gap-2">
                                    Sort by:
                                    <Select v-model="selectedFilter" @update:model-value="(value) => router.get(route('launchpads.index', { type: value === 'trending' ? '' : value }))">
                                        <SelectTrigger class="w-[120px] md:w-[140px]">
                                            <SelectValue>
                                                <div class="flex items-center gap-2">
                                                    {{ filters.find(f => f.id === selectedFilter)?.label }}
                                                </div>
                                            </SelectValue>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem v-for="filter in filters" :key="filter.id" :value="filter.id">
                                                    <div class="flex items-center gap-2">
                                                        {{ filter.label }}
                                                    </div>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                            </div>
                        </div>
                        <div class="items-center gap-4 hidden md:flex">
                            <label class="flex items-center text-sm text-white gap-2">
                                <Checkbox id="animations" />
                                Animations
                            </label>
                            <label class="flex items-center text-sm text-white gap-2">
                                <Checkbox id="nsfw" />
                                NSFW
                            </label>
                        </div>
                    </div>
                    <template v-if="selectedTab === 'all'">
                        <div v-if="launchpadsInfo.launchpads.value.length > 0">
                            <Carousel class="w-full mt-4 mb-4" :opts="{
                                align: 'start',
                            }">
                                <CarouselContent>
                                    <CarouselItem v-for="(category, index) in categories" :key="index"
                                        class="!basis-auto pl-4">
                                        <button
                                            class="flex items-center gap-2 py-2 md:py-4 px-2 md:px-3 text-white bg-gray-800/50 text-sm border border-zinc-700/50 rounded-full hover:bg-zinc-800 whitespace-nowrap">
                                            <span>{{ category.emoji }}</span>
                                            <span>{{ category.label }}</span>
                                        </button>
                                    </CarouselItem>
                                </CarouselContent>
                                <CarouselPrevious
                                    class="absolute top-0 left-0 translate-x-0 translate-y-0 text-white border-none h-full !bg-transparent" />
                                <CarouselNext
                                    class="absolute top-0 right-0 translate-x-0 translate-y-0 text-white border-none h-full !bg-transparent" />
                            </Carousel>
                        </div>
                    </template>
                </div>
                <div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        <IndexCard v-for="(launchpad, index) in launchpadsInfo.launchpads.value" :key="index"
                            :launchpad="launchpad" />
                    </div>
                </div>
            </template>
            <LoaderCircle v-if="type !== 'mine' && launchpadsInfo.loading.value"
                class="w-8 mt-5 text-white h-8 mr-2 animate-spin" />
            <Pagination :meta="launchpads.meta" />
            <HowItWorksModal v-model:show="showHowItWorks" />
        </div>
    </AppLayout>
    <!-- Add Modal -->
    <template v-if="showAddModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50">
            <div class="bg-gray-900 rounded-2xl p-10 w-full max-w-fit relative shadow-xl border border-white/10">
                <button class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl" @click="showAddModal = false">&times;</button>
                <h2 class="text-3xl font-normal text-white text-center mb-2">Manage lists</h2>
                <div class="text-gray-400 text-center mb-6">Create new lists or manage your existing lists here</div>
                <div class="border-t border-white/10 mb-6"></div>
                <div class="space-y-3 max-h-[300px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-gray-900 pr-1">
                  <!-- Input field at the top -->
                  <div class="flex items-center gap-2 px-4 py-3 bg-transparent">
                    <input
                      v-model="newListName"
                      type="text"
                      placeholder="Name list"
                      class="flex-1 bg-transparent border border-zinc-700/50 rounded-xl px-4 py-2 text-white placeholder-gray-500 outline-none focus:border-primary"
                    />
                    <button
                      @click="addList"
                      class="bg-gradient-to-r from-orange-400 to-yellow-500 text-white font-semibold rounded-xl px-6 py-2 disabled:opacity-50"
                      :disabled="!newListName.trim()"
                    >Add</button>
                  </div>
                  
                  <!-- Existing lists -->
                  <template v-for="(list, idx) in lists" :key="idx">
                    <div class="flex items-center justify-between bg-[#232326] rounded-xl px-4 py-3">
                      <span class="text-white font-medium">{{ list.name }}</span>
                      <div class="flex items-center gap-2">
                        <button v-if="!list.default" @click="editList(idx)" class="text-gray-400 hover:text-primary">
                          <PencilLine class="w-4 h-4" />
                        </button>
                        <button v-if="!list.default" @click="deleteList(idx)" class="text-red-500 hover:text-red-700">
                          <Trash2 class="w-4 h-4" />
                        </button>
                      </div>
                    </div>
                  </template>
                </div>
            </div>
        </div>
    </template>
</template>
