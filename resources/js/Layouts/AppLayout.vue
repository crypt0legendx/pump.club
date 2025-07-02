<script setup>
import { ref, onMounted } from "vue";
import FlashMessages from "@/Layouts/AppLayout/FlashMessages.vue";
import Footer from "@/Layouts/AppLayout/Footer.vue";
import TopNav from "@/Layouts/AppLayout/TopNav.vue";
	// Props
	defineProps({
		title: {
			type: String,
			default: "",
		},
		compact: Boolean,
	});

	// We'll add state management and other functionality as needed
const navHeight = ref(90)
const navRef = ref(null)

onMounted(() => {
  if (navRef.value) {
    navHeight.value = navRef.value.offsetHeight
  }
})

</script>
<template>
	<div class="min-h-screen bg-black relative home-bg" >	
		<!-- Flash Messages Component (Will be created next) -->
		<FlashMessages />
		<!-- Top Navigation (Will be created next) -->
		<TopNav />
		<slot name="header" />
		<!-- Main Content -->
		<main
			class="min-h-[calc(100vh-4rem)]" :style="{ paddingTop: `calc(10px + ${navHeight}px)` }">
			<!-- Main Content Area -->
			<div class="mx-auto">
				<slot></slot>
			</div>
		</main>
		<!-- Footer (Will be created next) -->
		<Footer />
	</div>
</template>