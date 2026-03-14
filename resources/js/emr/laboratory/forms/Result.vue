<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="editMode ? updateResult() :createResult()">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Analyte</th>
                <th>Specimen</th>
                <th width="120">Value</th>
                <th width="80">Unit</th>
                <th>Reference</th>
                <th width="80">Flag</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, index) in resultForm.values" :key="item.analyte_id">
                <td>{{ item.analyte_name }}</td>
                <td>
                    <select class="form-control" v-model="resultForm.values[index].specimen_id">
                        <option value="">--Select Specimen--</option>
                        <option v-for="specimen in specimens" :value="specimen.id">{{ specimen.unique_id }}  [{{ specimen.bottle?.name }}]</option>
                    </select>
                </td>
                <td><input class="form-control" v-model="item.value" @input="updateFlag(item)"></td>
                <td>{{ item.unit }}</td>
                <td>{{ item.reference }}</td>
                <td><span class="badge" :class="flagClass(firstUp(item.flag))">{{ firstUp(item.flag) }}</span></td>
            </tr>
        </tbody>
    </table>
    <button class="btn btn-sm btn-primary" @click="submit" type="submit">{{editMode ? 'Update' : 'Save'}}</button>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: false,
            specimens: [],
            request: {},
            result:{},
            resultForm: new Form({
                id: '',
                result_id: null,
                request_id: null,
                values: '',
            }),
        }
    },
    emits:['refreshResultForm'],
    mounted() {
        this.getInitials();
    },
    methods: {
        createResult(){
            this.loading = true;
            this.resultForm.post('/api/emr/laboratory/results')
            .then(response => {
                this.$swal.fire('Entered!', 'Result has been entered.', 'success');
                this.$emit('refreshResultForm');
            })
            .catch(()=>{
                this.$swal.fire('Oops!', 'Something went wrong', 'error');
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        flagClass(flag){
            return {
                'badge-warning': flag === 'High' || flag === 'Low',
                'badge-danger': flag === 'Critical High' || flag === 'Critical Low',
                'badge-success': flag === 'Normal'
            }
        },
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/results/'+this.request_id+'/initials')
            .then(response => {
                this.refreshQueue(response)
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Result form did not load successfully',
                })
            });
        },
        refreshQueue(response) {
            const data = response.data
            this.result = data.result,
            this.specimens = data.specimens
            this.resultForm.result_id = data.result?.id
            this.resultForm.request_id = data.result?.request?.id
            
            if(data.result?.latest_version?.values && data.result?.latest_version?.values.length){
                this.resultForm.values = data.result?.latest_version?.values
                this.editMode = true;
            }
            else{
                this.editMode = false;
                this.resultForm.values = data.analytes
            }
        },
        updateResult(){
            this.loading = true;
            this.resultForm.put('/api/emr/laboratory/results/'+this.result.id)
            .then(response => {
                this.$swal.fire('Updated!', 'Result has been updated.', 'success');
                this.$emit('refreshResultForm');
            })
            .catch(()=>{
                this.$swal.fire('Oops!', 'Something went wrong', 'error');
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        updateFlag(item){
            if (item.value <= item.reference_critical_low){item.flag = "Critical Low"}
            else if(item.value > item.reference_critical_low && item.value <= item.reference_low){item.flag = "Low"}
            else if(item.value > item.reference_normal && item.value <= item.reference_high ){item.flag = "High"}
            else if (item.value > item.reference_high){item.flag = "Critical High"}
            else{item.flag = "Normal"}
        },
    },
    props: {
        //editMode: {type: Boolean, default: false},
        request_id: {type: [Number, String]},
        //result: {type: Object, default: () =>({})},
    }
}
</script>