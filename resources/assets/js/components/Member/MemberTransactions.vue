<template>
	<div class="ui inverted segment">
		<div class="ui inverted dimmer" :class="{ active: showLoading }">
			<div class="ui text loader">Loading</div>
		</div>
		
		<div class="ui grid container">
			<div class="row">
				<h3>Transaction List</h3>
				<table class="ui celled table">
					<thead>
						<tr>
							<th>Transaction Type</th>
							<th>Transaction Cost</th>
							<th>Status</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(transaction, index) in transactions" :key="index">
							<td>{{ transaction.transaction_type }}</td>
							<td>
								<span v-if="transaction.transaction_type == 'membership'">{{ transaction.plan.name }} - ${{ transaction.plan.price }}</span>
								<span v-if="transaction.transaction_type == 'activate_account'">{{ transaction.points }} pts</span>
							</td>
							<td>{{ transaction.status }}</td>
							<td>{{ transaction.created_at | moment('MMM DD, YYYY h:mm:ss a') }}</td>
						</tr>
					</tbody>
				</table>

			</div>
			<div class="row">
				<div class="three wide column"></div>
				<div class="ten wide center aligned column">
					<pagination :data="transactionsData" v-bind:showDisabled="true" icon="chevron" v-on:change-page="getTransactions" size="tiny"></pagination>
				</div>
			</div>
		</div>
		
	</div>
</template>
<script>
	import pagination from 'laravel-vue-semantic-ui-pagination';
	export default {	
		name: 'member-transactions',
		props: {
			user_id: {
				type: String,
				required: true
			}
		},
		components: {
			pagination
		},
		data: () => ({
			transactions: [],
			transactionsData: {},
			showLoading: true,

		}),
		created() {
			this.getTransactions();
		},
		methods: {
			getTransactions(page) {
				if (typeof page === 'undefined') {
					page = 1;
				}

				this.showLoading = true;

				axios.get('/member/api/'+this.user_id+'/transactions', {
					params: {
						page: page
					}
				}).then((resp) => {
					console.log(resp);
					this.transactions = resp.data.transactions.data;
					this.transactionsData = resp.data.transactions;
					this.showLoading = false;
				}).catch((err) => {
					console.log(err);
				});
			},
		}
	}
</script>