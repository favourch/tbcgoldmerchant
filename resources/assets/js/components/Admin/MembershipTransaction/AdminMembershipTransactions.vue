<template>
	<div>
		<div class="ui grid basic segment container" :class="{ loading: show_loading }">
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
									<option value="transaction_hash">Transaction Hash</option>
									<option value="alias_name">Alias Name</option>
									<option value="fullname">Member Name</option>
								</select>
							</div>
							<div class="field">
								<div class="ui icon input" :class="{ loading: search_loading }">
									<input type="text" placeholder="Search..." v-model="search_input" @keyup.enter="getTransactions">
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
								<th>Market Plan</th>
								<th>Member(alias)</th>
								
								<th>Date</th>
								<th></th>
							</tr>
						</thead>
						<tbody v-if="(membership_transactions.length > 0 && show_loading == false)">
							<tr v-for="(transaction, index) in membership_transactions" :key="transaction.membership_transaction_id">
								
								<td>
									<label class="ui orange label">{{ transaction.status | uppercase }}</label>
								</td>
								<td>
									
									<label class="ui pink label" v-if="transaction.name == 'level-1'">
										<strong>Matrix 1 - {{ transaction.price | currency }}</strong>
									</label>
									<label class="ui blue label" v-if="transaction.name == 'level-2'">
										<strong>Matrix 2 - {{ transaction.price | currency }}</strong>
									</label>
								</td>
								<td><strong>{{ transaction.fullname }} ({{ transaction.alias_name }})</strong></td>
								<td>{{ transaction.created_at | moment('MMM DD, YYYY h:mm:ss a') }}</td>
								<td>
									<button class="ui fluid mini button" @click.prevent="viewTransaction(transaction.membership_transaction_id)">
										VIEW
									</button>
								</td>
							</tr>
						</tbody>
						<tbody v-if="(membership_transactions.length < 1 && show_loading == false)">
							<tr>
								<td colspan="6">No transactions found...</td>
							</tr>
						</tbody>
					</table>
				<pagination :data="membership_transactions_data" v-bind:showDisabled="true" icon="chevron" v-on:change-page="getTransactions" size="tiny" :limit="limit"></pagination>
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
			membership_transactions_data: {},
			membership_transactions: [],
			selected_member_id: 0,
			selected_transaction_id: 0,
			filter_by_status: 'pending',
			search_by: '',
			search_input: '',
			total: 0,
			pending: 0,
			confirmed: 0,
			denied: 0,
			total_cashout: 0,
			total_cashout_confirmed: 0,
			total_cashout_pending: 0,
			total_cashout_denied: 0,
			limit: 3
		}),
		created() {
			this.getTransactions();
		},
		watch: {
			filter_by_status(val) {
				this.getTransactions(1);
			}
		},
		methods: {
			updateStatus(status) {
				this.filter_by_status = status;
			},

			getTransactions(page) {
				this.show_loading = true;
				if (typeof page === 'undefined') {
					page = 1;
				}

				this.membership_transactions = [];
				this.membership_transactions_data = {};

				axios.get('/tbcgoldadmin/api/transactions/membership', {
					params: {
						page: page,
						search_by: this.search_by,
						search_input: this.search_input,
						filter_by_status: this.filter_by_status
					}
				}).then((resp) => {
					console.log(resp);
					this.membership_transactions = resp.data.membership_transactions.data;
					this.membership_transactions_data = resp.data.membership_transactions;

					this.total_cashout = resp.data.total_cashout;
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

			viewTransaction(id) {
				window.location.href = '/tbcgoldadmin/transactions/membership/details/' + id;
			}
		}
	}
</script>