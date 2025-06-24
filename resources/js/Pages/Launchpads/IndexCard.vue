<script setup>

defineProps({
    launchpad: {
        type: Object,
        required: true,
    },
});


</script>

<template>
    <Link :href="route('launchpads.show', { launchpad: launchpad.contract })"
        class="relative bg-gray-800/50 rounded-[32px] p-4 flex flex-col gap-3">
        <div
            v-if="launchpad.status"
            :class="launchpad.status && launchpad.status.toLowerCase() === 'bonding'
                    ? 'bg-gray-500 text-white border-gray-400 border'
                    : launchpad.status && launchpad.status.toLowerCase() === 'prebond'
                        ? 'border-gray-700 bg-gray-900 text-white border'
                        : 'bg-[#DA5200]/30 text-[#DA5200] border-[#DA5200] border'
                "
            class="absolute flex items-center gap-2 -top-4 left-1/2 -translate-x-1/2 text-sm capitalize px-4 py-2 rounded-xl shadow text-center z-10"
        >
            {{ launchpad.status }}
            <img src="/star.png" class="w-4 h-4" alt="Bonding" v-if="launchpad.status && launchpad.status.toLowerCase() === 'finalized'" />
        </div>
        <div class="flex flex-row justify-center gap-3">
        <div class="w-1/3">
            <img :src="launchpad.logo ?? '/indexcard.png'" class="w-full h-full object-cover rounded-3xl"
                alt="Launchpad Image" @error="$event.target.src = '/indexcard.png'" />
        </div>
        <div class="flex flex-col gap-1 w-2/3 p-3">
            <div class="flex items-center gap-2">
                <img :src="launchpad.profile_photo_url ?? '/avatar.png'" class="w-6 h-6 rounded-full" alt="User Avatar"
                    @error="$event.target.src = '/avatar.png'" />
                <div class="text-sm text-gray-400">
                    <span class="font-semibold text-white">
                        @{{ launchpad.name }}
                    </span>
                    <span class="ml-2">{{ launchpad.createdAgo }}</span>
                </div>
            </div>
            <div>
                <h3 class="text-white font-normal">
                    {{ launchpad.name }} ({{ launchpad.symbol }})
                </h3>
            </div>
            <div class="text-xs text-green-400 bg-green-500/10 rounded-full px-2 py-1 self-start">
                Market cap: {{ launchpad.marketCap }}
            </div>
            <div class="text-sm text-gray-400">
                Replies: {{ launchpad.msg_count ?? 746 }}
            </div>
        </div>
    </div>
    <div>
        <p class="text-sm text-gray-400 line-clamp-3">
            {{ launchpad.description }}
        </p>
    </div>
    </Link>
</template>
