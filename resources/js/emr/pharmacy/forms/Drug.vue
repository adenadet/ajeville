<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? editDrug() : createDrug()">
        <alert-error :form="drugForm"></alert-error>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Drug Name*</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="drugForm.name" :class="{ 'is-invalid': drugForm.errors.has('name') }" :min="today" />
                    <has-error :form="drugForm" field="name"></has-error>
                </div>
            </div>  
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Interactions</label>
                    <select class="form-control" id="specific_drugs[]" name="specific_drugs[]" v-model="drugForm.specific_drugs"
                        :class="{ 'is-invalid': drugForm.errors.has('specific_drugs') }">
                        <option value=''>--Select Drug--</option>
                        <option v-for="item in items" :value='item.id'>{{ item.name }}</option>
                    </select>
                    <has-error :form="drugForm" field="specific_drugs"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>High Alert Medication</label>
                    <select class="form-control" id="ham" name="ham" v-model="drugForm.ham" :class="{ 'is-invalid': drugForm.errors.has('ham') }">
                        <option value="">--Select HAM Status--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <has-error :form="drugForm" field="ham"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="drugForm.status" :class="{ 'is-invalid': drugForm.errors.has('status') }">
                        <option value="">--Select Status--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <has-error :form="drugForm" field="start_date"></has-error>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor id="description" name="description" theme="snow" content-type="html" v-model:content="drugForm.description" :class="{ 'is-invalid': drugForm.errors.has('description') }"/>
                    <has-error :form="drugForm" field="description"></has-error>
                </div>
            </div>
        </div>
        <button class="btn btn-sm btn-primary" type="submit">Submit</button>
    </form>
</section>

</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            drugForm: new Form({
                id: '',
                name: '',
                description:'',
                ham: '',
                status: 1,
                interactions: [],
            }), 
            query: '',
            type: 'active',
        }
    },
    emits:['refreshDrugForm'],
    mounted() {},
    methods: {
        createDrug(){
            this.loading = true;
            this.drugForm.post('/api/emr/pharmacy/drugs')
            .then( () =>{
                this.$emit('refreshDrugForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Drug detail has been captured',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;    
            });
        },
        updateDrug(){
            this.loading = true;
            this.drugForm.put('/api/emr/pharmacy/drugs/'+this.drugForm.id)
            .then( () =>{
                this.$emit('refreshDrugForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Drug detail has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;    
            });
        },
    },
    props: {
        drug: Object,
    },
    watch:{
        drug(){
            this.drugForm.fill(this.drug);
        }
    }
}
</script>