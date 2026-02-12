<template>
    <form @submit.prevent="submit">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name </label>
                    <input type="text" class="form-control" id="name" placeholder="Enter name" v-model="templateData.name">
                </div>
            </div>
            <div class="col-md-6">
                <label>Department</label>
                <select v-model="templateData.department_id" class="form-control" required>
                <option value="">Select Department</option>
                <option v-for="d in categories" :key="d.id" :value="d.id">
                    {{ d.name }}
                </option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Status</label>
                <select v-model="templateData.status" class="form-control">
                    <option value="">--Choose Status---</option>
                    <option value=1>Active</option>
                    <option value=0>Inactive</option>
                </select>
            </div>
        </div>
        <!--hr /-->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Analytes</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-secondary btn-sm" @click="addAnalyte"> + Add Analyte</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Unit</th>
                                    <th>Range</th>
                                    <th>Flag</th>
                                    <th width="80"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(a, index) in templateData.analytes" :key="index">
                                    <td><input v-model="a.name" class="form-control" /></td>
                                    <td><input v-model="a.unit" class="form-control" /></td>
                                    <td class="text-center"><input type="checkbox" v-model="a.show_range" /></td>
                                    <td class="text-center"><input type="checkbox" v-model="a.show_flag" /></td>
                                    <td><button type="button" class="btn btn-danger btn-sm" @click="removeAnalyte(index)"><i class="fa fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>    
                </div>    
            </div>
        </div>
        <!--hr /-->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Interpretation Template</label>
                    <textarea v-model="templateData.interpretation_template" class="form-control" rows="4"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <label><input type="checkbox" v-model="templateData.layout.show_header" /> Show Header</label>
            </div>
            <div class="col-md-6">
                <label><input type="checkbox" v-model="templateData.layout.show_footer" /> Show Footer</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Interpretation Position</label>
                <select v-model="templateData.layout.interpretation_position" class="form-control">
                    <option value="top">Top</option>
                    <option value="bottom">Bottom</option>
                </select>
            </div>
        </div>

        <!--hr /-->
        <h5>Preview</h5>
        <div class="border p-3 bg-light"><EMRLaboratoryDetailResultTemplatePreview :template.sync="templateData" /></div>
        

        <div class="row float-right">
            <button class="btn btn-primary" :disabled="loading">Save Template</button>
            <button type="button" class="btn btn-secondary" @click="$emit('cancel')">Cancel</button>
        </div>
    </form>
</template>
<script>
export default {
    computed: {
        isEdit() {
            return !!this.modelValue
        }
    },
    data() {
        return {
            categories: [],
            loading: false,
            services: [],
            templateData: new Form({
                analytes: [{
                    name: '',
                    unit: '',
                    reference_range: '',
                    input_type: 'number'
                }],
                category: '',
                description: '',
                layout: {
                    font_size: 'normal',
                    show_reference: true,
                    show_units: true
                },
                name: '',
            }),
        }
    },
    mounted() {
        /*if (this.isEdit) {
            this.form = JSON.parse(JSON.stringify(this.modelValue))
        }*/
       this.getInitials();
    },
    methods: {
        addAnalyte() {
            this.templateData.analytes.push({
                name: '',
                unit: '',
                reference_range: '',
                input_type: 'number'
            })
        },
        getInitials(){
            axios.get('/api/emr/laboratory/result_templates/initials')
            .then(response => {
                this.categories = response.data.categories;
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Result Template form did not load successfully',
                })
            });
        },
        removeAnalyte(index) {
            this.templateData.analytes.splice(index, 1)
        },
        submit() {
            this.$emit('submit', this.form)
        }
    },
    props: {
        editMode: Boolean,
        result_template: {type: Object, default: null}
    },
    watch:{
        result_template(){
            this.templateData.fill(this.result_template);
        }
    },
    
}
</script>