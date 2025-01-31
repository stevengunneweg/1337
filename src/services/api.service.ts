import axios from 'axios';

export type LeaderboardOptions = 'yesterday' | 'week' | 'month' | 'year' | 'top';

export interface Statistics {
	achievements: {
		first: boolean;
		second: boolean;
		streak_3: boolean;
		streak_5: boolean;
		streak_10: boolean;
		third: boolean;
		v1_user: boolean;
		v2_user: boolean;
		late: boolean;
	};
	stats: {
		amount_first: number;
		average: string;
		best: string;
		count: number;
		count_on_time: number;
		current_streak: number;
		current_top_streak: number;
		longest_streak: number;
	};
}

export class ApiService {
	static endpoint = (() => {
		switch (import.meta.env.VITE_ENVIRONMENT) {
			case 'local':
				return 'http://127.0.0.1:1234/api';
			case 'uat':
				return 'https://dev.1337online.com/api';
			case 'prod':
			default:
				return 'https://1337online.com/api';
		}
	})();

	static async getLeaderboard(period?: LeaderboardOptions) {
		const response = await axios.get(
			`${ApiService.endpoint}/handler.php?action=leaderboard${period ? '&period=' + period : ''}`,
		);
		return response.data.data;
	}

	static async getCurrentTime() {
		const response = await axios.get(`${ApiService.endpoint}/handler.php?action=time`);
		return response.data.data.time.substr(11, 8);
	}

	static async getActiveUsers(username: string) {
		const response = await axios.get(
			`${ApiService.endpoint}/handler.php?action=users&name=${username}`,
		);
		return response.data.data.activeUsers;
	}

	static async postEntry(username: string) {
		return axios.post(`${ApiService.endpoint}/handler.php?action=post`, {
			name: username,
		});
	}

	static async getUserStats(username: string) {
		return axios.get(`${ApiService.endpoint}/handler.php?action=stats&user=${username}`);
	}
}
