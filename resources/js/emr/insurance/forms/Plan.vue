<template>
<section>
    <form>
        <alert-error :form="PlanData"></alert-error>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="PlanData.name" />
                    <has-error :form="PlanData" field="name"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group" v-if="provider == null">
                    <label>Provider</label>
                    <select type="text" class="form-control" id="provider_id" name="provider_id" v-model="PlanData.provider_id">
                        <option value="">--Select Provider--</option>
                        <option v-for="provider in providers" :value="provider.id" :key="provider.id">{{provider.name}}</option>
                    </select>
                    <has-error :form="PlanData" field="provider_id"></has-error>
                </div>
                <div class="form-group" v-if="provider != null">
                    <label>Provider</label>
                    <div class="form-control" v-html="provider.name"></div>
                    <input type="hidden" name="provider_id" id="provider_id" v-model="PlanData.provider_id" />
                    <has-error :form="PlanData" field="provider_id"></has-error>
                </div>                
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="PlanData.status">
                        <option value="">--Select Status--</option>
                        <option value=0>Inactive</option>
                        <option value=1>Active</option>
                    </select>
                    <has-error :form="PlanData" field="status"></has-error>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" id="description" name="description" v-model:content="PlanData.description" :class="{'is-invalid' : PlanData.errors.has('description') }"></QuillEditor>
                    <has-error :form="PlanData" field="description"></has-error>
                </div>
            </div>
        </div>
        <button @click.prevent="editMode ? updatePlan() : createPlan()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            plan: {},
            providers: [],
            PlanData: new Form({
                name: '',
                description: '',
                status: '',
                provider_id: '',
                id: '',
            }),
        }
    },
    emits:['refreshpage', 'requestDataFill'],
    mounted() {
        /*this.$on('planDataFill', plan => {
            alert("Working");
            if (plan != null){this.PlanData.fill(plan);}
            if (this.provider != null){ this.PlanData.provider_id = this.provider.id}
        });
        this.$on('searchInstance', ()=>{
            let query = this.$parent.search;
            axios.get('api/emr/domiciliary/search?q='+query)
            .then((response ) => {this.applicants = response.data.applicants;})
            .catch(()=>{});
        });*/
    },
    methods: {
        addType(request){
            this.loading = true;
            this.request = request;
            this.editMode = false;
            this.$emit('requestDataFill', {});
            $('#requestModal').modal('show');
            this.loading = false;
        },
        createPlan(){
            this.loading = true;
            this.PlanData.post('/api/emr/insurance/plans')
            .then(response => {
                this.loading = false;
                this.$emit('refreshPage', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Plan has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });
        },
        updatePlan(){
            this.loading = true;
            this.PlanData.put('/api/emr/insurance/plans/'+this.PlanData.id)
            .then(response => {
                    this.loading = false;
                    this.$emit('refreshPage', response);
                    this.$swal.fire({
                        icon: 'success',
                        title: 'The Plan has been updated',
                        showConfirmButton: false,
                        timer: 1500
                    });
                })
                .catch(() => {
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        footer: 'Please try again later!'
                    });
                    this.loading = false;
                });
        },
    },
    props: {
        editMode: Boolean,
        provider: Object,
    }
}
</script>