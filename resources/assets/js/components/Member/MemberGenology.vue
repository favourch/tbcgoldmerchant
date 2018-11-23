<template>
	<div style="max-width: 1500px; overflow-x: scroll;">
		<div class="tree" style="min-width: 20000px;">
				<ul>
					<li>
						<a href="#" @click.prevent="clickEvent(previous_link, previous_base_link)">{{ base_link }}</a>
						
						<ul v-for="(dl, index) in downlines" v-if="index == 0" :key="index">
							<li v-for="(dl2, index2) in dl" :key="index2">
								<a href="#" @click.prevent="clickEvent(dl2.referral_link, dl2.detail.fullname)" @dblclick="getDownline2(dl2.referral_link, dl2.detail.fullname)"> {{ dl2.alias_name }}</a>
								<ul v-for="(dl3, index3) in downlines" v-if="index3 == 1" :key="index3">
									<li v-for="(dl4, index4) in dl3" v-if="dl4.referral_id == dl2.referral_link" :key="index4">
										<a href="#" @click.prevent="clickEvent(dl4.referral_link, dl4.detail.fullname, dl2.referral_link, dl2.detail.fullname)" @dblclick="getDownline2(dl4.referral_link, dl4.detail.fullname)"> {{ dl4.alias_name }}</a>
										<ul v-for="(dl5, index5) in downlines" v-if="index5 == 2" :key="index5">
											<li v-for="(dl6, index6) in dl5" v-if="dl6.referral_id == dl4.referral_link" :key="index6">
												<a href="#" @click.prevent="clickEvent(dl6.referral_link, dl6.detail.fullname, dl4.referral_link, dl4.detail.fullname)" @dblclick="getDownline2(dl6.referral_link, dl6.detail.fullname)"> {{ dl6.alias_name }}</a>
												<ul v-for="(dl7, index7) in downlines" v-if="index7 == 3" :key="index7">
													<li v-for="(dl8, index8) in dl7" v-if="dl8.referral_id == dl6.referral_link" :key="index8">
														<a href="#" @click.prevent="clickEvent(dl8.referral_link, dl8.detail.fullname, dl6.referral_link, dl6.detail.fullname)" @dblclick="getDownline2(dl8.referral_link, dl8.detail.fullname)"> {{ dl8.alias_name }}</a>
														<ul v-for="(dl9, index9) in downlines" v-if="index9 == 4" :key="index9">
															<li v-for="(dl10, index8) in dl9" v-if="dl10.referral_id == dl8.referral_link" :key="index8">
																<a href="#" @click.prevent="clickEvent(dl10.referral_link, dl10.detail.fullname, dl8.referral_link, dl8.detail.fullname)" @dblclick="getDownline2(dl10.referral_link, dl10.detail.fullname)">{{ dl10.alias_name }}</a>
																<ul v-for="(dl11, index11) in downlines" v-if="index11 == 5" :key="index11">
																	<li v-for="(dl12, index12) in dl11" v-if="dl12.referral_id == dl10.referral_link" :key="index12">
																		<a href="#" @click.prevent="clickEvent(dl12.referral_link, dl12.detail.fullname, dl10.referral_link, dl10.detail.fullname)" @dblclick="getDownline2(dl12.referral_link, dl12.detail.fullname)"> {{ dl12.alias_name }}</a>
																		<ul v-for="(dl13, index13) in downlines" v-if="index13 == 6" :key="index13">
																			<li v-for="(dl14, index14) in dl13" v-if="dl14.referral_id == dl12.referral_link" :key="index14">
																				<a href="#" @click.prevent="clickEvent(dl14.referral_link, dl14.detail.fullname, dl12.referral_link, dl12.detail.fullname)" @dblclick="getDownline2(dl14.referral_link, dl14.detail.fullname)"> {{ dl14.alias_name }}</a>
																				<ul v-for="(dl15, index15) in downlines" v-if="index15 == 7" :key="index15">
																					<li v-for="(dl16, index16) in dl15" v-if="dl16.referral_id == dl14.referral_link" :key="index16">
																						<a href="#" @click.prevent="clickEvent(dl16.referral_link, dl16.detail.fullname, dl14.referral_link, dl14.detail.fullname)" @dblclick="getDownline2(dl16.referral_link, dl16.detail.fullname)"> {{ dl16.alias_name }}</a>
																					</li>
																				</ul>
																			</li>
																		</ul>
																	</li>
																	
																</ul>
															</li>
														</ul>
													</li>
												</ul>
											</li>
										</ul>
									</li>
								</ul>
							</li>
						</ul>
					</li>
				</ul>
			</div>
	</div>
</template>

<script>

	export default {
		name: 'member-genology',

		props: {
			parent_referral_link: {
				type: String,
				required: true
			}
		},

		data: () => ({
			downlines: [],
			firstDownlines: [],
			total: 0,
			base_link: 'You',
			previous_link: '',
			previous_base_link: '',
			dialogVisible: false,
			selectedMemberToGive: '',
			selectedMemberToRecieve: '',
			selectedMemberToRecieveName: '',
			confirmationPopUp: false,
			showLoading: false,
			delay: 500,
	        clicks: 0,
	        timer: null,
		}),

		methods: {
			initialize() {
				this.getDownline();
				this.getFirstDownlines();
			},

			getFirstDownlines() {
				axios.get('/member/api/first-downlines', {
					params: {
						referral_link: this.parent_referral_link
					}
				}).then((resp) => {
					console.log(resp);
					this.firstDownlines = resp.data.members;
				}).catch((err) => {
					console.log(err);
				});
			},

			clickEvent(link, base_link, previous_link, previous_base_link) {
				this.clicks++;
				if(this.clicks === 1) {
					var self = this
					this.timer = setTimeout(() => {
						// self.result.push(event.type);
						this.getDownline(link, base_link, previous_link, previous_base_link);
						self.clicks = 0
					}, this.delay);
				} else {
					clearTimeout(this.timer);
					// this.result.push('dblclick');
					this.getDownline2(link, base_link);
					this.clicks = 0;
				}
			},

			getDownline(link, base_link, previous_link, previous_base_link) {
				if (link == this.parent_referral_link)
				{
					link = '';
				}
				axios.get('/member/api/downlines', {
					params: {
						referral_link: link
					}
				}).then((resp) => {
					console.log(resp.data);
					this.downlines = resp.data.downlines;
					this.total = resp.data.downlines.length;
					if (link != null)
					{
						this.base_link = base_link;
					}
					this.previous_link = resp.data.previous_link;
					this.previous_base_link = resp.data.previous_base_link == '' ? 'You' : resp.data.previous_base_link;
				}).catch((err) => {
					console.log(err);
				});
			},

			getDownline2(referral_link, referral_name) {
				this.dialogVisible = true;
				this.selectedMemberToRecieve = referral_link;
				this.selectedMemberToRecieveName = referral_name;
			},

			giveRecruit() {
				this.dialogVisible = false;
				this.confirmationPopUp = false;
				this.showLoading = true;
				axios.post('/member/api/give-recruit', {
					member_to_give: this.selectedMemberToGive,
					member_to_recieve: this.selectedMemberToRecieve
				}).then((resp) => {
					console.log(resp);
					if (resp.data.status == 'ok') {
						this.showLoading = false;
						
						this.initialize();
					}
				}).catch((err) => {
					console.log(err);
				});
			}
		},

		created() {
			this.initialize();
		}
	}
</script>
<style scoped>

</style>