<template>
	<div class="ui basic segment" :class="{ loading: show_loading }">
	
		<div class="sixteen wide column">
			<button class="ui pink huge fluid button" @click.prevent="showConfirmationModal()" v-if="is_level_two_pending == 0">
			<i class="sort amount up icon"></i> Upgrade Membership
			</button>
			<button class="ui pink huge fluid button" v-if="is_level_two_pending > 0">
			<i class="notched circle loading icon"></i> Upgrading is on process...
			</button>
			<div class="ui divider"></div>
		</div>


		<div class="ui membership-upgrade small modal">
			<div class="header">
				Upgrade Membership = Level 2
			</div>
			<div class="image content">
				<div class="description">
					<div class="ui form">
						<div class="field" >
							<table class="ui celled table">
								<tbody>
									<tr>
										<td style="width: 30%">Upgrade Cost ($):</td>
										<td>{{ plan2 | currency }}</td>
									</tr>
									<tr>
										<td style="width: 30%">Upgrading Deduction ($):</td>
										<td>{{ upgrading_deduction }}</td>
									</tr>
									<tr>
										<td style="width: 30%">Total value to send ($):</td>
										<td>{{ total_upgrade_cost }}</td>
									</tr>
									<tr>
										<td style="width: 30%">Total value to send (BTC):</td>
										<td>{{ total_value_to_send_btc }}</td>
									</tr>
								</tbody>
							</table>
							<h4>Send amount on this BTC Account</h4>
							<div class="ui action fluid input">
								<input type="text" :value="company_btc_account" readonly="" />
								<button class="ui teal right labeled icon button" @click.prevent="copyText(company_btc_account)">
									<i class="copy icon"></i>
									Copy
								</button>
							</div>
						</div>
					
						<div class="field" :class="{ error: errors.has('transaction_hash') }">
							<label>Transaction Hash</label>
							<input 
								type="text" 
								name="transaction_hash" 
								v-model="transaction_hash" 
								v-validate="'required|transaction_hash_unique'" 
								data-vv-as="Transaction Hash" 
							/>
							<div class="ui pointing red basic label" v-if="errors.has('transaction_hash')">
								{{ errors.first('transaction_hash') }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="closeConfirmTransactionModal">Cancel</div>
				<div class="ui violet button" @click.prevent.stop="submitUpgrade">
					Submit Upgrade Request
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import { Validator } from 'vee-validate';
	export default {
		props: {
			plan1: {
				type: Number,
				required: true
			},
			plan2: {
				type: Number,
				required: true
			},
			plan3: {
				type: Number,
				required: true
			},
			plan1_btc: {
				type: Number,
				required: true
			},
			plan2_btc: {
				type: Number,
				required: true
			},
			plan3_btc: {
				type: Number,
				required: true
			},
			user_id: {
				type: Number,
				required: true
			},
			upgrading_deduction: {
				type: Number,
				required: true
			},
			total_value_to_send_btc: {
				type: Number,
				required: true
			},
			total_upgrade_cost: {
				type: Number,
				required: true
			},
			has_level_two_pending: {
				type: Number,
				required: true
			},
		},
		data: () => ({
			show_loading: true,
			company_btc_account: '',
			transaction_hash: '',
			checkingTransactionHash: false,
			show_success: false,
			is_level_two_pending: 0
		}),
		methods: {
			showConfirmationModal() {
				$('.ui.membership-upgrade.modal').modal('show');
			},

			closeConfirmTransactionModal() {
				$('.ui.membership-upgrade.modal').modal('hide');
			},

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

			getCompanyAsset() {
				axios.get('/company-btc-account').then((resp) => {
					console.log(resp);
					this.company_btc_account = resp.data.company.btc_wallet3;
				}).catch((err) => {
					console.log(err);
				});
			},

			copyText(value) {
		        this.$copyText(value).then((resp) => {
		        	this.$notify({
			          title: 'Copied',
			          message: '',
			          type: 'success'
			        });
		        }).catch((err) => {
		        	console.log(err);
		        });
			},

			submitUpgrade() {

				this.$validator.validateAll().then((result) => {
					if (result) {
						this.show_loading = true;

						let formData = new FormData();
					
						formData.append('user_id', this.user_id);
						formData.append('plan_id', 2);
						formData.append('plan_price', this.plan2);
						formData.append('transaction_hash', this.transaction_hash);
						formData.append('current_btc_value', this.total_value_to_send_btc);
						formData.append('wallet_address', this.company_btc_account);
						formData.append('upgrading_deduction', this.upgrading_deduction);
						
						axios.post('/member/api/membership/upgrade', formData).then((resp) => {
							console.log(resp);
							if (resp.data.status == 'ok') {
								this.is_level_two_pending += 1;
								this.show_loading = false;
								this.show_success = true;
								this.closeConfirmTransactionModal();
								this.$notify({
						          title: 'Success',
						          message: 'Upgrade Request Sent',
						          type: 'success'
						        });
							}
						}).catch((err) => {
							console.log(err);
						});

					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				});

				
			},
		},
		created() {
			this.getMemberDetails();
			this.getCompanyAsset();
			this.is_level_two_pending = this.has_level_two_pending;

			const isTransactionHashUnique = (value) => {
				this.checkingTransactionHash = true;
			  return axios.post('/member/api/validate-transaction-hash', { transaction_hash: value }).then((resp) => {
			    // Notice that we return an object containing both a valid property and a data property.
			    this.checkingTransactionHash = false;
			    return {
			      valid: resp.data.valid,
			      data: {
			        message: resp.data.message
			      }
			    };
			  });
			}

			// The messages getter may also accept a third parameter that includes the data we returned earlier.
			Validator.extend('transaction_hash_unique', {
			  validate: isTransactionHashUnique,
			  getMessage: (field, params, data) => {
			    return data.message;
			  }
			});
		}
	}
</script>