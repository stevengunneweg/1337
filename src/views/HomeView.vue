<script setup lang="ts">
import ViewportComponent from '@/components/ViewportComponent.vue';
import { ApiService, type LeaderboardOptions } from '@/services/api.service';
import dayjs from 'dayjs';
import weekOfYear from 'dayjs/plugin/weekOfYear';
import { onMounted, onUnmounted, ref, watch, type Ref } from 'vue';
import { RouterLink } from 'vue-router';

dayjs.extend(weekOfYear);

const time = ref('--:--:--');
const username = ref('');
const activeUsers = ref<Array<string>>([]);
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const leaderboards: Ref<{ [key: string]: any }> = ref({});
const feedbackSuccess = ref('');
const feedbackError = ref('');
const today = ref(dayjs().format('D/M/YYYY'));
const leaderboardVisible = ref<LeaderboardOptions>('yesterday');
const intervals: NodeJS.Timeout[] = [];
const leaderboardData = {
	yesterday: dayjs().subtract(1, 'day').format('D MMM YYYY'),
	week: `Week ${dayjs().week()}`,
	month: dayjs().format('MMMM'),
	year: dayjs().format('YYYY'),
	top: 'all time',
};
const isTimeVisible = ref(true);
const isTimeLeet = ref(false);
let leaderboardDebounce: NodeJS.Timeout | undefined;

watch(leaderboardVisible, (value) => {
	if (!leaderboards.value[value]) {
		getLeaderboard(value);
	}
});

onMounted(() => {
	username.value = localStorage.getItem('username') || '';

	getCurrentTime();
	intervals.push(
		setInterval(() => {
			getCurrentTime();
		}, 100),
	);

	getActiveUsers();
	intervals.push(
		setInterval(() => {
			getActiveUsers();
		}, 1000),
	);

	getLeaderboard();
	getLeaderboard('yesterday');
});

onUnmounted(() => {
	intervals.forEach((interval) => clearInterval(interval));
	if (leaderboardDebounce) {
		clearTimeout(leaderboardDebounce);
	}
});

function updateName() {
	localStorage.setItem('username', username.value);
}

async function getCurrentTime() {
	time.value = await ApiService.getCurrentTime();
	if (time.value.startsWith('13:37')) {
		isTimeLeet.value = true;
		getLeaderboardThrottled();
	} else {
		isTimeLeet.value = false;
	}
}

function getLeaderboardThrottled() {
	if (leaderboardDebounce) {
		return;
	}
	leaderboardDebounce = setTimeout(() => {
		getLeaderboard();
		leaderboardDebounce = undefined;
	}, 1000);
}

async function getActiveUsers() {
	activeUsers.value = await ApiService.getActiveUsers(username.value);
}

async function submit(event: Event) {
	feedbackSuccess.value = '';
	feedbackError.value = '';
	event.preventDefault();
	ApiService.postEntry(username.value)
		.then((response) => {
			feedbackSuccess.value = `${response.data.data.status}<br/>at <span class="font-bold">${response.data.data.time}</span>`;
		})
		.catch((error) => {
			feedbackError.value = error.response.data.data.status;
		});
}

async function getLeaderboard(period?: LeaderboardOptions) {
	leaderboards.value[period || 'default'] = await ApiService.getLeaderboard(period);
}
</script>

<template>
	<div class="relative">
		<div class="flex min-h-[100dvh] justify-between gap-4 max-lg:flex-col max-lg:pb-10">
			<div
				class="border-ui-200 max-lg:bg-tertiary-100 dark:max-lg:bg-tertiary-900 w-full max-w-96 shrink grow max-lg:order-2 max-lg:mx-auto lg:max-w-[min(380px,calc((100%-423px-32px)/2))] lg:border-r"
			>
				<div class="flex h-full flex-col max-lg:pt-4 lg:max-h-[100dvh]">
					<div class="p-4">
						<div class="text-center text-3xl font-bold">Today</div>
						<div class="text-center text-lg">{{ today }}</div>
						<div class="font-alarm mt-3 text-center text-2xl font-bold max-lg:hidden">
							{{ time }}
						</div>
					</div>
					<div class="scrollbox grow">
						<div class="p-4 pt-1 lg:shrink lg:grow lg:overflow-auto">
							<div v-if="!leaderboards['default']?.length" class="text-center">
								This list is currently empty
							</div>
							<div v-for="entry of leaderboards['default']" :key="entry.id">
								<div class="flex w-full justify-between gap-2">
									<RouterLink
										class="text-secondary-500 dark:text-secondary-100 outline-secondary-500 dark:outline-secondary-100 max-w-[calc(100%-140px)] overflow-hidden rounded text-ellipsis whitespace-nowrap underline outline-0 transition-all focus:outline-2 focus:outline-offset-1"
										:to="'/stats/' + entry.name"
										:title="entry.name"
									>
										{{ entry.name }}
									</RouterLink>
									<div>
										{{ entry.time.substr(11) }}
									</div>
								</div>
							</div>
						</div>
					</div>
					<div
						class="bg-ui-100 dark:bg-ui-800 lg:border-ui-900 lg:dark:border-ui-100 lg:border-t"
					>
						<div class="flex items-center gap-4 p-4 font-bold">
							<div>
								Currently online
								<span class="text-sm">({{ activeUsers.length }})</span>
							</div>
							<div
								class="bg-success-500 outline-success-500 inline-block size-2 animate-pulse rounded-full outline-1 outline-offset-2"
							></div>
						</div>
						<div class="scrollbox-ui-100 max-h-[25dvh] p-4 pt-0 lg:h-[25dvh]">
							<div v-if="!activeUsers.length">No users online</div>
							<div v-for="user of activeUsers" :key="user">
								<RouterLink
									class="outline-secondary-500 dark:outline-secondary-100 max-w-[calc(100%-140px)] overflow-hidden rounded text-ellipsis whitespace-nowrap underline outline-0 transition-all focus:outline-2 focus:outline-offset-1"
									:to="'/stats/' + user"
									:title="user"
								>
									{{ user }}
								</RouterLink>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="grow max-lg:order-1 lg:py-8">
				<div
					class="flex h-full flex-col items-center justify-center gap-4 text-center max-lg:p-6"
				>
					<ViewportComponent @visible="isTimeVisible = $event">
						<span
							class="font-alarm max-xs:text-7xl text-8xl transition-colors"
							:class="{
								'text-success-500 dark:text-success-500': isTimeLeet,
							}"
						>
							{{ time }}
						</span>
					</ViewportComponent>
					<form class="flex max-w-[280px] flex-col gap-4" v-on:submit="submit($event)">
						<div class="relative">
							<div class="flex items-center gap-2">
								<div
									class="bg-success-500 outline-success-500 inline-block size-2 animate-pulse rounded-full outline-1 outline-offset-2"
								></div>
								{{ activeUsers.length }} user{{
									activeUsers.length === 1 ? '' : 's'
								}}
								online
							</div>
						</div>
						<input
							placeholder="name"
							v-model="username"
							v-on:input="updateName()"
							maxlength="20"
							tabindex="1"
							class="border-ui-900 dark:border-ui-100 outline-primary-500 rounded border p-2 py-1.5 text-center text-4xl text-black outline-0 transition-all focus:outline-2 focus:outline-offset-2 lg:text-5xl dark:text-white"
						/>
						<button
							class="border-ui-900 dark:border-ui-100 bg-ui-100 hover:bg-ui-200 dark:bg-ui-800 dark:hover:bg-ui-700 font-alarm outline-primary-500 cursor-pointer rounded border pt-6 pr-6 pb-4 pl-2 text-7xl text-black outline-0 transition-all focus:outline-2 focus:outline-offset-2 lg:text-8xl dark:text-white"
							tabindex="2"
						>
							1337
						</button>
						<div
							class="lg:text-xl"
							:class="{
								'text-success-500': feedbackSuccess,
								'text-error-500': feedbackError,
							}"
							v-html="`${feedbackSuccess || feedbackError}&nbsp;`"
						></div>
					</form>
					<div>
						If you do not get what this site is about,
						<RouterLink
							to="/about"
							class="outline-secondary-500 dark:outline-secondary-100 max-w-[calc(100%-140px)] overflow-hidden rounded text-ellipsis whitespace-nowrap underline outline-0 transition-all focus:outline-2 focus:outline-offset-1"
							tabindex="3"
							>read me</RouterLink
						>.
					</div>
				</div>
			</div>
			<div
				class="border-ui-200 w-full max-w-96 shrink grow max-lg:order-3 max-lg:mx-auto lg:max-w-[min(380px,calc((100%-423px-32px)/2))] lg:border-l"
			>
				<div class="flex flex-col lg:max-h-[100dvh]">
					<div class="flex flex-col gap-4 p-4">
						<div class="relative flex items-stretch">
							<button
								class="focus:outline-primary-900 grow cursor-pointer rounded-t-lg p-1.5 text-sm transition-all focus:z-10 focus:outline-2"
								:class="{
									'bg-secondary-500 text-secondary-500-contrast hover:bg-secondary-900 hover:text-secondary-900-contrast dark:bg-secondary-800 dark:text-secondary-800-contrast dark:hover:bg-secondary-700 dark:hover:text-secondary-700-contrast':
										leaderboardVisible === 'yesterday',
									'bg-ui-100 hover:bg-ui-200 hover:text-ui-200-contrast dark:bg-ui-800 dark:hover:bg-ui-700 dark:hover:text-ui-700-contrast':
										leaderboardVisible !== 'yesterday',
								}"
								type="button"
								@click="leaderboardVisible = 'yesterday'"
							>
								Yesterday
							</button>
							<button
								class="focus:outline-primary-900 grow cursor-pointer rounded-t-lg p-1.5 text-sm transition-all focus:z-10 focus:outline-2"
								:class="{
									'bg-secondary-500 text-secondary-500-contrast hover:bg-secondary-900 hover:text-secondary-900-contrast dark:bg-secondary-800 dark:text-secondary-800-contrast dark:hover:bg-secondary-700 dark:hover:text-secondary-700-contrast':
										leaderboardVisible === 'week',
									'bg-ui-100 hover:bg-ui-200 hover:text-ui-200-contrast dark:bg-ui-800 dark:hover:bg-ui-700 dark:hover:text-ui-700-contrast':
										leaderboardVisible !== 'week',
								}"
								type="button"
								@click="leaderboardVisible = 'week'"
							>
								Week
							</button>
							<button
								class="focus:outline-primary-900 grow cursor-pointer rounded-t-lg p-1.5 text-sm transition-all focus:z-10 focus:outline-2"
								:class="{
									'bg-secondary-500 text-secondary-500-contrast hover:bg-secondary-900 hover:text-secondary-900-contrast dark:bg-secondary-800 dark:text-secondary-800-contrast dark:hover:bg-secondary-700 dark:hover:text-secondary-700-contrast':
										leaderboardVisible === 'month',
									'bg-ui-100 hover:bg-ui-200 hover:text-ui-200-contrast dark:bg-ui-800 dark:hover:bg-ui-700 dark:hover:text-ui-700-contrast':
										leaderboardVisible !== 'month',
								}"
								type="button"
								@click="leaderboardVisible = 'month'"
							>
								Month
							</button>
							<button
								class="focus:outline-primary-900 grow cursor-pointer rounded-t-lg p-1.5 text-sm transition-all focus:z-10 focus:outline-2"
								:class="{
									'bg-secondary-500 text-secondary-500-contrast hover:bg-secondary-900 hover:text-secondary-900-contrast dark:bg-secondary-800 dark:text-secondary-800-contrast dark:hover:bg-secondary-700 dark:hover:text-secondary-700-contrast':
										leaderboardVisible === 'year',
									'bg-ui-100 hover:bg-ui-200 hover:text-ui-200-contrast dark:bg-ui-800 dark:hover:bg-ui-700 dark:hover:text-ui-700-contrast':
										leaderboardVisible !== 'year',
								}"
								type="button"
								@click="leaderboardVisible = 'year'"
							>
								Year
							</button>
							<button
								class="focus:outline-primary-900 grow cursor-pointer rounded-t-lg p-1.5 text-sm transition-all focus:z-10 focus:outline-2"
								:class="{
									'bg-secondary-500 text-secondary-500-contrast hover:bg-secondary-900 hover:text-secondary-900-contrast dark:bg-secondary-800 dark:text-secondary-800-contrast dark:hover:bg-secondary-700 dark:hover:text-secondary-700-contrast':
										leaderboardVisible === 'top',
									'bg-ui-100 hover:bg-ui-200 hover:text-ui-200-contrast dark:bg-ui-800 dark:hover:bg-ui-700 dark:hover:text-ui-700-contrast':
										leaderboardVisible !== 'top',
								}"
								type="button"
								@click="leaderboardVisible = 'top'"
							>
								All time
							</button>
						</div>
						<div class="flex flex-wrap justify-center gap-1.5 text-center text-xl">
							<span class="whitespace-nowrap">Leaderboard of</span>
							<span class="font-bold">{{ leaderboardData[leaderboardVisible] }}</span>
						</div>
					</div>
					<div class="scrollbox">
						<div class="p-4 pt-1 lg:shrink lg:grow lg:overflow-auto">
							<div
								v-if="!leaderboards[leaderboardVisible]?.length"
								class="text-center"
							>
								This list is currently empty
							</div>
							<div
								v-for="entry of leaderboards[leaderboardVisible]"
								:key="`${leaderboardVisible}-${entry.id}`"
								:title="entry.time.substr(0, 10)"
							>
								<div class="flex w-full justify-between gap-2">
									<RouterLink
										class="text-secondary-500 dark:text-secondary-100 outline-secondary-500 dark:outline-secondary-100 max-w-[calc(100%-140px)] overflow-hidden rounded text-ellipsis whitespace-nowrap underline outline-0 transition-all focus:outline-2 focus:outline-offset-1"
										:to="'/stats/' + entry.name"
										:title="entry.name"
									>
										{{ entry.name }}
									</RouterLink>
									<div>
										{{ entry.time.substr(11) }}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div v-if="!isTimeVisible" class="fixed top-0 right-0 left-0 lg:hidden">
			<ViewportComponent>
				<div
					class="font-alarm dark:bg-ui-900 border-ui-900 dark:border-ui-200 border-b bg-white px-4 py-2 text-center text-2xl font-bold"
					:class="{
						'text-success-500 dark:text-success-500': isTimeLeet,
					}"
				>
					{{ time }}
				</div>
			</ViewportComponent>
		</div>
	</div>
</template>
