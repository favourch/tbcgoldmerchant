<template>
	<div>
		<div class="ui basic segment" :class="{ loading: show_loading }">
			<div class="ui grid container">
				<div class="row">
					<div class="four wide column">
						<strong>Total Cash Out: {{ total_cashout | currency }}</strong>
					</div>
					<div class="four wide column">
						<strong>Total Pending: {{ total_cashout_pending | currency }}</strong>
					</div>
					<div class="four wide column">
						<strong>Total Confirmed: {{ total_cashout_confirmed | currency }}</strong>
					</div>
					<div class="four wide column">
						<strong>Total Denied: {{ total_cashout_denied | currency }}</strong>
					</div>	
					<div class="sixteen wide column">
						<div class="ui divider"></div>
					</div>
					<div class="four wide column">
						<strong>Total Cash Out: {{ total_cashout | currency }}</strong>
					</div>
					<div class="four wide column">
						<strong>Total Referral: {{ total_cashout_referral | currency }}</strong>
					</div>
					<div class="four wide column">
						<strong>Total Pairing: {{ total_cashout_pairing | currency }}</strong>
					</div>
					<div class="four wide column">
						
					</div>	
					<div class="sixteen wide column">
						<div class="ui divider"></div>
					</div>
					<div class="nine wide column">
						<div class="ui compact small menu">
							<a class="item" :class="{active: filter_by_status == ''}" @click.prevent="updateStatus('')">
								Total <div class="ui label">{{ total }}</div>
							</a>
							<a class="item" :class="{active: filter_by_status == 'pending'}" @click.prevent="updateStatus('pending')">
								Pending <div class="ui orange label">{{ pending }}</div>
							</a>
							<a class="item" :class="{active: filter_by_status == 'confirmed'}" @click.prevent="updateStatus('confirmed')">
								Confirmed <div class="ui green label">{{ confirmed }}</div>
							</a>
							<a class="item" :class="{active: filter_by_status == 'denied'}" @click.prevent="updateStatus('denied')">
								Denied <div class="ui red label">{{ denied }}</div>
							</a>
						</div>
					</div>

					<div class="seven wide column">
						<div class="ui form">
							<div class="two fields">
								<div class="field">
									<select class="ui fluid dropdown" v-model="search_by">
										<option value="">Search By</option>
										<option value="alias_name">Alias Name</option>
										<option value="fullname">Member Name</option>
									</select>
								</div>
								<div class="field">
									<div class="ui icon input" :class="{ loading: search_loading }">
										<input type="text" placeholder="Search..." v-model="search_input" @keyup.enter="getCashoutTransactions">
										<i class="search icon"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="sixteen wide column">
						<table class="ui sortable striped table">
							<thead>
								<tr>
									<th>Status</th>
									<th>Member (alias)</th>
									<th>Cash Out Type</th>
									<th>Total Cash Out (pts)</th>
									<th>Date</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(transaction, index) in cashout_transactions" :key="transaction.id">
									<td class="collapsing">
										<label class="ui orange label">{{ transaction.status | uppercase }}</label>
									</td>
									<td>
										<strong>{{ transaction.fullname }} ({{ transaction.alias_name }})</strong>
									</td>
									<td>
										<a class="ui red label">
											<strong>{{ transaction.cashout_transaction_type | capitalize }}</strong>
										</a>
									</td>
									<td>
										{{ getTotalCashOutPoints(transaction.cashout_transaction_type, transaction.referral_points, transaction.left_points, transaction.right_points) }}pts
									</td>
									<td>
										{{ transaction.created_at | moment('MMM DD, YYYY h:mm:ss a') }}
									</td>
									<td>
										<a 
											class="ui icon fluid tiny button" 
											:href="viewTransaction(transaction.cashout_transaction_id)"
										>
											VIEW
										</a>
									</td>
								</tr>
							</tbody>
							<tbody v-if="(cashout_transactions.length < 1 && show_loading == false)">
								<tr>
									<td></td>
									<td colspan="4">No cash out requests found...</td>
								</tr>
							</tbody>
						</table>
						<pagination :data="cashout_transactions_data" v-bind:showDisabled="true" icon="chevron" v-on:change-page="getCashoutTransactions" size="tiny" :limit="limit"></pagination>
					</div>
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
			show_loading: null,
			search_loading: null,
			cashout_transactions_data: {},
			cashout_transactions: [],
			total: 0,
			pending: 0,
			confirmed: 0,
			denied: 0,
			filter_by_status: 'pending',
			search_by: '',
			search_input: '',
			total_cashout: 0,
			total_cashout_referral: 0,
			total_cashout_pairing: 0,
			total_cashout_confirmed: 0,
			total_cashout_pending: 0,
			total_cashout_denied: 0,
			limit: 3
		}),
		created() {
			this.getCashoutTransactions();
		},
		watch: {
			filter_by_status(val) {
				this.getCashoutTransactions(1);
			}
		},
		methods: {

			updateStatus(status) {
				this.filter_by_status = status;
			},
			
			getCashoutTransactions(page) {

				if (typeof page === 'undefined') {
					page = 1;
				}

				this.show_loading = true;

				axios.get('/tbcgoldadmin/api/transactions/cashout/matrix-2', {
					params: {
						page: page,
						search_by: this.search_by,
						search_input: this.search_input,
						filter_by_status: this.filter_by_status
					}
				}).then((resp) => {
					this.cashout_transactions = [];
					this.cashout_transactions_data = {};
					this.cashout_transactions = resp.data.cashout_transactions.data;
					this.cashout_transactions_data = resp.data.cashout_transactions;
					this.total_cashout = resp.data.total_cashout;
					this.total_cashout_referral = resp.data.total_cashout_referral;
					this.total_cashout_pairing = resp.data.total_cashout_pairing;
					this.total_cashout_pending = resp.data.total_cashout_pending;
					this.total_cashout_confirmed = resp.data.total_cashout_confirmed;
					this.total_cashout_denied = resp.data.total_cashout_denied;
					this.total = resp.data.total;
					this.pending = resp.data.pending;
					this.confirmed = resp.data.confirmed;
					this.denied = resp.data.denied;
					this.show_loading = false;
				}).catch((err) => {
					console.log(err);
				});
			},

			getTotalCashOutPoints(cashout_type, referral_points, left_points, right_points) {
				if (cashout_type == 'pairing') {
					return parseInt(left_points) + parseInt(right_points);
				} else if (cashout_type == 'referral') {
					return parseInt(referral_points);
				} else if (cashout_type == 'all') {
					return parseInt(left_points) + parseInt(right_points) + parseInt(referral_points);
				}
			},

			viewTransaction(id) {
				return '/tbcgoldadmin/transactions/cashout/matrix-2/details/' + id;
			}
		}
	}
</script>