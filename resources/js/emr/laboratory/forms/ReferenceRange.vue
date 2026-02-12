<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>                  
    <form @submit.prevent="editMode ? updateReferenceRange() : createReferenceRange()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Analyte </label>
                    <select v-if="!analyte_known" class="form-control" id="analyte.id" placeholder="Enter analyte.id" v-model="referenceRangeData.analyte_id">
                        <option value="">--Select Analyte</option>
                        <option v-for="a in analytes" :value="a.id" :key="a.id">{{ a.name }}</option>
                    </select>
                    <div v-else class="form-control">
                        {{ analyte.name }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Gender</label>
                    <select class="form-control" name="gender" id="gender" v-model="referenceRangeData.gender">
                        <option value="">--Select Gender--</option>
                        <option  v-for="category in genders" :value="category">{{ category }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Age (Start)</label>
                    <input class="form-control" type="number" name="age_min" id="age_min" v-model="referenceRangeData.age_min">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Age (End)</label>
                    <input class="form-control" type="number" name="age_max" id="age_max" v-model="referenceRangeData.age_max">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Normal</label>
                    <input class="form-control" type="number" step="0.001" name="normal_value" id="normal_value" v-model="referenceRangeData.normal_value">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Low</label>
                    <input class="form-control" :max="referenceRangeData.normal_value" type="number" step="0.001" name="low_value" id="low_value" v-model="referenceRangeData.low_value">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Critical Low</label>
                    <input class="form-control" :max="referenceRangeData.low_value" type="number" step="0.001" name="critical_low" id="critical_low" v-model="referenceRangeData.critical_low">
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <label>High</label>
                    <input class="form-control" :min="referenceRangeData.normal_value" type="number" step="0.001" name="high_value" id="high_value" v-model="referenceRangeData.high_value">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" >Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            analytes: [],
            analyte_known: false,
            genders: ['Male', 'Female', 'Any'],
            loading: false,
            referenceRangeData: new Form({
                analyte_id: '',
                age_min: '',
                age_max: '',
                critical_low:'',
                high_value: '',
                id: '',
                low_value: '',
                normal_value: '',
                gender: '',
            }),
        }
    },
    emits:['refreshReferenceRangeForm'],
    methods: {
        createReferenceRange(){
            this.loading = true;
            this.referenceRangeData.post('/api/emr/laboratory/reference_ranges')
            .then(response => {
                this.$swal.fire('Created!', 'Reference Range has been created.', 'success');
                this.$emit('refreshReferenceRangeForm');
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Reference Range did not create',});
            })
            .finally(()=> {
                this.loading = false;
            });
        },
        getInitials() {
            axios.get('/api/emr/laboratory/reference_ranges/initials')
            .then(response => {
                this.refreshPage(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Reference Range form did not load successfully',});
            });
        },
        refreshPage(response) {
            this.analytes = response.data.analytes;
        },
        updateReferenceRange(){
            this.loading = true;
            this.referenceRangeData.put('/api/emr/laboratory/reference_ranges/'+this.referenceRangeData.id)
            .then(response => {
                this.$swal.fire('Updated!', 'Reference Range has been updated.', 'success');
                this.$emit('refreshReferenceRangeForm');
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Reference Range did not update',});
            })
            .finally(()=> {
                this.loading = false;
            });
        }
    },
    mounted() {
        //this.getInitials();
    },
    props: {
        analyte: Object,
        editMode: Boolean,
        reference_range: Object,
    },
    watch:{
        analyte(){
            if(this.analyte == null){
                this.analyte_known = false;
                this.referenceRangeData.analyte_id = '';
                
            }
            else{
                this.analyte_known = true;
                this.referenceRangeData.analyte_id = this.analyte.id;
            }
        },
        reference_range(){
            this.referenceRangeData.fill(this.reference_range);
            if (this.analyte) {
                this.referenceRangeData.analyte_id = this.analyte.id;
            }
        }
    },
}
</script>