<template>
	<div>
		<div class="ui inverted dimmer" :class="{ active: showLoading }">
			<div class="ui text loader">Loading</div>
		</div>
		
		<div class="ui grid container">
			<div class="row">
				<div class="nine wide column">
					<div class="ui segment">
						<form class="ui form" @submit.prevent="updateDetails(details)">
							<div class="field" :class="{ error: errors.has('alias') }">
								<label>Alias</label>
								<input
									type="text"
									name="alias"
									placeholder="john"
									v-model="details.alias"
									v-validate="'required|alias_unique'"
									data-vv-as="Alias"
								/>
								
								<div class="ui pointing label" v-if="errors.has('alias')">
									{{ errors.first('alias') }}
								</div>
							</div>
							
							<div class="field" :class="{ error: errors.has('fullname') }">
								<label>Full Name</label>
								<input
									type="text"
									name="fullname"
									placeholder="John Doe"
									v-model="details.fullname"
									v-validate="'required'"
									data-vv-as="Full Name"
								/>
								
								<div class="ui pointing label" v-if="errors.has('fullname')">
									{{ errors.first('fullname') }}
								</div>
								
							</div>
							<div class="field" :class="{ error: errors.has('contact_no') }">
								<label>Contact No.</label>
								<input
									type="text"
									name="contact_no"
									placeholder="09099999999"
									v-model="details.contact_no"
									v-validate="'required|numeric'"
									data-vv-as="Contact No."
								/>
								
								<div class="ui pointing label" v-if="errors.has('contact_no')">
									{{ errors.first('contact_no') }}
								</div>
								
							</div>
							<div class="ui divider"></div>
							<br />
							<div class="field" :class="{ error: errors.has('tbc_wallet') }">
								<label>TBC Wallet</label>
								<input
									type="text"
									name="tbc_wallet"
									placeholder="XXXXXXXXXXX"
									v-model="details.tbc_wallet"
									v-validate="'required'"
									data-vv-as="Tbc Wallet"
								/>
								
								<div class="ui pointing label" v-if="errors.has('tbc_wallet')">
									{{ errors.first('tbc_wallet') }}
								</div>
								
							</div>
							<div class="field" :class="{ error: errors.has('btc_wallet') }">
								<label>BTC Wallet</label>
								<input
									type="text"
									name="btc_wallet"
									placeholder="XXXXXXXXXXX"
									v-model="details.btc_wallet"
									v-validate="'required'"
									data-vv-as="BTC Wallet"
								/>
								
								<div class="ui pointing label" v-if="errors.has('btc_wallet')">
									{{ errors.first('btc_wallet') }}
								</div>
								
							</div>

							<div class="field" :class="{ error: errors.has('paylance_wallet') }">
								<label>Paylance Wallet</label>
								<input
									type="text"
									name="paylance_wallet"
									placeholder="XXXXXXXXXXX"
									v-model="details.paylance_wallet"
									v-validate="'required'"
									data-vv-as="Paylance Wallet"
								/>
								
								<div class="ui pointing label" v-if="errors.has('paylance_wallet')">
									{{ errors.first('paylance_wallet') }}
								</div>
								
							</div>
	
							<button class="ui button positive" type="submit">Save Changes</button>
						</form>
					</div>
				</div>
				<div class="seven wide column">
					<div class="ui fluid card">
						<div class="content">
							<div class="header">Username: {{ credentials.email }}</div>
						</div>
						<div class="content">
							<!-- <h4 class="ui sub header">Activity</h4> -->
							<div class="ui small feed">
								<div class="event">
									<div class="content">
										<div class="summary">
											<p>Referral Points: {{ details.referral_points }}</p>
											<table class="ui celled table">
												<thead>
													<tr>
														<th>Left Points</th>
														<th>Right Points</th>
														<th>Total</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>{{ details.left_points }}</td>
														<td>{{ details.right_points }}</td>
														<td>{{ details.points }}</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="event">
									<div class="content">
										<div class="summary">
											<div class="ui action input">
												<input type="text" :value="details.referral_link" style="width: 82%" readonly />
												<button class="ui teal right labeled icon button" @click.prevent="copyText">
													<i class="copy icon"></i>
													Referral Link										
												</button>
											</div>
											<!-- <a :href="details.referral_link" target="_blank">{{ details.referral_link }}</a> -->
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="extra content">
							<h4>Update Password</h4>
							<form class="ui form" @submit.prevent="updatePassword(credentials)">
								
								<div class="field" :class="{ error: errors.has('old_password') }">
									<label>Old Password</label>
									<input
										type="password"
										name="old_password"
										placeholder="XXXXXXXXXXX"
										v-model="credentials.old_password"
										v-validate="'required|min:5'"
										data-vv-as="Old Password"
									/>
									
									<div class="ui pointing red basic label" v-if="errors.has('old_password')">
										{{ errors.first('old_password') }}
									</div>
									
								</div>

								<div class="field" :class="{ error: errors.has('new_password') }">
									<label>New Password</label>
									<input
										type="password"
										name="new_password"
										placeholder="XXXXXXXXXXX"
										v-model="credentials.new_password"
										v-validate="'required|min:5'"
										data-vv-as="New Password"
									/>
									
									<div class="ui pointing red basic label" v-if="errors.has('new_password')">
										{{ errors.first('new_password') }}
									</div>
									
								</div>
								
								<div class="field" :class="{ error: errors.has('new_password_confirmation') }">
									<label>New Password Confirmation</label>
									<input
										type="password"
										name="new_password_confirmation"
										placeholder="XXXXXXXXXXX"
										v-model="credentials.new_password_confirmation"
										v-validate="'confirmed:new_password'"
										data-vv-as="New Password"
									/>
									
									<div class="ui pointing red basic label" v-if="errors.has('new_password_confirmation')">
										{{ errors.first('new_password_confirmation') }}
									</div>
									
								</div>
								
								<button class="ui positive button" type="submit">Update Password</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import { Validator } from 'vee-validate';
	export default {
		name: 'member-account',
		data: () => ({
			details: {
				alias: '',
				fullname: '',
				contact_no: '',
				tbc_wallet: '',
				btc_wallet: '',
				paylance_wallet: '',
				referral_link: '',
				points: 0,
				right_points: 0,
				left_points: 0,
				referral_points: 0,
			},
			credentials: {
				email: '',
				old_password: '',
				new_password: '',
				new_password_confirmation: '',
			},
			user_id: '',
			showLoading: false,
			showSuccess: false,
			checkingAlias2: false
		}),
		created () {
			this.getMemberAccountDetails();

			const isAliasUnique = (value) => {
				this.checkingAlias2 = true;
			  return axios.post('/member/api/validate-alias-unique', { alias: value, user_id: this.user_id }).then((resp) => {
			    // Notice that we return an object containing both a valid property and a data property.
			    this.checkingAlias2 = false;
			    return {
			      valid: resp.data.valid,
			      data: {
			        message: resp.data.message
			      }
			    };
			  });
			};

			// The messages getter may also accept a third parameter that includes the data we returned earlier.
			Validator.extend('alias_unique', {
			  validate: isAliasUnique,
			  getMessage: (field, params, data) => {
			    return data.message;
			  }
			});
		},
		methods: {

			copyText() {
		        this.$copyText(this.details.referral_link).then((resp) => {
		        	this.$notify({
			          title: 'Copied',
			          message: '',
			          type: 'success'
			        });
		        }).catch((err) => {
		        	console.log(err);
		        });
			},

			getMemberAccountDetails() {
				axios.get('/member/api/account-details').then((resp) => {
						console.log(resp)
						this.credentials.email = resp.data.user.email;
						this.details.alias = resp.data.user.alias_name;
						this.details.fullname = resp.data.user.detail.fullname;
						this.details.contact_no = resp.data.user.detail.contact_no;
						this.details.tbc_wallet = resp.data.user.detail.tbc_wallet;
						this.details.btc_wallet = resp.data.user.detail.btc_wallet;
						this.details.paylance_wallet = resp.data.user.detail.paylance_wallet;
						this.details.points = resp.data.user.points;
						this.details.referral_link = this.$baseUrl + '/member/sign-up/' + resp.data.user.referral_link;
						this.user_id = resp.data.user.id;
						this.details.left_points = resp.data.user.left_points;
						this.details.right_points = resp.data.user.right_points;
						this.details.referral_points = resp.data.user.referral_points;
				}).catch((err) => {
					console.log(err);
				});
			},

			updateDetails(scope) {
				this.$validator.validateAll(scope).then((result) => {
					if (result) {
						this.showLoading = true

						let formData = new FormData();

						formData.append('fullname', this.details.fullname);
						formData.append('alias', this.details.alias);
						formData.append('contact_no', this.details.contact_no);
						formData.append('tbc_wallet', this.details.tbc_wallet);
						formData.append('btc_wallet', this.details.btc_wallet);
						formData.append('paylance_wallet', this.details.paylance_wallet);
						formData.append('user_id', this.user_id);

						axios.post('/member/api/details/update', formData).then((resp) => {
							console.log(resp);
							if (resp.data.status == 'ok') {
								this.showLoading = false;
								this.showSuccess = true;
								this.$notify({
						          title: 'Saved!',
						          message: '',
						          type: 'success'
						       	});
							} else if (resp.data.status == 'fail') {
								this.$notify({
						          title: 'Fail!',
						          message: '',
						          type: 'error'
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

			updatePassword(scope) {
				this.$validator.validateAll(scope).then((result) => {
					if (result) {
						axios.post('/member/api/password/update', {
							old_password: this.credentials.old_password,
							new_password: this.credentials.new_password
						}).then((resp) => {
							console.log(resp);
							if (resp.data.status == 'ok') {
								this.$notify({
						          title: 'Saved!',
						          message: '',
						          type: 'success'
						       	});
						       	this.credentials.new_password = '';
						       	this.credentials.old_password = '';
						       	this.credentials.new_password_confirmation = '';
						       	setTimeout(() => {
						        	this.$validator.errors.clear();
						        }, 100);
							} else if (resp.data.status == 'fail') {
								this.$notify({
						          title: 'Error!',
						          message: '',
						          type: 'error'
						       	});
							} else if (resp.data.status == 'error') {
								this.$notify({
						          title: 'Error!',
						          message: 'Wrong password',
						          type: 'error'
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
				
			}

		}
	}
</script>