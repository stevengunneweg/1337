<script setup lang="ts">
import { RouterView } from 'vue-router';
</script>

<template>
	<div class="flex min-h-full flex-col">
		<div class="mx-auto flex min-h-full w-full grow flex-col">
			<main class="grow">
				<RouterView />
			</main>
		</div>
	</div>
</template>
