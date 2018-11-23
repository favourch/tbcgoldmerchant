<template>
	<div>
		<div class="ui grid container">
			
			<div class="row">
				<div class="sixteen wide column">
					
						
						<div class="ui inverted dimmer" :class="{ active: show_loading }">
							<div class="ui text loader">Loading</div>
						</div>
						
						<div class="ui grid container">
							<div class="row">
								<div class="sixteen wide right aligned column">
									<button class="ui green button" type="button" @click.prevent="showNewMessageModal">New Message</button>
								</div>

								<div class="sixteen wide column" style="padding-top: 10px;">
									<table class="ui small compact striped sortable table">
										<thead>
											<tr>
												<th>Member</th>
												<th>Subject</th>
												<th>Date</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="(message, index) in messages" :key="index">
												<td>
													<span v-if="message.user_id > 0">
													{{ message.user_detail.fullname }} ({{ message.user.alias_name }})</span> 
													<span v-else>ALL</span>
												</td>
												<td>{{ message.subject }}</td>
												<td>{{ message.created_at }}</td>
												<td>
													<button class="ui icon fluid button mini" type="button" @click.prevent="showEditMessageModal(message.id)">
													View
													</button>
												</td>
											</tr>
											<tr>
												<td colspan="4" v-if="messages.length < 1">No Messages...</td>
											</tr>
										</tbody>
									</table>

									<pagination :data="messages_data" v-bind:showDisabled="true" icon="chevron" v-on:change-page="getMessages" size="tiny"></pagination>

								</div>
							</div>								
						</div>

					
				</div>
			</div>
		</div>

		<div class="ui new-message-modal modal small">
			<div class="header">
				New Message
			</div>
			<div class="scrolling content">
				<div class="description">
					<div class="ui form">
						
						<div class="field" :class="{ error: errors.has('subject'), disabled: show_loading_sending }">
							<label>Subject</label>
							<input
								type="text"
								name="subject"
								v-model="message.subject"
								v-validate="{ required: true }"
								data-vv-as="Subject"
							/>
							<div class="ui pointing red basic label" v-if="errors.has('subject')">
								{{ errors.first('subject') }}
							</div>
						</div>

						<div class="field" :class="{ error: errors.has('member') }">
							<label>Member</label>
							<select class="ui search dropdown" :class="{disabled: show_loading_sending}" v-model="message.user_id">
								<option value="0"><i class="users icon"></i> All</option>
								<option v-for="(member, index) in members" :value="member.id">
									<template v-if="member.detail == null">
										<i class="user icon"></i> ({{ member.alias_name }})
									</template>
									<template v-else>
										<i class="user icon"></i> {{ member.detail.fullname }} ({{ member.alias_name }})
									</template>
								</option>
							</select>

							<div class="ui pointing red basic label" v-if="errors.has('member')">
								{{ errors.first('member') }}
							</div>
						</div>

						<div class="field" :class="{ error: errors.has('message'), disabled: show_loading_sending }">
							<label>Message</label>
							<textarea 
								name="message"
								v-model="message.message"
								v-validate="{ required: true }"
								data-vv-as="Message"
							>
							</textarea>

							<div class="ui pointing red basic label" v-if="errors.has('message')">
								{{ errors.first('message') }}
							</div>
						</div>
						
					</div>
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="closeNewMessageModal">Cancel</div>
				<div class="ui orange button" @click.prevent="sendNewMessage" :class="{loading: show_loading_sending}">Send New Message</div>
			</div>
		</div>

		<div class="ui edit-message-modal modal small">
			<div class="header">
				Edit Message
			</div>
			<div class="scrolling content">
				<div class="description">
					<div class="ui form">

						<div class="field" :class="{ error: errors.has('subject'), disabled: show_loading_sending }">
							<label>Subject</label>
							<input
								type="text"
								name="subject"
								v-model="message.subject"
								v-validate="{ required: true }"
								data-vv-as="Subject"
							/>
							<div class="ui pointing red basic label" v-if="errors.has('subject')">
								{{ errors.first('subject') }}
							</div>
						</div>

						<div class="field" :class="{ error: errors.has('member') }">
							<label>Member</label>

							<select class="ui search dropdown" :class="{disabled: show_loading_sending}" v-model="message.user_id">
								<option value="0"><i class="users icon"></i> All</option>
								<option v-for="(member, index) in members" :value="member.id">
									<template v-if="member.detail == null">
										<i class="user icon"></i> ({{ member.alias_name }})
									</template>
									<template v-else>
										<i class="user icon"></i> {{ member.detail.fullname }} ({{ member.alias_name }})
									</template>
								</option>
							</select>
							
							
							<div class="ui pointing red basic label" v-if="errors.has('member')">
								{{ errors.first('member') }}
							</div>
						</div>
		
						<div class="field" :class="{ error: errors.has('message'), disabled: show_loading_sending }">
							<label>Message</label>
							<textarea 
								name="message"
								v-model="message.message"
								v-validate="{ required: true }"
								data-vv-as="Message"
							>
							</textarea>

							<div class="ui pointing red basic label" v-if="errors.has('message')">
								{{ errors.first('message') }}
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="closeEditMessageModal">Cancel</div>
				<div class="ui red button" @click.prevent="deleteMessage" :class="{loading: show_loading_sending}">Delete</div>
				<div class="ui orange button" @click.prevent="updateMessage" :class="{loading: show_loading_sending}">Update Message</div>
			</div>
		</div>

	</div>
</template>
<script>
	import pagination from 'laravel-vue-semantic-ui-pagination';
	export default {
		components: {
			pagination
		},
		data: () => ({
			messages: [],
			messages_data: {},
			members: [],
			message: {
				subject: '',
				message: '',
				admin_id: 0,
				user_id: 0,
				id: 0,
			},
			show_loading: false,
			show_loading_members: false,
			show_loading_sending: false
		}),
		created() {
			this.getMessages();
			this.getMembers();
		},
		methods: {
			getMessages(page) {

				this.show_loading = true;

				if (page === undefined) {
					page = 1;
				}

				axios.get('/tbcgoldadmin/api/messages', {
					params: {
						page: page
					}
				}).then((resp) => {
					console.log(resp);
					this.messages = resp.data.messages.data;
					this.messages_data = resp.data.messages;
					this.show_loading = false;
				}).catch((err) => {
					console.log(err);
				});
			},

			getMembers() {
				this.show_loading_members = true;
				axios.get('/tbcgoldadmin/api/members/active').then((resp) => {
					console.log(resp);
					this.members = resp.data.members;
				}).catch((err) => {
					console.log(err);
				})
			},

			sendNewMessage() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.show_loading_sending = true;
						axios.post('/tbcgoldadmin/api/messages', {
							user_id: this.message.user_id,
							subject: this.message.subject,
							message: this.message.message
						}).then((resp) => {
							console.log(resp);
							this.$notify({
					          title: 'Okay',
					          message: 'Message Sent Successfully',
					          type: 'success'
					        });
					        this.show_loading_sending = false;
					        this.closeNewMessageModal();
					        this.getMessages();
					        this.clearFields();
					        
						}).catch((err) => {
							console.log(err);
						});
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				})
				
			},

			showNewMessageModal() {
				this.clearFields();
				$('.ui.new-message-modal.modal').modal('show');
			},

			closeNewMessageModal() {
				$('.ui.new-message-modal.modal').modal('hide');
			},

			showEditMessageModal(id) {
				this.getMessage(id);
				$('.ui.edit-message-modal.modal').modal('show');
			},

			closeEditMessageModal() {
				$('.ui.edit-message-modal.modal').modal('hide');
			},

			getMessage(id) {
				axios.get('/tbcgoldadmin/api/message/' + id).then((resp) => {
					console.log(resp);
					this.message.user_id = resp.data.message.user_id;
					this.message.subject = resp.data.message.subject;
					this.message.message = resp.data.message.message;
					this.message.id = resp.data.message.id;
					$('.ui.dropdown').dropdown('set selected', '' + this.message.user_id);
				}).catch((err) => {
					console.log(err);
				});
			},

			clearFields() {
				this.message.subject = '';
				this.message.user_id = 0;
				this.message.message = '';
				this.message.id = 0;
				$('.ui.dropdown').dropdown('set selected', '0');
				setTimeout(() => {
					this.$validator.errors.items = [];
				}, 100);

			},

			updateMessage() {
				this.show_loading_sending = true;
				axios.post('/tbcgoldadmin/api/message/' + this.message.id, {
					user_id: this.message.user_id,
					subject: this.message.subject,
					message: this.message.message
				}).then((resp) => {
					console.log(resp);
					this.$notify({
						title: 'Okay',
						message: 'Message Updated Successfully',
						type: 'success'
					});
					this.show_loading_sending = false;
					this.closeEditMessageModal();
					this.getMessages();
					this.clearFields();
				}).catch((err) => {
					console.log(err);
				})
			},

			deleteMessage() {
				axios.post('/tbcgoldadmin/api/message/delete/' + this.message.id).then((resp) => {
					console.log(resp);
					this.$notify({
						title: 'Okay',
						message: 'Message Deleted Successfully',
						type: 'success'
					});
					this.show_loading_sending = false;
					this.closeEditMessageModal();
					this.getMessages();
					this.clearFields();
				}).catch((err) => {
					console.log(err);
				});
			},
			
		}
	}
</script>