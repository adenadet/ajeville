<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? editDrug() : createDrug()">
        <alert-error :form="drugFormData"></alert-error>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Drug Name*</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="drugFormData.name" :class="{ 'is-invalid': drugFormData.errors.has('name') }" />
                    <has-error :form="drugFormData" field="name"></has-error>
                </div>
            </div>  
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="drugFormData.status" :class="{ 'is-invalid': drugFormData.errors.has('status') }">
                        <option value="">--Select Status--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <has-error :form="drugFormData" field="start_date"></has-error>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor id="description" name="description" theme="snow" content-type="html" v-model:content="drugFormData.description" :class="{ 'is-invalid': drugFormData.errors.has('description') }"/>
                    <has-error :form="drugFormData" field="description"></has-error>
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
            drugFormData: new Form({
                id: '',
                name: '',
                description:'',
                status: 1,
            }), 
            loading: false,
            query: '',
            type: 'active',
        }
    },
    emits:['refreshDrugForm'],
    mounted() {},
    methods: {
        createDrugForm(){
            this.loading = true;
            this.drugFormData.post('/api/emr/pharmacy/drug_forms')
            .then( () =>{
                this.$emit('refreshFormDrugForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Drug Form detail has been captured',
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
        updateDrugForm(){
            this.loading = true;
            this.drugFormData.put('/api/emr/pharmacy/drug_forms/'+this.drugFormData.id)
            .then( () =>{
                this.$emit('refreshFormDrugForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Drug Form detail has been updated',
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
        drug_form: Object,
    },
    watch:{
        drug_form(){
            this.drugFormData.fill(this.drug_form);
        }
    }
}
</script>