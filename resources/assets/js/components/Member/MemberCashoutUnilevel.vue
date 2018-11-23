<template>
	<div>
		<div class="ui form">
		<div class="ui message">
			<div class="header">
				Note:
			</div>
			<ul class="list">
				<li>To cash out unilevel bonus you must have atleast 10$.</li>
				<li>Cash out is only available on every 5th day of the beginning of the month.</li>
			</ul>
		</div>
		
		<div v-if="isCashoutDay">
			<div class="ui grid container">
				<div class="row">
					<div class="three wide column"></div>
					<div class="ten wide column">
						<div class="ui raised segment" v-if="!show_success">
							<div class="ui inverted dimmer" :class="{ active: show_loading }">
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
										<div class="ui red label">{{ unilevel_bonus | currency }}</div>
									</div>
									<!-- <img class="ui avatar image" src="/images/avatar2/small/lena.png"> -->
									<i class="caret right large icon"></i>
									<div class="content">
										<h4><strong>Your Total Unilevel Bonus</strong></h4>
									</div>
								</div>
							</div>

							<div class="field" :class="{ error: errors.has('unilevel_bonus') }">
								<label>Enter Unilevel Bonus:</label>
								<input
									type="number"
									name="unilevel_bonus"
									v-model.number="cashout.unilevel_bonus"
									v-validate="{ required: true, numeric: true, max_value:unilevel_bonus, min_value:10 }"
									data-vv-as="Unilevel Bonus"
									placeholder="Unilevel Bonus"
									step="100"
									min="0"
									style="text-align: center"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('unilevel_bonus')">
									{{ errors.first('unilevel_bonus') }}
								</div>
								
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
						<div class="ui raised center aligned segment" v-if="show_success">
							<i class="check green massive circular icon success-cashout"></i>
							<h2>Sent Succcessfully!</h2>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div v-if="!isCashoutDay">
			<h3 class="ui center aligned header">Oops! Not a cash out day.</h3>
		</div>
	</div>
	<div class="ui cashout-referral-modal modal mini">
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
			<div class="ui orange button" @click.prevent="cashoutBonus" v-if="validPointsToCashout">Confirm Cash Out</div>
			
		</div>
	</div>
	</div>
</template>
<script>
	export default {
		props: {
			user_id: {
				type: Number,
				required: true
			}
		},

		data: () => ({
			show_loading: false,
			show_success: false,
			unilevel_bonus: 0,
			cashout: {
				unilevel_bonus: 0,
			}
		}),

		computed: {

			isCashoutDay() {
				let today = new Date();
				if (today.getDate() == '05') {
					return true;
				} else {
					return false;
				}
			},

			getTotalUnilevelBonus() {
				return this.cashout.unilevel_bonus;
			},

			validPointsToCashout() {
				if (this.getTotalUnilevelBonus > 0 && this.getTotalUnilevelBonus <= 100) {
					return true;
				} else {
					return false;
				}
				
			},

		},

		methods: {

			getMemberDetails() {
				this.show_loading = true;

				axios.get('/member/api/account-details').then((resp) => {
					console.log(resp);
					
					this.unilevel_bonus = parseFloat(resp.data.user.unilevel_bonus);

					this.show_loading = false;

				}).catch((err) => {
					console.log(err);
				});
			},

			cashoutBonus() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.show_loading = true;
						this.closeConfirmTransactionModal();

						axios.post('/member/api/cashout/unilevel', {
							user_id: this.user_id,
							unilevel_bonus: this.cashout.unilevel_bonus
						}).then((resp) => {
							console.log(resp);
							this.$notify({
					          title: 'Cash Out Request Sent',
					          message: 'Please wait for the confirmation of admin thank you.',
					          type: 'success'
					        });

					        
					        this.show_success = true;

							setTimeout(() => {
								$('.success-cashout').transition({
									animation: 'jiggle',
									duration: '2s',
								});
							}, 500);

						}).catch((err) => {
							console.log(err);
						})
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				})
			},

			showConfirmTransactionModal() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						$('.ui.cashout-referral-modal.modal').modal('show');
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				})
			},

			closeConfirmTransactionModal() {
				$('.ui.cashout-referral-modal.modal').modal('hide');
			},

		},

		created() {
			this.getMemberDetails();
		}
	}
</script>