<script setup>
	import { Link } from "@inertiajs/vue3";
</script>
<template>
	<Link href="/">
		<img
			v-if="$page.props.appLogo"
			src="/logo.png"
			v-bind="$attrs"
			alt="Windster Logo" />
		<h1 v-else v-bind="$attrs" class="text-3xl text-white">
			{{ "Pump.com"}}
		</h1>
	</Link>
</template>