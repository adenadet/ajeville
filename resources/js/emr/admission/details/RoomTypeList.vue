<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="roomTypeFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Room Type Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRAdmissionFormRoomType :room_type.sync="room_type" :editMode="editMode" @refreshRoomTypeForm="closeModals()" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="roomTypeViewModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Room Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRAdmissionDetailRoomType :room_type.sync="room_type" :editMode="editMode" @refreshRoomTypeForm="closeModals()" />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th><button class="btn btn-primary btn-xs" @click="addRoomType()"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="room_types.length > 0">
            <tr v-for="(room_type, index) in room_types" :key="room_type.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ room_type.name }}</td>
                <td class="text-small" :title="room_type.description" v-html="readMore(room_type.description, 25, '...')"></td>
                <td>
                    <span v-if="room_type.status == 1"class="badge badge-success">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewRoomType(room_type)"><i class="fa fa-eye mr-1 text-primary"></i> View Room Type</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateRoomType(room_type)"><i class="fa fa-edit mr-1 text-warning"></i> Update Room Type</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="deactivateRoomType(room_type)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Room Type</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5" class="text-center">No Room Types Found</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: false,
            room_type: {},
        }
    },
    emits:['refreshRoomTypeList'],
    methods: {
        addRoomType(){
            this.loading = true;
            this.editMode = false;
            this.room_type = {};
            $('#roomTypeFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            this.$emit('refreshRoomTypeList');
            $('#roomTypeFormModal').modal('hide');
            $('#roomTypeViewModal').modal('hide');            
        },
        deactivateRoomType(room_type){
            this.$swal.fire({
                title: 'Are you sure?',
                text: 'You are about to deactivate this room type!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Deactivate it!',
                cancelButtonText: 'Cancel'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    this.loading = true;
                    axios.delete('/api/emr/admission/room_types/'+room_type.id)
                    .then((response)=>{
                        this.$swal.fire(
                            'Deactivated!',
                            'Room Type has been deactivated.',
                            'success'
                        );
                        this.$emit('refreshRoomTypeList');
                        this.loading = false;
                    })
                    .catch((error)=>{
                        this.loading = false;
                        this.$swal.fire(
                            'Error!',
                            'An error occurred while deactivating room type.',
                            'error'
                        );
                    });
                }
            });
        },
        updateRoomType(room_type){
            this.loading = true;
            this.editMode = true;
            this.room_type = room_type;
            $('#roomTypeFormModal').modal('show');
            this.loading = false;
        },
        viewRoomType(room_type){
            this.loading = true;
            this.room_type = room_type;
            $('#roomTypeViewModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {
        
    },
    props:{
        room_types: Array,
    }
}
</script>