<template>
	<div>
		<div class="ui basic segment" :class="{ loading: show_loading }">
			<div class="ui grid container">
				
				<div class="row">

					<div class="nine wide column">
						<div class="ui compact small menu">
							<a class="item" :class="{active: filter_by_status == ''}" @click.prevent="updateStatus('')">
								Total <div class="ui label">{{ total_member }}</div>
							</a>
							<a class="item" :class="{active: filter_by_status == 'active'}" @click.prevent="updateStatus('active')">
								Active <div class="ui green label">{{ active_member }}</div>
							</a>
							<a class="item" :class="{active: filter_by_status == 'inactive'}" @click.prevent="updateStatus('inactive')">
								Inactive <div class="ui red label">{{ inactive_member }}</div>
							</a>
							<a class="item" :href="add_member_link">
								<i class="plus icon"></i> Add Member
							</a>
						</div>
					</div>
					<div class="seven wide right aligned column" style="padding-top: 5px;">
						<div class="ui form">
							<div class="two fields">
								<div class="field">
									<select class="ui fluid dropdown" v-model="search_by">
										<option value="">Search By</option>
										<option value="alias_name">Alias Name</option>
										<option value="fullname">Member Name</option>
									</select>
								</div>
								<div class="field">
									<div class="ui icon input" :class="{ loading: search_loading }">
										<input type="text" placeholder="Search..." v-model="search_input" @keyup.enter="getMembers(1)">
										<i class="search icon"></i>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="sixteen wide column">
						<table class="ui striped sortable table">
							<thead>
								<tr>
									<th>Status</th>
									<th>Member(alias)</th>
									<th>Pairing Points</th>
									<th>Contact No.</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody v-if="members.length > 0">
								<tr v-for="(member, index) in members" :key="index">
									<td>
										<label class="ui orange label" v-if="member.active == 1">ACTIVE</label>
										<label class="ui orange label" v-if="member.active == 0">INACTIVE</label>
									</td>
									<td>{{ member.fullname }} ({{ member.alias_name }})</td>
									<td>{{ member.points }}</td>
									<td>{{ member.contact_no }}</td>
									<td>
										<a class="ui icon fluid button mini" :href="goToEdit(member.user_id)">
										View
										</a>
									</td>
								</tr>
							</tbody>
							<tbody v-if="members.length < 1">
								<tr>
									<td colspan="6">No members found...</td>
								</tr>
							</tbody>
						</table>
						<pagination :data="members_data" v-bind:showDisabled="true" icon="chevron" v-on:change-page="getMembers" size="tiny" :limit="limit"></pagination>
					</div>
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
		data: () => ({
			members_data: {},
			members: [],
			active_member: 0,
			inactive_member: 0,
			total_member: 0,
			add_member_link: '',
			search_input: '',
			search_by: '',
			filter_by_status: '',
			search_loading: false,
			show_loading: false,
			limit: 3
		}),
		watch: {
			filter_by_status(val) {
				this.getMembers(1);
			}
		},
		methods: {
			updateStatus(status) {
				this.filter_by_status = status;
			},

			getMembers(page) {
				this.show_loading = true;
				this.search_loading = true;
				if (typeof page === 'undefined') {
	                page = 1;
	            }

				axios.get('/tbcgoldadmin/api/members', {
					params: {
						page: page,
						search_input: this.search_input,
						filter_by_status: this.filter_by_status,
						search_by: this.search_by,
					}
				}).then((resp) => {
					console.log(resp);
					this.members_data = resp.data.members;
					this.members = resp.data.members.data;
					this.show_loading = false;
					this.search_loading = false;
				}).catch((err) => {
					console.log(err);
				})
			},

			goToEdit(id) {
				return '/tbcgoldadmin/member/edit/' + id;
			},

			getTransactionsStat() {
				axios.get('/tbcgoldadmin/api/members/stats').then((resp) => {
					console.log(resp);
					this.total_member = resp.data.total;
					this.active_member = resp.data.active;
					this.inactive_member = resp.data.inactive;
				}).catch((err) => {
					console.log(err);
				});
			},
		},
		created () {
			this.getMembers();
			this.getTransactionsStat();
			this.add_member_link = '/tbcgoldadmin/member/add';
		}
	}
</script>