<template>
	<div>
		<div class="ui inverted dimmer" :class="{ active: showLoading }">
			<div class="ui text loader">Loading</div>
		</div>
		
		<div class="ui grid container">
			<div class="row">
				<div class="sixteen wide column">
					<form class="ui segment form" @submit.prevent="addMember">
						<div class="ui grid container">
							<div class="row">
								<div class="sixteen wide column">
									<h3>Member Registration Form</h3>
									<div class="ui divider"></div>
								</div>
								<div class="six wide column">
									<h4>Account Credentials</h4>
									<div class="field" :class="{ error: errors.has('email') }">
										<label>Username</label>
										<input 
											type="text" 
											v-model="email" 
											name="email" 
											v-validate="'required|unique_email'" 
											data-vv-as="Username"
										/>
										<div class="ui pointing red basic label" v-if="errors.has('email')">
											{{ errors.first('email') }}
										</div>
									</div>
									<div class="field" :class="{ error: errors.has('password') }">
										<label>Password</label>
										<input 
											type="password" 
											v-model="password" 
											name="password" 
											v-validate="'required|min:5'" 
											data-vv-as="Password"
										/>
										<div class="ui pointing red basic label" v-if="errors.has('password')">
											{{ errors.first('password') }}
										</div>
									</div>
									<div class="field" :class="{ error: errors.has('password_confirmation') }">
										<label>Password Confirmation</label>
										<input 
											type="password" 
											v-model="password_confirmation" 
											name="password_confirmation" 
											v-validate="'required|confirmed:password'" 
											data-vv-as="Password"
										/>
										<div class="ui pointing red basic label" v-if="errors.has('password_confirmation')">
											{{ errors.first('password_confirmation') }}
										</div>
									</div>
									
									<div class="field" :class="{ error: errors.has('alias') }">
										<label>Alias</label>
										<input 
											type="text" 
											v-model="alias" 
											name="alias" 
											v-validate="'required|alias_unique'" 
											data-vv-as="Alias"
										/>
										<div class="ui pointing red basic label" v-if="errors.has('alias')">
											{{ errors.first('alias') }}
										</div>
									</div>
								</div>
								<div class="two wide column"></div>
								<div class="six wide column">
									<h4>Personal Details</h4>
									<div class="field" :class="{ error: errors.has('fullname') }">
										<label>Full Name</label>
										<input 
											type="text" 
											v-model="fullname" 
											name="fullname" 
											v-validate="'required'" 
											data-vv-as="Full Name"
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
											data-vv-as="Contact No."
										/>
										<div class="ui pointing red basic label" v-if="errors.has('contact_no')">
											{{ errors.first('contact_no') }}
										</div>
									</div>
								</div>
							</div>
							<div class="ui divider"></div>
							<div class="row">
								<div class="six wide column">
									<h4>Wallet Details</h4>
									<div class="field" :class="{ error: errors.has('tbc_wallet') }">
										<label>TBC Wallet</label>
										<input 
											type="text" 
											v-model="tbc_wallet" 
											name="tbc_wallet" 
											v-validate="'required'" 
											data-vv-as="TBC Wallet"
										/>
										<div class="ui pointing red basic label" v-if="errors.has('tbc_wallet')">
											{{ errors.first('tbc_wallet') }}
										</div>
									</div>
									<div class="field" :class="{ error: errors.has('btc_wallet') }">
										<label>BTC Wallet</label>
										<input 
											type="text" 
											v-model="btc_wallet" 
											name="btc_wallet" 
											v-validate="'required'" 
											data-vv-as="BTC Wallet"
										/>
										<div class="ui pointing red basic label" v-if="errors.has('btc_wallet')">
											{{ errors.first('btc_wallet') }}
										</div>
									</div>
									<div class="field" :class="{ error: errors.has('paylance_wallet') }">
										<label>Paylance Wallet</label>
										<input 
											type="text" 
											v-model="paylance_wallet" 
											name="paylance_wallet" 
											v-validate="" 
											data-vv-as="Paylance Wallet"
										/>
										<div class="ui pointing red basic label" v-if="errors.has('paylance_wallet')">
											{{ errors.first('paylance_wallet') }}
										</div>
									</div>
								</div>
								<div class="two wide column"></div>
								<div class="six wide column">
									<h4>Account Positioning</h4>

									<div class="field" :class="{ error: errors.has('direct_sponsor') }">
										<label>Direct Sponsor (alias)</label>
										<input 
											type="text" 
											v-model="direct_sponsor" 
											name="direct_sponsor" 
											v-validate="'required|alias_exists'" 
											data-vv-as="Direct Sponsor"
										/>
										<div class="ui pointing red basic label" v-if="errors.has('direct_sponsor')">
											{{ errors.first('direct_sponsor') }}
										</div>
									</div>
									
									<div class="field" :class="{ error: errors.has('direct_upline') }">
										<label>Direct Upline (alias)</label>
										<input 
											type="text" 
											v-model="direct_upline" 
											name="direct_upline" 
											v-validate="'required|sponsor_exists2'" 
											data-vv-as="Direct Upline"
										/>
										<div class="ui pointing red basic label" v-if="errors.has('direct_upline')">
											{{ errors.first('direct_upline') }}
										</div>
									</div>
									
								</div>
							</div>
							<div class="row">
								<div class="sixteen right aligned wide column">
									<div class="field">
										<button type="submit" class="ui positive button"><i class="user plus icon"></i> Add Member</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import { Validator } from 'vee-validate';

	export default {

		data: () => ({
			fullname: '',
			alias: '',
			email: '',
			password: '',
			password_confirmation: '',
			contact_no: '',
			tbc_wallet: '',
			btc_wallet: '',
			paylance_wallet: '',
			active: '',
			members: [],
			sponsor_id: 'leader',
			direct_sponsor: 'leader',
			direct_upline: 'leader',
			showLoading: false,
			checkingAlias2: false,
			checkingAlias: false,
		}),

		created () {
			const isEmailUnique = (value) => {
				this.checkingEmail = true;
			  return axios.post('/member/api/validate-email', { email: value }).then((resp) => {
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

			const isAliasUnique = (value) => {
				this.checkingAlias2 = true;
			  return axios.post('/member/api/validate-alias-unique', { alias: value }).then((resp) => {
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

			const isAliasExists = (value) => {
				this.checkingAlias = true;
			  return axios.post('/member/api/validate-alias-exists2', { alias: value }).then((resp) => {
			    // Notice that we return an object containing both a valid property and a data property.
			    this.checkingAlias = false;
			    return {
			      valid: resp.data.valid,
			      data: {
			        message: resp.data.message
			      }
			    };
			  });
			};

			// The messages getter may also accept a third parameter that includes the data we returned earlier.
			Validator.extend('alias_exists', {
			  validate: isAliasExists,
			  getMessage: (field, params, data) => {
			    return data.message;
			  }
			});

			const isSponsorAliasExists2 = (value) => {
				this.checkingSponsorAlias = true;
			  return axios.post('/member/api/validate-sponsor-alias3', { sponsor_alias: value, direct_sponsor: this.direct_sponsor}).then((resp) => {
			    // Notice that we return an object containing both a valid property and a data property.
			    this.checkingSponsorAlias = false;
			    return {
			      valid: resp.data.valid,
			      data: {
			        message: resp.data.message
			      }
			    };
			  });
			}

			// The messages getter may also accept a third parameter that includes the data we returned earlier.
			Validator.extend('sponsor_exists2', {
			  validate: isSponsorAliasExists2,
			  getMessage: (field, params, data) => {
			    return data.message;
			  }
			});

			this.getMembers();
		},

		methods: {

			getMembers() {
				axios.get('/tbcgoldadmin/api/members/active').then((resp) => {
					console.log(resp);
					this.members = resp.data.members;
				}).catch((err) => {
					console.log(err);
				});
			},

			addMember() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.showLoading = true;
						axios.post('/tbcgoldadmin/api/member/add', {
							email: this.email,
							password: this.password,
							fullname: this.fullname,
							alias_name: this.alias,
							contact_no: this.contact_no,
							tbc_wallet: this.tbc_wallet,
							btc_wallet: this.btc_wallet,
							paylance_wallet: this.paylance_wallet,
							direct_upline: this.direct_upline,
							direct_sponsor: this.direct_sponsor,
						}).then((resp) => {
							console.log(resp);
							if (resp.data.status == 'ok') {
								this.$notify({
						          title: 'Saved!',
						          message: '',
						          type: 'success'
						        });
							} else if (resp.data.status == 'fail') {
								this.$notify({
						          title: 'Error!',
						          message: 'Cannot add member',
						          type: 'error'
						        });
							}
							this.showLoading = false;
							this.clearFields();
							setTimeout(() => {
								this.$validator.errors.clear();
							}, 100);
						}).catch((err) => {
							console.log(err);
							this.showLoading = false;
						});
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
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
				this.sponsor_id = 'leader';
				this.direct_sponsor = 'leader';
				this.direct_upline = 'leader';
				this.alias = '';
				this.getMembers();
			}

		},
		
	}
</script>