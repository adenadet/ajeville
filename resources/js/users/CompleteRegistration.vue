<template>
    <section class="overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-5">
                Thank you for choosing to join our platform! We're excited to have you as part of our community and can't wait for you to explore all we have to offer. Welcome aboard!
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title">Complete Your Registration</h3>
                    </div>
                    <div class="card-body">
                        <UmsFormBioData :user.sync="user" :editMode="editMode" source="registration" @reloadUser="relogin"/>
                    </div>
                </div>
                
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data(){
        return  {
            areas:[],  
            branches:[],  
            departments:[], 
            editMode: true, 
            loading: false,
            nok:{},
            states:[],
            nations: [],  
            user:{}, 
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/registration/'+this.$route.params.id).then(response =>{
                this.reloadProfile(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Profile loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Profile not loaded successfully',
                })
            });
        },
        getProfilePic(){
            let  photo = (this.form.image.length >= 150) ? this.form.image : "./"+this.form.image;
            return photo;
        },
        reloadProfile(response){
            this.user = response.data.user;
        },
        relogin(response){
            this.$router.push('/login');
        },
    }
}
</script>