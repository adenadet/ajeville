<template>
<form>
    <alert-error :form="shiftTypeForm"></alert-error> 
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" required class="form-control" id="name" name="name" v-model="shiftTypeForm.name">
                <has-error :form="shiftTypeForm" field="name"></has-error> 
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Start Time</label>
                <input type="time" required class="form-control" id="start_time" name="start_time" v-model="shiftTypeForm.start_time">
                <has-error :form="shiftTypeForm" field="start_time"></has-error> 
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>End Time</label>
                <input type="time" required class="form-control" id="end_time" name="end_time" v-model="shiftTypeForm.end_time">
                <has-error :form="shiftTypeForm" field="end_time"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Status *</label>
                <select type="text" required class="form-control" id="status" name="status" v-model="shiftTypeForm.status">
                    <option value=''>--Select Status--</option>
                    <option value="0">Pending</option>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
                <has-error :form="shiftTypeForm" field="status"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Description</label>
                <textarea rows="5" class="form-control" id="description" name="description" v-model="shiftTypeForm.description">
                </textarea>
                <has-error :form="shiftTypeForm" field="description"></has-error> 
            </div>
        </div>
    </div>
    <button class="btn btn-sm btn-primary" v-html="editMode ? 'Update': 'Create'" @click.prevent="editMode ? updateShiftType() : createShiftType()"></button>
</form>
</template>
<script>
export default {
    data(){
        return  {
            shiftTypeForm: new Form({
                id: '',
                name: '',
                description: '',
                start_time: '',
                end_time: '',
                status: '',
            }),
        }
    },
    mounted() {
        const date =new Date().toJSON().slice(0, 10);
        this.today = date;
        Fire.$on('shiftTypeDataFill', shift_type =>{
            if (shift_type != null){this.shiftTypeForm.fill(shift_type);}
            else{this.shiftTypeForm.reset();}
        });
        Fire.$on('AfterCreation', ()=>{
            //axios.get("api/profile").then(({ data }) => (this.ApplicantData.fill(data)));
        });
    },
    methods:{
        createShiftType(){
            this.$Progress.start();
            this.shiftTypeForm.post('/api/emr/domiciliary/shift_types')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshShiftTypes', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Shift Type details has been created',
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
        updateShiftType(){
            this.$Progress.start();
            this.shiftTypeForm.put('/api/emr/domiciliary/shift_types/'+this.shiftTypeForm.id)
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshShiftTypes', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Shift Type details has been updated',
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
        editMode: Boolean,
    }
}
</script>