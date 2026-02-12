<template>
<div class="card card-success card-outline mt-3">
    <div class="card-body box-profile">
        <h3 class="profile-username text-center">{{ bed?.name || 'Loading...' }}</h3>
        <p class="text-muted text-center">{{ bed?.status  }}</p>
        <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item"><b>Assignment Status:</b> <a class="float-right">{{ bed.assignment_status ? 'Occupied' : 'Free' }}</a></li>
        </ul>
        <button class="btn btn-warning btn-block" @click="releaseBed(bed)"><b>Release Bed</b></button>
    </div>
</div>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: false,
        }
    },
    emits:['refreshBedDetail'],
    methods: {
        releaseBed(bed){
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
                    axios.delete('/api/emr/admission/beds/'+bed.id+'/release')
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
    },
    mounted() {
        
    },
    props:{
        bed: Object,
    }
}
</script>