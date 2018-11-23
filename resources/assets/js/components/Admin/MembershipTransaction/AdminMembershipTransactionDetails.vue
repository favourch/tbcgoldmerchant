<template>
	<div>	
		<div class="ui basic segment" :class="{ loading: show_loading }">
			<div class="ui grid container">
				<div class="row">
					<div class="sixteen wide column right aligned">
						<a class="ui button" :href="viewTransactionListing()"><i class="arrow left icon"></i> Back</a>
					</div>
				</div>
				<div class="row">
					<div class="eight wide column">
						<h3>
							<label class="ui orange large label">
								Status: {{ status | capitalize }}
							</label>
						</h3>
					</div>
					<div class="eight wide column right aligned">
						<h3>Created Date: {{ created_date | moment('MMM DD, YYYY h:mm:ss a') }}</h3>
					</div>
					<div class="sixteen wide column">
						<div class="ui divider"></div>
						<h3>Member: {{ member_name | capitalize}} ({{ member_alias_name }})</h3>
					</div>
				</div>
				<div class="row">
					<div class="sixteen wide column">
						<table class="ui celled table">
							<tbody>
								<tr>
									<td style="width: 20%;">
										<strong>Transaction Type</strong>
									</td>
									<td>
										<strong>{{ transaction_type | uppercase }}</strong>
									</td>
								</tr>
								<tr>
									<td><strong>Transaction Hash</strong></td>
									<td><a :href="transaction_link" target="_blank"><strong>{{ transaction_hash }}</strong></a></td>
								</tr>
								<tr>
									<td><strong>Membership Plan</strong></td>
									<td v-if="plan_name == 'level-1'">Matrix-1 ({{ plan_cost | currency }})</td>
									<td v-if="plan_name == 'level-2'">Matrix-2 ({{ plan_cost | currency }})</td>
									<td v-if="plan_name == 'level-3'">Matrix-3 ({{ plan_cost | currency }})</td>
								</tr>
								<tr>
									<td><strong>Upgrading Deduction</strong></td>
									<td>{{ upgrading_deduction | currency }}</td>
								</tr>
								<tr>
									<td><strong>Current BTC Value</strong></td>
									<td>{{ current_btc_value | uppercase }}</td>
								</tr>
								<tr>
									<td><strong>Reciever Wallet Address</strong></td>
									<td>{{ admin_btc_wallet | uppercase }}</td>
								</tr>
								<tr>
									<td><strong>Total Gift Cheque Deducted</strong></td>
									<td>{{ total_gc | currency }}</td>
								</tr>
								<tr v-if="status == 'confirmed'">
									<td><strong>CD Balance</strong></td>
									<td>{{ commission_balance | currency }}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="sixteen wide column" style="padding-left: 20px;">
						<button class="ui violet button" type="button" @click.prevent="showConfirmTransactionModal" v-if="(status !== 'confirmed')">Confirm</button>
						<button class="ui red button" type="button" @click.prevent="showDenyTransactionModal" v-if="status == 'pending'">Deny</button>
					</div>
				</div>
			</div>
		</div>

		<div class="ui confirm-transaction small modal">
			<div class="header">
				Confirm Transaction
			</div>
			<div class="image content">
				<div class="description">
					<div class="ui form">
					<div class="field" :class="{ error: errors.has('commission_balance') }">
						<label>Enter Commission Deduction ($) <i>(leave 0 if no commission deduction</i>)</label>
							<input
								type="number"
								name="commission_balance"
								v-model.number="commission_balance"
								v-validate="{ required: true }"
								data-vv-as="Commission Deduction"
							/>
							<div class="ui pointing red basic label" v-if="errors.has('commission_balance')">
								{{ errors.first('commission_balance') }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="closeConfirmTransactionModal">Cancel</div>
				<div class="ui violet button" @click.prevent="confirmTransaction" v-if="transaction_type == 'membership'" :class="{ loading: show_loading }">Confirm</div>
				
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
				<div class="ui red button" @click.prevent="denyTransaction" v-if="transaction_type == 'membership'">Deny</div>
				
			</div>
		</div>
	</div>
</template>
<script>
	export default {
		name: 'admin-transaction-transaction',
		props: {
			membership_transaction_id: {
				type: Number,
				required: true
			}
		},
		data: () => ({
			transaction_type: '',
			transaction_hash: '',
			transaction_confirm_no: 0,
			current_btc_value: 0,
			upgrading_deduction: 0,
			total_gc: 0,
			plan_id: null,
			plan_cost: 0,
			plan_name: '',
			user_id: '',
			user_tbc_wallet: '',
			admin_tbc_wallet: '',
			admin_btc_wallet: '',
			status: '',
			member_name: '',
			member_alias_name: '',
			commission_balance: 0,
			transaction_link: '',
			created_date: '',
			show_loading: false
		}),
		created () {
			this.getTransactionDetails();

		},
		methods: {
			getTransactionDetails() {
				axios.get('/tbcgoldadmin/api/transactions/membership/details/' + this.membership_transaction_id).then((resp) => {
					console.log(resp);
					this.user_id = resp.data.membership_transaction.user_detail.user_id;
					this.member_name = resp.data.membership_transaction.user_detail.fullname;
					this.member_alias_name = resp.data.membership_transaction.user.alias_name;
					this.transaction_type = resp.data.membership_transaction.transaction_type;
					this.transaction_hash = resp.data.membership_transaction.transaction_hash;
					this.upgrading_deduction = resp.data.membership_transaction.upgrading_deduction;
					this.transaction_confirm_no = resp.data.membership_transaction.transaction_confirm_no;
					this.current_btc_value = resp.data.membership_transaction.current_btc_value;
					this.admin_btc_wallet = resp.data.membership_transaction.admin_btc_wallet;
					this.status = resp.data.membership_transaction.status;
					this.plan_id = resp.data.membership_transaction.plan.id;
					this.plan_name = resp.data.membership_transaction.plan.name;
					this.plan_cost = resp.data.membership_transaction.plan.price;
					this.commission_balance =  resp.data.membership_transaction.user.commission_balance;
					this.transaction_link = 'https://blockchain.info/tx/' + this.transaction_hash;
					this.created_date = resp.data.membership_transaction.created_at;
					this.total_gc = resp.data.total_gc;
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

			confirmTransaction() {
				this.show_loading = true;

				if (this.plan_id == 1) {
					axios.post('/tbcgoldadmin/api/transactions/membership/confirm', {
						user_id: this.user_id,
						membership_transaction_id: this.membership_transaction_id,
						commission_balance: this.commission_balance
					}).then((resp) => {
						console.log(resp);
						if (resp.data.status == 'ok') {
							this.closeConfirmTransactionModal();
							this.getTransactionDetails();
							this.$notify({
					          title: 'Success',
					          message: 'Membership Confirmed',
					          type: 'success'
					        });
						}
						this.show_loading = false;
					}).catch((err) => {
						console.log(err);
					});
				} else if (this.plan_id == 2) {
					axios.post('/tbcgoldadmin/api/upgrade-member', {
						user_id: this.user_id,
						membership_transaction_id: this.membership_transaction_id,
						commission_balance: this.commission_balance
					}).then((resp) => {
						console.log(resp);
						if (resp.data.status == 'ok') {
							this.closeConfirmTransactionModal();
							this.getTransactionDetails();
							this.$notify({
					          title: 'Success',
					          message: 'Membership Confirmed',
					          type: 'success'
					        });
						}
						this.show_loading = false;
					}).catch((err) => {
						console.log(err);
					});
				}
				
			},

			denyTransaction() {
				this.show_loading = true;
				axios.post('/tbcgoldadmin/api/transactions/membership/deny', {
					user_id: this.user_id,
					membership_transaction_id: this.membership_transaction_id
				}).then((resp) => {
					console.log(resp);
					if (resp.data.status == 'ok') {
						this.closeDenyTransactionModal();
						this.getTransactionDetails();
						this.$notify({
				          title: 'Success',
				          message: 'Membership Denied',
				          type: 'success'
				        });
					}
					this.show_loading = false;
				}).catch((err) => {
					console.log(err);
				});
			},

			viewTransactionListing() {
				return '/tbcgoldadmin/transactions/membership';
			}
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