<template>
	<div>
		<div class="ui piled segment">
			<div class="ui inverted dimmer" :class="{ active: showLoading }">
			    <div class="ui text loader">Loading</div>
			</div>

			<div class="ui icon message" v-if="showSuccess">
			  <i class="check circle outline icon"></i>
			  <div class="content">
			    <div class="header">
			      You are now registered
			    </div>
			    <p>In order to sign-in we need you to validate the email address that you used to sign-up. We have sent you an email together with the link to activate your account. Please do check it now thank you.</p>
			  </div>
			</div>
			
			<div v-if="!showSuccess">
				<h3>Membership Form - Referred By Member: {{ referral.fullname }}</h3>
				<form class="ui form" @submit.prevent="submitForm()">
					<div class="field" :class="{ error: errors.has('email') }">
						<label>Email (username)</label>
						<input 
							type="text" 
							name="email" 
							placeholder="john.doe@example.com" 
							v-model="email" 
							v-validate="'required|email'" 
							data-vv-as="Email"
						/>
						
						<div class="ui pointing label" v-if="errors.has('email')">
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
						
						<div class="ui pointing label" v-if="errors.has('password')">
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
						
						<div class="ui pointing label" v-if="errors.has('password_confirmation')">
							{{ errors.first('password_confirmation') }}
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
							
							<div class="ui pointing label" v-if="errors.has('firstname')">
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
							
							<div class="ui pointing label" v-if="errors.has('midname')">
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
							
							<div class="ui pointing label" v-if="errors.has('lastname')">
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
						
						<div class="ui pointing label" v-if="errors.has('contact_no')">
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
							v-model="btc_wallet"
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
							v-model="paylance_wallet" 
							v-validate="'required'"
							data-vv-as="Paylance Wallet"
						/>
						
						<div class="ui pointing label" v-if="errors.has('paylance_wallet')">
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
							
							<div class="ui pointing label" v-if="errors.has('accept_terms')">
								You must accept our terms &amp; conditions
							</div>
							
						</div>
					</div>
					<button class="ui button positive" type="submit">Sign up</button>
				</form>
			</div>
		</div>
	</div>
</template>
<script>
	export default {
		name: 'admin-auth-sign-up',
		props: {
			referral_link: {
				type: String,
				required: true
			}
		},
		data: () => ({
			email: '',
			password: '',
			password_confirmation: '',
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
			referral: []
		}),
		methods: {

			initialize() {
				this.getReferralDetails();
			},

			getReferralDetails() {
				axios.get('/api/referral/details', {
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

			submitForm () {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.showLoading = true

						let formData = new FormData();

						formData.append('email', this.email);
						formData.append('password', this.password);
						formData.append('fullname', this.firstname + ' ' + this.midname + ' ' + this.lastname);
						formData.append('contact_no', this.contact_no);
						formData.append('tbc_wallet', this.tbc_wallet);
						formData.append('btc_wallet', this.btc_wallet);
						formData.append('paylance_wallet', this.paylance_wallet);
						formData.append('referral_id', this.referral_link);

						axios.post('/api/sign-up', formData).then((resp) => {
							console.log(resp);
							if (resp.data.status == 'ok') {
								this.showLoading = false
								this.showSuccess = true
							}
						}).catch((err) => {
							console.log(err);
						})
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"})
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus()
					}
				})
			}

		},
		created () {
			this.initialize();
		}
	}
</script>