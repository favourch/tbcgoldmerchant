<template>
	<div>
		<div v-if="!showLoading">
			<div class="ui grid container">
				<div class="row">
		
					<div class="four wide column">
						<div class="ui raised segment form">
							
							<div class="field">
								<img :src="preview_image" class="ui rounded fluid image" />
								<br />
								<input 
									type="file" 
									ref="photo" 
									@change="chooseFile"
								/>
							</div>
							
						</div>
					</div>
					<div class="twelve wide column">
						<div class="ui raised segment form">
							
							<div class="field">
								<label>Merchant Name</label>
								<input 
									type="text" 
									name="merchant_name" 
									v-model="merchant_name" 
									v-validate="'required'" 
									data-vv-as="Merchant Name"
								/>
							</div>
							<div class="field">
								<label>Address</label>
								<textarea 
									v-model="address"
								>
									
								</textarea>
							</div>
							
							<div class="field">
								<label>Contact No.</label>
								<input 
									type="text" 
									v-model="contact_no" 
								/>
							</div>
							<div class="field">
								<label>Email</label>
								<input 
									type="text" 
									v-model="email" 
									name="email"
									v-validate="'email'" 
								/>
							</div>
							<div class="field">
								<button 
									class="ui positive button" 
									type="button" 
									@click.prevent="saveMerchant"
								>
									Save
								</button>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	export default {
		name: 'admin-merchant-merchant-add',
		data: () => ({
			showLoading: false,
			preview_image: '',
			merchant_name: '',
			contact_no: '',
			address: '',
			email: '',
			photo: null,
		}),
		created() {
			this.setDefaultImage();
		},
		methods: {

			setDefaultImage() {
				this.preview_image = this.$baseUrl + '/images/no-image.jpg';
			},

			chooseFile(event) {
				let files = event.target.files[0];

				var reader = new FileReader();

				reader.onload = (e) => {
					this.preview_image = e.target.result;
				}

				reader.readAsDataURL(files);

				this.photo = files;
			},

			saveMerchant() {
				this.$validator.validateAll().then((result) => {
					if (result) {
						let formData = new FormData;

						formData.append('name', this.merchant_name);
						formData.append('contact_no', this.contact_no);
						formData.append('address', this.address);
						formData.append('email', this.email);
						formData.append('photo', this.photo);

						axios.post('/tbcgoldadmin/api/merchant/add', formData).then((resp) => {
							console.log(resp);
							this.resetForm();
							this.$notify({
					          title: 'Saved Successfully',
					          message: '',
					          type: 'success'
					        });
						}).catch((err) => {
							console.log(err);
						});
					} else {
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').scrollIntoView({behavior: "smooth", block: "end"});
						this.$el.querySelector('[name="' + this.$validator.errors.items[0].field + '"]').focus();
					}
				});
				
			},

			resetForm() {
				this.merchant_name = '';
				this.contact_no = '';
				this.address = '';
				this.photo = null;
				this.email = '';
				this.setDefaultImage();
			}
		}
	}
</script>