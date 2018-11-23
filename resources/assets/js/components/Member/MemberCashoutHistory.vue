<template>
	<div>
		<div class="ui dimmer" :class="{ active: showLoading }">
			<div class="ui text loader">Loading</div>
		</div>
		
		<div class="ui grid container">
			<div class="row">
				<!-- <h3>Cash Out History</h3> -->
				<h4></h4>

				<table class="ui compact striped celled table">
					<thead>
						<tr>
							<th colspan="2">
								<i class="money bill alternate outline icon"></i> Total Cash Out: {{ total_cashout | currency }}
							</th>
							<th colspan="2">
								<i class="money bill alternate outline icon"></i> Total Referral: {{ total_cashout_referral | currency }}
							</th>
							<th colspan="2">
								<i class="money bill alternate outline icon"></i> Total Pairing: {{ total_cashout_pairing | currency }}
							</th>
						</tr>
					</thead>
					<thead>
						<tr>
							<th>Status</th>
							<th>Cash Out Type</th>
							<th>Cash Out Points</th>
							<th>Date</th>
							<th></th>
						</tr>
					</thead>
					<tbody v-if="pointTransactions.length > 0">
						<tr v-for="(transaction, index) in pointTransactions" :key="index">
							<td style="text-align: center;">
								<label class="ui orange label">{{ transaction.status | uppercase }}</label>
							</td>
							<td style="text-align: center;">
								<label class="ui red label">{{ transaction.cashout_transaction_type | uppercase }}</label>
							</td>
							<td style="text-align: right;">
								{{ totalCashOutPoints(transaction.cashout_transaction_type, transaction.referral_points, transaction.left_points, transaction.right_points) }}pts
							</td>
							<td>{{ transaction.created_at | moment('MMM DD, YYYY h:mm:ss a') }}</td>
							<td>
								<a class="ui fluid mini button" :href="pointTransactionLink(transaction.id)">View</a>
							</td>
						</tr>
					</tbody>
					<tbody v-if="pointTransactions.length < 1">
						<tr>
							<td colspan="5">No Cash Out History</td>
						</tr>
					</tbody>
				</table>

			</div>
			<div class="row">
				<div class="three wide column"></div>
				<div class="ten wide center aligned column">
					<pagination :data="pointTransactionsData" v-bind:showDisabled="true" icon="chevron" v-on:change-page="getPointTransactions" size="tiny"></pagination>
				</div>
			</div>
		</div>
		
	</div>
</template>
<script>
	import pagination from 'laravel-vue-semantic-ui-pagination';
	export default {	
		props: {
			user_id: {
				type: String,
				required: true
			},
			limit: {
				type: String
			}
		},
		components: {
			pagination
		},
		data: () => ({
			pointTransactions: [],
			pointTransactionsData: {},
			total_cashout: 0,
			total_cashout_pairing: 0,
			total_cashout_referral: 0,
			showLoading: true,
		}),
		created() {
			this.getPointTransactions();
		},
		methods: {
			getPointTransactions(page) {
				if (typeof page === 'undefined') {
					page = 1;
				}

				this.showLoading = true;

				axios.get('/member/api/cashout/' + this.user_id, {
					params: {
						page: page,
						limit: this.limit
					}
				}).then((resp) => {
					console.log(resp);
					this.pointTransactions = resp.data.point_transactions.data;
					this.pointTransactionsData = resp.data.point_transactions;
					this.showLoading = false;
					this.total_cashout = resp.data.total_cashout;
					this.total_cashout_pairing = resp.data.total_cashout_pairing;
					this.total_cashout_referral = resp.data.total_cashout_referral;
				}).catch((err) => {
					console.log(err);
				});
			},

			totalCashOutPoints(cashout_type, referral_points, left_points, right_points) {
				if (cashout_type == 'referral') {
					return parseInt(referral_points);
				} else if (cashout_type == 'pairing') {
					return parseInt(left_points) + parseInt(right_points);
				} else if (cashout_type == 'all') {
					return parseInt(left_points) + parseInt(right_points) + parseInt(referral_points);
				}
			},

			totalConfirmedPoints(cashout_type, referral_points, left_points, right_points) {
				if (cashout_type == 'referral') {
					return parseInt(referral_points);
				} else if (cashout_type == 'pairing') {
					return parseInt(left_points) + parseInt(right_points);
				}
			},

			transactionHashLink(hash) {
				return 'https://blockchain.info/tx/' + hash;
			},

			pointTransactionLink(id) {
				return '/member/cashout/details/' + id;
			}
		}
	}
</script>