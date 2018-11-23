<template>
	<div>
		<div class="ui divider"></div>
		
			<div class="ui grid container">
				<div class="row">
					<div class="sixteen wide column">
						<table class="ui celled table">
							<thead>
								<tr>
									<th colspan="2">
										<div class="ui search" :class="{ loading: showLoading }">
											<label>Enter TBC Amount: </label>
											<div class="ui icon input form">
												
												<input class="text" type="text" name="tbc_amount" v-model="tbc_amount" placeholder="Press enter to get result." v-validate="'numeric'" @keyup.enter="getTbcRates">
												<i class="search icon"></i>
												
											</div>
										</div>
									</th>
								</tr>
							</thead>
							<thead>
								<tr>
									<th>Currency</th>
									<th>Value</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>PHP</td>
									<td>{{ tbc_php_value }}</td>
								</tr>
								<tr>
									<td>USD</td>
									<td>{{ tbc_usd_value }}</td>
								</tr>
								<tr>
									<td>BTC</td>
									<td>{{ tbc_btc_value }}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		
</div>
</template>
<script>
	export default {
		data: () => ({
			tbc_btc_value: 0,
			tbc_usd_value: 0,
			tbc_php_value: 0,
			tbc_amount: 0,
			showLoading: false
		}),
		created() {
			this.getTbcRates();
		},
		methods: {
			getTbcRates() {
				
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.showLoading = true;
						axios.get('/tbc-rates').then((resp) => {
							console.log(resp);
							this.tbc_btc_value = parseFloat(resp.data.result.tbc_btc_value);
							this.tbc_usd_value = parseFloat(resp.data.result.tbc_usd_value);
							this.tbc_php_value = parseFloat(resp.data.result.tbc_php_value);

							let amount = this.tbc_amount;

							if (amount > 0) {
								this.tbc_btc_value *= amount;
								this.tbc_usd_value *= amount;
								this.tbc_php_value *= amount;
							}
							

							this.showLoading = false;

						}).catch((err) => {
							console.log(err);
						});
					}
				})
				
			},
		}
	}
</script>