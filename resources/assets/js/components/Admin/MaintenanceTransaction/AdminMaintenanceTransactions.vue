<template>
	<div>
		<div class="ui basic segment" :class="{ loading: show_loading }">
			<div class="ui grid container" >
				<div class="row">
					<div class="nine wide column">
						<div class="ui compact small menu">
							<a class="item" :class="{active: filter_by_status == ''}" @click.prevent="updateStatus('')">
								Total <div class="ui label">{{ total }}</div>
							</a>
							<a class="item" :class="{active: filter_by_status == 'pending'}" @click.prevent="updateStatus('pending')">
								Pending <div class="ui orange label">{{ total_pending }}</div>
							</a>
							<a class="item" :class="{active: filter_by_status == 'confirmed'}" @click.prevent="updateStatus('confirmed')">
								Confirmed <div class="ui green label">{{ total_confirmed }}</div>
							</a>
							<a class="item" :class="{active: filter_by_status == 'denied'}" @click.prevent="updateStatus('denied')">
								Denied <div class="ui red label">{{ total_denied }}</div>
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
										<input type="text" placeholder="Search..." v-model="search_input" @keyup.enter="getMaintenanceTransactions">
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
									<th>Member(alias)</th>
									<th>Maintenance Cost</th>
									<th>Date</th>
									<th></th>
								</tr>
							</thead>
							<tbody v-if="maintenance_transactions.length > 0">
								<tr v-for="(transaction, index) in maintenance_transactions" :key="transaction.maintenance_transaction_id">
									<td class="collapsing">
										<label class="ui orange label">{{ transaction.status | uppercase }}</label>
									</td>
									<td><strong>{{ transaction.fullname }} ({{ transaction.alias_name }})</strong></td>
									<td><strong>{{ transaction.maintenance_cost | currency }}</strong></td>
									<td>{{ transaction.created_at | moment('MMM DD, YYYY h:mm:ss a') }}</td>
									<td>
										<button class="ui fluid mini button" @click.prevent="viewTransaction(transaction.maintenance_transaction_id)">
										VIEW
										</button>
									</td>
								</tr>
							</tbody>
							<tbody v-if="(maintenance_transactions.length < 1 && show_loading == false)">
								<tr>
									<td colspan="6">No transactions found...</td>
								</tr>
							</tbody>
						</table>
					<pagination :data="maintenance_transactions_data" v-bind:showDisabled="true" icon="chevron" v-on:change-page="getMaintenanceTransactions" size="tiny" :limit="limit"></pagination>
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
			show_loading: false,
			search_loading: false,
			maintenance_transactions_data: {},
			maintenance_transactions: [],
			selected_member_id: 0,
			selected_transaction_id: 0,
			filter_by_status: 'pending',
			search_by: '',
			search_input: '',
			total: 0,
			total_pending: 0,
			total_confirmed: 0,
			total_denied: 0,
			total_maintenance: 0,
			total_maintenance_pending: 0,
			total_maintenance_confirmed: 0,
			total_maintenance_denied: 0,
			limit: 3
		}),
		created() {
			this.getMaintenanceTransactions();
		},
		watch: {
			filter_by_status(val) {
				this.getMaintenanceTransactions(1);
			}
		},
		methods: {
			updateStatus(status) {
				this.filter_by_status = status;
			},

			getMaintenanceTransactions(page) {
				if (typeof page === 'undefined') {
					page = 1;
				}

				this.maintenance_transactions = [];
				this.maintenance_transactions_data = {};

				axios.get('/tbcgoldadmin/api/transactions/maintenance', {
					params: {
						page: page,
						search_by: this.search_by,
						search_input: this.search_input,
						filter_by_status: this.filter_by_status
					}
				}).then((resp) => {
					console.log(resp);
					this.maintenance_transactions = resp.data.maintenance_transactions.data;
					this.maintenance_transactions_data = resp.data.maintenance_transactions;

					this.total_maintenance = resp.data.total_maintenance;
					this.total_maintenance_pending = resp.data.total_maintenance_pending;
					this.total_maintenance_confirmed = resp.data.total_maintenance_confirmed;
					this.total_maintenance_denied = resp.data.total_maintenance_denied;

					this.total= resp.data.total;
					this.total_pending = resp.data.total_pending;
					this.total_confirmed = resp.data.total_confirmed;
					this.total_denied = resp.data.total_denied;

				}).catch((err) => {
					console.log(err);
				})
			},


			viewTransaction(id) {
				window.location.href = '/tbcgoldadmin/transactions/maintenance/details/' + id;
			}
		}
	}
</script>