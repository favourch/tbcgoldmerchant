<template>
	<div>
		<div class="ui basic segment" :class="{ loading: show_loading }">
			<div class="ui grid container">
				
				<div class="row">

					<div class="ui relaxed divided list">
						<div class="item" v-for="(log, index) in logs" :key="index">
							<i class="large gift middle aligned icon"></i>
							<div class="content">
								<a class="header">{{ log.action_type }}</a>
								<div class="description">{{ log.action_detail }}</div>
								<div class="description">Points: +{{ log.points }}</div>
								<div class="description">From: <strong>{{ log.alias_name }}</strong></div>
								<div class="description">Date: <strong>{{ log.created_at | moment('MMM DD, YYYY h:mm:ss a') }}</strong></div>
							</div>
						</div>
					</div>
					<pagination :data="logsData" v-bind:showDisabled="true" icon="chevron" v-on:change-page="getMemberBonusPointsLogs" size="tiny" :limit="limit"></pagination>
	
					
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import pagination from 'laravel-vue-semantic-ui-pagination';
	export default {
		components: {
			pagination
		},
		data: () => ({
			members_data: {},
			members: [],
			logs: [],
			logsData: {},
			search_loading: false,
			show_loading: true,
			loading_members: false,
			loading_bonus_logs: false,
			search_input: 132,
			limit: 6
		}),
		methods: {
			async init() {
				await this.getMembers();
				await this.getMemberBonusPointsLogs();
				this.show_loading = false;
			},
			getMembers() {
				this.loading_members = true;
				axios.get('/tbcgoldadmin/api/members').then((resp) => {
					console.log(resp);
					this.loading_members = false;
				}).catch((err) => {
					console.log(err);
				})
			},
			getMemberBonusPointsLogs(page) {
				this.loading_bonus_logs = true;
				if (typeof page === 'undefined') {
					page = 1;
				}
				axios.get('/tbcgoldadmin/api/member/bonus-logs', {
					params: {
						page: page,
						user_input: this.user_input
					}
				}).then((resp) => {
					console.log(resp)
					if(resp.data.bonus_logs.length) {
						this.logs = resp.data.bonus_logs.data;
						this.logsData = resp.data.bonus_logs;
					}
					this.loading_bonus_logs = false;
					
				}).catch((err) => {
					console.log(err)
				})
			},
		},
		created () {
			this.init();
		}
	}
</script>