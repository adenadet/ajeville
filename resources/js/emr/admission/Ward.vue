<template>
    <section class="overlay-wrapper p-0">
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
                        <EMRAdmissionFormRoom :room.sync="room" :editMode="true" @refreshRoomForm="getAllInitials" />
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="wardFormModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Room Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <EMRAdmissionFormWard :ward.sync="ward" :editMode="editMode" @refreshWardList="getAllInitials" />
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ward Details</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-primary mr-1"><i class="fas fa-edit"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <EMRAdmissionDetailWard :ward.sync="ward" />
                        </div>
                        <div class="col-md-8">
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="room-tab" data-toggle="pill" href="#room" role="tab" aria-controls="room" aria-selected="true">Rooms</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="occupants-tab" data-toggle="pill" href="#occupants" role="tab" aria-controls="occupants" aria-selected="false">Occupants</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="messages-tab" data-toggle="pill" href="#messages" role="tab" aria-controls="messages" aria-selected="false">Messages</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="settings-tab" data-toggle="pill" href="#settings" role="tab" aria-controls="settings" aria-selected="false">Settings</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-room" role="tabpanel" aria-labelledby="custom-tabs-four-room-tab">
                                            <EMRAdmissionDetailRoomList :rooms.sync="rooms" :ward.sync="ward" @refreshRoomList="getAllInitials" />
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-occupants" role="tabpanel" aria-labelledby="custom-tabs-four-occupants-tab">
                                            Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam. 
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-messages" role="tabpanel" aria-labelledby="custom-tabs-four-messages-tab">
                                            Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna. 
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-settings" role="tabpanel" aria-labelledby="custom-tabs-four-settings-tab">
                                            Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis. 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: false,
            query: '',
            room: {},
            rooms: [],
            type: 1,
            ward: {},
            wards: {total: 0, data: []},
        }
    },
    methods: {
        addWard(){
            this.editMode = false;
            this.loading = true;
            this.ward = {};
            $('#wardFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#roomFormModal').modal('hide');
            $('#wardFormModal').modal('hide');
        },
        deactivateWard(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    axios.delete('/api/emr/admissions/wards/'+id)
                    .then(() => {
                        this.$swal.fire({ icon: 'success', title: 'The Ward has been cancelled', showConfirmButton: false, timer: 1500 });
                        this.getAllInitials();
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
                    })
                }
            })
        },
        getAllInitials(){
            this.loading = true;
            this.closeModals();
            axios.get('/api/emr/admissions/wards/'+this.$route.params.id)
            .then(res => {
                this.rooms = res.data.rooms;
                this.ward = res.data.ward;
            })
            .finally(() => {
                this.loading = false
            })
        },
        updateWard(ward){
            this.editMode = true;
            this.loading = true;
            this.ward = ward;
            $('#wardFormModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>