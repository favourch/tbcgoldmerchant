<template>
	<div>
		<div class="ui inverted dimmer" :class="{ active: showLoading }">
			<div class="ui text loader">Loading</div>
		</div>
		<div v-if="!showLoading">
			<div class="ui grid container">
				<div class="row">
					<div class="sixteen wide column">
						<table class="ui celled table">
							<thead>
								<tr>
									<th></th>
									<th>Merchant</th>
									<th>Address</th>
									<th>Contact #</th>
									<th>Email</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(merchant, index) in merchants" :key="index">
									<td>
										<a :href="merchant.photo" data-fancybox :data-caption="merchant.name">
											<img class="ui mini image" :src="merchant.photo" :alt="merchant.photo_alt" />
										</a>
									</td>
									<td>
										<strong>{{ merchant.name }}</strong>
									</td>
									<td>
										{{ merchant.address }}
									</td>
									<td>
										{{ merchant.contact_no }}
									</td>
									<td>
										{{ merchant.email }}
									</td>
									<td>
										<button 
											class="ui mini button" 
											type="button" 
											@click.prevent="viewMerchant(merchant.id)" 
										>
											View
										</button>
										<button 
											class="ui mini red button" 
											type="button" 
											@click.prevent="showDeleteMerchantModal(merchant.id)"
										>
											Delete
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="ui delete-merchant modal mini">
			<div class="header">
				Action: Delete Merchant
			</div>
			<div class="image content">
				<div class="description">
					Are you sure?
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="closeDeleteMerchantModal">Cancel</div>
				<div class="ui red button" @click.prevent="deleteMerchant">Delete</div>
			</div>
		</div>
	</div>
</template>
<script>
	export default {
		name: 'admin-merchant-merchants',
		data: () => ({
			merchants: [],
			merchantsData: {},
			showLoading: false,
			selectedMerchantId: null
		}),
		created() {
			this.getMerchants();
		},
		methods: {
			getMerchants() {
				axios.get('/tbcgoldadmin/api/merchants').then((resp) => {
					console.log(resp);
					this.merchants = resp.data.merchants.data;
					this.merchantsData = resp.data.merchants;
				}).catch((err) => {
					console.log(err);
				});
			},

			showDeleteMerchantModal(id) {
				this.selectedMerchantId = id;
				$('.ui.delete-merchant.modal').modal('show');
			},

			closeDeleteMerchantModal() {
				this.selectedMerchantId = null;
				$('.ui.delete-merchant.modal').modal('hide');
			},

			deleteMerchant() {
				axios.post('/tbcgoldadmin/api/merchant/delete', {
					merchant_id: this.selectedMerchantId
				}).then((resp) => {
					console.log(resp);
					this.$notify({
					title: 'Deleted Successfully',
					message: '',
					type: 'success'
					});
					this.getMerchants();
				}).catch((err) => {
					console.log(err);
				});
				$('.ui.delete-merchant.modal').modal('hide');
			},

			viewMerchant(id) {
				window.location.href = '/tbcgoldadmin/merchant/update/' + id;
			}

		}
	}
</script>