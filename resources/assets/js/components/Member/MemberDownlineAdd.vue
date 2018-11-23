<template>
	<div>
		<div class="ui grid container">
			<div class="row">
				<div class="three wide column"></div>
				<div class="ten wide column">
					<form class="ui raised segment form" :class="{ loading: show_loading }">
						<h3>Refer A Friend</h3>
						<div class="field" :class="{ error: errors.has('email') }">
							<label>Username/Email</label>
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
							<div class="ui pointing red basic label" v-if="checkingEmail">
								Checking email...
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
							<div class="ui pointing red basic label" v-if="checkingAlias2">
								Checking alias...
							</div>
						</div>

						<div class="ui divider"></div>

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
						<div class="ui divider"></div>
						<div class="field" :class="{ error: errors.has('direct_upline') }">
							<label>Direct Upline(alias)</label>
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
							<div class="ui pointing red basic label" v-if="checkingAlias">
								Checking alias...
							</div>
						</div>
						
						<div class="field">
							<table class="ui table">
								<tbody>
									<tr>
										<td>Membership Price ($):</td>
										<td>
											<div class="ui action input">
												<input type="text" :value="plan_price" readonly="" />
												<button class="ui teal right labeled icon button" @click.prevent="copyPlanPrice">
												<i class="copy icon"></i>
												Copy
												</button>
											</div>
										</td>
									</tr>
									<tr>
										<td>BTC Value:</td>
										<td>
											<div class="ui action input">
												<input type="text" :value="btc_value" readonly="" />
												<button class="ui teal right labeled icon button" @click.prevent="copyBtcValue">
												<i class="copy icon"></i>
												Copy
												</button>
											</div>
										</td>
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

						<div class="field">
							<button type="button" class="ui violet button" @click.prevent="toggleConfirmModal()">Submit Member Details</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="ui confirm-refer-modal modal mini">
			<div class="header">
				Confirm Submission of Member
			</div>
			<div class="image content">
				<div class="description">
					Are you sure?
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="toggleConfirmModal">Cancel</div>
				<div class="ui violet button" @click.prevent.stop="addMember()" :class="{ loading: show_loading }">Confirm</div>
				
			</div>
		</div>

	</div>
</template>
<script>
	import { Validator } from 'vee-validate';
	export default {
		props: {
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
			},
			direct_sponsor: {
				type: String,
				required: true
			}
		},
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
			direct_uplines: [],
			direct_upline: '',
			transaction_hash: '',
			company_btc_account: '',
			show_loading: false,
			checkingEmail: false,
			checkingAlias: false,
			checkingAlias2: false,
			checkingTransactionHash: false
		}),
		created() {
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

			const isAliasExists = (value) => {
				this.checkingAlias = true;
			  return axios.post('/member/api/validate-alias-exists', { alias: value }).then((resp) => {
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

			const isSponsorAliasExists2 = (value) => {
				this.checkingSponsorAlias = true;
			  return axios.post('/member/api/validate-sponsor-alias2', { sponsor_alias: value, direct_sponsor: this.direct_sponsor }).then((resp) => {
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
		},
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

			copyPlanPrice() {
		        this.$copyText(this.plan_price).then((resp) => {
		        	this.$notify({
			          title: 'Copied',
			          message: '',
			          type: 'success'
			        });
		        }).catch((err) => {
		        	console.log(err);
		        });
			},

			copyBtcValue() {
		        this.$copyText(this.btc_value).then((resp) => {
		        	this.$notify({
			          title: 'Copied',
			          message: '',
			          type: 'success'
			        });
		        }).catch((err) => {
		        	console.log(err);
		        });
			},

			toggleConfirmModal() {
				this.$validator.validateAll().then((result) => {
					if (result) { 
						$('.confirm-refer-modal').modal('toggle');
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				});
			},

			addMember() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.show_loading = true;
						axios.post('/member/api/downline/add', {
							email: this.email,
							password: this.password,
							fullname: this.fullname,
							alias_name: this.alias,
							contact_no: this.contact_no,
							tbc_wallet: this.tbc_wallet,
							btc_wallet: this.btc_wallet,
							paylance_wallet: this.paylance_wallet,
							direct_upline: this.direct_upline,
							transaction_hash: this.transaction_hash,
							transaction_no: this.transaction_no,
							current_btc_value: this.btc_value,
							wallet_address: this.wallet_address,
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
						          message: 'Something went wrong!',
						          type: 'error'
						        });
							}
							// this.show_loading = false;
							// this.clearFields();
							setTimeout(() => {
								window.location.href = '/member/downline/add'
							}, 2000);
							
						}).catch((err) => {
							console.log(err);
							this.$notify({
								title: 'Error!',
								message: 'Something went wrong!',
								type: 'error'
							});
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
			}
		}
	}
</script>