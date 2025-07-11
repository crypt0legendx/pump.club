<script setup>
import { computed, ref, onMounted } from "vue";

import { FunnelIcon } from "@heroicons/vue/24/outline";
import { Link, router } from "@inertiajs/vue3";
import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
import { ChartBarIncreasing, CircleAlert, EyeOff, GripVertical, House, Inbox, PackagePlus, PlayIcon, Plus, Search, Settings, X } from "lucide-vue-next";

import AdvancedCard from "./AdvancedCard.vue";
import Web3Auth from "../Auth/Web3Auth.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ScrollArea } from "@/Components/ui/scroll-area";
import { Select, SelectContent, SelectItem, SelectTrigger } from "@/Components/ui/select";
import { Separator } from "@/Components/ui/separator";
import { Switch } from "@/Components/ui/switch";
import { useLaunchpadsData } from "@/hooks/useLaunchpadsData";
import useLocalStorage from '@/hooks/useLocalStorage';
import { TRADE_SETTINGS } from "@/store/constants";
import axios from "axios";

const props = defineProps({
  launchpads: [Array, Object],
  top: Array,
  usdRates: [Array, Object],
  type: String,
});

const presetsLists = [
  {
    name: "Preset 1",
    value: "p1",
  },
  {
    name: "Preset 2",
    value: "p2",
  },
  {
    name: "Preset 3",
    value: "p3",
  }
];

const defaultColumns = [
  { title: "Newly Created", subtitle: "newly created", enabled: true },
  { title: "About To Graduate", subtitle: "about to graduate", enabled: true },
  { title: "Graduated", subtitle: "graduated", enabled: true },
  { title: "Watchlist", subtitle: "watchlist", enabled: false },
  { title: "Featured", subtitle: "featured", enabled: false },
];

const footerHeight = ref(76);
const footerRef = ref(null);
const selectedPreset = ref(presetsLists[0].value);
const addColumnModal = ref(false);
const tempColumnManage = ref([]);
const columnManage = useLocalStorage('advanced_column_manage', defaultColumns);
const tradeSettingsModal = ref(false);
const tradeTab = ref('buy');
const tradePreset = ref('p1');
const slippage = ref(20);
const priority = ref(0.001);
const bribe = ref(0.001);
const autoBribe = ref(true);
const autoBribeMax = ref(0.01);
const mevProtection = ref(true);
const launchpadsList = computed(() => props.launchpads?.data || []);
const launchpadsInfo = useLaunchpadsData(launchpadsList, props.usdRates);
const searchModal = ref(false);
const searchedLaunchpads = ref([]);

const params = useUrlSearchParams("history");
const search = ref(params.search ?? "");

debouncedWatch(
  [search],
  ([search]) => {
    if (search.trim()) {
      axios.get(window.route("advanced.search"), { params: { search: search.trim() } })
        .then(response => {
          searchedLaunchpads.value = response.data;
        });
    } else {
      searchedLaunchpads.value = [];
    }
  },
  {
    maxWait: 700,
  },
);

function resetAllColumns() {
  tempColumnManage.value.forEach((col, idx) => {
    col.enabled = idx < 3;
  });
}

function saveColumns() {
  columnManage.value = JSON.parse(JSON.stringify(tempColumnManage.value));
  addColumnModal.value = false;
}

function openColumnModal() {
  tempColumnManage.value = JSON.parse(JSON.stringify(columnManage.value));
  addColumnModal.value = true;
}

function openTradeSettings() {
  tradeSettingsModal.value = true;
}

function setTradeTab(tab) {
  tradeTab.value = tab;
}

function setTradePreset(preset) {
  tradePreset.value = preset;
}

function closeTradeSettings() {
  tradeSettingsModal.value = false;
}

function openSearchModal() {
  searchModal.value = true;
}

function closeSearchModal() {
  searchModal.value = false;
}

// Computed property for enabled columns
const enabledColumns = computed(() => {
  if (!columnManage.value || !Array.isArray(columnManage.value)) {
    return defaultColumns.filter(col => col.enabled);
  }
  return columnManage.value.filter(col => col.enabled === true);
});

const getWatchlistContracts = () => {
  try {
    const stored = localStorage.getItem('watchLists');
    if (stored) {
      return JSON.parse(stored).map(item => item.c_address);
    }
  } catch { }
  return [];
};

// Computed property: map column title to filtered launchpads
const launchpadsByColumn = computed(() => {
  const map = {};
  const watchlistContracts = getWatchlistContracts();
  enabledColumns.value.forEach(col => {
    let filtered;
    if (col.title === 'Newly Created') {
      filtered = launchpadsInfo.launchpads.value.filter(lp => (lp.status || '').toLowerCase() === 'prebond');
    } else if (col.title === 'About To Graduate') {
      filtered = launchpadsInfo.launchpads.value.filter(lp => (lp.status || '').toLowerCase() === 'bonding');
    } else if (col.title === 'Graduated') {
      filtered = launchpadsInfo.launchpads.value.filter(lp => (lp.status || '').toLowerCase() === 'finalized');
    } else if (col.title === 'Watchlist') {
      filtered = launchpadsInfo.launchpads.value.filter(lp => watchlistContracts.includes(lp.contract));
    } else {
      filtered = [];
    }
    map[col.title] = filtered;
  });
  return map;
});

onMounted(() => {
  if (!localStorage.getItem('advanced_column_manage')) {
    localStorage.setItem('advanced_column_manage', JSON.stringify(defaultColumns));
  }
});

onMounted(() => {
  if (footerRef.value) {
    navHeight.value = navRef.value.offsetHeight;
  }
});

</script>

<template>
  <div class="flex flex-col home-bg h-screen">
    <!-- Ticker Bar -->
    <div class="w-full border-b border-white/10 py-0 px-2 flex overflow-x-auto whitespace-nowrap gap-6"
      style="min-height: 38px;">
      <div v-for="i in 8" :key="i" class="flex items-center gap-2 min-w-max">
        <img src="/indexcard.png" class="w-5 h-5 rounded-full" />
        <span class="text-white font-semibold text-xs">PNDA</span>
        <span v-if="i % 2 === 0" class="flex items-center text-xs text-red-400 font-semibold">
          <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
          </svg>
          0.7838 AVAX
        </span>
        <span v-else class="flex items-center text-xs text-green-400 font-semibold">
          <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
          </svg>
          0.7838 AVAX
        </span>
        <span class="text-white/60 text-xs">@kevin_91</span>
      </div>
    </div>
    <div class="flex flex-row items-center gap-2 py-1 px-2 border-b border-white/10">
      <div class="flex flex-row items-center gap-1">
        <House class="w-3.5 h-3.5 text-primary" />
        <span class="text-primary text-xs">scan</span>
      </div>
      <button class="flex flex-row items-center gap-1 border border-white/10 rounded-lg px-2 py-1"
        @click="openSearchModal">
        <Search class="w-3.5 h-3.5 text-white/50" />
        <span class="text-white/50 text-xs">search</span>
      </button>
      <Separator orientation="vertical" class="h-4" />
    </div>

    <!-- End Ticker Bar -->
    <div class="flex flex-row items-center gap-3 py-2 px-2 border-b border-white/10">
      <!-- Quick Buy -->
      <!-- Amount + Settings -->
      <div class="flex items-center">
        <div class="relative flex items-center bg-white/4 rounded-lg">
          <div class="flex items-center">
            <button class="flex items-center gap-2 text-white/80 whitespace-nowrap text-sm font-normal px-2 py-1">
              <img src="/avax-icon.svg" class="w-4 h-4" />
              Quick Buy
            </button>
          </div>
          <FormInput size="sm" class="w-24" type="number" inputClasses="!bg-transparent">
            <template #trail>
              <button class="flex items-center" @click="openTradeSettings">
                <Settings class="w-4 h-4 ml-1 text-gray-400" />
              </button>
            </template>
          </FormInput>
        </div>
      </div>
      <!-- Dropdown -->
      <div class="flex items-center gap-1 bg-white/4 rounded-lg px-3 py-2 cursor-pointer">
        <Select v-model="selectedPreset">
          <SelectTrigger
            class="bg-transparent border-none shadow-none p-0 h-auto w-auto focus:ring-0 focus:outline-none">
            <span class="text-white/80 text-sm capitalize">{{ selectedPreset }}</span>
          </SelectTrigger>
          <SelectContent class="w-40 min-w-0 right-0 left-auto mt-2 bg-black/40">
            <SelectItem v-for="list in presetsLists" :key="list.value" :value="list.value">
              {{ list.name }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>
      <!-- Hidden -->
      <button class="flex items-center gap-1 text-white/40 text-sm cursor-pointer">
        <EyeOff class="w-4 h-4" />
        Hidden
      </button>
      <!-- Add columns -->
      <button class="flex items-center gap-1 text-white/40 text-sm cursor-pointer" @click="openColumnModal">
        <PackagePlus class="w-4 h-4" />
        Add columns
      </button>
      <!-- Legend -->
      <button class="flex items-center gap-1 text-white/40 text-sm cursor-pointer">
        <CircleAlert class="w-4 h-4" />
        Legend
      </button>
    </div>

    <div class="flex flex-row w-full py-5">
      <div v-for="(col, index) in enabledColumns" :key="col.title"
        class="flex flex-col border-r border-r-white/10 last:border-r-0"
        :style="{ width: `${100 / enabledColumns.length}%` }">
        <div class="flex flex-row items-center justify-between gap-2 px-2 py-1">
          <div class="flex flex-row items-center gap-2">
            <GripVertical class="w-4 h-4 text-white/50 cursor-pointer" />
            <span class="text-white font-medium text-lg">{{ col.title }}</span>
            <FunnelIcon class="w-4 h-4 text-white cursor-pointer" />
          </div>
          <button class="text-white/50 hover:text-white transition-colors bg-[#da540061] rounded-xl py-1 px-2">
            <PlayIcon class="w-4 h-4 fill-white" />
          </button>
        </div>
        <div class="flex flex-col w-full">
          <ScrollArea class="w-full overflow-hidden p-2" :style="{ height: `calc(100vh - 205px - ${footerHeight}px)` }">
            <div v-if="launchpadsByColumn[col.title].length > 0" class="flex flex-col w-full">
              <AdvancedCard v-for="(launchpad, idx) in launchpadsByColumn[col.title]" :key="idx"
                :launchpad="launchpad" />
            </div>
            <div v-else class="flex flex-col items-center justify-center bg-white/10 rounded-lg p-4 w-full gap-2"
              :style="{ height: `calc(100vh - 225px - ${footerHeight}px)` }">
              <Inbox class="w-7 h-7 text-white/50" />
              <div class="text-white/50 text-sm">It's empty here so far</div>
            </div>
          </ScrollArea>
        </div>
      </div>
    </div>

    <footer class="w-full z-50 border border-white/10 px-4 py-2 fixed bottom-0 left-0 right-0">
      <div class="flex flex-row items-center justify-between">
        <!-- Logo and Brand -->
        <div class="flex flex-row items-center gap-4">
          <Link class="flex items-center" href="/">
          <ApplicationLogo class="block h-full w-auto font-semibold" />
          <!-- <span class="text-white text-2xl font-bold">pump.club</span> -->
          </Link>
          <div class="flex flex-row items-center gap-2">
            <img src="/avax-icon.svg" class="w-4 h-4" />
            <span class="text-white/50 text-sm">$156.17</span>
          </div>
          <div class="flex flex-row items-center gap-2">
            <ChartBarIncreasing class="w-4 h-4 text-white/50" />
            <span class="text-white/50 text-sm">$63.2K</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-green-400"></span>
            <span class="text-green-400 font-normal text-sm">Stable Connection</span>
          </div>
        </div>
        <div class="flex flex-row gap-3 items-center">
          <Link class="text-primary hover:bg-transparent flex items-center gap-1 text-sm whitespace-nowrap"
            href="/launch">
          {{ $t('Create Coin') }}
          <Plus class="w-4 h-4 text-primary" />
          </Link>
          <Web3Auth />
          <button class="text-white/50 hover:text-white transition-colors">
            <Settings class="w-4 h-4" />
          </button>
        </div>
      </div>
    </footer>
  </div>

  <!-- Add Column Modal -->
  <template v-if="addColumnModal">
    <div class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-[999999]">
      <div class="bg-gray-900 rounded-3xl p-8 w-full max-w-sm relative shadow-xl border border-white/10">
        <!-- Close button -->
        <button class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl" @click="addColumnModal = false">
          <X class="w-5 h-5" />
        </button>
        <!-- Title and subtitle -->
        <h2 class="text-2xl font-semibold text-white mb-1">Column Manager</h2>
        <div class="text-gray-400 mb-4">You can manage your existing columns below</div>
        <!-- Reset all -->
        <button @click="resetAllColumns"
          class="mb-4 px-4 py-1 rounded-lg bg-white/10 text-white text-sm hover:bg-white/20 transition">Reset
          all</button>
        <!-- Column list -->
        <div class="flex flex-col gap-3 mb-6">
          <div v-for="(col, idx) in tempColumnManage" :key="idx"
            class="flex items-center justify-between rounded-lg px-4 py-3 border border-white/10 w-full">
            <div class="flex items-center gap-3">
              <div class="flex flex-col gap-1">
                <div class="text-white font-medium">{{ col.title }}</div>
                <div class="text-xs text-gray-400">{{ col.subtitle }}</div>
              </div>
            </div>
            <Switch :checked="col.enabled" @update:checked="col.enabled = $event" />
          </div>
        </div>
        <!-- Save changes -->
        <button @click="saveColumns"
          class="w-full py-3 rounded-xl bg-gradient-to-r from-orange-400 to-orange-600 text-white font-semibold text-lg shadow hover:opacity-90 transition">
          Save changes
        </button>
      </div>
    </div>
  </template>

  <!-- Trade Settings Modal -->
  <template v-if="tradeSettingsModal">
    <div class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-[999999]">
      <div class="bg-gray-900 rounded-3xl p-8 w-full max-w-sm relative shadow-xl border border-white/10">
        <!-- Close button -->
        <button class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl" @click="closeTradeSettings">
          <X class="w-5 h-5" />
        </button>
        <!-- Title and subtitle -->
        <h2 class="text-2xl font-semibold text-white mb-1">Advanced Trade Settings</h2>
        <div class="text-gray-400 mb-4 text-sm">These settings will apply to all advanced quick buy and sell orders,
          including those placed on the advanced coin page order form.</div>
        <!-- Buy/Sell Tabs -->
        <div class="flex mb-4 bg-white/5 rounded-full p-1">
          <button v-for="tab in TRADE_SETTINGS" :key="tab.value"
            :class="['flex-1 py-2 rounded-full font-semibold text-sm transition', tradeTab === tab.value ? 'bg-gradient-to-r from-orange-400 to-orange-600 text-white shadow' : 'text-white/40']"
            @click="setTradeTab(tab.value)">{{ tab.name }}</button>
        </div>
        <!-- Preset Tabs -->
        <div class="flex gap-2 mb-4">
          <button v-for="list in presetsLists" :key="list.value"
            :class="['px-4 py-1 rounded-lg text-sm font-medium', tradePreset === list.value ? 'bg-white/10 text-white' : 'bg-transparent text-white/40']"
            @click="setTradePreset(list.value)">{{ list.name }}</button>
        </div>
        <!-- Settings Fields -->
        <div class="grid grid-cols-3 gap-3 mb-4">
          <div class="flex flex-col justify-between rounded-lg p-3 border border-white/10">
            <span class="text-xs text-white/50 mb-1">Slippage</span>
            <FormInput v-model="slippage" type="number" min="0" max="100"
              inputClasses="bg-transparent text-white text-lg font-semibold outline-none" size="sm">
              <template #trail>
                <span class="text-white/40 text-sm">%</span>
              </template>
            </FormInput>
          </div>
          <div class="flex flex-col justify-between rounded-lg p-3 border border-white/10">
            <span class="text-xs text-white/50 mb-1">Priority</span>
            <FormInput v-model="priority" type="number" step="0.001" min="0"
              inputClasses="bg-transparent text-white text-lg font-semibold w-full outline-none" size="sm" />
          </div>
          <div class="flex flex-col justify-between rounded-lg p-3 border border-white/10">
            <span class="text-xs text-white/50 mb-1">Bribe</span>
            <FormInput v-model="bribe" type="number" step="0.001" min="0"
              inputClasses="bg-transparent text-white text-lg font-semibold w-full outline-none" size="sm" />
          </div>
        </div>
        <!-- Auto Bribe -->
        <div class="flex items-center justify-between border border-white/10 rounded-lg p-3 mb-3">
          <div class="flex items-center gap-2">
            <span class="text-white/80 text-sm">Auto Bribe</span>
            <span class="text-white/40 text-xs">Max</span>
            <FormInput v-model="autoBribeMax" type="number" step="0.01" min="0"
              inputClasses="bg-transparent text-white text-sm outline-none" />
          </div>
          <Switch v-model:checked="autoBribe" />
        </div>
        <!-- MEV Protection -->
        <div class="flex items-center justify-between border border-white/10 rounded-lg p-3 mb-6">
          <span class="text-white/80 text-sm">MEV Protection</span>
          <Switch v-model:checked="mevProtection" />
        </div>
        <!-- Done Button -->
        <button @click="closeTradeSettings"
          class="w-full py-3 rounded-xl bg-gradient-to-r from-orange-400 to-orange-600 text-white font-semibold text-lg shadow hover:opacity-90 transition">
          Done
        </button>
      </div>
    </div>
  </template>

  <!-- Search Modal -->
  <template v-if="searchModal">
    <div class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-[999999]">
      <div class="bg-gray-900 rounded-3xl p-8 w-full max-w-sm relative shadow-xl border border-white/10">
        <!-- Close button -->
        <button class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl" @click="closeSearchModal">
          <X class="w-5 h-5" />
        </button>
        <!-- Title -->
        <h2 class="text-2xl font-semibold text-white mb-6">Search</h2>
        <!-- Search input -->
        <div class="flex flex-col gap-4 w-full">
          <FormInput v-model="search" class="ml-auto mr-auto w-full py-3" size="md" inputClasses="!rounded-3xl"
            placeholder="Search for meme">
            <template #lead>
              <Search class="w-4 h-4 ml-1 text-gray-400" />
            </template>
            <template #trail>
              <PrimaryButton size="xs" class="rounded-full text-white hover:bg-transparent px-4 py-2.5 cursor-pointer"
                style="background: linear-gradient(to right, #6C2801 0%, #DA5200 34%, #E97C02 100%);">
                {{ $t("Search") }}
              </PrimaryButton>
            </template>
          </FormInput>
        </div>
        <!-- History -->
        <div>
          <div class="text-white/60 text-sm mb-2">History:</div>
          <ScrollArea class="w-full overflow-hidden p-2 h-[400px]">
            <div class="flex flex-col gap-2">
              <div v-for="(item, idx) in searchedLaunchpads" :key="idx"
                class="flex items-center justify-between py-2 px-2 rounded-lg hover:bg-white/5 transition cursor-pointer">
                <div class="flex items-center gap-3">
                  <img :src="item.logo" class="w-10 h-10 rounded-full object-cover border border-white/10" />
                  <div class="flex flex-col">
                    <span class="text-white font-medium leading-tight">{{ item.name }}</span>
                    <span class="text-white/40 text-xs font-mono">{{ item.symbol }}</span>
                  </div>
                </div>
                <div class="text-white/80 text-sm whitespace-nowrap">Market cap: <span class="font-semibold">{{
                  item.marketCap }}</span></div>
              </div>
            </div>
          </ScrollArea>
        </div>
      </div>
    </div>
  </template>
</template>