<template>
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Referral Ins</h3>
                        <div class="card-tools">
                            <div class="input-group" style="width: 500px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default" @click="getInitials"><i class="fas fa-search"></i></button>
                                    <select v-model="status" class="form-control ml-3" @change="getInitials">
                                        <option value="all">All</option>
                                        <option value="pending">Sample Pending</option>
                                        <option value="processing">Processing</option>
                                        <option value="reported">Reported</option>
                                        <option value="submitted">Submitted</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height:600px;">
                        <!--EMRRadiologyDetailReferralList :refferals.sync="referrals.data" source="in"/-->
                    </div>
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="referrals.per_page != null ? referrals.per_page : 52" :records="referrals.total != null ? referrals.total : 550" ></pagination>
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
            current_page: 1,
            editMode: true,
            referrals: {data: []},
            search: '',
            status: 'all',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addRequest(){
            this.editMode = false;
            this.request = {};
            //Fire.$emit('AppointmentDataFill', {});
            $('#requestFormModal').modal('show');
        },
        getInitials(page=1) {
            axios.get('/api/emr/radiology/refferals?page='+page+'&search='+this.search+'&status='+this.status+'&type=in')
            .then(response => {
                this.refreshList(response)
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Referrals did not load successfully',
                })
            });
        },
        refreshList(response) {
            this.referrals = response.data.referrals;
        }
    },
    props: {}
}
</script>