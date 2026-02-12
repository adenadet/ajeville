<template>
<div class="modal fade" id="bedFormModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bed Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <EMRAdmissionFormBed :bed.sync="bed" :room.sync="room" :editMode="editMode" @refreshBedForm="refreshPage" />
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="roomFormModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Room Details</h4>
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
<div class="card card-primary card-outline">

    <div class="card-body box-profile">
        <h3 class="profile-username text-center">{{ room?.name || 'Loading...' }}</h3>
        <p class="text-muted text-center">{{ room.status ? 'Active' : 'Inactive'  }}</p>
        <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item"><b>Ward:</b> <a class="float-right">{{ room?.ward?.name }}</a></li>
            <li class="list-group-item"><b>Room Type:</b> <a class="float-right">{{ room?.room_type?.name }}</a></li>
            <li class="list-group-item"><b>Description:</b> <a class="float-right" v-html="room?.description"></a></li>
        </ul>
        <button class="btn btn-primary btn-block" @click="updateRoom()"><b>Update Room Details</b></button>
        <button class="btn btn-success btn-block" @click="addBed()"><b>Add New Bed</b></button>
    </div>
</div>
</template>
<script>
export default {
    data() {
        return {
            editMode: true,
            loading: false,
        }
    },
    emits:['refreshRoomDetail'],
    methods: {
        addBed(){
            this.loading = true;
            this.editMode = false;
            $('#bedFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#bedFormModal').modal('hide');
            $('#roomFormModal').modal('hide');
        },
        refreshPage(){
            this.closeModals();
            this.$emit('refreshRoomDetail');
        },
        updateRoom(){
            this.loading = true;
            this.editMode = true;
            $('#roomFormModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {
        
    },
    props:{
        room: Object,
    }
}
</script>