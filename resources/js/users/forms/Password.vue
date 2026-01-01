<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form id="password_form" @submit.prevent="changePassword">
        <div class="row">
            <div class="col-md-9 col-sm-12">
                <div class="form-group">
                    <label>Current Password*</label>
                    <input type="password" class="form-control" id="opw" name="opw" placeholder="Enter Current Password" v-model="pwForm.opw" required>
                </div>
            </div>
            <div class="col-md-9 col-sm-12">
                <div class="form-group">
                    <label>New Password*</label>
                    <input type="password" class="form-control" id="npw" name="npw" placeholder="New Password"  v-model="pwForm.npw" minlength="8" required>
                </div>
            </div>
            <div class="col-md-9 col-sm-12">
                <div class="form-group">
                    <label>Confirm New Password*</label>
                    <input type="password" class="form-control" id="cpw" name="cpw" placeholder="Re-enter New Password"  v-model="pwForm.cpw" required>
                </div>
            </div>
        </div>
        <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
    </form>
</section>
</template>
<script>
import Form from 'vform';
    export default {
        data(){
            return {
                loading: false,
                pwForm: new Form({
                    opw: '',
                    npw: '',
                    cpw: '',
                }),
            }
        },
        methods:{
            changePassword(){
                if (this.pwForm.npw != this.pwForm.cpw){ 
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Your new passwords do not match',
                        footer: 'Please try again later!'
                        }); 
                    }
                else{
                this.loading = true;
                this.pwForm.post('/api/hrms/password')
                .then(response =>{
                    this.$swal.fire({
                        icon: response.data.status,
                        title: 'Success',
                        text: response.data.message,
                        footer: 'Please try again later!'
                        });    
                    })
                .catch(()=>{
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        footer: 'Please try again later!'
                        });
                    });
                }
                this.loading = false;    
            },          
        },
        mounted() {},
        props:{},
    }
</script>