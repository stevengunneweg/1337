<script setup lang="ts">
import Header from '../components/HeaderComponent.vue';
</script>

<template>
	<Header back-link="/"></Header>
	<div class="max-w-optimized-reading mx-auto flex flex-col gap-4 p-4">
		<h1 class="xs:text-3xl text-center text-2xl font-bold">1337online</h1>
		<p>
			1337online is a very basic platform where there's only one goal, be the first to click
			on the button when the clock hits 13:37. By beating others you have the chance to win
			ethernal glory (or at least a brief moment of glory).
		</p>
		<p>
			Find the project on
			<a href="https://github.com/stevengunneweg/1337" target="_blank" class="underline"
				>https://github.com/stevengunneweg/1337</a
			>
		</p>
	</div>
</template>
