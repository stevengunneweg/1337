<script setup lang="ts">
import { defineAsyncComponent } from 'vue';

const props = defineProps<{ name: string }>();
const icon = defineAsyncComponent(() => import(`@/assets/icons/${props.name}.svg?component`));
</script>

<template>
	<component :is="icon" class="fill-current" />
</template>
