<template>
	<div>
		<div class="ui grid container">
			<div class="row">
				<div class="sixteen wide column">
					<div class="ui red segment" style="max-height: 250px; overflow-y: scroll;">
						<h3 class="header">
							 <i class="list alternate outline big icon"></i> Admin Messages 
						</h3>
						<div class="ui divider"></div>
						<div class="relaxed divided" style="padding: 10px;">
							<div v-for="(message, index) in messages" :key="index" class="item">
								<h3 class="ui red header">
									<i class="users icon" v-if="message.user_id == '0'"></i> {{message.subject}} 
								</h3> 
								<p>{{ message.message }}</p>
								
							</div>
						</div>
					</div>
					<pagination :data="messages_data" v-bind:showDisabled="true" icon="chevron" v-on:change-page="getMessages" size="tiny"></pagination>
				</div>
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
		props: {
			user_id: {
				type: String,
				required: true
			}
		},
		data: () => ({
			messages: [],
			messages_data: {},
			show_loading: false
		}),
		created() {
			this.getMessages();
		},
		methods: {
			getMessages(page) {

				this.show_loading = true;

				if (page === undefined) {
					page = 1;
				}

				axios.get('/tbcgoldadmin/api/messages2', {
					params: {
						page: page,
						user_id: this.user_id
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
		}
	}
</script>