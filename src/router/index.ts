import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: '/',
			name: 'home',
			component: HomeView,
		},
		{
			path: '/stats/:username',
			name: 'stats',
			component: () => import('../views/StatsView.vue'),
		},
		{
			path: '/about',
			name: 'about',
			component: () => import('../views/AboutView.vue'),
		},
		{
			path: '/:pathMatch(.*)',
			redirect: { name: 'home' },
		},
	],
	scrollBehavior: (to, from, savedPosition) => {
		// Skip if destination full path has query parameters and differs in no other way from previous
		if (from && to.fullPath.split('?')[0] == from.fullPath.split('?')[0]) {
			return;
		}
		if (savedPosition) {
			return savedPosition;
		} else {
			return { top: 0 };
		}
	},
});

export default router;
