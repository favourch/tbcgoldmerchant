<template>
	<div>
		<div class="ui raised segment">
			<div class="ui inverted dimmer" :class="{ active: showLoading }">
			    <div class="ui text loader">Loading</div>
			</div>
			<h2>Contact Us</h2>
			<div class="ui icon message" v-if="showSuccess">
			  <i class="check circle outline icon"></i>
			  <div class="content">
			    <div class="header">
			      Successfully Sent!
			    </div>
			  </div>
			</div>
			<div v-if="!showSuccess">
				<form class="ui form" @submit.prevent="sendMessage">
					<div class="field" :class="{ error: errors.has('fullname') }">
						<label>Full Name</label>
						<input
						type="text"
						name="fullname"
						v-model="fullname"
						v-validate="'required'"
						data-vv-as="Full Name"
						/>
						<div class="ui pointing red basic label" v-if="errors.has('fullname')">
							{{ errors.first('fullname') }}
						</div>
					</div>
					<div class="field" :class="{ error: errors.has('email') }">
						<label>Email</label>
						<input 
							type="text" 
							name="email" 
							v-model="email" 
							v-validate="'required|email'"
							data-vv-as="Email"
						/>
						<div class="ui pointing red basic label" v-if="errors.has('email')">
							{{ errors.first('email') }}
						</div>
					</div>
					<div class="field" :class="{ error: errors.has('subject') }">
						<label>Subject</label>
						<input 
							type="text" 
							name="subject" 
							v-model="subject" 
							v-validate="'required'"
							data-vv-as="Subject"
						/>
						<div class="ui pointing red basic label" v-if="errors.has('subject')">
							{{ errors.first('subject') }}
						</div>
					</div>
					<div class="field" :class="{ error: errors.has('fullname') }">
						<label>Message (optional)</label>
						<textarea v-model="message"></textarea>
					</div>
					<div class="field">
						<input type="submit" class="ui positive button" value="Send Message" />
					</div>
				</form>
			</div>
		</div>
	</div>
</template>
<script>
	export default {
		data: () => ({
			fullname: '',
			email: '',
			subject: '',
			message: '',
			showLoading: false,
			showSuccess: false
		}),

		created() {

		},

		methods: {
			sendMessage() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.showLoading = true;
						axios.post('/api/send-message', {
							fullname: this.fullname,
							email: this.email,
							subject: this.subject,
							message: this.message
						}).then((resp) => {
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
			}
		}
	}
</script>