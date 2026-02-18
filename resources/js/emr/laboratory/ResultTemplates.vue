<template>
<section class="container-fluid overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="resultTemplateFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Result Template Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRLaboratoryFormResultTemplate :result_template.sync="result_template" :editMode="editMode" @refreshResultTemplateForm="getInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Laboratory Result Templates</h3>
                </div>
                <div class="card-body p-0 table-responsive" style="height:500px;">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>
                                    <button class="btn btn-xs btn-primary" @click="addResultTemplate"><i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(template, index) in result_templates.data" :key="template.id">
                                <td>{{ addOne(index) }}</td>
                                <td>{{ template.name }}</td>
                                <td>{{ template.status == 1 ? 'Active' : 'Inactive'}}</td>
                                <td>{{ template.description}}</td>
                                <td>
                                    <span class="nav-link" data-toggle="dropdown" href="#">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                        <button class="btn btn-block dropdown-item" @click="updateResultTemplate(template)"><i class="fas fa-edit mr-2 text-primary"></i> Edit Result Template</button>
                                        <button class="btn btn-block dropdown-item" @click="deactivateResultTemplate(template.id)"><i class="fas fa-power-off mr-2"></i> Deactivate Result Template</button>
                                    </div>
                                </td>
                            </tr>    
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="result_templates.per_page != null ? result_templates.per_page : 52" :records="result_templates.total != null ? result_templates.total : 550" ></pagination>
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
            current_page: 1,
            editMode: true,
            form: new Form({}),
            loading: false,
            query: '',
            result_template: {},
            result_templates: {data: [], total: 0},
            template: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addResultTemplate(){
            this.loading = true;
            this.editMode = false;
            this.result_template =  this.normalizeTemplate(null);
            $('#resultTemplateFormModal').modal('show');    
            this.loading = false;     
        },
        deactivateResultTemplate(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                if(result.value){
                    this.form.delete('/api/emr/laboratory/result_templates/'+id)
                    .then(response=>{
                        this.getInitials();
                        this.$swal.fire('Deleted!', 'Result Template has been deactivated/reactivated.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/emr/laboratory/result_templates?page='+this.current_page+'&query='+this.query)
            .then(response => {
                $('#resultTemplateFormModal').modal('hide');    
                this.refreshPage(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            })
            .finally(() => {
                this.loading = false;
            });
        },
        normalizeTemplate(template){
            return { 
                analytes: template?.analytes || [{input_type: 'number', name: '', show_flag: '', show_range: '', unit: '',}],
                category: template?.category || '',
                description: template?.description ||'',
                layout: template?.layout ||{ font_size: 'normal', show_reference: true, show_units: true},
                name: template?.name || '',
            };
        },
        refreshPage(response){
            this.result_templates = response.data.result_templates;
        },
        updateResultTemplate(template){
            this.loading = true;
            this.editMode = true;
            this.result_template = this.normalizeTemplate(template);
            $('#resultTemplateFormModal').modal('show');    
            this.loading = false;     
        }
    },
    props: {
    }
}
</script>