<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>                  
    <form @submit.prevent="editMode ? updateService() : createService()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter name" v-model="serviceForm.name">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" name="category_id" id="category_id" v-model="serviceForm.category_id">
                        <option value="">--Select Category--</option>
                        <option  v-for="category in categories" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Result Template</label>
                    <select class="form-control" name="category_id" id="category_id" v-model="serviceForm.result_template_id">
                        <option value="">--Select Result Template--</option>
                        <option  v-for="result_template in result_templates" :value="result_template.id">{{ result_template.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Analytes</label>
                    <Multiselect id="tagging" v-model="serviceForm.analytes" tag-placeholder="Add this as new tag" placeholder="Search or add a tag" label="name" track-by="id" :options="analytes" :multiple="true" :close-on-select="false" :clear-on-select="false" :taggable="true" @tag="addTag" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Bottle Type</label>
                    <select class="form-control" name="bottle_type_id" id="bottle_type_id" v-model="serviceForm.bottle_type_id">
                        <option value="">--Select Bottle Type--</option>
                        <option  v-for="bottle_type in bottle_types" :value="bottle_type.id">{{ bottle_type.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Specimen Type</label>
                    <select class="form-control" name="specimen_type_id" id="specimen_type_id" v-model="serviceForm.specimen_type_id">
                        <option value="">--Specimen Type--</option>
                        <option  v-for="specimen in specimen_types" :value="specimen.id">{{ specimen.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor class="form-control" theme="snow" content-type="html" name="description" id="description" v-model:content="serviceForm.description" />
                </div>
            </div>
        </div>    
        <button type="submit" class="btn btn-primary">{{editMode ? 'Update' :'Submit'}}</button>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            analytes: [],
            bottle_types: [],
            categories: [],
            loading: false,
            result_templates: [],
            specimen_types: [],
            serviceForm: new Form({
                analytes: [],
                bottle_type_id: '',
                category_id: '',
                description: '',
                name: '',
                result_template_id: '',
                specimen_type_id: '',
                id: '',
            }),
        }
    },
    emits:['refreshServiceForm'],
    methods: {
        addTag (newTag) {
            const tag = {
                name: newTag,
                code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
            }
            this.analytes.push(tag)
            this.serviceForm.analytes.push(tag)
        },
        createService(){
            this.loading = true;
            this.serviceForm.post('/api/emr/laboratory/services')
            .then(response => {
                this.$emit('refreshServiceForm');
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Service form did not load successfully',});
            })
            .finally(()=> {
                this.loading = false;
            });
        },
        getInitials() {
            axios.get('/api/emr/laboratory/services/initials')
            .then(response => {
                this.refreshPage(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Service form did not load successfully',});
            });
        },
        refreshPage(response) {
            this.analytes = response.data.analytes;
            this.bottle_types = response.data.bottle_types;
            this.categories = response.data.categories;
            this.result_templates = response.data.result_templates;
            this.specimen_types = response.data.specimen_types;
        },
        updateService(){
            this.loading = true;
            this.serviceForm.put('/api/emr/laboratory/services/'+this.serviceForm.id)
            .then(() => {
                this.$emit('refreshServiceForm');
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Service form did not load successfully',});
            })
            .finally(()=> {
                this.loading = false;
            });
        },
    },
    mounted() {
        this.getInitials();
    },
    props: {
        editMode: Boolean,
        service: Object,
    },
    watch:{
        service(){
            this.serviceForm.fill(this.service);
            this.serviceForm.name = this.service?.service?.item?.name || '';
        }
    }
}
</script>