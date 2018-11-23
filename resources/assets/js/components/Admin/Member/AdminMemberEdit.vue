<template>
	<div>
		<div class="ui basic segment" :class="{ loading: show_loading }">
			<div class="ui grid container">
				<div class="row">
					<div class="eight wide column">
						<h3>Member Details</h3>
					</div>
					<div class="eight wide right aligned column">
						<button class="ui button" @click.prevent="goToMemberList"><i class="arrow left icon"></i> Back</button>
						<button type="button" class="ui negative button" @click.prevent="toggleDeleteModal" v-if="!active">Delete</button>
						<button type="button" class="ui violet button" @click.prevent="toggleModal">Save Changes</button>
					</div>

					<div class="sixteen wide column">

						<div class="ui divider"></div>

						<button type="button" class="ui pink button" @click.prevent="toggleUpgradeModal" v-if="(is_level_two_pending < 1 && has_level_two_confirmed < 1)">Upgrade Membership</button>

						<button type="button" class="ui pink button" v-if="is_level_two_pending > 0">
							<i class="notched circle loading icon"></i> Upgrading is on process...
						</button>

						<button type="button" class="ui pink button" v-if="has_level_two_confirmed > 0">
							<i class="check icon"></i> Upgraded Already
						</button>

						<div class="ui divider"></div>

						<div class="inline field">
							<h5>Account Status: 
							<div class="ui toggle checkbox">
								<input type="checkbox" class="hidden" v-model="account_status">
								<label v-if="account_status">Active</label>
								<label v-else>Inactive</label>
							</div>
							</h5>
						</div>
						<h5>Direct Sponsor: {{ direct_sponsor }}</h5>
						<h5>Direct Upline: {{ current_sponsor }}</h5>
						<h5>Referral Link: <a href="#" @click.prevent="copyText">{{ referral_link }}</a></h5>
					</div>
				</div>

				<div class="ui divider"></div>

				<div class="row">
					<div class="seven wide column">
						<div class="ui form">
							<h4>Account Credentials</h4>
							<div class="field" :class="{ error: errors.has('email') }">
								<label>Email Address</label>
								<input
								type="text"
								v-model="email"
								name="email"
								v-validate="'required|unique_email'"
								data-vv-as="Email"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('email')">
									{{ errors.first('email') }}
								</div>
							</div>

							<div class="field">
								<label>Current Password: {{ sub_password }}</label><br />
								<label>Enter password to overide the current password:</label>
								<input
									type="password"
									v-model="password"
									name="password"
								/>
								
							</div>

							<div class="field" :class="{ error: errors.has('btc_wallet') }">
								<label>BTC Wallet</label>
								<input
									type="text"
									v-model="btc_wallet"
									name="btc_wallet"
									v-validate="'required'"
									data-vv-as="btc_wallet"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('btc_wallet')">
									{{ errors.first('btc_wallet') }}
								</div>
							</div>

							<div class="field" :class="{ error: errors.has('tbc_wallet') }">
								<label>TBC Wallet</label>
								<input
									type="text"
									v-model="tbc_wallet"
									name="tbc_wallet"
									v-validate="'required'"
									data-vv-as="tbc_wallet"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('tbc_wallet')">
									{{ errors.first('tbc_wallet') }}
								</div>
							</div>

							<div class="field" :class="{ error: errors.has('paylance_wallet') }">
								<label>Paylance Wallet</label>
								<input
									type="text"
									v-model="paylance_wallet"
									name="paylance_wallet"
									v-validate="'required'"
									data-vv-as="paylance_wallet"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('paylance_wallet')">
									{{ errors.first('paylance_wallet') }}
								</div>
							</div>

						</div>
					</div>

					<div class="two wide column"></div>
					<div class="seven wide column">
						<h4>Personal Details</h4>
						<div class="ui form">
							<div class="field" :class="{ error: errors.has('fullname') }">
								<label>Full Name</label>
								<input
									type="text"
									v-model="fullname"
									name="fullname"
									v-validate="'required'"
									data-vv-as="fullname"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('fullname')">
									{{ errors.first('fullname') }}
								</div>
							</div>

							<div class="field" :class="{ error: errors.has('contact_no') }">
								<label>Contact No.</label>
								<input
									type="text"
									v-model="contact_no"
									name="contact_no"
									v-validate="'required'"
									data-vv-as="contact_no"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('contact_no')">
									{{ errors.first('contact_no') }}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="ui divider"></div>
				<div class="row">
					<div class="seven wide column">
						<h4>Membership Details</h4>
						<div class="ui form">
							<div class="field">
								<label>Membership Plan</label>
								<select class="ui fluid dropdown" name="selected_plan" v-model="selected_plan">
									<option v-for="(mp, index) in membership_plans" :key="index" :value="mp.id">{{ mp.name }}</option>
								</select>
							</div>

							<div class="field" :class="{ error: errors.has('plan_cost') }">
								<label>Plan Cost</label>
								<input
									type="text"
									v-model="plan_cost"
									name="plan_cost"
									v-validate="'required'"
									data-vv-as="plan_cost"
									readonly
								/>
								<div class="ui pointing red basic label" v-if="errors.has('plan_cost')">
									{{ errors.first('plan_cost') }}
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="ui divider"></div>
				<div class="row">
					<div class="seven wide column">
						<h4>Accumulated Points</h4>
						<div class="ui form">
							<div class="field" :class="{ error: errors.has('referral_points') }">
								<label>Referral Points</label>
								<input
									type="text"
									v-model="referral_points"
									name="referral_points"
									v-validate="'required'"
									data-vv-as="referral_points"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('referral_points')">
									{{ errors.first('referral_points') }}
								</div>
							</div>
							<div class="field" :class="{ error: errors.has('total_points') }">
								<label>Total Points(left &amp; right)</label>
								<input
									type="text"
									v-model="total_points"
									name="total_points"
									v-validate="'required'"
									data-vv-as="total_points"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('total_points')">
									{{ errors.first('total_points') }}
								</div>
							</div>

							<div class="field" :class="{ error: errors.has('left_points') }">
								<label>Left Points</label>
								<input
									type="text"
									v-model="left_points"
									name="left_points"
									v-validate="'required'"
									data-vv-as="left_points"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('left_points')">
									{{ errors.first('left_points') }}
								</div>
							</div>
							
							<div class="field" :class="{ error: errors.has('right_points') }">
								<label>Right Points</label>
								<input
									type="text"
									v-model="right_points"
									name="right_points"
									v-validate="'required'"
									data-vv-as="right_points"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('right_points')">
									{{ errors.first('right_points') }}
								</div>
							</div>

							<div class="field" :class="{ error: errors.has('unilevel_bonus') }">
								<label>Unilevel Bonus ($)</label>
								<input
									type="text"
									v-model="unilevel_bonus"
									name="unilevel_bonus"
									v-validate="'required'"
									data-vv-as="unilevel_bonus"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('unilevel_bonus')">
									{{ errors.first('unilevel_bonus') }}
								</div>
							</div>

							<div class="field" :class="{ error: errors.has('upgrading_total') }">
								<label>Upgrading Deduction ($)</label>
								<input
									type="text"
									v-model="upgrading_total"
									name="upgrading_total"
									v-validate="'required'"
									data-vv-as="upgrading_total"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('upgrading_total')">
									{{ errors.first('upgrading_total') }}
								</div>
							</div>

							<div class="field" :class="{ error: errors.has('commission_balance') }">
								<label>CD Deduction ($)</label>
								<input
									type="text"
									v-model="commission_balance"
									name="commission_balance"
									v-validate="'required'"
									data-vv-as="commission_balance"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('commission_balance')">
									{{ errors.first('commission_balance') }}
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="ui divider"></div>
				<div class="row">
					<div class="seven wide column">
						<h4>Account Activities</h4>
						<p>Created At: <strong>{{ created_at | moment('MMM DD, YYYY h:mm:ss a') }}</strong></p>
						<p>Last Activated At: <strong>{{ last_activated_at | moment('MMM DD, YYYY h:mm:ss a') }}</strong></p>
						<p>Last Cashout At: <strong>{{ last_cashout_at | moment('MMM DD, YYYY h:mm:ss a') }}</strong></p>
						<p>Last Updated At: <strong>{{ last_updated_at | moment('MMM DD, YYYY h:mm:ss a') }}</strong></p>
					</div>
				</div>
				
			</div>
			<div class="ui grid container">
				<div class="row">
					<div class="sixteen wide column">
						<button type="button" class="ui violet button" @click.prevent="toggleModal">Save Changes</button>
					</div>
				</div>
			</div>
		</div>


		<div class="ui update-member tiny modal">
			<div class="header">
				Action: Update Member Details
			</div>
			<div class="scrolling content">
				<div class="description">
					<p>Are you sure?</p>
					
					<p v-if="password != ''">You have entered a new password. This will overide member current password.</p>
					
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="toggleModal">Cancel</div>
				<div class="ui violet button" @click.prevent="editMember">Update</div>
			</div>
		</div>

		<div class="ui delete-member mini modal">
			<div class="header">
				Delete Member
			</div>
			<div class="scrolling content">
				<div class="description">
					<p>Are you sure?</p>
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="toggleDeleteModal">Cancel</div>
				<div class="ui negative button" @click.prevent="deleteMember">Delete</div>
			</div>
		</div>

		<div class="ui upgrade-member mini modal">
			<div class="header">
				Upgrade Membership
			</div>
			<div class="scrolling content">
				<div class="description">
					<p>Are you sure?</p>
					<p>Upgrading Cost: <strong> {{ plan2 | currency }} </strong></p>
					<p>Upgrading Deduction: <strong> {{ upgrading_total | currency }} </strong></p>
					<p>Total Upgrading Cost: <strong> {{ total_upgrade_cost | currency }} </strong></p>
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="toggleUpgradeModal">Cancel</div>
				<div class="ui pink button" @click.prevent="upgradeMembership">Upgrade</div>
			</div>
		</div>

	</div>
</template>
<script>
	import { Validator } from 'vee-validate';

	export default {
		props: {
			user_id: {
				type: Number,
				required: true
			},
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
			has_level_two_confirmed: {
				type: Number,
				required: true
			},
		},
		data: () => ({
			fullname: '',
			email: '',
			password: '',
			sub_password: '',
			contact_no: '',
			tbc_wallet: '',
			btc_wallet: '',
			paylance_wallet: '',
			active: '',
			members: [],
			created_at: '',
			last_activated_at: '',
			last_cashout_at: '',
			last_updated_at: '',
			total_points: 0,
			left_points: 0,
			right_points: 0,
			referral_points: 0,
			unilevel_bonus: 0,
			upgrading_total: 0,
			commission_balance: 0,
			plan_id: '',
			plan_name: '',
			plan_cost: '',
			referral_id: '',
			referral_link: '',
			direct_sponsor: '',
			current_sponsor: '',
			account_status: '',
			membership_plans: [],
			selected_plan: '',
			show_loading: false,
			company_btc_account: '',
			is_level_two_pending: false
		}),

		created () {
			this.is_level_two_pending = this.has_level_two_pending;

			const isEmailUnique = (value) => {
				this.checkingEmail = true;
			  return axios.post('/member/api/validate-email', { email: value, user_id: this.user_id }).then((resp) => {
			    // Notice that we return an object containing both a valid property and a data property.
			    this.checkingEmail = false;
			    return {
			      valid: resp.data.valid,
			      data: {
			        message: resp.data.message
			      }
			    };
			  });
			};

			// The messages getter may also accept a third parameter that includes the data we returned earlier.
			Validator.extend('unique_email', {
			  validate: isEmailUnique,
			  getMessage: (field, params, data) => {
			    return data.message;
			  }
			});

			this.getMemberDetails();
			this.getMemberSponsors();
			this.getMembershipPlans();

			this.getCompanyAsset();
		},

		methods: {

			toggleModal() {
				$('.ui.update-member.modal').modal('toggle');
			},

			toggleDeleteModal() {
				$('.ui.delete-member.modal').modal('toggle');
			},

			toggleUpgradeModal() {
				$('.ui.upgrade-member.modal').modal('toggle');
			},

			copyText() {
				this.$copyText(this.referral_link).then((resp) => {
		        	this.$notify({
			          title: 'Copied',
			          message: '',
			          type: 'success'
			        });
		        }).catch((err) => {
		        	console.log(err);
		        });
			},
			getMemberDetails() {
				this.show_loading = true;
				axios.get('/tbcgoldadmin/api/member/details/' + this.user_id).then((resp) => {
					console.log(resp);
					this.fullname = resp.data.member.detail.fullname;
					this.email = resp.data.member.email;
					this.total_points = resp.data.member.points;
					this.right_points = resp.data.member.right_points;
					this.left_points = resp.data.member.left_points;
					this.referral_points = resp.data.member.referral_points;
					this.unilevel_bonus = resp.data.member.unilevel_bonus;
					this.upgrading_total = resp.data.member.upgrading_total;
					this.commission_balance = resp.data.member.commission_balance;
					this.account_status = parseInt(resp.data.member.active);
					this.contact_no = resp.data.member.detail.contact_no;
					this.tbc_wallet = resp.data.member.detail.tbc_wallet;
					this.btc_wallet = resp.data.member.detail.btc_wallet;
					this.paylance_wallet = resp.data.member.detail.paylance_wallet;
					this.plan_id = resp.data.member.plan.id;
					this.plan_name = resp.data.member.plan.name;
					this.plan_cost = resp.data.member.plan.price;
					this.last_updated_at = resp.data.member.updated_at;
					this.last_activated_at = resp.data.member.last_activated_at;
					this.last_cashout_at = resp.data.member.last_exchanged_at;
					this.created_at = resp.data.member.created_at;
					this.selected_plan = resp.data.member.plan.id;
					this.referral_link = this.$baseUrl + '/member/sign-up/' + resp.data.member.referral_link;
					this.sub_password = resp.data.member.sub_password;
					this.show_loading = false;
				}).catch((err) => {
					console.log(err);
				});
			},

			goToMemberList() {
				window.location.href = '/tbcgoldadmin/members';
			},

			getMemberSponsors() {
				axios.get('/tbcgoldadmin/api/member/sponsors', {
					params: {
						user_id: this.user_id
					}
				}).then((resp) => {
					console.log(resp);
					this.direct_sponsor = resp.data.direct_sponsor;
					this.current_sponsor = resp.data.current_sponsor;
				}).catch((err) => {
					console.log(err);
				});
			},

			getMembershipPlans() {
				axios.get('/tbcgoldadmin/api/membership/plans').then((resp) => {
					console.log(resp);
					this.membership_plans = resp.data.plans;
				}).catch((err) => {
					console.log(err);
				});
			},

			editMember() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.show_loading = true;
						axios.post('/tbcgoldadmin/api/member/edit', {
							user_id: this.user_id,
							email: this.email,
							password: this.password,
							fullname: this.fullname,
							contact_no: this.contact_no,
							tbc_wallet: this.tbc_wallet,
							btc_wallet: this.btc_wallet,
							paylance_wallet: this.paylance_wallet,
							plan_id: this.selected_plan,
							points: this.total_points,
							left_points: this.left_points,
							right_points: this.right_points,
							active: this.account_status
						}).then((resp) => {
							console.log(resp);
							if (resp.data.status == 'ok') {
								this.$notify({
						          title: 'Success',
						          message: 'Saved Member Details',
						          type: 'success'
						        });
							} else if (resp.data.status == 'fail') {
								this.$notify({
						          title: 'Error!',
						          message: 'Something went wrong!',
						          type: 'error'
						        });
							}

							this.show_loading = false;
							this.getMemberDetails();
							this.getMemberSponsors();
							this.getMembershipPlans();
							this.toggleModal();
							
						}).catch((err) => {
							console.log(err);
							this.show_loading = false;
							this.toggleModal();
						});
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				});
			},

			deleteMember() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.show_loading = true;
						axios.post('/tbcgoldadmin/api/member/delete', {
							user_id: this.user_id,
						}).then((resp) => {
							if (resp.data.status == 'ok') {
								this.$notify({
						          title: 'Deleted!',
						          message: '',
						          type: 'success'
						        });

						        setTimeout(() => {
						        	this.goToMemberList();
						        }, 1000);

							} else if (resp.data.status == 'fail') {
								this.$notify({
						          title: 'Error!',
						          message: 'Something went wrong!',
						          type: 'error'
						        });

						        this.show_loading = false;
							}
	
						}).catch((err) => {
							console.log(err);
							this.show_loading = false;
						});
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				});

			},

			upgradeMembership() {

					this.show_loading = true;

						let formData = new FormData();
					
						formData.append('user_id', this.user_id);
						formData.append('plan_id', 2);
						formData.append('plan_price', this.plan2);
						formData.append('transaction_hash', 'cash');
						formData.append('current_btc_value', this.total_value_to_send_btc);
						formData.append('wallet_address', this.company_btc_account);
						formData.append('upgrading_deduction', this.upgrading_total);
						
						axios.post('/member/api/membership/upgrade', formData).then((resp) => {
							console.log(resp);
							if (resp.data.status == 'ok') {
								this.is_level_two_pending += 1;
								this.show_loading = false;
								this.show_success = true;
								this.toggleUpgradeModal();
								this.$notify({
						          title: 'Success',
						          message: 'Upgrade Request Sent',
						          type: 'success'
						        });
							}
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

			clearFields() {
				this.fullname = '';
				this.email = '';
				this.password = '';
				this.password_confirmation = '';
				this.contact_no = '';
				this.tbc_wallet = '';
				this.btc_wallet = '';
				this.paylance_wallet = '';
				this.sponsor_id = '';
				this.getMembers();
			}

		},
		
	}
</script>