<script setup lang="ts">
import Button from '../components/ButtonComponent.vue';

withDefaults(
	defineProps<{
		backLink?: string;
	}>(),
	{
		backLink: undefined,
	},
);
</script>

<template>
	<header class="bg-secondary-500 flex items-center justify-between p-4 py-2">
		<nav class="flex items-center gap-8">
			<Button v-if="backLink" @clicked="$router.push(backLink)" :style="'navigation'">
				← Back
			</Button>
		</nav>
	</header>
</template>
