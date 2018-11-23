<template>
<div>
	<div class="ui form">
		<div class="ui message">
			<div class="header">
				Note:
			</div>
			<ul class="list">
				<li>For Pairing Points: 1 pair(Left pts &amp; Right pts) is equivalent to <u>200pts</u> = <u>10$</u></li>
				<li>To cash out you need to have atleast <u>{{min_cashout_points}}pts</u>.</li>
				<li>Cash out is limited for a total of	 <u>{{max_cashout_points}}pts</u> for every week.</li>
				<li>Cash out is only available on every Friday Philippine Time.</li>
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
										<div class="ui red label">{{ left_points }}pts</div>
									</div>
									<!-- <img class="ui avatar image" src="/images/avatar2/small/lena.png"> -->
									<i class="caret right large icon"></i>
									<div class="content">
										<h4><strong>Your Total Left Points</strong></h4>
									</div>
								</div>
								<div class="item">
									<div class="right floated content">
										<div class="ui red label">{{ right_points }}pts</div>
									</div>
									<!-- <img class="ui avatar image" src="/images/avatar2/small/lindsay.png"> -->
									<i class="caret right large icon"></i>
									<div class="content">
										<h4><strong>Your Total Right Points</strong></h4>
									</div>
								</div>
								<div class="item">
									<div class="right floated content">
										<div class="ui red label">{{ getTotalPairingPoints }}pts</div>
									</div>
									<!-- <img class="ui avatar image" src="/images/avatar2/small/mark.png"> -->
									<i class="caret right large icon"></i>
									<div class="content">
										<h4><strong>Total Points</strong></h4>
									</div>
								</div>
							</div>
							
							<div class="field" :class="{ error: errors.has('left_points') }">
								<label>Enter Left Points:</label>
								<input
									type="text"
									name="left_points"
									v-model="cashout.left_points"
									v-validate="{ numeric: true, max_value:left_points, min_value:100 }"
									data-vv-as="Left Points"
									placeholder="Left Points"
									style="text-align: center"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('left_points')">
									{{ errors.first('left_points') }}
								</div>
							</div>
							<div class="field" :class="{ error: errors.has('right_points') }">
								<label>Enter Right Points:</label>
								<input
									type="text"
									name="right_points"
									v-model="cashout.right_points"
									v-validate="{ numeric: true, max_value:right_points, min_value:100 }"
									data-vv-as="Right Points"
									placeholder="Right Points"
									style="text-align: center"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('right_points')">
									{{ errors.first('right_points') }}
								</div>
								
							</div>
							<div class="field">
								<p
									style="margin-left: 10px;padding-top: 10px;"
									v-if="(cashout.left_points > 0 || cashout.right_points > 0)"
									>
									Equivalent of {{ getTotalPointsToCashOut }}pts = {{ getUsdValueOfPairingPoints | currency }}
								</p>
							</div>
							<div class="field">
								
								<button
									class="ui large fluid positive button"
									@click.prevent="showConfirmationModal"
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
	<div class="ui cashout-pairing-modal modal mini">
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
			<div class="ui orange button" @click.prevent.stop="cashoutPairingBonus" v-if="validPointsToCashout">Confirm Cash Out</div>
			
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
			user_id: {
				type: String,
				required: true
			}
		},
		data: () => ({
			showLoading: false,
			points: 0,
			right_points: 0,
			left_points: 0,
			last_activated_at: null,
			last_cashout_at: null,
			cashout: {
				left_points: 0,
				right_points: 0,
				total: 0
			},
			showSuccess: false
		}),
		created() {
			this.getMemberDetails();
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
				if (this.getTotalPointsToCashOut >= parseInt(this.min_cashout_points) && this.getTotalPointsToCashOut <= parseInt(this.max_cashout_points)) {
					return true;
				} else {
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
				let pairing_points = parseInt(this.cashout.left_points) + parseInt(this.cashout.right_points);
				this.cashout.total = pairing_points;
				return pairing_points;
			},

			getUsdValueOfPairingPoints() {
				return ((parseInt(this.cashout.left_points) + parseInt(this.cashout.right_points)) / 100) * 5;
			},

			getTotalPairingPoints() {
				return parseInt(this.left_points) + parseInt(this.right_points);
			}

		},
		methods: {
			getMemberDetails() {
				this.showLoading = true;

				axios.get('/member/api/account-details').then((resp) => {
					console.log(resp);
					
					this.points = resp.data.user.points;
					this.left_points = resp.data.user.left_points;
					this.right_points = resp.data.user.right_points;
					this.last_cashout_at = resp.data.last_cashout_at;
					this.last_activated_at = resp.data.last_activated_at;

					this.showLoading = false;

				}).catch((err) => {
					console.log(err);
				});
			},

			showConfirmationModal() {
				$('.ui.cashout-pairing-modal.modal').modal('show');
			},

			closeConfirmTransactionModal() {
				$('.ui.cashout-pairing-modal.modal').modal('hide');
			},

			cashoutPairingBonus() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.closeConfirmTransactionModal();
						this.showLoading = true;
						axios.post('/member/api/cashout-points', {
							left_points: this.cashout.left_points,
							right_points: this.cashout.right_points,
							user_id: this.user_id,
							point_transaction_type: 'pairing'
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