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
					<span class="ui large orange label">Status: {{ transaction_status | capitalize }}</span> |
					<span class="ui large red label">Cash Out Type: {{ transaction_type | capitalize }}</span>
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
							<td><strong>Referral Points</strong></td>
							<td>{{ member.referral_points }}pts</td>
						</tr>
						<tr>
							<td><strong>Right Points</strong></td>
							<td>{{ member.right_points }}pts</td>
						</tr>
						<tr>
							<td><strong>Left Points</strong></td>
							<td>{{ member.left_points }}pts</td>
						</tr>
						<tr>
							<td><strong>BTC Wallet</strong></td>
							<td>{{ member.btc_wallet }}</td>
						</tr>
						<tr>
							<td><strong>Upgrading Total Deducted</strong></td>
							<td>{{ member.upgrading_total | currency }}</td>
						</tr>
						<tr class="active" v-if="member.commission_balance > 0">
							<td><strong>CD Acount</strong></td>
							<td>Yes</td>
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
							<td style="width: 20%"><strong>Total Referral Points</strong></td>
							<td>{{ referral.referral_points }}pts = {{ getUsdValueOfReferralPts | currency }}</td>
						</tr>
						<tr>
							<td><strong>Total Pairing Points</strong></td>
							<td>{{ getTotalPairingPoints }}pts = {{ getUsdValueOfPairingPts | currency }}</td>
						</tr>
						<tr>
							<td><strong>Left Points</strong></td>
							<td>{{ pairing.left_points }}pts</td>
						</tr>
						<tr>
							<td><strong>Right Points</strong></td>
							<td>{{ pairing.right_points }}pts</td>
						</tr>
					</tbody>
				</table>
	
				
				<div class="ui compact message">
					<h4 v-if="transaction_status == 'pending'">
						<i class="exclamation large orange circle icon"></i> Member Cash Out Pairing Level: <span class="ui black label">From {{ member.current_cashout_pairing_level }} To {{ member.added_cashout_pairing_level }}</span>
					</h4>
					<h4 v-if="transaction_status == 'confirmed'">
						<i class="exclamation large orange circle icon"></i> Member Cash Out Pairing Level: <span class="ui black label">{{ member.current_cashout_pairing_level }}</span>
					</h4>

					<h4 v-if="member.commission_balance > 0">
						<i class="exclamation large orange circle icon"></i> CD Balance: <span class="ui black label">{{ member.commission_balance | currency }}</span>
					</h4>
				</div>


				<div v-if="transaction_status == 'pending'">
	
					<div class="ui divider"></div>
					<div class="ui grid container">
						<div class="row">
							<div class="eight wide column">
								<div class="ui segment2">
									<div class="ui form">
										<h4>Cash Out Points to Confirm</h4>
										<div class="field" :class="{ error: errors.has('confirmed_referral_points') }" v-if="transaction_type == 'all' || transaction_type == 'referral'">
											<label>Enter Referral Points to Confirm (pts)</label>
											<input
											type="number"
											name="confirmed_referral_points"
											v-model.number="referral.confirmed_referral_points"
											v-validate="{ required: true, numeric: true, max_value:member.referral_points }"
											data-vv-as="Referral Points"
											/>
											<div class="ui pointing red basic label" v-if="errors.has('confirmed_referral_points')">
												{{ errors.first('confirmed_referral_points') }}
											</div>
										</div>
										<div class="field" :class="{ error: errors.has('confirmed_left_points') }" v-if="transaction_type == 'all' || transaction_type == 'pairing'">
											<label>Enter Left Points to Confirm (pts)</label>
											<input
											type="number"
											name="confirmed_left_points"
											v-model.number="pairing.confirmed_left_points"
											v-validate="{ required: true, max_value:member.left_points, is:pairing.confirmed_right_points }"
											data-vv-as="Left Points"
											
											/>
											<div class="ui pointing red basic label" v-if="errors.has('confirmed_left_points')">
												{{ errors.first('confirmed_left_points') }}
											</div>
										</div>
										<div class="field" :class="{ error: errors.has('confirmed_right_points') }" v-if="transaction_type == 'all' || transaction_type == 'pairing'">
											<label>Enter Right Points to Confirm</label>
											<input
											type="number"
											name="confirmed_right_points"
											v-model.number="pairing.confirmed_right_points"
											v-validate="{ required: true, max_value:member.right_points }"
											data-vv-as="Right Points"
											
											/>
											<div class="ui pointing red basic label" v-if="errors.has('confirmed_right_points')">
												{{ errors.first('confirmed_right_points') }}
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="eight wide column">
								<div class="ui segment2">
									<div class="ui form">
										<h4>Deductions</h4>
										<div class="field" :class="{ error: errors.has('deduction_gc') }" v-if="transaction_type == 'all' || transaction_type == 'pairing'">
											<label>Enter Gift Cheque Deduction ($) <small><em>applicable only on pairing pts</em></small></label>
											<input
											type="text"
											name="deduction_gc"
											:value="getGiftChequeDeduction"
											v-validate="{ required: true }"
											data-vv-as="Gift Cheque Deduction"
											/>
											<div class="ui pointing red basic label" v-if="errors.has('deduction_gc')">
												{{ errors.first('deduction_gc') }}
											</div>
										</div>
										<div class="field" :class="{ error: errors.has('deduction_admin_fee') }">
											<label>Enter Admin Fee Deduction (%)</label>
											<input
											type="text"
											name="deduction_admin_fee"
											v-model.number="deduction_admin_fee"
											v-validate="{ required: true }"
											data-vv-as="Admin Fee Deduction"
											
											/>
											<div class="ui pointing red basic label" v-if="errors.has('deduction_admin_fee')">
												{{ errors.first('deduction_admin_fee') }}
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="three wide column"></div>
							<div class="ten wide column">
								<button class="ui fluid violet large button" type="button" @click.prevent="showConfirmPointTransactionModal" v-if="transaction_status == 'pending'">
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
								<td colspan="2" class="ui center aligned">
									<h4>Referral Cash Out</h4>
								</td>
								<td colspan="2" class="ui center aligned">
									<h4>Pairing Cash Out</h4>
								</td>
							</tr>
							<tr>
								<td style="width: 20%">
									Total Confirmed Referral Points
								</td>
								<td style="width: 30%">
									{{ referral.confirmed_referral_points }}pts = {{ getUsdValueOfTotalConfirmedReferralPoints | currency }}
								</td>
								<td style="width: 20%">
									Total Confirmed Pairing Points
								</td>
								<td style="width: 30%">
									{{ getTotalConfirmedPairingPoints }}pts = {{ getUsdValueOfTotalConfirmedPairingPoints | currency }}
								</td>
							</tr>
							
							<tr>
								<td style="width: 20%">Admin Fee Deduction</td>
								<td style="width: 30%">({{ deduction_admin_fee }}%)</td>
								<td style="width: 20%">Admin Fee Deduction</td>
								<td style="width: 30%">({{ deduction_admin_fee }}%)</td>
							</tr>
							<tr>
								<td style="width: 20%"><s>Gift Cheque Deduction</s></td>
								<td style="width: 30%">(0)</td>
								<td style="width: 20%">Gift Cheque Deduction</td>
								<td style="width: 30%">({{ pairing.deduction_gc | currency }})</td>
							</tr>
							<tr>
								<td style="width: 20%">Referral Total</td>
								<td style="width: 30%">{{ getUsdValueOfGrandTotalConfirmedReferralPoints | currency }}</td>
								<td style="width: 20%">Pairing Total</td>
								<td style="width: 30%">{{ getUsdValueOfGrandTotalConfirmedPairingPoints | currency }}</td>
							</tr>
							<tr>
								<td colspan="4"></td>
							</tr>
							<tr>
								<td class="ui right aligned">
									Sub-Total
								</td>
								<td colspan="3">
									<h3 class="ui blue big label">{{ getUsdValueOfGrandTotalConfirmedReferralPoints + getUsdValueOfGrandTotalConfirmedPairingPoints | currency }}</h3>
								</td>
							</tr>
							<tr>
								<td class="ui right aligned">
									Upgrading Deduction
								</td>
								<td colspan="3">
									<h3 class="ui blue big label">({{ deduction_leveling | currency }})</h3>
								</td>
							</tr>
							<tr>
								<td class="ui right aligned">
									CD Deduction
								</td>
								<td colspan="3">
									<h3 class="ui blue big label">({{ deduction_commission | currency }})</h3>
								</td>
							</tr>
							<tr>
								<td class="ui right aligned">
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
										<div class="ui green button" :class="{loading: transaction_hash_loading}" @click.prevent.stop="updateTransactionHash(cashout_transaction_id)"><i class="edit icon"></i> Update</div>
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
					<div class="field" :class="{ error: errors.has('deduction_leveling') }" v-if="pairing.id !== null">
						<label>Enter Upgrading Deduction ($)</label>
						<input
							type="number"
							name="deduction_leveling"
							v-model.number="deduction_leveling"
							v-validate="{ required: true }"
							data-vv-as="Upgrading Deduction"
						/>
						<div class="ui pointing red basic label" v-if="errors.has('deduction_leveling')">
							{{ errors.first('deduction_leveling') }}
						</div>
					</div>
					<div class="field" :class="{ error: errors.has('deduction_commission') }" v-if="member.commission_balance > 0">
						<label>Enter CD Deduction ($)</label>
						<input
							type="number"
							name="deduction_commission"
							v-model.number="deduction_commission"
							v-validate="{ required: true }"
							data-vv-as="Commission Deduction"
						/>
						<div class="ui pointing red basic label" v-if="errors.has('deduction_commission')">
							{{ errors.first('deduction_commission') }}
						</div>
					</div>
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
			<div class="ui button" @click.prevent="closeConfirmPointTransactionModal">Cancel</div>
			<div class="ui violet button" @click.prevent="confirmPointTransaction">
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
			cashout_transaction_id: {
				type: Number,
				required: true
			}
		},
		data: () => ({

			user_id: 0,
			deduction_admin_fee: 0,
			deduction_leveling: 0,
			deduction_commission: 0,
			current_btc_value: 0,
			confirmed_points: 0,
			btc_value: 0,
			usd_value: 0,
			transaction_hash: 'hash',
			transaction_type: '',
			transaction_status: '',
			confirmed_at: '',
			created_at: '',

			referral: {
				referral_points: 0,
				confirmed_referral_points: 0
			},

			pairing: {
				right_points: 0,
				left_points: 0,
				confirmed_points: 0,
				confirmed_right_points: 0,
				confirmed_left_points: 0,
				deduction_gc: 0
			},

			member: {
				fullname: '',
				alias: '',
				referral_points: 0,
				left_points: 0,
				right_points: 0,
				btc_wallet: '',
				current_cashout_pairing_level: 0,
				previous_cashout_pairing_level: 0,
				added_cashout_pairing_level: 0,
				commission_balance: 0,
				upgrading_total: 0
			},

			show_loading: false,
			btc_currency_loading: false,
			transaction_hash_loading: false
		}),

		created () {
			this.getPointTransactionDetails();

		},

		watch: {
			confirmed_points: _.debounce(function() {
				this.getBtcCurrency(this.confirmed_points);

				let pairing_pts = this.pairing.confirmed_right_points + this.pairing.confirmed_left_points;

				let pairing_usd = (pairing_pts / 100) * 5;
				
				let referral_usd = (this.referral.referral_points / 100) * 10;
			
				let total_usd = referral_usd + pairing_usd;
				this.getUsdValueToBtcCurrency(total_usd);
			}, 800)
		},

		computed: {
			getTotalPairingPoints() {
				return this.pairing.right_points + this.pairing.left_points;
			},
			getTotalConfirmedPairingPoints() {
				return this.pairing.confirmed_right_points + this.pairing.confirmed_left_points;
			},
			
			getUsdValueOfTotalConfirmedReferralPoints() {
				let total = (this.referral.confirmed_referral_points / 100) * 10;
				return total;
			},
			getUsdValueOfTotalConfirmedPairingPoints() {
				let total = (this.getTotalConfirmedPairingPoints / 100) * 5;
				return total;
			},
			getUsdValueOfGrandTotalConfirmedPairingPoints() {
				let pairing_pts = this.pairing.confirmed_right_points + this.pairing.confirmed_left_points;
				let pairing_usd = (pairing_pts / 100) * 5;
				let pairing_deducted = (pairing_usd - this.pairing.deduction_gc) - (pairing_usd * this.deduction_admin_fee);
				
				return pairing_deducted;
			},
			getUsdValueOfGrandTotalConfirmedReferralPoints() {
				let referral_usd = (this.referral.referral_points / 100) * 10;
				let referral_deducted = referral_usd - (referral_usd * this.deduction_admin_fee);
				return referral_deducted;
			},
			getGiftChequeDeduction() {
				let count_fifth_level = 0;

				if (this.pairing.confirmed_left_points <= this.pairing.confirmed_right_points) {
					this.member.added_cashout_pairing_level = this.member.current_cashout_pairing_level + this.pairing.confirmed_left_points / 100;
				} else {
					this.member.added_cashout_pairing_level = this.member.current_cashout_pairing_level + this.pairing.confirmed_right_points / 100;
				}

				for (let i = this.member.current_cashout_pairing_level; i <= this.member.added_cashout_pairing_level; i++) {
					if (i % 5 == 0 && i != 0) {
						count_fifth_level++;
					}
					// if (i % 5 == 0 && i != 0 || i % 2 == 0 && i != 0 || i % 13 == 0 && i != 0 || i % 14 == 0 && i != 0)
				}

				this.pairing.deduction_gc = count_fifth_level * 10;

				return this.pairing.deduction_gc;
			},
			getPtsToUsdValue(value) {
				let pairing_pts = this.pairing.confirmed_right_points + this.pairing.confirmed_left_points;

				let pairing_usd = (pairing_pts / 100) * 5;
				let pairing_deducted = (pairing_usd - this.pairing.deduction_gc) - (pairing_usd * this.deduction_admin_fee);
				// this.pairing.confirmed_points = pairing_deducted;
				
				let referral_usd = (this.referral.referral_points / 100) * 10;
				let referral_deducted = referral_usd - (referral_usd * this.deduction_admin_fee);

				this.usd_value = (pairing_usd + referral_usd) - this.deduction_commission;
				this.confirmed_points = ((referral_deducted + pairing_deducted) - this.deduction_leveling) - this.deduction_commission;
				// this.member.commission_balance = this.member.commission_balance - this.deduction_commission;
				return this.confirmed_points;
			},
			getUsdValueOfReferralPts() {
				return (this.referral.referral_points / 100) * 10;
			},
			getUsdValueOfPairingPts() {
				let pairing_pts = this.pairing.right_points + this.pairing.left_points;

				let pairing_usd = (pairing_pts / 100) * 5;
				return pairing_usd;
			}
		},

		methods: {
		
			getPointTransactionDetails() {

				this.show_loading = true;

				axios.get('/tbcgoldadmin/api/transactions/cashout/details/' + this.cashout_transaction_id).then((resp) => {

					console.log(resp);

					if (resp.data.status == 'ok') {

						this.user_id = parseInt(resp.data.cashout_transaction.user_id);
						this.transaction_status = resp.data.cashout_transaction.status;
						this.transaction_type = resp.data.cashout_transaction.cashout_transaction_type;
						this.deduction_admin_fee = parseFloat(resp.data.cashout_transaction.deduction_admin_fee);
						this.usd_value = parseFloat(resp.data.cashout_transaction.usd_value);
						this.btc_value = parseFloat(resp.data.cashout_transaction.btc_value);
						this.created_at = resp.data.cashout_transaction.created_at;

						// pairing
						this.pairing.left_points = parseInt(resp.data.cashout_transaction.left_points);
						this.pairing.right_points = parseInt(resp.data.cashout_transaction.right_points);
						this.pairing.deduction_gc = parseFloat(resp.data.cashout_transaction.deduction_gc);
						this.deduction_leveling = parseFloat(resp.data.cashout_transaction.deduction_leveling);
						this.deduction_commission = parseFloat(resp.data.cashout_transaction.deduction_commission);
						// referral
						this.referral.referral_points = parseInt(resp.data.cashout_transaction.referral_points);

						if (this.transaction_status == 'pending') {

							if (parseInt(resp.data.cashout_transaction.left_points) <= parseInt(resp.data.cashout_transaction.right_points)) {

								this.pairing.confirmed_left_points = parseInt(resp.data.cashout_transaction.left_points);
								this.pairing.confirmed_right_points = parseInt(resp.data.cashout_transaction.left_points);

							} else {

								this.pairing.confirmed_left_points = parseInt(resp.data.cashout_transaction.right_points);
								this.pairing.confirmed_right_points = parseInt(resp.data.cashout_transaction.right_points);

							}

							this.referral.confirmed_referral_points = parseInt(resp.data.cashout_transaction.referral_points);

						} else if (this.transaction_status == 'confirmed') {

							// this.member.current_cashout_pairing_level = resp.data.member.current_cashout_pairing_level;
							this.pairing.confirmed_left_points = parseInt(resp.data.cashout_transaction.confirmed_left_points);
							this.pairing.confirmed_right_points = parseInt(resp.data.cashout_transaction.confirmed_right_points);
							this.referral.confirmed_referral_points = parseInt(resp.data.cashout_transaction.confirmed_referral_points);
							this.transaction_hash = resp.data.cashout_transaction.transaction_hash;
							this.confirmed_at = resp.data.cashout_transaction.confirmed_at;

						}
						
						// member
						this.member.alias = resp.data.member.alias_name;
						this.member.fullname = resp.data.member.detail.fullname;
						this.member.referral_points = parseInt(resp.data.member.referral_points);
						this.member.left_points = parseInt(resp.data.member.left_points);
						this.member.right_points = parseInt(resp.data.member.right_points);
						this.member.btc_wallet = resp.data.member.detail.btc_wallet;
						this.member.current_cashout_pairing_level = parseInt(resp.data.member.current_cashout_pairing_level);
						this.member.previous_cashout_pairing_level = parseInt(resp.data.member.previous_cashout_pairing_level);
						this.member.commission_balance = parseFloat(resp.data.member.commission_balance);
						this.member.upgrading_total = parseFloat(resp.data.member.upgrading_total);

					}
					
					this.show_loading = false;

				}).catch((err) => {

					console.log(err);

				});
			},

			confirmPointTransaction() {
				this.show_loading = true;
				this.closeConfirmPointTransactionModal();

				axios.post('/tbcgoldadmin/api/transactions/cashout/confirm', {
					cashout_transaction_id: this.cashout_transaction_id,
					transaction_type: this.transaction_type,
					user_id: this.user_id,
					transaction_hash: this.transaction_hash,	
					btc_value: this.btc_value,
					usd_value: this.usd_value,
					deduction_admin_fee: this.deduction_admin_fee,
					deduction_gc: this.pairing.deduction_gc,
					deduction_leveling: this.deduction_leveling,
					deduction_commission: this.deduction_commission,
					confirmed_referral_points: this.referral.confirmed_referral_points,
					confirmed_left_points: this.pairing.confirmed_left_points,
					confirmed_right_points: this.pairing.confirmed_right_points,
				}).then((resp) => {

					console.log(resp);
					
					if (resp.data.status == 'ok') {
						this.$notify({
				          title: 'Success',
				          message: 'Cash Out Confirmed',
				          type: 'success'
				        });
						

						setTimeout(() => {
							this.show_loading = false;
							window.location.reload();
						}, 1000);
					} else {
						this.$notify({
				          title: 'Error',
				          message: 'Something went wrong',
				          type: 'error'
				        });
						
						setTimeout(() => {
							this.show_loading = false;
							window.location.reload();
						}, 1000);
					}
					

				}).catch((err) => {
					console.log(err);
				});
			},

			denyTransaction() {
				this.closeDenyTransactionModal();
				this.show_loading = true;
				axios.post('/tbcgoldadmin/api/transactions/cashout/deny', {
					cashout_transaction_id: this.cashout_transaction_id
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

			getUsdValueToBtcCurrency(usd_value) {
				this.btc_currency_loading = true;
				axios.get('/tbcgoldadmin/api/get-usd-to-btc-value', {
					params: {
						usd_value: usd_value
					}
				}).then((resp) => {
					this.btc_value = resp.data;
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
				axios.post('/tbcgoldadmin/api/transactions/cashout/hash/update', {
					cashout_transaction_id: id,
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

			showConfirmPointTransactionModal() {
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

			closeConfirmPointTransactionModal() {
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