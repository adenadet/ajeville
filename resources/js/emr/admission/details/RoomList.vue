<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="roomFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Room Details </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRAdmissionFormRoom :room.sync="room" :editMode="editMode" @refreshRoomForm="refreshPage" />
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
                <th><button class="btn btn-primary btn-xs" @click="addRoom"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="rooms.length > 0">
            <tr v-for="(room, index) in rooms" :key="room.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ room.name }}</td>
                <td :title="room.description" v-html="readMore(room.description, 25, '...')"></td>
                <td>
                    <span v-if="room.status == 1" class="badge badge-success">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link class="dropdown-item btn btn-block btn-sm" :to="'/emr/admission/rooms/'+room.id"><i class="fa fa-eye mr-1 text-primary"></i> View Room </router-link>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateRoom(room)"><i class="fa fa-edit mr-1 text-warning"></i> Update Room </button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="deactivateRoom(room)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Room </button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5" class="text-center">No Room Found</td>
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
            room: {},
        }
    },
    emits:['refreshRoomList'],
    methods: {
        addRoom(){
            this.loading = true;
            this.editMode = false;
            this.room = {};
            $('#roomFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#roomFormModal').modal('show');
            
        },
        deactivateRoom(room){
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
                    axios.delete('/api/emr/admission/rooms/'+room.id)
                    .then((response)=>{
                        this.$swal.fire(
                            'Deactivated!',
                            'Room  has been deactivated.',
                            'success'
                        );
                        this.$emit('refreshRoomList');
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
        refreshPage(){
            this.closeModals();
            this.$emit('refreshRoomList');
        },
        updateRoom(room){
            this.loading = true;
            this.editMode = true;
            this.room = room;
            $('#roomFormModal').modal('show');
            this.loading = false;
        },
        viewRoom(room){
            this.loading = true;
            this.room = room;
            $('#roomViewModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {
        
    },
    props:{
        rooms: Array,
        ward: Object,
    }
}
</script>