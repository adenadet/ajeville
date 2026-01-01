<template>
<section class="overlay-wrapper p-0 m-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateServiceType() : createServiceType()">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" required class="form-control" id="name" name="name" placeholder="Name *" v-model="serviceTypeData.name" :class="{'is-invalid' : serviceTypeData.errors.has('name') }">
                    <has-error :form="serviceTypeData" field="name"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Queueable</label>
                    <select required class="form-control" id="queueable" name="queueable" placeholder="Select HOD *" v-model="serviceTypeData.queueable" :class="{'is-invalid' : serviceTypeData.errors.has('queueable') }">
                        <option value="">--Select Status--</option>
                        <option value=1>Yes</option>
                        <option value=0>No</option>                     
                    </select>
                    <has-error :form="serviceTypeData" field="queueable"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Status</label>
                    <select required class="form-control" id="status" name="status" placeholder="Phone *" v-model="serviceTypeData.status" :class="{'is-invalid' : serviceTypeData.errors.has('status') }">
                        <option value="">--Select Status--</option>
                        <option value=1>Active</option>
                        <option value=0>Inactive</option>
                    </select>
                    <has-error :form="serviceTypeData" field="status"></has-error>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" id="description" name="description" placeholder="Description" v-model:content="serviceTypeData.description" :class="{'is-invalid' : serviceTypeData.errors.has('description') }"/>
                    <has-error :form="serviceTypeData" field="description"></has-error>
                </div>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            serviceTypeData: new Form({
                description: '',
                id: '',
                name: '', 
                queueable: '',
                status: '',
            }),
            employees: [],
            loading: false,
            users: [],
        }
    },
    emits: ['reloadServiceTypeForm'],
    mounted() {
        //this.getAllInitials();
    },
    methods:{
        createServiceType(){
            this.loading = true;
            this.serviceTypeData.post('/api/operations/service_types')
            .then(response =>{
                this.loading = false;
                this.$emit('reloadServiceTypeForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Branch has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
                this.loading = false;
            });  
        },
        /*
        getAllInitials(){
            this.loading = true;
            axios.get('/api/operations/service_types/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'BranchPriceList Form not loaded successfully',
                })
            });
        },*/
        refreshPage(response){
            this.employees = response.data.employees;
        },
        updateServiceType(){
            this.loading = true;
            this.serviceTypeData.put('/api/operations/service_types/'+this.serviceTypeData.id)
            .then(response =>{
                this.$emit('reloadServiceTypeForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The BranchPriceList has been updated',
                    showConfirmButton: false,
                    timer: 1500
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
            this.loading = false;                 
        },
    },
    props:{
        service_type: Object,
        editMode: Boolean,        
    },
    watch:{
        service_type(){
            this.serviceTypeData.fill(this.service_type);
        }
    }
}
</script>