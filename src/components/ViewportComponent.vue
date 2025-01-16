<script setup lang="ts">
import { onMounted, ref } from 'vue';

const emit = defineEmits<{
	(e: 'visible', isVisible: boolean): void;
}>();

const target = ref<Element>();
const isInViewport = ref<boolean>(false);
const observer = new IntersectionObserver(
	([entry]) => {
		isInViewport.value = entry.isIntersecting;
		emit('visible', isInViewport.value);
	},
	{
		threshold: 0.5,
	},
);

onMounted(() => {
	setTimeout(() => {
		observer.observe(target.value as Element);
	}, 250);
});
</script>

<template>
	<div ref="target">
		<div
			class="transition-all duration-750 ease-out"
			:class="isInViewport ? 'translate-y-0 opacity-100' : 'translate-y-14 opacity-0'"
		>
			<slot></slot>
		</div>
	</div>
</template>
