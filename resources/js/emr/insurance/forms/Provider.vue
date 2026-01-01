<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <alert-error :form="ProviderData"></alert-error>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Name </label>
                    <input type="text" class="form-control" id="name" name="name" v-model="ProviderData.name" />
                    <has-error :form="ProviderData" field="name"></has-error>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Type</label>
                    <select class="form-control" id="hmo_type_id" name="hmo_type_id" v-model="ProviderData.hmo_type_id" required>
                        <option value="">--Select Insurance Provider Type--</option>
                        <option v-for="provider_type in provider_types" :key="provider_type.id" :value="provider_type.id">{{ provider_type.name }}</option>
                    </select>
                    <has-error :form="ProviderData" field="hmo_type_id"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="ProviderData.status" required>
                        <option value="">--Select Status--</option>
                        <option value=0>Inactive</option>
                        <option value=1>Active</option>
                    </select>
                    <has-error :form="ProviderData" field="status"></has-error>
                </div>
            </div> 
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Portal</label>
                    <input type="text" class="form-control" id="portal" name="portal" v-model="ProviderData.portal" />
                    <has-error :form="ProviderData" field="portal"></has-error>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" v-model="ProviderData.phone" />
                    <has-error :form="ProviderData" field="phone"></has-error>
                </div>
            </div> 
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" id="email" name="email" v-model="ProviderData.email" />
                    <has-error :form="ProviderData" field="email"></has-error>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" id="description" name="description" v-model:content="ProviderData.description" :class="{'is-invalid' : ProviderData.errors.has('description') }"></QuillEditor>
                    <has-error :form="ProviderData" field="description"></has-error>
                </div>
            </div>
        </div>
        <button @click.prevent="editMode ? updateProvider() : createProvider()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
    </form>
</section>
</template>
<script>
import { QuillEditor } from '@vueup/vue-quill';

export default {
    data() {
        return {
            loading: false,
            ProviderData: new Form({
                id: '',
                description: '',
                hmo_type_id: '',
                name: '',
                portal: '',
                phone: '',
                status: '',
                website: '',
            }),
            provider_types: [],
        }
    },
    emits: ['refreshProviders'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        createProvider(){
            this.loading = true;
            this.ProviderData.post('/api/emr/insurance/providers')
            .then(response => {
                this.loading = false;
                this.$emit('refreshProviders', response);
                this.$swal.fire({icon: 'success', title: 'The Provider has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(() => {
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.loading = false;
            });
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/emr/insurance/providers/initials').then(response =>{
                this.providers = response.data.providers;
                this.provider_types = response.data.provider_types;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Providers were not loaded successfully',
                })
            });
        },
        updateProvider(){
            this.loading = true;
            this.ProviderData.put('/api/emr/insurance/providers/' + this.ProviderData.id)
            .then(response => {
                this.loading = false;
                this.$emit('refreshProviders', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Provider has been updated',
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
        }
    },
    props: {
        editMode: Boolean,
        provider: Object,
    },
    watch: {
        provider() {
            this.ProviderData.fill(this.provider);
        },
    },
}
</script>