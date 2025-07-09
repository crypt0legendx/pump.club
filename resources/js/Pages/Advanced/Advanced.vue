<script setup>
import { computed, ref, onMounted } from "vue";
import { useLaunchpadsData } from "@/hooks/useLaunchpadsData";
import { Carousel, CarouselContent, CarouselItem } from "@/Components/ui/carousel";
import BarButton from "../Launchpads/BarButton.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
import NavActions from "@/Layouts/AppLayout/NavActions.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Web3Auth from "../Auth/Web3Auth.vue";
import { ChartBarIncreasing, CircleAlert, EyeOff, GripVertical, House, PackagePlus, Play, PlayIcon, Plus, Search, Settings, X } from "lucide-vue-next";
import FormInput from "@/Components/FormInput.vue";
import { Select, SelectContent, SelectItem, SelectSeparator, SelectTrigger } from "@/Components/ui/select";
import useLocalStorage from '@/hooks/useLocalStorage';
import { Switch } from "@/Components/ui/switch";
import { ScrollArea, ScrollBar } from "@/Components/ui/scroll-area";
import AdvancedCard from "./AdvancedCard.vue";
import { FunnelIcon } from "@heroicons/vue/24/outline";

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

const selectedPreset = ref(presetsLists[0].value);
const addColumnModal = ref(false);
const tempColumnManage = ref([]);
const columnManage = useLocalStorage('advanced_column_manage', defaultColumns);

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

const launchpadsList = computed(() => props.launchpads?.data || []);
const launchpadsInfo = useLaunchpadsData(launchpadsList, props.usdRates);

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
  } catch {}
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

const footerHeight = ref(76)
const footerRef = ref(null)

onMounted(() => {
  if (footerRef.value) {
    navHeight.value = navRef.value.offsetHeight
  }
})

</script>  

<template>
  <div class="flex flex-col home-bg h-screen">
    <!-- Ticker Bar -->
    <div class="w-full border-b border-white/10 py-1 px-2 flex overflow-x-auto whitespace-nowrap gap-6" style="min-height: 38px;">
      <div v-for="i in 8" :key="i" class="flex items-center gap-2 min-w-max">
        <img src="/indexcard.png" class="w-5 h-5 rounded-full" />
        <span class="text-white font-semibold text-xs">PNDA</span>
        <span v-if="i % 2 === 0" class="flex items-center text-xs text-red-400 font-semibold">
          <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          0.7838 AVAX
        </span>
        <span v-else class="flex items-center text-xs text-green-400 font-semibold">
          <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
          0.7838 AVAX
        </span>
        <span class="text-white/60 text-xs">@kevin_91</span>
      </div>
    </div>
    <div class="flex flex-row items-center gap-2 py-2 px-2 border-b border-white/10">
        <div class="flex flex-row items-center gap-2">
            <House class="w-4 h-4 text-primary" />
            <span class="text-primary text-sm">Scan</span>
        </div>
        <div class="flex flex-row items-center gap-2">
            <Search class="w-4 h-4 text-white/50" />
            <span class="text-white/50 text-sm">Search</span>
        </div>
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
                    <button class="flex items-center">
                        <Settings class="w-4 h-4 ml-1 text-gray-400" />
                    </button>
                </template>
            </FormInput>
        </div>
      </div>
      <!-- Dropdown -->
      <div class="flex items-center gap-1 bg-white/4 rounded-lg px-3 py-2 cursor-pointer">
        <Select v-model="selectedPreset">
            <SelectTrigger class="bg-transparent border-none shadow-none p-0 h-auto w-auto focus:ring-0 focus:outline-none">
                <span class="text-white/80 text-sm capitalize">{{ selectedPreset }}</span>
            </SelectTrigger>
            <SelectContent class="w-40 min-w-0 right-0 left-auto mt-2 bg-black/40">
                <SelectItem
                    v-for="list in presetsLists" 
                    :key="list.value" 
                    :value="list.value"
                >
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
                    <div v-else class="flex flex-col items-center justify-center bg-white/10 rounded-lg p-4 w-full" :style="{ height: `calc(100vh - 225px - ${footerHeight}px)` }"> 
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
          <Link
            class="text-primary hover:bg-transparent flex items-center gap-1 text-sm whitespace-nowrap"
            href="/launch"
          >
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
        <button @click="resetAllColumns" class="mb-4 px-4 py-1 rounded-lg bg-white/10 text-white text-sm hover:bg-white/20 transition">Reset all</button>
        <!-- Column list -->
        <div class="flex flex-col gap-3 mb-6">
            <div v-for="(col, idx) in tempColumnManage" :key="idx" class="flex items-center justify-between rounded-lg px-4 py-3 border border-white/10 w-full">
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
        <button @click="saveColumns" class="w-full py-3 rounded-xl bg-gradient-to-r from-orange-400 to-orange-600 text-white font-semibold text-lg shadow hover:opacity-90 transition">
            Save changes
        </button>
        </div>
    </div>
  </template>
</template>