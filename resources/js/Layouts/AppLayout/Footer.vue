<script setup>
	import { Link } from "@inertiajs/vue3";
	import ApplicationLogo from "@/Components/ApplicationLogo.vue";
</script>

<template>
	<footer class=" text-gray-400 py-12 footer-layout border-t border-white/10">
		<div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
			<div class="flex justify-center mb-4">
				<Link href="/">
					<ApplicationLogo class="block h-10 w-auto" />
				</Link>
			</div>
			<div class="text-sm mb-6">
				&copy; pump.club 2025
			</div>
			<div class="flex justify-center items-center space-x-6 text-sm">
				<Link
					href="/privacy"
					class="hover:text-white transition-colors"
				>
					Privacy policy
				</Link>
				<Link
					href="/terms"
					class="hover:text-white transition-colors"
				>
					Terms of service
				</Link>
				<Link
					href="#"
					class="hover:text-white transition-colors"
				>
					Fees
				</Link>
				<Link
					href="#"
					class="hover:text-white transition-colors"
				>
					Blogposts
				</Link>
			</div>
		</div>
	</footer>
</template>
