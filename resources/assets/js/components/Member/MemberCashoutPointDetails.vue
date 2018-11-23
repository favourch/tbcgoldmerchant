<template>
	<div>
		<div class="ui grid container">
			<div class="row">
				<div class="sixteen wide column right aligned">
					<a class="ui button" :href="viewTransactionListing()"><i class="arrow left icon"></i> Cash Out Listing</a>
				</div>
			</div>

			<div class="row">
				<div class="sixteen wide column">
					<div class="ui raised segment">
						<div class="ui grid container">
							<div class="row">
								<div class="eight wide column">
									<h3>
										<label class="ui orange large label">
											 Status: {{ status | capitalize }}
										</label>
										|
										<label class="ui red large label">
											 Cash Out Type: {{ cashout_transaction_type | capitalize }}
										</label>
									</h3>
								</div>
								<div class="eight wide right aligned column">
									<h3>
										<label>
											Created Date: {{ created_at | moment('MMM DD, YYYY h:mm:ss a') }}
										</label>
									</h3>
								</div>
							
								<div class="sixteen wide column">
									<div class="ui divider"></div>
								</div>		

								<div class="sixteen wide column" v-if="cashout_transaction_type == 'referral' && status == 'pending'">
									
									<div class="ui message">
										<div class="ui header">
											Cash Out Details
										</div>
										<p>You have requested for cash out on your <strong><u>Referral Points</u></strong> for a total of <strong><u>{{ referral_points }}pts</u></strong> equivalent for <strong><u>{{ getUsdValueOfReferralPoints | currency }}</u></strong>.</p>
										<p>The Admin is working on it. Please be advised that we need to validate this transaction your patience is much appreciated. Thank you!</p>
									</div>
								</div>	
								<div class="sixteen wide column" v-if="cashout_transaction_type == 'pairing' && status == 'pending'">
									
									<div class="ui message">
										<div class="ui header">
											Cash Out Details
										</div>
										<p>You have requested for cash out on your <strong><u>Pairing Points</u></strong> for a total of <strong><u>{{ left_points }} Left pts</u></strong> and <strong><u>{{ right_points }} Right pts</u></strong> equivalent for <strong><u>{{ getUsdValueOfPairingPoints | currency }}</u></strong>.</p>
										<p>The Admin is working on it. Please be advised that we need to validate this transaction your patience is much appreciated. Thank you!</p>
									</div>
								</div>	
								<div class="sixteen wide column" v-if="cashout_transaction_type == 'all' && status == 'pending'">
									
									<div class="ui message">
										<div class="ui header">
											Cash Out Details
										</div>
										<p>You have requested for cash out on your <strong><u>Pairing &amp; Referral Points</u></strong> for a total of <strong><u>{{ left_points }} Left pts</u></strong> and <strong><u>{{ right_points }} Right pts</u></strong> and <strong><u>{{ referral_points }}pts</u></strong> equivalent for <strong><u>{{ getUsdValueOfAllPoints | currency }}</u></strong>.</p>
										<p>The Admin is working on it. Please be advised that we need to validate this transaction your patience is much appreciated. Thank you!</p>
									</div>
								</div>	
								<div class="sixteen wide column" v-if="cashout_transaction_type == 'pairing' && status == 'confirmed'">
									<br />
									<h2 class="ui header">
										<i class="circular check green icon"></i>
										<div class="content">
											Cash Out Confirmed!
											<div class="sub header">Congratulations!, your cash out request have been confirmed you may now check it on your BTC Wallet.</div>
										</div>	
									</h2>
									<h4>Your Cash Out Request:</h4>
									<table class="ui celled table">
										<tbody>
											<tr>
												<td style="width: 30%;">Total Cash Out Points</td>
												<td>{{ getTotalPairingPoints }}pts</td>
											</tr>
											<tr>
												<td style="width: 30%;">Left Points</td>
												<td>{{ left_points }}pts</td>
											</tr>
											<tr>
												<td style="width: 30%;">Right Points</td>
												<td>{{ right_points }}pts</td>
											</tr>
										</tbody>
									</table>

									<h4>Admin Confirmed Points:</h4>
									<table class="ui celled table">
										<tbody>
											<tr>
												<td style="width: 30%;">Total Confirmed Points</td>
												<td>{{ getTotalConfirmedPairingPoints }}pts</td>
											</tr>
											<tr>
												<td style="width: 30%;">Confirmed Left Points</td>
												<td>{{ confirmed_left_points }}pts</td>
											</tr>
											<tr>
												<td style="width: 30%;">Confirmed Right Points</td>
												<td>{{ confirmed_right_points }}pts</td>
											</tr>
											<tr>
												<td colspan="2">
													<h4>Confirmed Points Equivalent in USD($):</h4>
												</td>
											</tr>
											<tr>
												<td style="width: 30%;">Confirmed Pairing Points</td>
												<td>{{ getUsdValueOfConfirmedPairingPoints | currency }}</td>
											</tr>
											<tr>
												<td style="width: 30%;">Gift Cheque:</td>
												<td>({{ deduction_gc | currency }})</td>
											</tr>
											<tr>
												<td style="width: 30%;">Admin Fee (10%)</td>
												<td>({{ getDeductionAdminFee | currency }})</td>
											</tr>
											<tr>
												<td style="width: 30%;">Admin Deduction for Upgrading</td>
												<td>({{ deduction_leveling | currency }})</td>
											</tr>
											<tr>
												<td style="width: 30%;">Admin Deduction for Commission</td>
												<td>({{ deduction_commission | currency }})</td>
											</tr>
											<tr>
												<td style="width: 30%;">Total Remaining Cash Out Recieved</td>
												<td>{{ getUsdValueOfTotalPairingRemainingBalance | currency }}</td>
											</tr>
											<tr>
												<td style="width: 30%;">Transaction Hash</td>
												<td><a :href="transactionHashLink()" target="_blank">{{ transaction_hash }}</a></td>
											</tr>
										</tbody>
									</table>
								</div>

								<div class="sixteen wide column" v-if="cashout_transaction_type == 'referral' && status == 'confirmed'">
									<br />
									<h2 class="ui header">
										<i class="circular check green icon"></i>
										<div class="content">
											Cash Out Confirmed!
											<div class="sub header">Congratulations!, your cash out request have been confirmed you may now check it on your BTC Wallet.</div>
										</div>	
									</h2>
									<h4>Your Cash Out Request:</h4>
									<table class="ui celled table">
										<tbody>
											<tr>
												<td style="width: 30%;">Total Cash Out Points</td>
												<td>{{ referral_points }}pts</td>
											</tr>
										</tbody>
									</table>

									<h4>Admin Confirmed Points:</h4>
									<table class="ui celled table">
										<tbody>
											<tr>
												<td style="width: 30%;">Total Confirmed Points</td>
												<td>{{ confirmed_referral_points }}pts</td>
											</tr>
											<tr>
												<td colspan="2">
													<h4>Confirmed Points Equivalent in USD($):</h4>
												</td>
											</tr>
											<tr>
												<td style="width: 30%;">Confirmed Referral Points</td>
												<td>{{ getUsdValueOfConfirmedReferralPoints | currency }}</td>
											</tr>
											<tr>
												<td style="width: 30%;">Admin Fee (10%):</td>
												<td>({{ getDeductionAdminFee | currency }})</td>
											</tr>
											<tr>
												<td style="width: 30%;">Admin Deduction for Upgrading</td>
												<td>({{ deduction_leveling | currency }})</td>
											</tr>
											<tr>
												<td style="width: 30%;">Admin Deduction for CD</td>
												<td>({{ deduction_commission | currency }})</td>
											</tr>
											<tr>
												<td style="width: 30%;">Remaining Total Cash Out Recieved</td>
												<td>{{ getUsdValueOfTotalReferralRemainingBalance | currency }}</td>
											</tr>
											<tr>
												<td style="width: 30%;">Transaction Hash</td>
												<td><a :href="transactionHashLink()" target="_blank">{{ transaction_hash }}</a></td>
											</tr>
											<tr>
												<td style="width: 30%;">Confirmed Date</td>
												<td>{{ confirmed_at | moment('MMM DD, YYYY h:mm:ss a') }}</td>
											</tr>
										</tbody>
									</table>
								</div>		

								<div class="sixteen wide column" v-if="cashout_transaction_type == 'all' && status == 'confirmed'">
									<br />
									<h2 class="ui header">
										<i class="circular check green icon"></i>
										<div class="content">
											Cash Out Confirmed!
											<div class="sub header">Congratulations!, your cash out request have been confirmed you may now check it on your BTC Wallet.</div>
										</div>	
									</h2>
									<h4>Your Cash Out Request:</h4>
									<table class="ui celled table">
										<tbody>
											<tr>
												<td style="width: 30%;">Total Cash Out Points</td>
												<td>{{ getTotalAllPoints }}pts</td>
											</tr>
											<tr>
												<td style="width: 30%;">Left Points</td>
												<td>{{ left_points }}pts</td>
											</tr>
											<tr>
												<td style="width: 30%;">Right Points</td>
												<td>{{ right_points }}pts</td>
											</tr>
											<tr>
												<td style="width: 30%;">Referral Points</td>
												<td>{{ referral_points }}pts</td>
											</tr>
										</tbody>
									</table>

									<h4>Admin Confirmed Points:</h4>
									<table class="ui celled table">
										<tbody>
											<tr>
												<td style="width: 30%;">Total Confirmed Points</td>
												<td>{{ getTotalConfirmedAllPoints }}pts</td>
											</tr>
											<tr>
												<td style="width: 30%;">Confirmed Left Points</td>
												<td>{{ confirmed_left_points }}pts</td>
											</tr>
											<tr>
												<td style="width: 30%;">Confirmed Right Points</td>
												<td>{{ confirmed_right_points }}pts</td>
											</tr>
											<tr>
												<td style="width: 30%;">Confirmed Referral Points</td>
												<td>{{ confirmed_referral_points }}pts</td>
											</tr>
											<tr>
												<td colspan="2">
													<h4>Confirmed Points Equivalent in USD($):</h4>
												</td>
											</tr>
											<tr>
												<td style="width: 30%;">Confirmed Pairing Points</td>
												<td>{{ getUsdValueOfConfirmedPairingPoints | currency }}</td>
											</tr>
											<tr>
												<td style="width: 30%;">Confirmed Referral Points</td>
												<td>{{ getUsdValueOfReferralPoints | currency }}</td>
											</tr>
											<tr>
												<td style="width: 30%;">Gift Cheque:</td>
												<td>({{ deduction_gc | currency }})</td>
											</tr>
											<tr>
												<td style="width: 30%;">Admin Fee (10%)</td>
												<td>({{ getDeductionAdminFee | currency }})</td>
											</tr>
											<tr>
												<td style="width: 30%;">Admin Deduction for Upgrading</td>
												<td>({{ deduction_leveling | currency }})</td>
											</tr>
											<tr>
												<td style="width: 30%;">Admin Deduction for CD</td>
												<td>({{ deduction_commission | currency }})</td>
											</tr>
											<tr>
												<td style="width: 30%;">Total Remaining Cash Out Recieved</td>
												<td>{{ getUsdValueOfTotalAllRemainingBalance | currency }}</td>
											</tr>
											<tr>
												<td style="width: 30%;">Transaction Hash</td>
												<td><a :href="transactionHashLink()" target="_blank">{{ transaction_hash }}</a></td>
											</tr>
										</tbody>
									</table>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	export default {
		props: {
			cashout_transaction_id: Number
		},
		data: () => ({
			referral_points: 0,
			left_points: 0,
			right_points: 0,
			confirmed_referral_points: 0,
			confirmed_left_points: 0,
			confirmed_right_points: 0,
			transaction_hash: '',
			transaction_confirm_no: 0,
			btc_value: 0,
			usd_value: 0,
			deduction_gc: 0,
			deduction_admin_fee: 0,
			deduction_leveling: 0,
			deduction_commission: 0,
			status: '',
			cashout_transaction_type: '',
			created_at: '',
			confirmed_at: '',
		}),
		created() {
			this.getCashoutDetails();
		},
		computed: {
			getTotalAllPoints() {
				return parseInt(this.left_points) + parseInt(this.right_points) + parseInt(this.referral_points);
			},
			getUsdValueOfAllPoints() {
				let pairing = ((parseInt(this.left_points) + parseInt(this.right_points)) / 100) * 5;
				let referral = (parseInt(this.referral_points) / 100) * 10;
				return pairing + referral;
			},
			getUsdValueOfPairingPoints() {
				return ((parseInt(this.left_points) + parseInt(this.right_points)) / 100) * 5;
			},
			getUsdValueOfConfirmedPairingPoints() {
				return ((parseInt(this.confirmed_left_points) + parseInt(this.confirmed_right_points)) / 100) * 5;
			},
			getUsdValueOfReferralPoints() {
				return (parseInt(this.referral_points) / 100) * 10;
			},
			getUsdValueOfConfirmedReferralPoints() {
				return (parseInt(this.confirmed_referral_points) / 100) * 10;
			},
			getTotalPairingPoints() {
				return parseInt(this.left_points) + parseInt(this.right_points);
			},
			getTotalConfirmedPairingPoints() {
				return parseInt(this.confirmed_left_points) + parseInt(this.confirmed_right_points);
			},
			getTotalConfirmedAllPoints() {
				return parseInt(this.confirmed_left_points) + parseInt(this.confirmed_right_points) + parseInt(this.confirmed_referral_points);
			},
			getDeductionAdminFee() {
				if (this.cashout_transaction_type == 'pairing') {
					return parseInt(this.getUsdValueOfConfirmedPairingPoints) * parseFloat(this.deduction_admin_fee);
				} else if (this.cashout_transaction_type == 'referral') {
					return parseFloat(this.getUsdValueOfConfirmedReferralPoints) * parseFloat(this.deduction_admin_fee);
				} else if (this.cashout_transaction_type == 'all') {
					let pairing = parseInt(this.getUsdValueOfConfirmedPairingPoints) * parseFloat(this.deduction_admin_fee);
					let referral = parseFloat(this.getUsdValueOfConfirmedReferralPoints) * parseFloat(this.deduction_admin_fee);
					console.log(pairing);
					console.log(referral);
					return pairing + referral;

				}
			},
			getUsdValueOfTotalPairingRemainingBalance() {
				return parseFloat(this.getUsdValueOfConfirmedPairingPoints) - parseFloat(this.getDeductionAdminFee) - parseFloat(this.deduction_gc) - parseFloat(this.deduction_leveling) - parseFloat(this.deduction_commission);
			},
			getUsdValueOfTotalReferralRemainingBalance() {
				return parseFloat(this.getUsdValueOfConfirmedReferralPoints) - parseFloat(this.getDeductionAdminFee) - parseFloat(this.deduction_leveling) - parseFloat(this.deduction_commission);
			},
			getUsdValueOfTotalAllRemainingBalance() {
				let pairing = this.getUsdValueOfConfirmedPairingPoints;
				let referral = this.getUsdValueOfConfirmedReferralPoints;
				return (pairing + referral) - parseInt(this.deduction_gc) - parseFloat(this.getDeductionAdminFee) - parseFloat(this.deduction_leveling) - parseFloat(this.deduction_commission);
			},
		},
		methods: {

			getCashoutDetails() {
				axios.get('/member/api/cashout/details/' + this.cashout_transaction_id).then((resp) => {
					console.log(resp);
					this.referral_points = resp.data.cashout_transaction.referral_points;
					this.right_points = resp.data.cashout_transaction.right_points;
					this.left_points = resp.data.cashout_transaction.left_points;
					this.confirmed_referral_points = resp.data.cashout_transaction.confirmed_referral_points;
					this.confirmed_left_points = resp.data.cashout_transaction.confirmed_left_points;
					this.confirmed_right_points = resp.data.cashout_transaction.confirmed_right_points;
					this.transaction_hash = resp.data.cashout_transaction.transaction_hash;
					this.btc_value = resp.data.cashout_transaction.btc_value;
					this.usd_value = resp.data.cashout_transaction.usd_value;
					this.deduction_gc = resp.data.cashout_transaction.deduction_gc;
					this.deduction_admin_fee = resp.data.cashout_transaction.deduction_admin_fee;
					this.deduction_leveling = resp.data.cashout_transaction.deduction_leveling;
					this.deduction_commission = resp.data.cashout_transaction.deduction_commission;
					this.status = resp.data.cashout_transaction.status;
					this.cashout_transaction_type = resp.data.cashout_transaction.cashout_transaction_type;
					this.created_at = resp.data.cashout_transaction.created_at;
					this.confirmed_at = resp.data.cashout_transaction.confirmed_at;
				}).catch((err) => {
					console.log(err);
				});
			},

			transactionHashLink() {
				return 'https://blockchain.info/tx/' + this.transaction_hash;
			},

			viewTransactionListing() {
				return '/member/cashout';
			}
		}
	}
</script>