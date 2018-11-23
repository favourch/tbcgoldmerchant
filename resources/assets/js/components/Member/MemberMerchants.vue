<template>
	<div>
		<div class="ui inverted dimmer" :class="{ active: showLoading }">
			<div class="ui text loader">Loading</div>
		</div>
		
		<div class="ui grid container">
			<div class="row" v-if="merchants.length > 0">
				<div class="sixteen wide column">
					<h3>Our Merchants Partners</h3>
					<div class="ui divider"></div>
				</div>
				<div class="sixteen wide column">
					<div class="ui items">
						<div class="item" v-for="(merchant, index) in merchants" :key="index">
							<div class="ui small rounded bordered image">
								<a :href="merchant.photo" data-fancybox="gallery" :data-caption="merchant.name">
									<img :src="merchant.photo" :alt="merchant.photo_alt" />
								</a>
							</div>
							<div class="content">
								<a class="header">{{ merchant.name }}</a>
								<!-- <div class="meta">
									<span></span>
								</div> -->
								<div class="description">
									<p>{{ merchant.address }}</p>
								</div>
								<div class="extra">
									<div class="ui label">
										<i class="phone square icon"></i> {{ merchant.contact_no }}
									</div>
	        						<div class="ui label">
	        							<i class="envelope square icon"></i> {{ merchant.email }}
	        						</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row" v-if="merchants.length < 1">
				<div class="sixteen wide center aligned column">
					<h3>No merchants yet...</h3>
				</div>
			</div>
			<div class="row">
				<div class="three wide column"></div>
				<div class="ten wide center aligned column">
					<pagination :data="merchantsData" v-bind:showDisabled="true" icon="chevron" v-on:change-page="getMerchants" size="tiny"></pagination>
				</div>
			</div>
		</div>
		
	</div>
</template>
<script>
	import pagination from 'laravel-vue-semantic-ui-pagination';
	export default {	
		name: 'member-merchants',
		components: {
			pagination
		},
		data: () => ({
			merchants: [],
			merchantsData: {},
			showLoading: true,
		}),
		created() {
			this.getMerchants();
		},
		methods: {
			getMerchants(page) {
				if (typeof page === 'undefined') {
					page = 1;
				}

				this.showLoading = true;

				axios.get('/member/api/merchants', {
					params: {
						page: page
					}
				}).then((resp) => {
					console.log(resp);
					this.merchants = resp.data.merchants.data;
					this.merchantsData = resp.data.merchants;
					this.showLoading = false;
				}).catch((err) => {
					console.log(err);
				});
			},
		}
	}
</script>