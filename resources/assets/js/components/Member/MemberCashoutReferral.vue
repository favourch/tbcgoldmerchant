<template>
<div>
	<div class="ui form">
		<div class="ui message">
			<div class="header">
				Note:
			</div>
			<ul class="list">
				<li>For Referral Points: <u>100pts</u> is equavalent to <u>$10</u>.</li>
				<li>Cash out is only available on every Friday Philippine Time .</li>
			</ul>
		</div>
		
		<div v-if="isCashoutDay">
			<div class="ui grid container">
				<div class="row">
					<div class="three wide column"></div>
					<div class="ten wide column">
						<div class="ui raised segment" v-if="!showSuccess">
							<div class="ui inverted dimmer" :class="{ active: showLoading }">
								<div class="ui text loader">Loading</div>
							</div>
							<h2 class="ui center aligned icon header">
								<i class="money bill alternate outline icon"></i>
								Cash Out
							</h2>
							<div class="ui divider"></div>
							<div class="ui middle aligned divided list">
								<div class="item">
									<div class="right floated content">
										<div class="ui red label">{{ referral_points }}pts</div>
									</div>
									<!-- <img class="ui avatar image" src="/images/avatar2/small/lena.png"> -->
									<i class="caret right large icon"></i>
									<div class="content">
										<h4><strong>Your Total Referral Points</strong></h4>
									</div>
								</div>
							</div>

							<div class="field" :class="{ error: errors.has('referral_points') }">
								<label>Enter Referral Points:</label>
								<input
									type="text"
									name="referral_points"
									v-model="cashout.referral_points"
									v-validate="{ required: true, numeric: true, max_value:referral_points }"
									data-vv-as="Referral Points"
									placeholder="Referral Points"
									style="text-align: center"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('referral_points')">
									{{ errors.first('referral_points') }}
								</div>
								<p 
									style="margin-left: 10px;padding-top: 10px;" 
									v-if="cashout.referral_points > 0"
								>
								Equivalent of {{ cashout.referral_points }}pts = {{ getUsdValueOfReferralPoints | currency }}
								</p>
							</div>
							<div class="field">
								
								<button
									class="ui large fluid positive button"
									@click.prevent="showConfirmTransactionModal"
									v-if="validPointsToCashout"
								>
									Submit Cash Out Request
								</button>

								<button
									class="ui large fluid positive disabled button"
									v-else
								>
									Submit Cash Out Request
								</button>
								
							</div>
						</div>
						<div class="ui raised center aligned segment" v-if="showSuccess">
							<i class="check green massive circular icon successpairing"></i>
							<h2>Sent Succcessfully!</h2>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div v-if="!isCashoutDay">
			<h3 class="ui center aligned header">Oops! Not a cash out day. Please come back on Friday Philippine time.</h3>
		</div>
	</div>
	<div class="ui cashout-referral-modal modal mini">
		<div class="header">
			Action: Confirm Cash Out
		</div>
		<div class="image content">
			<div class="description">
				Are you sure?
			</div>
		</div>
		<div class="actions">
			<div class="ui button" @click.prevent="closeConfirmTransactionModal">Cancel</div>
			<div class="ui orange button" @click.prevent="cashoutReferralBonus" v-if="validPointsToCashout">Confirm Cash Out</div>
			
		</div>
	</div>
</div>
</template>
<script>
	export default {	
		props: {
			min_cashout_points: {
				type: String,
				required: true
			},
			max_cashout_points: {
				type: String,
				required: true
			},
			min_cashout_referral_points: {
				type: String,
				required: true
			},
			user_id: {
				type: String,
				required: true
			}
		},
		data: () => ({
			showLoading: false,
			showSuccess: false,
			referral_points: 0,
			last_activated_at: null,
			last_cashout_at: null,
			cashout: {
				referral_points: 0,
			}
		}),
		created() {
			this.getMemberDetails();
			let today = new Date();
			console.log(today.getDay())
		},
		computed: {

			canCashout() {
				if (parseInt(this.referral_points) >= parseInt(this.min_cashout_points)) {
					return true;
				} else if (parseInt(this.referral_points) < parseInt(this.min_cashout_points)) {
					return false;
				}
			},

			isCashoutDay() {
				let today = new Date();
				if (today.getDay() == 5) {
					return true;
				} else {
					return false;
				}
			},

			getTotalPointsToCashOut() {
				return parseInt(this.cashout.referral_points);
			},

			getUsdValueOfReferralPoints() {
				return (parseInt(this.cashout.referral_points) / 100) * 10;
			},

			validPointsToCashout() {
				// if (this.getTotalPointsToCashOut >= this.min_cashout_points && this.getTotalPointsToCashOut <= this.max_cashout_points) {
				// 	return true;
				// } else {
				// 	return false;
				// }
				if (this.getTotalPointsToCashOut > 0 && this.getTotalPointsToCashOut >= this.min_cashout_referral_points) {
					return true;
				} else {
					return false;
				}
				
			},
		},
		methods: {
			getMemberDetails() {
				this.showLoading = true;

				axios.get('/member/api/account-details').then((resp) => {
					console.log(resp);
					
					this.referral_points = resp.data.user.referral_points;
					this.last_cashout_at = resp.data.last_cashout_at;
					this.last_activated_at = resp.data.last_activated_at;

					this.showLoading = false;

				}).catch((err) => {
					console.log(err);
				});
			},

			showConfirmTransactionModal() {
				$('.ui.cashout-referral-modal.modal').modal('show');
			},

			closeConfirmTransactionModal() {
				$('.ui.cashout-referral-modal.modal').modal('hide');
			},

			cashoutReferralBonus() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.showLoading = true;
						this.closeConfirmTransactionModal();
						axios.post('/member/api/cashout-points', {
							referral_points: this.cashout.referral_points,
							user_id: this.user_id,
							point_transaction_type: 'referral'
						}).then((resp) => {
							console.log(resp);
							
							

							this.$notify({
					          title: 'Cash Out Request Sent',
					          message: 'Please wait for the confirmation of admin thank you.',
					          type: 'success'
					        });

							this.showLoading = false;
							this.showSuccess = true;

					        setTimeout(() => {
								$('.successpairing').transition({
									animation: 'jiggle',
									duration: '2s',
								});
							}, 500);
					        
						}).catch((err) => {
							console.log(err);
						});
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				});
			}
		}
	}
</script>