<template>
<form>
    <alert-error :form="batchAssignForm"></alert-error> 
    <div class="row">
        <div class="col-sm-8">
            <div class="form-group">
                <label>Shift</label>
                <input disabled type="text" class="form-control" :value="request.patient != null ? request.shift_type.name+' | '+request.patient.last_name+', '+request.patient.first_name+' '+request.patient.middle_name : 'Loading Patient Data'" />
                <input type="hidden" name="batch_id" id="batch_id" v-model="batchAssignForm.batch_id" />
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Date</label>
                <input disabled type="date" class="form-control" :value="request != null ? request.raw_date : 'Loading Patient Data'" />
                <input type="hidden" name="date" id="date" v-model="batchAssignForm.date" />
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Staff *</label>
                <select type="text" required class="form-control" id="staff_id" name="staff_id" v-model="batchAssignForm.staff_id">
                    <option value="">--Select Staff--</option>
                    <option v-for="staff in staffs" :value="staff.id">{{staff.unique_id}} | {{staff.user != null ? staff.user.first_name+' '+staff.user.last_name : 'Old Staff'}}</option>
                </select>
                <has-error :form="batchAssignForm" field="staff_id"></has-error> 
            </div>
        </div>
    </div>
    <button @click.prevent="editMode ? editBatch() :assignBatch()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
</form>
</template>
<script>
export default {
    data(){
        return  {
            batchAssignForm: new Form({
                batch_id:"",
                staff_id: "",
                date: '',
            }),
            request: {},
        }
    },
    mounted() {
        Fire.$on('assignBatchDataFill', request =>{
            this.request = request;
            if (request != null){
                this.batchAssignForm.batch_id = request.batched_id
                this.batchAssignForm.date = request.raw_date;
                this.batchAssignForm.staff_id = request.staff_id;
            }
            else{this.batchAssignForm.reset();}
        });
    },
    methods:{
        assignBatch(){
            this.$Progress.start();
            this.batchAssignForm.post('/api/emr/domiciliary/batch_assigns')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('shiftResponse', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Shift has been assigned successfully',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });  
        },
        editBatch(){
            this.$Progress.start();
            this.batchAssignForm.put('/api/emr/domiciliary/batch_assigns/'+this.batchAssignForm.id)
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('shiftResponse', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Shift has been updated successfully',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });  
        },
    },
    props:{
        staffs: Array,
    }
}
</script>