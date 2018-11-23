<template>
	<div>
		<div class="ui basic segment" :class="{loading: show_loading}">
			<div class="ui grid container">
				<div class="row">
					<div class="sixteen wide right aligned column">
						<a class="ui button" :href="maintenanceTransactionList()"><i class="arrow left icon"></i> Back</a>
						<br />
						<br />
					</div>
				</div>
				<div class="row">
					<div class="eight wide column">
						<h3 class="ui header">
							<label class="ui orange label">Status: {{ status | uppercase }}</label>
						</h3>
					</div>
					<div class="eight wide right aligned column">
						<h3 class="ui header">Created Date: {{ created_at | moment('MMMM DD, YYYY h:mm:ss a') }}</h3>
					</div>

					<div class="sixteen wide column">
						
						<div class="ui divider"></div>

						<table class="ui table">
							<tbody>
								<tr>
									<td style="width: 15%;"><strong>Member (alias)</strong></td>
									<td>{{ member.fullname }} ({{ member.alias }})</td>
								</tr>
								<tr>
									<td style="width: 15%;"><strong>Transaction Hash</strong></td>
									<td>
										<div class="ui action fluid input">
											<input type="text" placeholder="Transaction Hash" v-model="transaction_hash">
											<div class="ui button" @click.prevent="transactionHashLink()"><i class="external alternate icon"></i> Go To Link</div>
											<div class="ui green button" :class="{loading: transaction_hash_loading}" @click.prevent.stop="updateTransactionHash(maintenance_transaction_id)"><i class="edit icon"></i> Update</div>
										</div>
									</td>
								</tr>
								<tr>
									<td style="width: 15%;"><strong>Maintenance Cost</strong></td>
									<td>{{ maintenance_cost | currency }} = {{ current_btc_value }} BTC</td>
								</tr>
								<tr>
									<td style="width: 15%;"><strong>Admin BTC Wallet</strong></td>
									<td>{{ admin_btc_wallet }}</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="sixteen wide column" style="padding-left: 20px">
						<br />
						<button class="ui violet button" v-if="status != 'confirmed'" @click.prevent="showConfirmTransactionModal()">Confirm</button>	
						<button class="ui red button" v-if="status == 'pending'" @click.prevent="showDenyTransactionModal()">Deny</button>	
					</div>

				</div>
			</div>
		</div>

		<div class="ui confirm-transaction modal mini">
			<div class="header">
				Confirm Transaction
			</div>
			<div class="image content">
				<div class="description">
					Are you sure?
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="closeConfirmTransactionModal">Cancel</div>
				<div class="ui violet button" @click.prevent="confirmTransaction">Confirm</div>
				
			</div>
		</div>

		<div class="ui deny-transaction modal mini">
			<div class="header">
				Deny Transaction
			</div>
			<div class="image content">
				<div class="description">
					Are you sure?
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="closeDenyTransactionModal">Cancel</div>
				<div class="ui red button" @click.prevent="denyTransaction">Deny</div>
				
			</div>
		</div>

	</div>
</template>
<script>
	export default {
		props: {
			maintenance_transaction_id: {
				type: Number,
				required: true
			}
		},

		data: () => ({
			show_loading: false,
			user_id: null,
			transaction_hash: '',
			transaction_link: '',
			status: '',
			maintenance_cost: 0,
			current_btc_value: 0,
			admin_btc_wallet: '',
			member: {
				fullname: '',
				alias: ''
			},
			created_at: '',
			transaction_hash_loading: false
		}),

		created() {
			this.getTransactionDetails();
		},

		methods: {
			getTransactionDetails() {
				axios.get('/tbcgoldadmin/api/transactions/maintenance/details/' + this.maintenance_transaction_id).then((resp) => {
					console.log(resp);
					this.user_id = resp.data.maintenance_transaction.user_id;
					this.transaction_hash = resp.data.maintenance_transaction.transaction_hash;
					this.transaction_link = 'https://blockchain.info/tx/' + this.transaction_hash;
					this.status = resp.data.maintenance_transaction.status;
					this.maintenance_cost = resp.data.maintenance_transaction.maintenance_cost;
					this.current_btc_value = resp.data.maintenance_transaction.current_btc_value;
					this.admin_btc_wallet = resp.data.maintenance_transaction.admin_btc_wallet;
					this.member.fullname = resp.data.maintenance_transaction.user_detail.fullname;
					this.member.alias = resp.data.maintenance_transaction.user.alias_name;
					this.created_at = resp.data.maintenance_transaction.created_at;

					// this.getBtcCurrency(this.maintenance_cost);
				}).catch((err) => {
					console.log(err);
				});
			},

			confirmTransaction() {
				axios.post('/tbcgoldadmin/api/transactions/maintenance/confirm', {
					maintenance_transaction_id: this.maintenance_transaction_id
				}).then((resp) => {
					console.log(resp);
					this.getTransactionDetails();
					this.closeConfirmTransactionModal();
					this.$notify({
				        title: 'Success',
				        message: 'Maintenance is Confirmed',
				        type: 'success'
				    });
				}).catch((err) => {
					console.log(err);
				})
			},

			denyTransaction() {
				axios.post('/tbcgoldadmin/api/transactions/maintenance/deny', {
					maintenance_transaction_id: this.maintenance_transaction_id
				}).then((resp) => {
					console.log(resp);
					this.getTransactionDetails();
					this.closeDenyTransactionModal();
					this.$notify({
				        title: 'Success',
				        message: 'Maintenance is Denied',
				        type: 'success'
				    });
				}).catch((err) => {
					console.log(err);
				})
			},

			getBtcCurrency(usd_value) {
				this.btc_currency_loading = true;
				axios.get('/tbcgoldadmin/api/get-usd-to-btc-value', {
					params: {
						usd_value: usd_value
					}
				}).then((resp) => {
					this.current_btc_value = resp.data;
				}).catch((err) => {
					console.log(err);
				});
			},

			showConfirmTransactionModal() {
				$('.ui.confirm-transaction.modal').modal('show');
			},

			closeConfirmTransactionModal() {
				$('.ui.confirm-transaction.modal').modal('hide');
			},

			showDenyTransactionModal() {
				$('.ui.deny-transaction.modal').modal('show');
			},

			closeDenyTransactionModal() {
				$('.ui.deny-transaction.modal').modal('hide');
			},

			maintenanceTransactionList() {
				return '/tbcgoldadmin/transactions/maintenance';
			},

			transactionHashLink() {
				window.open('https://blockchain.info/tx/' + this.transaction_hash, '_blank');
			},

			updateTransactionHash(id) {
				this.transaction_hash_loading = true
				axios.post('/member/api/monthly-maintenance/update/transaction-hash', {
					maintenance_transaction_id: id,
					transaction_hash: this.transaction_hash
				}).then((resp) => {
					console.log(resp);
					if (resp.data.status == 'ok') {
						this.$notify({
				          title: 'Success',
				          message: 'Transaction Hash Updated!',
				          type: 'success'
				        });
					}
					this.transaction_hash_loading = false;
				}).catch((err) => {
					console.log(err);
				});
			},
		}
	}
</script>
<style scoped>
	.ui.table {
		border: none;
	}
	.ui.table tr td {
		border-top: none;
	}
</style>