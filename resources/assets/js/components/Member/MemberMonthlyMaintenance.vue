<template>
	<div>
		<div class="ui basic segment" :class="{ loading: show_loading }">
			<div class="ui card">
				<div class="content">
					<div class="header">Monthly Maintenance</div>
				</div>
				<div class="content center aligned">
					<i class="calendar alternate outline massive green circular icon"></i>
					<!-- <div class="ui small feed">
						<div class="event">
							<div class="content">
								<div class="summary">
									<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloremque ad quisquam adipisci dolore porro sed laborum hic, architecto praesentium, ut, error aliquid. Eveniet, rerum doloremque deleniti corporis quasi officiis eum?</p>
								</div>
							</div>
						</div>
					</div> -->
				</div>
				<div class="extra content">
					<button type="button" class="ui violet fluid button" @click.prevent="openModal()">Monthly Maintenance</button>
				</div>
			</div>
		</div>

		<div class="ui maintenance-modal small modal">
			<div class="header">
				Pay Monthly Maintenance
			</div>
			<div class="image content">
				<div class="description">
					<div class="ui form">
						<div class="field">
							<table class="ui celled table">
								<tbody>
									<tr>
										<td style="width: 30%">Maintenance Cost (USD):</td>
										<td>{{ maintenance_cost | currency }}</td>
									</tr>
									<tr>
										<td style="width: 30%">Maintenance Cost (BTC):</td>
										<td>{{ btc_value }}</td>
									</tr>
								</tbody>
							</table>
							<h4>Send amount on this BTC Account</h4>
							<div class="ui action fluid input">
								<input type="text" :value="admin_btc_wallet" readonly="" />
								<button class="ui teal right labeled icon button" @click.prevent="copyText">
									<i class="copy icon"></i>
									Copy
								</button>
							</div>
						</div>

						<div class="field" :class="{ error: errors.has('transaction_hash') }">
							<label>Enter the transaction hash</label>
							<input 
								type="text"
								name="transaction_hash" 
								id="transaction_hash" 
								v-model="transaction_hash" 
								v-validate="'required|transaction_hash_unique'" 
							>
							<div class="ui pointing red basic label" v-if="errors.has('transaction_hash')">
								{{ errors.first('transaction_hash') }}
							</div>
						</div>
					</div>
					
				</div>
			</div>
			<div class="actions">
				<div class="ui button" @click.prevent="closeModal">Cancel</div>
				<div class="ui violet button" :class="{ loading: show_loading }" @click.prevent="activateAccount()">Pay Now</div>
			</div>
		</div>

	</div>
</template>
<script>
	import { Validator } from 'vee-validate';
	export default {
		props: {
			admin_btc_wallet: {
				type: String,
				required: true
			},
			maintenance_cost: {
				type: Number,
				required: true
			},
			btc_value: {
				type: Number,
				required: true
			},
		},
		data: () => ({
			transaction_hash: null,
			show_loading: false
		}),
		created() {
			const isTransactionHashUnique = (value) => {
				this.checkingTransactionHash = true;
			  return axios.post('/member/api/validate-transaction-hash', { transaction_hash: value }).then((resp) => {
			    // Notice that we return an object containing both a valid property and a data property.
			    this.checkingTransactionHash = false;
			    return {
			      valid: resp.data.valid,
			      data: {
			        message: resp.data.message
			      }
			    };
			  });
			}

			// The messages getter may also accept a third parameter that includes the data we returned earlier.
			Validator.extend('transaction_hash_unique', {
			  validate: isTransactionHashUnique,
			  getMessage: (field, params, data) => {
			    return data.message;
			  }
			});
		},
		methods: {
			copyText() {
		        this.$copyText(this.admin_btc_wallet).then((resp) => {
		        	this.$notify({
			          title: 'Copied',
			          message: '',
			          type: 'success'
			        });
		        }).catch((err) => {
		        	console.log(err);
		        });
			},

			closeModal() {
				$('.modal').modal('hide');
			},

			openModal() {
				$('.maintenance-modal').modal('show');
			},

			activateAccount() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						this.show_loading = true;
						axios.post('/member/api/monthly-maintenance/pay', {
							transaction_hash: this.transaction_hash,
							maintenance_cost: this.maintenance_cost,
							admin_btc_wallet: this.admin_btc_wallet,
							btc_value: this.btc_value,
						}).then((resp) => {

							if (resp.data.status == 'ok') {
								this.closeModal();
		
								this.$notify({
						        title: 'Success!',
						          message: 'Monthly maintenance transaction sent.',
						          type: 'success'
						        });
						        setTimeout(() => {
						        	window.location.href = '/member/monthly-maintenance';
						        }, 2000);
							} else if (resp.data.status == 'fail') {
								this.closeModal();
								this.$notify({
						        title: 'Error',
						          message: 'Something went wron!',
						          type: 'error'
						        });
						    
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