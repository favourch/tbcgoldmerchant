<template>
	<div>

		<div class="ui raised segment">
			<div class="row">
				<div class="four wide column"></div>
				<div class="eight wide column">
					<h2 class="ui center aligned icon header">
					<i class="user circle outline massive icon"></i>
					<font style="vertical-align: inherit;">
						<font style="vertical-align: inherit;">
							Admin Sign in Area
						</font>
					</font>
					</h2>
				</div>
			</div>

			<div class="ui inverted dimmer" :class="{ active: showLoading }">
			    <div class="ui text loader">Loading</div>
			</div>

			<div class="ui icon message" v-if="showSuccess">
			  <i class="check circle outline icon"></i>
			  <div class="content">
			    <div class="header">
			      Successfully Login!
			    </div>
			    <p><i class="circle notch icon loading"></i>Please wait redirecting...</p>
			  </div>
			</div>

			<div class="ui icon message" v-if="showError">
			  <i class="exclamation circle icon"></i>
			  <div class="content">
			    <div class="header">
			     	Error
			    </div>
			    <p>{{ showErrorMsg }}</p>
			  </div>
			</div>
			
			<div v-if="!showSuccess">
				<form class="ui form" @submit.prevent="submitForm()">
					<div class="field" :class="{ error: errors.has('email') }">
						<label>Email (username)</label>
						<input 
							type="text" 
							name="email" 
							placeholder="john.doe@example.com" 
							v-model="email" 
							v-validate="'required'" 
							data-vv-as="Email"
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
							v-validate="'required'"
							data-vv-as="Password"
						/>
						
						<div class="ui pointing red basic label" v-if="errors.has('password')">
							{{ errors.first('password') }}
						</div>
						
					</div>
	
					<button class="ui button primary" type="submit">Sign in</button>
				</form>
			</div>
		</div>
	</div>
</template>
<script>
	export default {
		name: 'admin-auth-sign-in',
		data: () => ({
			email: '',
			password: '',
			showLoading: false,
			showSuccess: false,
			showError: false,
			showErrorMsg: ''
		}),
		methods: {

			submitForm () {
				this.$validator.validateAll().then((result) => {
					if (result) {

						this.showLoading = true;
						
						let formData = new FormData();

						formData.append('email', this.email);
						formData.append('password', this.password);

						axios.post('/tbcgoldadmin/api/sign-in', formData).then((resp) => {
							console.log(resp);

							if (resp.data.status == 'ok') {

								this.showLoading = false;
								this.showSuccess = true;
								this.showError = false;

								setTimeout(() => {
									window.location.replace('/tbcgoldadmin/dashboard');
								}, 1500);

							} else if (resp.data.status == 'fail') {

								this.showLoading = false;
								this.showError = true;
								this.showErrorMsg = resp.data.error
							}


						}).catch((err) => {
							console.log(err);
						})

					} else {

						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();

					}

				})
			}

		},
		created () {
			
		}
	}
</script>