<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="wardFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Large Modal</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRAdmissionFormWard :ward.sync="ward" :editMode="editMode" @refreshWardForm="$emit('refreshWardList')" />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>No of Rooms</th>
                <th>No of Beds</th>
                <th>Occupied Beds</th>
                <th>Branch</th>
                <th>Status</th>
                <th><button class="btn btn-primary btn-sm" @click="addWard" type="button"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="wards.length > 0">
            <tr v-for="(ward, index) in wards" :key="ward.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ ward.name }}</td>
                <td>{{ ward.rooms.length }}</td>
                <td>{{ ward.beds.length }}</td>
                <td>{{ ward.occupied_beds }}</td>
                <td>{{ ward.branch != null ? ward.branch.name: 'No Branch Attached' }}</td>
                <td>
                    <span v-if="ward.status == 1" class="badge badge-success">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <button class="nav-link btn btn-default btn-xs" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link class="dropdown-item btn btn-block btn-sm" :to="'/emr/admission/wards/'+ward.id"><i class="fa fa-eye mr-1 text-primary"></i> View Ward</router-link>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateWard(ward)"><i class="fa fa-edit mr-1 text-warning"></i> Update Ward</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="deactivateWard(ward)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Ward</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="8" class="text-center">No wards found.</td>
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
            ward: {},
        }
    },
    emits:['refreshWardList'],
    methods: {
        addWard(){
            this.loading = true;
            this.editMode = false;
            this.ward = {};
            $('#wardFormModal').modal('show');
            this.loading = false;
        },
        deactivateWard(ward){
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
                    axios.delete('/api/emr/admission/wards/'+ward.id)
                    .then((response)=>{
                        this.$swal.fire(
                            'Deactivated!',
                            'Room Type has been deactivated.',
                            'success'
                        );
                        this.$emit('refreshWardList');
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
        updateWard(ward){
            this.loading = true;
            this.editMode = true;
            this.ward = ward;
            $('#wardFormModal').modal('show');
            this.loading = false;
        },
        viewWard(ward){
            this.loading = true;
            this.ward = ward;
            $('#wardViewModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {
        
    },
    props:{
        wards: Array,
    }
}
</script>