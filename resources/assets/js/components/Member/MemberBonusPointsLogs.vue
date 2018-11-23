<template>
	<div>
		<div class="ui inverted dimmer" :class="{ active: showLoading }">
			<div class="ui text loader">Loading</div>
		</div>
		
		<div v-if="!showLoading">

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
</template>
<script>
	import pagination from 'laravel-vue-semantic-ui-pagination';
	export default {
		name: 'member-bonus-points-logs',
		components: {
			pagination
		},
		data: () => ({
			logs: [],
			logsData: {},
			showLoading: false,
			limit: 6
		}),
		methods: {

			initialize() {
				this.getMemberBonusPointsLogs();
			},

			getMemberBonusPointsLogs(page) {
				this.showLoading = true;
				if (typeof page === 'undefined') {
					page = 1;
				}
				axios.get('/member/api/bonus-points-logs', {
					params: {
						page: page
					}
				}).then((resp) => {
					console.log(resp)
					this.showLoading = false;
					this.logs = resp.data.logs.data;
					this.logsData = resp.data.logs;
				}).catch((err) => {
					console.log(err)
				})
			},
		},
		created () {
			this.initialize();
		}
	}
</script>