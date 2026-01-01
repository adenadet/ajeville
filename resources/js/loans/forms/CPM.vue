<template>
    <section class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row" v-show="!final">
            <div class="col-md-12">
                <form @submit.prevent="createTemplate()">
                    <div class="row" v-for="(list, index) in cpmTemplateData.lists" :key="index">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>{{ index | addOne }} Module</label>
                                <select class="form-control" v-model="cpmTemplateData.lists[index].module_id" required
                                    @change="updateTemplate(index)">
                                    <option v-for="(module_unit, indent) in modules" :value="indent" :key="indent">{{ module_unit.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Template</label>
                                <select class="form-control" v-model="cpmTemplateData.lists[index].template_id">
                                    <option v-for="(template, inner) in cpmTemplateData.lists[index].templates"
                                        :value="inner" :key="inner">{{ template.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <p></p>
                                <button class="btn btn-sm btn-danger mt-2" type="button" @click="removeModule(list)"><i
                                        class="fa fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary mt-2" type="button" @click="addModule()">Add Module</button>
                        <button class="btn btn-sm btn-success mt-2" type="submit">Create CPM</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row" v-show="final">
            <form @submit.prevent="editMode ? updateCPM() : createCPM()">
                <alert-error :form="cpmData"></alert-error>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>CPM Details</label>
                        <wysiwyg rows="500" v-model="cpmData.detail" />
                    </div>
                </div>
                <button class="btn btn-sm btn-success mt-2" type="submit">Submit</button>
            </form>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            cpmTemplateData: new Form({
                lists: [
                    {
                        module_id: '',
                        template_id: 0,
                        templates: [],
                    },
                ],
            }),
            cpmData: new Form({
                id: '',
                loan_id: '',
                detail: '',
                hbd_id: '',
                hbd_date: '',
                ceo_id: '',
                ceo_at: '',
                chairman_id: '',
                chairman_at: '',
                rm_id: '',
                rm_at: '',
                ao_id: '',
                ao_at: '',
            }),
            editMode: false,
            final: false,
            item: {
                module_id: '',
                template_id: 0,
            },
            loan: {},
            modules: [],
            templates: [],

        };
    },
    methods: {
        addModule() {
            this.cpmTemplateData.lists.push({});
        },
        createCPM() {
            this.$Progress.start();
            this.cpmData.loan_id = this.account.id;

            this.cpmData.post('/api/loans/cpms')
                .then(response => {
                    this.$Progress.finish();
                    Fire.$emit('refreshCPM', response);
                    Swal.fire({ icon: 'success', title: 'The CPM has been saved', showConfirmButton: false, timer: 1500 });
                    this.cpmData.reset();
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        footer: 'Please try again later!'
                    });
                    this.$Progress.fail();
                });
        },
        createTemplate() {
            var details = "";
            for (let i = 0; i < this.cpmTemplateData.lists.length; i++) {
                details += '<h3>' + this.modules[this.cpmTemplateData.lists[i].module_id].name + '</h3>';
                if (this.modules[this.cpmTemplateData.lists[i].module_id].templates.length == 0) {
                    details += 'No template';
                }
                else if (typeof (this.cpmTemplateData.lists[i].template_id) !== "number") {
                    details += 'No template chosen';
                }
                else {
                    details += this.modules[this.cpmTemplateData.lists[i].module_id].templates[this.cpmTemplateData.lists[i].template_id].details;
                }
            }

            details = details.replace("[loan.user | FullName]", (this.account.user.last_name + ', ' + this.account.user.first_name + (this.account.user.middle_name != null ? ' ' + this.account.user.middle_name : '')));
            details = details.replace("[loan.amount]", this.account.amount);
            details = details.replace("[loan.name]", this.account.name);
            details = details.replace("[loan.duration]", this.account.duration);
            details = details.replace("[loan.frequency]", this.account.frequency)

            this.cpmData.detail = details;
            this.final = true;

        },
        getInitials() {
            axios.get('/api/loans/cpms/initials').then(response => {
                this.reloadPage(response);
            })
                .catch(() => {
                    this.$Progress.fail();
                    toast.fire({ icon: 'error', title: 'Assigned Loan Accounts not loaded successfully', });
                });
        },
        reloadPage(response) {
            this.modules = response.data.modules;
        },
        removeModule(item) {
            this.cpmTemplateData.lists.pop(item);
        },
        updateCPM() {
            this.$Progress.start();
            this.cpmData.loan_id = this.account.id;
            this.cpmData.put('/api/loans/cpms/' + this.cpmData.id)
                .then(response => {
                    this.$Progress.finish();
                    Fire.$emit('refreshCPM', response);
                    this.RescheduleData.reset();
                    Swal.fire({ icon: 'success', title: 'The Checklist has been saved', showConfirmButton: false, timer: 1500 });
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        footer: 'Please try again later!'
                    });
                    this.$Progress.fail();
                });
        },
        updateTemplate(index) {
            this.cpmTemplateData.lists[index].templates = this.modules[this.cpmTemplateData.lists[index].module_id].templates;
        }
    },
    mounted() {
        this.getInitials();
        Fire.$on('hidePreCPM', cpm => {
            if (cpm == null || cpm.id == null){
                this.editMode = false;
                this.final = false;
                this.cpmData.reset();
                this.cpmData.loan_id = account.id;
            }
            else{
                this.final = true;
                this.cpmData.fill(cpm);
            }
        });
    },
    props: {
        account: Object,
        bureau_products: Array,
        bureaus: Array,
    }
}
</script>