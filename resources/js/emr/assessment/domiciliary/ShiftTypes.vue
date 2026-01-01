<template>
<section>
    <div class="modal fade" id="shiftTypeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-show="editMode">Edit Shift Type: {{shift_type.name}}</h4>
                    <h4 class="modal-title" v-show="!editMode">New Shift Type</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeShiftType()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <DomFormShiftType :editMode="editMode" ></DomFormShiftType>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-calendar mr-1"></i>Shift Types</h3>
            <div class="card-tools"><button type="button" class="btn btn-sm btn-primary" @click="addShiftType">Add New</button></div>    
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(shift_type, index) in shift_types.data" :key="shift_type.id">
                    
                        <td>{{index | addOne}}</td>
                        <td>{{shift_type.name}}</td>
                        <td>{{shift_type.description}}</td>
                        <td>{{shift_type.start_time}}</td>
                        <td>{{shift_type.end_time}}</td>
                        <td v-html="shift_type.status == 0 ? 'Pending': (shift_type.status == 1 ? 'Active': 'Inactive')"></td>
                        <td><div class="btn-group"><button class="btn btn-sm btn-primary" @click.prevent="editShiftType(shift_type)"><i class="fa fa-edit"></i></button><button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <pagination :data="shift_types" @pagination-change-page="getShiftTypes">
                <span slot="prev-nav">&lt; Previous </span>
                <span slot="next-nav">Next &gt;</span>
            </pagination>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            editMode: false,
            shift_types: {},
            shift_type:{},
        }
    },
    mounted() {
        this.getInitials();
        Fire.$on('refreshShiftTypes', response=>{
            this.refresh(response);
            this.closeShiftType();
        });
        const date =new Date().toJSON().slice(0, 10);
        this.today = date;
    },
    methods:{
        addShiftType(){
            this.$Progress.start();
            this.editMode = false;
            //this.shift_type = shift_type;
            Fire.$emit('shiftTypeDataFill', {});
            $('#shiftTypeModal').modal('show');
            this.$Progress.finish();
        },
        closeShiftType(){
            $('#shiftTypeModal').modal('hide');
        },
        editShiftType(shift_type){
            this.$Progress.start();
            this.editMode = true;
            this.shift_type = shift_type;
            Fire.$emit('shiftTypeDataFill', shift_type);
            $('#shiftTypeModal').modal('show');
            this.$Progress.finish();
        },
        updateApplicantData(){
            console.log("Tested");
            this.$Progress.start();
            this.ApplicantData.put('/api/hims/patients/'+this.ApplicantData.id)
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshAppointment', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been updated',
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
        getInitials(){
            this.$Progress.start();
            axios.get('/api/emr/domiciliary/shift_types').then(response =>{
                this.refresh(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Courses were loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Courses were not loaded successfully',
                })
            });
        },
        getShiftTypes(page=1){
            axios.get('/api/emr/domiciliary/shift_types?page='+page)
            .then(response=>{this.refresh(response);});
        },
        refresh(response){
            this.shift_types = response.data.shift_types;
        }
        
    },
    props:{
    }
}
</script>