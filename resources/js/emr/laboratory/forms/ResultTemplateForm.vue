<template>
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                {{ editMode ? 'Edit Result Template' : 'Create Result Template' }}
            </h3>
        </div>

        <form @submit.prevent="submit">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Department</label>
                        <select v-model="templateData.department_id" class="form-control" required>
                        <option value="">Select Department</option>
                        <option v-for="d in departments" :key="d.id" :value="d.id">
                            {{ d.name }}
                        </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Laboratory Service (Optional)</label>
                        <select v-model="templateData.laboratory_service_id" class="form-control">
                        <option value="">All Services</option>
                        <option v-for="s in services" :key="s.id" :value="s.id">
                            {{ s.name }}
                        </option>
                        </select>
                    </div>
                </div>

                <hr />
                <h5>Analytes</h5>
                <table class="table table-bordered">
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
                            <td><input v-model="a.label" class="form-control" /></td>
                            <td><input v-model="a.unit" class="form-control" /></td>
                            <td class="text-center"><input type="checkbox" v-model="a.show_range" /></td>
                            <td class="text-center"><input type="checkbox" v-model="a.show_flag" /></td>
                            <td><button type="button" class="btn btn-danger btn-sm" @click="removeAnalyte(index)"><i class="fa fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" class="btn btn-secondary btn-sm" @click="addAnalyte"> + Add Analyte</button>

                <hr />

                <div class="form-group">
                    <label>Interpretation Template</label>
                    <textarea v-model="templateData.interpretation_template" class="form-control" rows="4"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label><input type="checkbox" v-model="templateData.layout.show_header" /> Show Header</label>
                    </div>
                    <div class="col-md-4">
                        <label><input type="checkbox" v-model="templateData.layout.show_footer" />Show Footer</label>
                    </div>
                    <div class="col-md-4">
                        <label>Interpretation Position</label>
                        <select v-model="templateData.layout.interpretation_position" class="form-control">
                            <option value="top">Top</option>
                            <option value="bottom">Bottom</option>
                        </select>
                    </div>
                </div>

                <hr />
                <h5>Preview</h5>
                <div class="border p-3 bg-light"><ResultTemplatePreview :template="form" /></div>
            </div>

            <div class="card-footer text-right">
                <button class="btn btn-primary" :disabled="loading">Save Template</button>
                <button type="button" class="btn btn-secondary" @click="$emit('cancel')">Cancel</button>
            </div>
        </form>
    </div>
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
            templateData: new Form({
                analytes: [],
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

<!--script setup>
import { computed, reactive, watch } from 'vue'
import ResultTemplatePreview from './ResultTemplatePreview.vue'

const props = defineProps({
  modelValue: Object,
  departments: Array,
  services: Array,
  loading: Boolean
})

const emit = defineEmits(['update:modelValue', 'submit', 'cancel'])

const form = reactive(JSON.parse(JSON.stringify(props.modelValue)))

const isEdit = computed(() => !!props.modelValue?.id)

watch(form, () => emit('update:modelValue', form), { deep: true })

function addAnalyte() {
  form.analytes.push({
    key: '',
    label: '',
    unit: '',
    show_range: true,
    show_flag: true
  })
}

function removeAnalyte(index) {
  form.analytes.splice(index, 1)
}

function submit() {
  emit('submit', form)
}
</script-->