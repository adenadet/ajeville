<template>
<section class="row p-0 overlay-wrapper">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Responsive Hover Table</h3>

                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="contribution in contributions">
                        <td>{{ excelDate(contribution.date) }}</td>
                        <td>John Doe</td>
                        <td>11-7-2014</td>
                        <td><span class="tag tag-success">Approved</span></td>
                        <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            editMode: false,
            insurance: {},
            insurances: [], 
            loading: false,
        }
    },
    mounted() {},
    methods:{
        addInsurance(){
            this.loading = true;
            this.editMode = false;
            this.insurance = {};
            //Fire.$emit('InsuranceDataFill', {});
            $('#insuranceModal').modal('show');
            this.loading =false;
        },
        closeModal(){
            $('#allergyModal').modal('hide');
            $('#contactModal').modal('hide');
            $('#contactModal').modal('hide');
        },
        getInitials(id){
            this.loading = true;
            axios.get('/api/cooperative/saving_c/'+id+'/insurances').then(response =>{
                this.loading = false;
                this.reloadPatient(response);
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Profile not loaded successfully',});
            });
        },
        reloadPatient(response){
            this.insurances = response.data.insurances; 
        },
    },
    props:{
        saving_account_id: String,
    },
    watch:{
        saving_account_id(){
            this.getInitials(this.saving_account_id);
        }
    }

}
</script>