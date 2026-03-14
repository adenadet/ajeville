<template>
<section>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Request Templates</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" v-model="query" placeholder="Search">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default" ><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 600px;">
                    <table class="table table-head-fixed table-striped text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Owner</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>
                                    <button class="btn btn-xs btn-primary"><i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                        </thead>
                        <tbody v-if="request_templates.total > 0">
                            <tr v-for="(template, index) in request_templates.data">
                                <td>{{ addOne(index) }}</td>
                                <td>{{ template.creator }}</td>
                                <td>{{ template.name }}</td>
                                <td>
                                    <span class="badge badge-success" v-if="template.status == 1">Active</span>
                                    <span class="badge badge-danger" v-if="template.status == 0">Inactive</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-tool text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu">
                                        <button class="btn btn-block dropdown-item" @click="updateRequestTemplate(classification)"><i class="fa fa-edit mr-1 text-primary"></i> Edit Request Template </button>
                                        <button class="btn btn-block dropdown-item" @click="deleteRequestTemplate(classification)"><i class="fa fa-recycle mr-1 text-danger"></i> {{classification.status == 1 ? 'Deactivate Request Template' : 'Reactivate Request Template'}} </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="5">No Request Template meets your requirements</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getInitials" :per-page="request_templates.per_page != null ? request_templates.per_page : 52" :records="request_templates.total != null ? request_templates.total : 550"></pagination>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRConsultantDetailRequestTemplate from '@/emr/consultant/details/RequestTemplate.vue';
import EMRConsultantFormRequestTemplate from '@/emr/consultant/forms/RequestTemplate.vue';
export default {
    components: { EMRConsultantDetailRequestTemplate, EMRConsultantFormRequestTemplate  },
    data() {
        return {
            current_page: 1,
            loading: false,
            query: '',
            request_template: {},
            request_templates: {data: [], total: 0},
        };
    },
    methods: {
        deleteRequestTemplate(){},
        getInitials() {
            this.loading = true;
            axios.get('/api/emr/consultations/request_templates?type=mine&query='+this.query)
            .then((response) => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Request Templates was not loaded successfully',
                })
            })
            .finally(()=> {
                this.loading = false;
            });
        },
        updateRequestTemplate(template){

        },
    },
    mounted() {
        this.getInitials();
    },
};
</script>