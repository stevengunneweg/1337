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
	scrollBehavior: (_to, _from, savedPosition) => {
		if (savedPosition) {
			return savedPosition;
		} else {
			return { top: 0 };
		}
	},
});

export default router;
