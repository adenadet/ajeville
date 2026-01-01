<template>
    <section>
        <form>
            <alert-error :form="ReferralData"></alert-error> 
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Choose Referral Template</label>
                        <select class="form-control" id="template" name="template" v-model="template" @change="loadTemplate()">
                            <option value="">--Select Template--</option>
                            <option v-for="(temp, index) in templates" :value="index" :key="temp.id">{{ temp.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Details</label>
                        <QuillEditor v-model:content="ReferralData.details" content-type="html"></QuillEditor>
                        <has-error :form="ReferralData" field="details"></has-error> 
                    </div>
                </div>
            </div>
            <button @click.prevent="editMode ? updateReferralData() : createReferralData()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
        </form>
    </section>
</template>
<script>
export default {
    data() {
        return {
            loading: false,
            ReferralData: new Form({appointment_id: "", details: "", id: "",}),
            template: '',
            templates: [],
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        createReferralData(){
            this.loading = true;
            this.ReferralData.appointment_id = this.appointment.id;
            this.ReferralData.post('/api/emr/referrals')
            .then(response =>{
                this.loading = false;
                this.$emit('reportReload', response);
                this.$swal.fire({icon: 'success', title: 'The Referral has been created successfully', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.loading = false;
            });
        },
        getInitials() {
            axios.get('/api/emr/referrals/initials')
            .then(response => {this.templates = response.data.templates;})
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Referral templates did not load successfully',})
            });
        },
        loadTemplate(){
            this.ReferralData.details = this.templates[this.template].content;
        },
        updateReferralData(){
            this.loading = true;
            this.ReferralData.appointment_id = this.appointment.id;
            this.ReferralData.put('/api/emr/referrals/'+ this.ReferralData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('reportReload', response);
                this.$swal.fire({icon: 'success', title: 'The Referral has been updated successfully', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
    },
    props:{
        appointment: Object,
        editMode: Boolean,
    },
    watch:{
        appointment(){
            this.ReferralData.appointment_id = this.appointment.id;
        }
    }
}
</script>
