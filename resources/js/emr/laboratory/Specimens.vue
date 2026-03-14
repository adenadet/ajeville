<template>
<section>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Specimens</h3>
                    <div class="card-tools">
                        <div class="input-group" style="width: 350px;">
                            <input type="text" v-model="query" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default mr-1" @click="getInitials"><i class="fas fa-search"></i></button>
                                <select class="form-control" v-model="status" @change="getInitials">
                                    <option value="pending">Pending</option>
                                    <option value="received">Received</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <EMRLaboratoryDetailSpecimenList :specimens="specimens.data" source="specimen" @refreshSpecimenList="getInitials"/>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="specimens.per_page != null ? specimens.per_page : 52" :records="specimens.total != null ? specimens.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRLaboratoryDetailSpecimenList from '@/emr/laboratory/details/SpecimenList.vue';
import EMRLaboratoryFormSpecimenAction from '@/emr/laboratory/forms/SpecimenAction.vue';
export default {
    components:{
        EMRLaboratoryFormSpecimenAction, EMRLaboratoryDetailSpecimenList
    },
    data() {
        return {
            current_page: 1,
            editMode: true,
            loading: false,
            query: '',
            specimen: {},
            specimens: {data:[], total:0,},
            status: 'pending',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addService(){
            this.loading = true;
            this.editMode = false;
            this.service = {};
            $('#serviceFormModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#serviceFormModal').modal('hide');
        },
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/specimens?page='+this.current_page+'&query='+this.query+'&status='+this.status)
            .then(response => {
                this.refreshQueue(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Specimens did not load successfully',
                })
            });
        },
        refreshQueue(response) {
            this.specimens = response.data.specimens;
        },
        updateService(service){
            this.loading = true;
            this.editMode = true;
            this.service = service;
            $('#specimenFormModal').modal('show');
            this.loading = false;
        }
    },
}
</script>