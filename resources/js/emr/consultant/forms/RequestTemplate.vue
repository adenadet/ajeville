<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="editMode ? updateTemplate() : createTemplate()">
        <div class="card card-secondary">
            <div class="card-body">
                <div class="form-group">
                    <label>Template Name</label>
                    <input type="text" class="form-control" v-model="templateData.name" />
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor class="form-control" v-model:content="templateData.description" content-type="html" theme="snow" />
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input"  v-model="templateData.active" />
                    <label class="form-check-label">Active</label>
                </div>
            </div>
        </div>
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col-3 col-md-2">
                        <div class="nav flex-column nav-pills">
                            <a class="nav-link active" data-toggle="pill" href="#edit-radiology">Radiology</a>
                            <a class="nav-link" data-toggle="pill" href="#edit-laboratory">Laboratory</a>
                        </div>
                    </div>
                    <div class="col-9 col-md-10">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="edit-radiology">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Fixed Header Table</h3>

                                        <div class="card-tools">
                                            <div class="input-group input-group-sm" style="width: 150px;">
                                                <button type="button" class="btn btn-sm btn-success mb-2" @click="addRadiology"> Add Radiology</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive p-0" style="height: 300px;">
                                        <table class="table table-head-fixed text-nowrap">
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
                                                <tr v-for="(item, index) in templateData.requests.radiology" :key="'rad-edit-' + index">
                                                    <td>183</td>
                                                    <td><input class="form-control" placeholder="Code" v-model="item.code" /></td>
                                                    <td><input class="form-control" placeholder="Name" v-model="item.name" /></td>
                                                    <td><input class="form-control" placeholder="Notes" v-model="item.notes" /></td>
                                                    <td><button type="button" class="btn btn-sm btn-danger" @click="removeRadiology(index)"><i class="fa fa-trash"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="edit-laboratory">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Fixed Header Table</h3>

                                        <div class="card-tools">
                                            <div class="input-group input-group-sm" style="width: 150px;">
                                                <button type="button" class="btn btn-sm btn-success mb-2" @click="addLaboratory"> Add Laboratory</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive p-0" style="height: 300px;">
                                        <table class="table table-head-fixed text-nowrap">
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
                                                <tr v-for="(item, index) in templateData.requests.laboratory" :key="'rad-edit-' + index">
                                                    <td>183</td>
                                                    <td><input class="form-control" placeholder="Code" v-model="item.code" /></td>
                                                    <td><input class="form-control" placeholder="Name" v-model="item.name" /></td>
                                                    <td><input class="form-control" placeholder="Notes" v-model="item.notes" /></td>
                                                    <td><button type="button" class="btn btn-sm btn-danger" @click="removeLaboratory(index)"><i class="fa fa-trash"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">{{editMode ? 'Update Template' :'Create Template'}}</button>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            templateData: {
                id: null,
                name: '',
                description: '',
                active: true,
                requests: {
                    laboratory: [],
                    prescription: [],
                    radiology: [],
                    physiotherapy: [],
                    procedures: [],
                }
            }
        }
    },
    emits: ['refreshRequestTemplateForm'],
    methods: {
        // --- Utilities ---
        createTemplate(){
            this.loading = true;
            this.templateData.post('/api/emr/consultations/request_templates')
            .then(()=>{
                this.$swal.fire({icon: 'success', title: 'Created', text: 'Request Template created successfully!',});
                this.$emit('refreshRequestTemplateForm');
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            })
            .finally(()=>{
                this.loading = false;
            })
        },
        cloneTemplate(template) {
            return JSON.parse(JSON.stringify(template))
        },
        addLaboratory() {
            this.templateData.requests.laboratory.push({
                code: '',
                name: '',
                notes: ''
            })
        },
        addPhysiotherapy(){},
        addProcedure(){
            this.templateData.requests.procedures.push({
                service_id: '',
                service_name: '',
                category: '',
                quantity: 1,
                urgency: 'routine',
                preferred_date: '',
                indication: '',
                notes: '',
            })
        },
        addPrescription(){
            this.templateData.requests.prescription.push({
                drug_id: '',
                drug_name: '',
                specific_drug: '',
                detail: '',
                dose: '',
                duration: '',
                form: '',
                form_id: '',
                frequency: '',
                route: '',
            });
        },
        addRadiology() {
            this.templateData.requests.radiology.push({
                code: '',
                name: '',
                notes: ''
            })
        },

        removeLaboratory(index) {
            this.templateData.requests.laboratory.splice(index, 1)
        },
        removePhysiotherapy(index){
            this.templateData.requests.physiotherapy.splice(index, 1)
        },
        removePrescription(index){
            this.templateData.requests.prescription.splice(index, 1)
        },
        removeProcedure(index){
            this.templateData.requests.procedures.splice(index, 1)
        },
        removeRadiology(index) {
            this.templateData.requests.radiology.splice(index, 1)
        },        
        saveTemplate() {
            const payload = this.cloneTemplate(this.form)
            // Emit for parent persistence
            this.$emit('save', payload)
            // Optional v-model sync
            this.$emit('update:modelValue', payload)
        },
        updateTemplate(){
            this.loading = true;
            this.templateData.put('/api/emr/consultations/request_templates/'+this.templateData.id)
            .then(()=>{
                this.$swal.fire({icon: 'success', title: 'Updated', text: 'Request Template updated successfully!',});
                this.$emit('refreshRequestTemplateForm');
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            })
            .finally(()=>{
                this.loading = false;
            })
        },

    },
    mounted() {
        
    },
    props: {
        request_template: {
            type: Object,
            required: false,
            default: null
        }
    },
    watch:{
        request_template(){
            if (this.modelValue) {
                this.templateData.fill(this.cloneTemplate(this.request_template));
            }
        }
    }
}
</script>