<template>
<div>
	<div class="ui form">
		<div class="ui message">
			<div class="header">
				Note:
			</div>
			<ul class="list">
				<li>Pairing Points: 1 pair(Left pts &amp; Right pts) is equivalent to <u>200pts</u> = <u>$10</u></li>
				<li>Referral Points: <u>100pts</u> is equavalent to <u>$10</u>.</li>
				<li>Minimum pairing points to cash out <u>{{min_cashout_points}}pts</u></li>
				<li>Minimum referral points to cash out <u>100pts</u></li>
				<li>Cash out for pairing points is limited for a total of <u>{{max_cashout_points}}pts</u> for every week.</li>
				<li>Cash out is only available on every Friday Philippine Time.</li>
			</ul>
		</div>
		
		<div v-if="isCashoutDay">
			<div class="ui grid container">
				<div class="row">
					<div class="three wide column"></div>
					<div class="ten wide column">
						<div class="ui raised segment" :class="{ loading: show_loading }" v-if="!show_success">

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
									
									<i class="caret right large icon"></i>
									<div class="content">
										<h4><strong>Your Referral Points</strong></h4>
									</div>
								</div>
								<div class="item">
									<div class="right floated content">
										<div class="ui red label">{{ left_points }}pts</div>
									</div>
									
									<i class="caret right large icon"></i>
									<div class="content">
										<h4><strong>Your Left Points</strong></h4>
									</div>
								</div>
								<div class="item">
									<div class="right floated content">
										<div class="ui red label">{{ right_points }}pts</div>
									</div>
									
									<i class="caret right large icon"></i>
									<div class="content">
										<h4><strong>Your Right Points</strong></h4>
									</div>
								</div>
								<div class="item">
									<div class="right floated content">
										<div class="ui red label">{{ getTotalPairingPoints }}pts</div>
									</div>
									
									<i class="caret right large icon"></i>
									<div class="content">
										<h4><strong>Total Pairing Points</strong></h4>
									</div>
								</div>

							</div>
							
							<br /><br />

							<div class="field">
								<div class="ui toggle checkbox">
									<input type="checkbox" v-model="include_pairing" />
									<label>Pairing Points</label>
								</div>
							</div>
							
							<transition
								enter-active-class="animated fadeIn"
    							leave-active-class="animated fadeOut"
    						>
    						<div v-if="include_pairing">
								<div class="field" :class="{ error: errors.has('left_points') }">
									<label>Enter Left Points:</label>
									<input
										type="number"
										name="left_points"
										v-model="cashout.left_points"
										v-validate="{ numeric: true, max_value:left_points, min_value:100 }"
										data-vv-as="Left Points"
										placeholder="Left Points"
										style="text-align: center"
										step="100"
										min="0"
									/>
									<div class="ui pointing red basic label" v-if="errors.has('left_points')">
										{{ errors.first('left_points') }}
									</div>
								</div>

								<div class="field" :class="{ error: errors.has('right_points') }">
									<label>Enter Right Points:</label>
									<input
										type="number"
										name="right_points"
										v-model="cashout.right_points"
										v-validate="{ numeric: true, max_value:right_points, min_value:100 }"
										data-vv-as="Right Points"
										placeholder="Right Points"
										style="text-align: center"
										step="100"
										min="0"
									/>
									<div class="ui pointing red basic label" v-if="errors.has('right_points')">
										{{ errors.first('right_points') }}
									</div>
								</div>
							</div>
							</transition>

							<div class="ui segment" v-if="exceed_limit">
								<p>Oops! You have exceeded on the maximum pairing points.</p>
							</div>
		
							<div class="ui divider"></div>

							<div class="field">
								<div class="ui toggle checkbox">
									<input type="checkbox" v-model="include_referral" />
									<label>Referral Points</label>
								</div>
							</div>

							<transition
								enter-active-class="animated fadeIn"
    							leave-active-class="animated fadeOut"
    						>
    						<div v-if="include_referral">
								<div class="field" :class="{ error: errors.has('referral_points') }">
									<label>Enter Referral Points:</label>
									<input
										type="number"
										name="referral_points"
										v-model="cashout.referral_points"
										v-validate="{ numeric: true, max_value:referral_points, min_value:200 }"
										data-vv-as="Referral Points"
										placeholder="Referral Points"
										style="text-align: center"
										step="100"
										min="0"
									/>
									<div class="ui pointing red basic label" v-if="errors.has('referral_points')">
										{{ errors.first('referral_points') }}
									</div>
								</div>
							</div>
							</transition>

							<br />

							<div class="field">
								<div class="ui right aligned segment" v-if="(cashout.left_points > 0 || cashout.right_points > 0 || cashout.referral_points > 0)">
									<label>
										Equivalent of total {{ getTotalPointsToCashOut }}pts = {{ getUsdValueOfPairingPoints | currency }}
									</label>
								</div>
							</div>

							<div class="field">
								
								<button
									class="ui large fluid violet button"
									@click.prevent="showConfirmationModal"
									v-if="validPointsToCashout"
								>
									Submit Cash Out Request
								</button>
								<button
									class="ui large fluid violet disabled button"
									v-else
								>
									 Submit Cash Out Request
								</button>
								
							</div>
						</div>

						<div class="ui raised center aligned segment" v-if="show_success">
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
	<div class="ui cashout-pairing-modal modal mini">
		<div class="header">
			Confirm Cash Out
		</div>
		<div class="image content">
			<div class="description">
				Are you sure?
			</div>
		</div>
		<div class="actions">
			<div class="ui button" @click.prevent="closeConfirmTransactionModal">Cancel</div>
			<div class="ui violet button" @click.prevent.stop="cashoutPoints" v-if="validPointsToCashout">Confirm Cash Out</div>
			
		</div>
	</div>
</div>
</template>
<script>
	export default {	
		props: {
			min_cashout_points: {
				type: Number,
				required: true
			},
			max_cashout_points: {
				type: Number,
				required: true
			},
			user_id: {
				type: Number,
				required: true
			}
		},
		data: () => ({
			show_loading: false,
			points: 0,
			right_points: 0,
			left_points: 0,
			referral_points: 0,
			last_activated_at: null,
			last_cashout_at: null,
			cashout: {
				left_points: 0,
				right_points: 0,
				referral_points: 0,
				total: 0
			},
			show_success: false,
			include_referral: false,
			include_pairing: false,
			exceed_limit: false
		}),
		created() {
			this.getMemberDetails();
		},
		watch: {
			'cashout.left_points': function(val) {
				if (val == '' || Number.isNaN(val)) {
					this.cashout.left_points = 0;
				} else {
					if (val > 1200) {
						this.cashout.left_points = 1200;
					}
				}
			},
			'cashout.right_points': function(val) {
				if (val == '' || Number.isNaN(val)) {
					this.cashout.right_points = 0;
				} else {
					if (val > 1200) {
						this.cashout.right_points = 1200;
					}
				}
			},
			'cashout.referral_points': function(val) {
				if (val == '' || Number.isNaN(val)) {
					this.cashout.referral_points = 0;
				}
			},
			include_referral: function(val) {
				if (val == false) {
					this.cashout.referral_points = 0;
				}
			},
			include_pairing: function(val) {
				if (val == false) {
					this.cashout.left_points = 0;
					this.cashout.right_points = 0;
				}
			}
		},
		computed: {
			canCashout() {
				if (parseInt(this.points) >= parseInt(this.min_cashout_points)) {
					return true;
				} else if (parseInt(this.points) < parseInt(this.min_cashout_points)) {
					return false;
				}
			},

			validPointsToCashout() {

					if (this.include_referral == true && this.include_pairing == true) {
						if (parseInt(this.cashout.left_points) >= 100 && parseInt(this.cashout.right_points) >= 100 && parseInt(this.cashout.referral_points) >= 100 && parseInt(this.left_points) >= 100 && parseInt(this.right_points) >= 100 && parseInt(this.referral_points) >= 100 && parseInt(this.cashout.left_points) <= 1200 && parseInt(this.cashout.right_points) <= 1200) {
							this.exceed_limit = false;
							return true;
						} else {
							return false;
						}
					} else if (this.include_pairing == true && this.include_referral == false) {
						if (parseInt(this.cashout.left_points) >= 100 && parseInt(this.cashout.right_points) >= 100 && parseInt(this.left_points) >= 100 && parseInt(this.right_points) >= 100 && parseInt(this.cashout.left_points) <= 1200 && parseInt(this.cashout.right_points) <= 1200) {
							return true;
						} else {
							return false;
						}
					} else if (this.include_referral == true && this.include_pairing == false) {
						if (parseInt(this.cashout.referral_points) >= 100 && parseInt(this.referral_points) >= 100) {
							return true;
						} else {
							return false;
						}
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
				let pairing_points = parseInt(this.cashout.left_points) + parseInt(this.cashout.right_points);
				let referral_points = parseInt(this.cashout.referral_points);
				this.cashout.total = pairing_points + referral_points;
				return this.cashout.total;
			},

			getUsdValueOfPairingPoints() {
				if (this.include_pairing == true && this.include_referral == false) {
					return ((parseInt(this.cashout.left_points) + parseInt(this.cashout.right_points)) / 100) * 5;
				} else if (this.include_pairing == false && this.include_referral == true) {
					return (parseInt(this.cashout.referral_points) / 100) * 10;
				} else if (this.include_pairing == true && this.include_referral == true) {
					let pairing = ((parseInt(this.cashout.left_points) + parseInt(this.cashout.right_points)) / 100) * 5;
					let referral = (parseInt(this.cashout.referral_points) / 100) * 10;

					return pairing + referral;
				}
				
			},

			getTotalPairingPoints() {
				return parseInt(this.left_points) + parseInt(this.right_points);
			}

		},
		methods: {
			getMemberDetails() {
				this.show_loading = true;

				axios.get('/member/api/account-details').then((resp) => {
					console.log(resp);
					
					this.points = resp.data.user.points;
					this.left_points = resp.data.user.left_points;
					this.right_points = resp.data.user.right_points;
					this.referral_points = resp.data.user.referral_points;
					this.last_cashout_at = resp.data.last_cashout_at;
					this.last_activated_at = resp.data.last_activated_at;

					this.show_loading = false;

				}).catch((err) => {
					console.log(err);
				});
			},

			showConfirmationModal() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						$('.ui.cashout-pairing-modal.modal').modal('show');
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				});
			},

			closeConfirmTransactionModal() {
				$('.ui.cashout-pairing-modal.modal').modal('hide');
			},

			cashoutPoints() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.closeConfirmTransactionModal();
						this.show_loading = true;

						let transaction_type = '';

						if (this.include_referral == true && this.include_pairing == true) {
							transaction_type = 'all';
						} else if (this.include_referral == true && this.include_pairing == false) {
							transaction_type = 'referral';
						} else if (this.include_referral == false && this.include_pairing == true) {
							transaction_type = 'pairing';
						}

						axios.post('/member/api/cashout', {
							left_points: this.cashout.left_points,
							right_points: this.cashout.right_points,
							referral_points: this.cashout.referral_points,
							user_id: this.user_id,
							cashout_transaction_type: transaction_type
						}).then((resp) => {
							console.log(resp);
							
							this.$notify({
					          title: 'Cash Out Request Sent',
					          message: 'Please wait for the confirmation of admin thank you.',
					          type: 'success'
					        });

					        // setTimeout(() => {
					        // 	window.location.reload();
					        // }, 2000);
					        
					        this.show_success = true;

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