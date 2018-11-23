<template>
<div>
	
	<div class="ui grid basic segment container" :class="{ loading: show_loading }">
		<div class="row">
			<div class="sixteen wide column right aligned">
				<a class="ui button" :href="viewTransactionListing()"><i class="arrow left icon"></i> Back</a>
			</div>
		</div>
		<div class="row">
			<div class="eight wide column">
				<h3>
					<span class="ui large orange label">Status: {{ transaction_status | capitalize }}</span>
				</h3>
			</div>
			<div class="eight wide right aligned column">
				<h3>Created Date: {{ created_at | moment('MMM DD, YYYY h:mm:ss a') }}</h3>
			</div>

			<div class="sixteen wide column">
				<div class="ui divider"></div>
				<table class="ui celled table">
					<tbody>
						<tr class="active">
							<td colspan="2"><h3>Member Details</h3></td>
						</tr>
						<tr>
							<td style="width: 20%"><strong>Member</strong></td>
							<td>{{ member.fullname }} ({{ member.alias }})</td>
						</tr>
						<tr>
							<td><strong>Unilevel Bonus</strong></td>
							<td>{{ member.unilevel_bonus | currency }}</td>
						</tr>
						<tr>
							<td><strong>BTC Wallet</strong></td>
							<td>{{ member.btc_wallet }}</td>
						</tr>
					</tbody>
				</table>
				
				<table class="ui inverted celled table">
					<tbody>
						<tr>
							<td colspan="2">
								<h3>Cash Out Request Details</h3>
							</td>
						</tr>
						<tr>
							<td style="width: 20%"><strong>Total Unilevel Bonus ($)</strong></td>
							<td>{{ unilevel_bonus | currency }}</td>
						</tr>
					</tbody>
				</table>


				<div v-if="transaction_status == 'pending'">
	
					<div class="ui divider"></div>
					<div class="ui grid container">
						<div class="row">
							<div class="three wide column"></div>
							<div class="ten wide column">
								<div class="ui segment2">
									<div class="ui form">
										<h4>Cash Out Unilevel Bonus to Confirm</h4>
										<div class="field" :class="{ error: errors.has('confirmed_unilevel_bonus') }">
											<label>Enter Unilevel Bonus to Confirm ($)</label>
											<input
											type="number"
											name="confirmed_unilevel_bonus"
											v-model.number="confirmed_unilevel_bonus"
											v-validate="{ required: true, numeric: true, max_value:member.unilevel_bonus }"
											data-vv-as="Unilevel Bonus"
											/>
											<div class="ui pointing red basic label" v-if="errors.has('confirmed_unilevel_bonus')">
												{{ errors.first('confirmed_unilevel_bonus') }}
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="three wide column"></div>
						</div>
						<div class="row">
							<div class="three wide column"></div>
							<div class="ten wide column">
								<button class="ui fluid violet large button" type="button" @click.prevent="showConfirmUnilevelTransactionModal" v-if="transaction_status == 'pending'">
								Confirm Cash Out
								</button>
							</div>
							<div class="three wide column"></div>
							<div class="three wide column"></div>
							<div class="ten wide column">
								<div class="ui horizontal divider">
									Or
								</div>
							</div>
							<div class="three wide column"></div>

							<div class="three wide column"></div>
							<div class="ten wide column">
								<button class="ui fluid red large button" type="button" @click.prevent="showDenyTransactionModal" v-if="transaction_status == 'pending'">
								Deny Cash Out
								</button>
							</div>
						</div>
					</div>
				</div>


				<div v-if="transaction_status == 'confirmed'">
					<table class="ui inverted celled table">
						<tbody>
							<tr class="ui center aligned">
								<td colspan="4">
									<h3>Confirmed Cash Out Request Details</h3>
								</td>
							</tr>
							<tr>
								<td style="width: 15%">
									Total Confirmed Unilevel
								</td>
								<td>
									{{ confirmed_unilevel_bonus | currency }}
								</td>
							</tr>
							
							<tr>
								<td colspan="4"></td>
							</tr>
							<tr>
								<td>
									Grand Total
								</td>
								<td colspan="3">
									<h3 class="ui blue big label">{{ getPtsToUsdValue | currency }} =  {{ current_btc_value }} BTC</h3>
								</td>
							</tr>
							<tr>
								<td colspan="4"></td>
							</tr>
						</tbody>
						
						<tbody>
							<tr>
								<td style="width: 20%">Transaction Hash</td>
								<td colspan="3">
									<div class="ui action fluid input">
										<input type="text" placeholder="Transaction Hash" v-model="transaction_hash">
										<div class="ui button" @click.prevent="transactionHashLink()"><i class="external alternate icon"></i> Go To Link</div>
										<div class="ui green button" :class="{loading: transaction_hash_loading}" @click.prevent.stop="updateTransactionHash(unilevel_transaction_id)"><i class="edit icon"></i> Update</div>
									</div>
								</td>
							</tr>
							<tr>
								<td style="width: 20%">Confirmed Date</td>
								<td colspan="3">{{ confirmed_at | moment('MMM DD, YYYY h:mm:ss a') }}</td>
							</tr>
						</tbody>
					</table>
				</div>
				
			</div>
		</div>
	</div>
	


	<div class="ui confirm-transaction modal small">
		<div class="header">
			Cash Out Request
		</div>
		<div class="scrolling content">
			<div class="description">
				<div class="ui form">
					<div class="field">
						<label>Total Value to Send in USD($)</label>
						<div class="ui action input">
							<input type="text" :value="getPtsToUsdValue" readonly />
							<button class="ui teal right labeled icon button" @click.prevent="copyText('usd')">
							<i class="copy icon"></i>
							Copy
							</button>
						</div>
					</div>
					<div class="field">
						<label>Total Value to Send in BTC </label>
						<div class="ui action input">
							<input type="text" :value="current_btc_value" readonly />
							<button class="ui teal right labeled icon button" @click.prevent="copyText('btc')">
							<i class="copy icon"></i>
							<span v-if="!btc_currency_loading">Copy</span>
							<span v-if="btc_currency_loading">Loading..</span>
							</button>
						</div>
					</div>
					<div class="ui divider"></div>
					<div class="field">
						<label>Member BTC Wallet</label>
						<div class="ui action input">
							<input type="text" :value="member.btc_wallet" readonly />
							<button class="ui teal right labeled icon button" @click.prevent="copyText('wallet')">
							<i class="copy icon"></i>
							Copy
							</button>
						</div>
					</div>
					<div class="field">
						<label>Transaction Hash</label>
						<input 
							type="text" 
							name="transaction_hash" 
							v-model="transaction_hash" 
							v-validate="'required'" 
							data-vv-as="Transaction Hash" 
						/>
					</div>
				</div>
			</div>
		</div>
		<div class="actions">
			<div class="ui button" @click.prevent="closeConfirmUnilevelTransactionModal">Cancel</div>
			<div class="ui violet button" @click.prevent="confirmUnilevelTransaction">
				<span v-if="transaction_status == 'pending'">Confirm</span> <span v-else>Update</span> Cash Out
			</div>
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
			unilevel_transaction_id: {
				type: Number,
				required: true
			}
		},
		data: () => ({
			user_id: 0,
			current_btc_value: 0,
			unilevel_bonus: 0,
			confirmed_unilevel_bonus: 0,
			btc_value: 0,
			usd_value: 0,
			transaction_hash: 'hash',
			transaction_status: '',
			confirmed_at: '',
			created_at: '',

			member: {
				fullname: '',
				alias: '',
				unilevel_bonus: 0,
				btc_wallet: ''
			},

			show_loading: false,
			btc_currency_loading: false,
			transaction_hash_loading: false
		}),

		created () {
			this.getUnilevelTransactionDetails();
		},

		watch: {
			confirmed_unilevel_bonus: _.debounce(function() {
				this.getBtcCurrency(this.confirmed_unilevel_bonus);
			}, 800)
		},

		computed: {
			getPtsToUsdValue() {
				return this.confirmed_unilevel_bonus;
			}
		},

		methods: {
			getUnilevelTransactionDetails() {

				this.show_loading = true;

				axios.get('/tbcgoldadmin/api/transactions/unilevel/details/' + this.unilevel_transaction_id).then((resp) => {

					console.log(resp);

					this.user_id = parseInt(resp.data.unilevel_transaction.user_id);
					this.transaction_status = resp.data.unilevel_transaction.status;
					this.created_at = resp.data.unilevel_transaction.created_at;
					this.unilevel_bonus = parseFloat(resp.data.unilevel_transaction.unilevel_bonus);

					if (this.transaction_status == 'pending') {
						this.confirmed_unilevel_bonus = parseFloat(resp.data.unilevel_transaction.unilevel_bonus);
					} else if (this.transaction_status == 'confirmed') {
						this.confirmed_unilevel_bonus = parseFloat(resp.data.unilevel_transaction.confirmed_unilevel_bonus);
						this.current_btc_value = resp.data.unilevel_transaction.current_btc_value;
						this.transaction_hash = resp.data.unilevel_transaction.transaction_hash;
						this.confirmed_at = resp.data.unilevel_transaction.confirmed_at;
					}

					// member
					this.member.alias = resp.data.unilevel_transaction.user.alias_name;
					this.member.fullname = resp.data.unilevel_transaction.user_detail.fullname;
					this.member.unilevel_bonus = parseFloat(resp.data.unilevel_transaction.user.unilevel_bonus);
					this.member.btc_wallet = resp.data.unilevel_transaction.user_detail.btc_wallet;
					
					this.show_loading = false;

				}).catch((err) => {

					console.log(err);

				});
			},

			confirmUnilevelTransaction() {
				this.show_loading = true;
				this.closeConfirmUnilevelTransactionModal();

				axios.post('/tbcgoldadmin/api/transactions/unilevel/confirm', {
					unilevel_transaction_id: this.unilevel_transaction_id,
					transaction_hash: this.transaction_hash,	
					confirmed_unilevel_bonus: this.confirmed_unilevel_bonus,
					user_id: this.user_id
				}).then((resp) => {

					console.log(resp);
					
					this.$notify({
			          title: 'Success',
			          message: 'Cash Out Confirmed',
			          type: 'success'
			        });
					
					setTimeout(() => {
						this.show_loading = false;
						window.location.reload();
					}, 1000);

				}).catch((err) => {
					console.log(err);
				});
			},

			denyTransaction() {
				this.show_loading = true;
				this.closeDenyTransactionModal();

				axios.post('/tbcgoldadmin/api/transactions/unilevel/deny', {
					unilevel_transaction_id: this.unilevel_transaction_id
				}).then((resp) => {
					console.log(resp);

					this.$notify({
			          title: 'Success',
			          message: 'Cash Out Denied',
			          type: 'success'
			        });
					
					setTimeout(() => {
						this.show_loading = false;
						window.location.reload();
					}, 1000);

				}).catch((err) => {
					console.log(err);
				});
			},

			getBtcCurrency(usd_value) {
				this.btc_currency_loading = true;
				axios.get('/tbcgoldadmin/api/get-usd-to-btc-value', {
					params: {
						usd_value: usd_value
					}
				}).then((resp) => {
					this.current_btc_value = resp.data;
					this.btc_currency_loading = false;
				}).catch((err) => {
					console.log(err);
				});
			},

			copyText(value) {
				let val = '';
				if (value == 'btc') {
					val = this.current_btc_value;
				} else if (value == 'usd') {
					val = this.getPtsToUsdValue;
				} else if (value == 'wallet') {
					val = this.member.btc_wallet;
				}

		        this.$copyText(val).then((resp) => {
		        	this.$notify({
			          title: 'Copied',
			          message: '',
			          type: 'success'
			        });
		        }).catch((err) => {
		        	console.log(err);
		        });
			},

			updateTransactionHash(id) {
				this.transaction_hash_loading = true
				axios.post('/tbcgoldadmin/api/transactions/unilevel/hash/update', {
					unilevel_transaction_id: id,
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

			showConfirmUnilevelTransactionModal() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.getBtcCurrency(this.getPtsToUsdValue);
						$('.ui.confirm-transaction.modal').modal('show');
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				})
				
			},

			closeConfirmUnilevelTransactionModal() {
				$('.ui.confirm-transaction.modal').modal('hide');
			},

			showDenyTransactionModal() {
				$('.ui.deny-transaction.modal').modal('show');
			},

			closeDenyTransactionModal() {
				$('.ui.deny-transaction.modal').modal('hide');
			},

			viewTransactionListing() {
				return '/tbcgoldadmin/transactions/cashout';
			},

			transactionHashLink() {
				window.open('https://blockchain.info/tx/' + this.transaction_hash, '_blank');
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