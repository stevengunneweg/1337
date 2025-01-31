<script setup lang="ts">
import { useRouter } from 'vue-router';
import Button from '../components/ButtonComponent.vue';

const router = useRouter();

const props = withDefaults(
	defineProps<{
		backLink?: string;
	}>(),
	{
		backLink: undefined,
	},
);

function navigateBack() {
	if (document.referrer && document.referrer.startsWith(location.origin)) {
		history.back();
	} else {
		router.push(props.backLink || '/');
	}
}
</script>

<template>
	<header class="bg-secondary-500 flex items-center justify-between p-4 py-2">
		<nav class="flex items-center gap-8">
			<Button v-if="backLink" @clicked="navigateBack()" :style="'navigation'">
				← Back
			</Button>
		</nav>
	</header>
</template>
