<template>
	<div>
		<div class="ui grid container">
			<div class="row">

				<div class="eight wide column">
					<div class="ui raised segment">
						<form class="ui form" @submit.prevent="updateUsername(account_usr)">
							<div class="field">
								<label>Full Name</label>
								<input 
									type="text" 
									name="fullname"
									v-model="account_usr.fullname" 
									v-validate="'required'"
									data-vv-as="Full Name"
								/>
							</div>
							<div class="field">
								<label>Username</label>
								<input 
									type="text" 
									name="username" 
									v-model="account_usr.username" 
									v-validate="'required'"
									data-vv-as="Username"
								/>
							</div>
							
							<div class="field">
								<button
									class="ui positive button"
									
								>
									Update Account
								</button>
							</div>
						</form>
					</div>
				</div>

				<div class="eight wide column">
					<div class="ui raised segment">
						<div class="ui icon message" v-if="wrong_password">
						  <i class="exclamation circle icon"></i>
						  <div class="content">
						    <div class="header">
						     	Error
						    </div>
						    <p>Wrong Old Password</p>
						  </div>
						</div>
						<form class="ui form" @submit.prevent="updatePassword(account_pwd)">
							<div class="field" :class="{ error: errors.has('old_password') }">
								<label>Old Password</label>
								<input 
									type="password" 
									name="old_password"
									v-model="account_pwd.old_password" 
									v-validate="'required'"
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
									v-model="account_pwd.new_password" 
									v-validate="'required'"
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
									v-model="account_pwd.new_password_confirmation" 
									v-validate="'confirmed:new_password'"
									data-vv-as="New Password Confirmation"
								/>
								<div class="ui pointing red basic label" v-if="errors.has('new_password_confirmation')">
									{{ errors.first('new_password_confirmation') }}
								</div>
							</div>
							
							<div class="field">
								<button
									class="ui positive button"
									
								>
									Update Password
								</button>
							</div>
						</form>
					</div>
				</div>

				<div class="eight wide column">
					<div class="ui raised segment">
						<form class="ui form" @submit.prevent="updateWallets(account_wallets)">
							<div class="field">
								<label>Membership Wallet</label>
								<input 
									type="text" 
									name="membership_wallet"
									v-model="account_wallets.membership_wallet" 
									v-validate="'required'"
									data-vv-as="Membership Wallet"
								/>
							</div>
							<div class="field">
								<label>Maintenance Wallet</label>
								<input 
									type="text" 
									name="maintenance_wallet" 
									v-model="account_wallets.maintenance_wallet" 
									v-validate="'required'"
									data-vv-as="Maintenance Wallet"
								/>
							</div>
							
							<div class="field">
								<button
									class="ui positive button"
									
								>
									Update Wallets
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import { ErrorBag } from 'vee-validate';
	export default {
		name: 'admin-account',
		data: () => ({
			account_id: '',
			account_usr: {
				fullname: '',
				username: '',
			},
			account_pwd: {
				old_password: '',
				new_password: '',
				new_password_confirmation: '',
			},
			account_wallets: {
				membership_wallet: '',
				maintenance_wallet: ''
			},
			wrong_password: false
		}),
		created() {
			this.getAccountDetails();
			
		},
		methods: {
			getAccountDetails() {
				axios.get('/tbcgoldadmin/api/account/details').then((resp) => {
					console.log(resp);
					this.account_usr.username = resp.data.account.email;
					this.account_usr.fullname = resp.data.account.fullname;
					this.account_id = resp.data.account.id;
					
					this.account_wallets.membership_wallet = resp.data.wallets.btc_wallet;
					this.account_wallets.maintenance_wallet = resp.data.wallets.btc_wallet2;
				}).catch((err) => {
					console.log(err);
				});
			},

			updateUsername(scope) {
				this.$validator.validateAll(scope).then((result) => {
					if (result) {
						axios.post('/tbcgoldadmin/api/account/update/username', {
							username: this.account_usr.username,
							fullname: this.account_usr.fullname,
							account_id: this.account_id,
						}).then((resp) => {
							console.log(resp);
							if (resp.data.status == 'ok') {
								this.$notify({
						          title: 'Updated Successfully',
						          message: '',
						          type: 'success'
						        });
							} else if (resp.data.status == 'fail') {
								this.$notify({
						          title: 'Something went wrong!',
						          message: '',
						          type: 'error'
						        });
							}
						}).catch((err) => {
							console.log(err);
						})
					}
				})
			},

			updateWallets(scope) {
				
				this.$validator.validateAll(scope).then((result) => {
					if (result) {
						axios.post('/tbcgoldadmin/api/account/update/wallets', {
							maintenance_wallet: this.account_wallets.maintenance_wallet,
							membership_wallet: this.account_wallets.membership_wallet,
						}).then((resp) => {
							console.log(resp);

							if (resp.data.status == 'ok') {
								this.$notify({
						          title: 'Updated Successfully',
						          message: '',
						          type: 'success'
						        });

						        setTimeout(() => {
						        	this.$validator.errors.clear();
						        }, 100);
							}
						}).catch((err) => {
							console.log(err);
						});
					}
				})
			},

			updatePassword(scope) {
				
				this.$validator.validateAll(scope).then((result) => {
					if (result) {
						axios.post('/tbcgoldadmin/api/account/update/password', {
							old_password: this.account_pwd.old_password,
							new_password: this.account_pwd.new_password,
							new_password_confirmation: this.account_pwd.new_password_confirmation,
							account_id: this.account_id,
						}).then((resp) => {
							console.log(resp);
							if (resp.data.status == 'ok') {
								this.wrong_password = false;
								this.$notify({
						          title: 'Updated Successfully',
						          message: '',
						          type: 'success'
						        });
						        this.account_pwd.old_password = '';
						        this.account_pwd.new_password = '';
						        this.account_pwd.new_password_confirmation = '';
						        console.log(this.$validator);
						        setTimeout(() => {
						        	this.$validator.errors.clear();
						        }, 100);
							} else if (resp.data.status == 'error') {
								this.wrong_password = true;
							}
						}).catch((err) => {
							console.log(err);
						})
					}
				})
			},
		}
	}
</script>
