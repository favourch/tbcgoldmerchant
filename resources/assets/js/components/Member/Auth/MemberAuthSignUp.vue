<template>
	<div>
		<div class="ui raised segment">
			<div class="ui inverted dimmer" :class="{ active: showLoading }">
			    <div class="ui text loader">Loading</div>
			</div>

			<div class="ui icon message" v-if="showSuccess">
			  <i class="check circle outline icon"></i>
			  <div class="content">
			    <div class="header">
			      You are now registered
			    </div>
			    <p>
			    	Your membership is on process now. Please be patient on waiting as we are validating the details you have submitted. It may take 1 day for validating.
			    </p>
			  </div>
			</div>
			
			<div v-if="!showSuccess">

				<h3>Membership Form - Referred By Member: {{ referral.fullname }}</h3>
				<div class="ui mini steps">
					<div class="step" :class="{ active: step1, disabled: !step1 }">
						<i class="pencil icon"></i>
						<div class="content">
							<div class="title">Fill Up</div>
							<div class="description">Input Personal &amp; Account Details</div>
						</div>
					</div>
					<div class="step" :class="{ active: step2, disabled: !step2 }">
						<i class="payment icon"></i>
						<div class="content">
							<div class="title">Payment</div>
							<div class="description">Send payment here</div>
						</div>
					</div>

					<div class="step" :class="{ active: step3, disabled: !step3 }">
						<i class="qrcode icon"></i>
						<div class="content">
							<div class="title">Transaction Hash</div>
							<div class="description">Input transaction hash</div>
						</div>
					</div>
				</div>
				<form class="ui form" @submit.prevent="submitForm()">
					<div v-show="step1">
						<div class="field" :class="{ error: errors.has('email') }">
							<label>Username/Email</label>
							<input 
								type="text" 
								name="email" 
								placeholder="john.doe@example.com" 
								v-model="email" 
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
								name="password" 
								placeholder="XXXXXXXXXXX" 
								v-model="password"
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
								name="password_confirmation" 
								placeholder="XXXXXXXXXXX" 
								v-validate="'confirmed:password'" 
								data-vv-as="Password Confirmation"
							/>
							
							<div class="ui pointing red basic label" v-if="errors.has('password_confirmation')">
								{{ errors.first('password_confirmation') }}
							</div>
							
						</div>

						<div class="field" :class="{ error: errors.has('alias') }">
							<label>Alias</label>
							<input 
								type="text" 
								name="alias" 
								placeholder="alias" 
								v-model="alias" 
								v-validate="'required|alias_unique'" 
								data-vv-as="Alias"
							/>
							
							<div class="ui pointing red basic label" v-if="errors.has('alias')">
								{{ errors.first('alias') }}
							</div>
							
						</div>

						<hr>
						<br />
						<div class="three fields">
							<div class="field" :class="{ error: errors.has('firstname') }">
								<label>First name</label>
								<input 
									type="text" 
									placeholder="First Name" 
									name="firstname"  
									v-model="firstname"
									v-validate="'required'"
									data-vv-as="First Name"
								/>
								
								<div class="ui pointing red basic label" v-if="errors.has('firstname')">
									{{ errors.first('firstname') }}
								</div>
								
							</div>
							<div class="field" :class="{ error: errors.has('midname') }">
								<label>Middle name</label>
								<input
									type="text" 
									placeholder="Middle Name" 
									name="midname" 
									v-validate="'required'" 
									data-vv-as="Middle Name"
								/>
								
								<div class="ui pointing red basic label" v-if="errors.has('midname')">
									{{ errors.first('midname') }}
								</div>
								
							</div>
							<div class="field" :class="{ error: errors.has('lastname') }">
								<label>Last name</label>
								<input 
									type="text" 
									placeholder="Last Name" 
									name="lastname" 
									v-model="lastname" 
									v-validate="'required'"
									data-vv-as="Last Name"
								/>
								
								<div class="ui pointing red basic label" v-if="errors.has('lastname')">
									{{ errors.first('lastname') }}
								</div>
								
							</div>
						</div>
						
						<div class="field" :class="{ error: errors.has('contact_no') }">
							<label>Contact No.</label>
							<input 
								type="text" 
								name="contact_no" 
								placeholder="09099999999" 
								v-model="contact_no" 
								v-validate="'required|numeric'" 
								data-vv-as="Contact No."
							/>
							
							<div class="ui pointing red basic label" v-if="errors.has('contact_no')">
								{{ errors.first('contact_no') }}
							</div>
							
						</div>
						<div class="field" :class="{ error: errors.has('tbc_wallet') }">
							<label>TBC Wallet</label>
							<input 
								type="text" 
								name="tbc_wallet" 
								placeholder="XXXXXXXXXXX" 
								v-model="tbc_wallet" 
								v-validate="'required'" 
								data-vv-as="Tbc Wallet"
							/>
							
							<div class="ui pointing red basic label" v-if="errors.has('tbc_wallet')">
								{{ errors.first('tbc_wallet') }}
							</div>
							
						</div>
						<div class="field" :class="{ error: errors.has('btc_wallet') }">
							<label>BTC Wallet</label>
							<input 
								type="text" 
								name="btc_wallet" 
								placeholder="XXXXXXXXXXX" 
								v-model="btc_wallet"
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
								name="paylance_wallet" 
								placeholder="XXXXXXXXXXX" 
								v-model="paylance_wallet" 
								data-vv-as="Paylance Wallet"
							/>
							
							<div class="ui pointing red basic label" v-if="errors.has('paylance_wallet')">
								{{ errors.first('paylance_wallet') }}
							</div>
							
						</div>
						<div class="field" :class="{ error: errors.has('accept_terms') }">
							<div class="ui checkbox">
								<input 
									type="checkbox" 
									name="accept_terms" 
									v-model="accept_terms"
									v-validate="'required'" 
									data-vv-as="Terms and Conditions"
								/>
								<label>I agree to the Terms and Conditions</label>
								
								<div class="ui pointing red basic label" v-if="errors.has('accept_terms')">
									You must accept our terms &amp; conditions
								</div>
								
							</div>
						</div>
						<button class="ui button primary" type="button" @click.prevent="validateFillUpForm">
							Proceed to next step <i class="angle double right icon"></i>
						</button>
					</div>

					<div v-show="step2">
						<div class="field">
							<table class="ui table">
								<tbody>
									<tr>
										<td>Plan Price:</td>
										<td>{{ plan_price | currency }}</td>
									</tr>
									<tr>
										<td>BTC Value:</td>
										<td>{{ btc_value }}</td>
									</tr>
								</tbody>
							</table>
							<h4>Send amount(BTC) on this BTC Account</h4>
							<div class="ui action input">
								<input type="text" :value="company_btc_account" readonly="" />
								<button class="ui teal right labeled icon button" @click.prevent="copyText">
								<i class="copy icon"></i>
								Copy
								</button>
							</div>
						</div>
						<div class="ui divider"></div>
						<button class="ui button" type="button" @click.prevent="showDetailsForm">Previous</button>
						<button class="ui button primary" type="button" @click.prevent="validateBillingForm">
							Proceed to final step <i class="angle double right icon"></i>
						</button>
					</div>

					<div v-show="step3">
						<div class="field" :class="{ error: errors.has('transaction_hash') }">
							<label>Please enter the transaction hash (the generated hash after you send bitcoin to our BTC account)</label>
							<input 
								type="text" 
								name="transaction_hash" 
								placeholder="XXXXXXXXXXXXXXXXXX" 
								v-model="transaction_hash" 
								v-validate="'required|transaction_hash_unique'" 
								data-vv-as="Transaction Hash"
							/>
							
							<div class="ui pointing red basic label" v-if="errors.has('transaction_hash')">
								{{ errors.first('transaction_hash') }}
							</div>
							<div class="ui pointing red basic label" v-if="checkingTransactionHash">
								Checking hash...
							</div>
						</div>
						
						<button class="ui button" type="button" @click.prevent="showBillingForm">Previous</button>

						<button class="ui button primary" type="button" @click.prevent="submitMemberData">
							Submit Registration Details <i class="angle double right icon"></i>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>
<script>
	import { Validator } from 'vee-validate';
	export default {
		name: 'member-auth-sign-up',
		props: {
			referral_link: {
				type: String,
				required: true
			},
			btc_value: {
				type: String,
				required: true
			},
			plan_price: {
				type: String,
				required: true
			},
			wallet_address: {
				type: String,
				required: true
			},
			transaction_no: {
				type: String,
				required: true
			}
		},
		data: () => ({
			email: '',
			password: '',
			password_confirmation: '',
			alias: '',
			firstname: '',
			midname: '',
			lastname: '',
			contact_no: '',
			tbc_wallet: '',
			btc_wallet: '',
			paylance_wallet: '',
			accept_terms: false,
			showLoading: false,
			showSuccess: false,
			referral: [],
			amount: 0,
			transaction_hash: 'cash',
			company_btc_account: null,
			step1: true,
			step2: null,
			step3: null,
			checkingTransactionHash: false
		}),
		methods: {

			copyText() {
		        this.$copyText(this.company_btc_account).then((resp) => {
		        	this.$notify({
			          title: 'Copied',
			          message: '',
			          type: 'success'
			        });
		        }).catch((err) => {
		        	console.log(err);
		        });
			},

			getReferralDetails() {
				axios.get('/member/api/referral/details', {
					params: {
						referral_link: this.referral_link
					}
				}).then((resp) => {
					this.referral = resp.data.user.detail;
					console.log(resp);
				}).catch((err) => {
					console.log(err);
				});
			},

			validateFillUpForm () {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.showBillingForm();
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				});
			},

			validateBillingForm () {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.showTransactionHashForm();
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				});
			},

			showDetailsForm() {
				this.step1 = true;
				this.step2 = false;
				this.step3 = false;
			},

			showBillingForm() {
				this.step1 = false;
				this.step2 = true;
				this.step3 = false;
			},

			showTransactionHashForm() {
				this.step1 = false;
				this.step2 = false;
				this.step3 = true;
			},

			submitMemberData() {

				this.$validator.validateAll().then((result) => {
					if (result) {
						this.showLoading = true;

						let formData = new FormData();
						formData.append('email', this.email);
						formData.append('password', this.password);
						formData.append('fullname', this.firstname + ' ' + this.midname + ' ' + this.lastname);
						formData.append('contact_no', this.contact_no);
						formData.append('tbc_wallet', this.tbc_wallet);
						formData.append('btc_wallet', this.btc_wallet);
						formData.append('paylance_wallet', this.paylance_wallet);
						formData.append('referral_id', this.referral_link);
						formData.append('transaction_hash', this.transaction_hash);
						formData.append('transaction_no', this.transaction_no);
						formData.append('current_btc_value', this.btc_value);
						formData.append('wallet_address', this.company_btc_account);
						formData.append('alias_name', this.alias);
						
						axios.post('/member/api/sign-up', formData).then((resp) => {
							console.log(resp);
							if (resp.data.status == 'ok') {
								this.showLoading = false;
								this.showSuccess = true;
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

			getCompanyAsset() {
				axios.get('/company-btc-account').then((resp) => {
					console.log(resp);
					this.company_btc_account = resp.data.company.btc_wallet;
				}).catch((err) => {
					console.log(err);
				});
			},

		},
		created () {
			this.getReferralDetails();
			this.getCompanyAsset();

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