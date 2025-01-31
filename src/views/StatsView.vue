<script setup lang="ts">
import { ApiService, type Statistics } from '@/services/api.service';
import { onMounted, ref, type Ref } from 'vue';
import { useRoute } from 'vue-router';
import Header from '../components/HeaderComponent.vue';

const route = useRoute();
const isLoading = ref(true);
const username = ref('');
const stats: Ref<Statistics | undefined> = ref(undefined);

onMounted(() => {
	username.value = (route.params.username as string) || '';
	getUserStats(username.value);
});

async function getUserStats(username: string) {
	const result = await ApiService.getUserStats(username);
	isLoading.value = false;
	stats.value = result.data.data;
}
</script>

<template>
	<Header back-link="/"></Header>
	<div class="mx-auto flex max-w-xl flex-col gap-4 p-4">
		<h1 class="text-center text-2xl text-[clamp(24px,7dvw,36px)] font-bold">
			{{ username }}
		</h1>
		<div v-if="isLoading" class="text-center">Statistics are loading...</div>
		<div v-if="!isLoading" class="contents">
			<h2 class="xs:text-2xl text-center text-2xl">Statistics</h2>
			<div>
				<div class="max-xs:flex-col max-xs:gap-0 flex flex-wrap gap-4 py-2">
					<div class="xs:w-1/2 grow basis-1/3">Total amount of attempts</div>
					<div class="xs:w-1/2 grow basis-1/3 font-bold">
						{{ stats?.stats.count || 0 }}
					</div>
				</div>
				<div class="max-xs:flex-col max-xs:gap-0 flex flex-wrap gap-4 py-2">
					<div class="xs:w-1/2 grow basis-1/3">Attempts at 13:37</div>
					<div class="xs:w-1/2 grow basis-1/3 font-bold">
						{{ stats?.stats.count_on_time || 0 }}
					</div>
				</div>
				<div class="max-xs:flex-col max-xs:gap-0 flex flex-wrap gap-4 py-2">
					<div class="xs:w-1/2 grow basis-1/3">Best ever attempt</div>
					<div class="xs:w-1/2 grow basis-1/3 font-bold">
						<span v-if="!stats?.stats.best">-</span>
						<span v-if="stats?.stats.best">
							{{ stats?.stats.best.split(' ')[1] }} ({{
								stats?.stats.best.split(' ')[0]
							}})
						</span>
					</div>
				</div>
				<div class="max-xs:flex-col max-xs:gap-0 flex flex-wrap gap-4 py-2">
					<div class="xs:w-1/2 grow basis-1/3">Average (at 13:37)</div>
					<div class="xs:w-1/2 grow basis-1/3 font-bold">
						{{ stats?.stats.average || '-' }}
					</div>
				</div>
				<div class="max-xs:flex-col max-xs:gap-0 flex flex-wrap gap-4 py-2">
					<div class="xs:w-1/2 grow basis-1/3">Times first of the day</div>
					<div class="xs:w-1/2 grow basis-1/3 font-bold">
						{{ stats?.stats.amount_first || 0 }}
					</div>
				</div>
				<div class="max-xs:flex-col max-xs:gap-0 flex flex-wrap gap-4 py-2">
					<div class="xs:w-1/2 grow basis-1/3">Current streak of posts</div>
					<div class="xs:w-1/2 grow basis-1/3 font-bold">
						{{ stats?.stats.current_streak || 0 }}
					</div>
				</div>
				<div class="max-xs:flex-col max-xs:gap-0 flex flex-wrap gap-4 py-2">
					<div class="xs:w-1/2 grow basis-1/3">Current streak in top 3</div>
					<div class="xs:w-1/2 grow basis-1/3 font-bold">
						{{ stats?.stats.current_top_streak || 0 }}
					</div>
				</div>
				<div class="max-xs:flex-col max-xs:gap-0 flex flex-wrap gap-4 py-2">
					<div class="xs:w-1/2 grow basis-1/3">Longest streak of posts</div>
					<div class="xs:w-1/2 grow basis-1/3 font-bold">
						{{ stats?.stats.longest_streak || 0 }}
					</div>
				</div>
			</div>
			<h2 class="xs:text-2xl text-center text-2xl">Achievements</h2>
			<div class="flex flex-col gap-4 p-2">
				<div v-if="stats?.achievements.first">🥇 First of the day</div>
				<div v-if="stats?.achievements.second">🥈 Second of the day</div>
				<div v-if="stats?.achievements.third">🥉 Third of the day</div>
				<div v-if="stats?.achievements.streak_3">3️⃣ 3 day streak</div>
				<div v-if="stats?.achievements.streak_5">5️⃣ 5 day streak</div>
				<div v-if="stats?.achievements.streak_10">🔟 10 day streak</div>
				<div v-if="stats?.achievements.v1_user">🏆 User since version 1</div>
				<div v-if="stats?.achievements.v2_user">🏆 User since version 2</div>
				<div v-if="stats?.achievements.late">
					🕑 You've been late (1447 or "laat" means "late" in Dutch)
				</div>
			</div>
		</div>
	</div>
</template>
