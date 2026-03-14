<template>
    Put the various list here.
</template>
<script>
import EMRAdmissionDetailRequestList from '@/emr/admission/details/RequestList.vue';
import EMRConsultantDetailQueueList from '@/emr/consultant/details/QueueList.vue';
import EMRLaboratoryDetailRequestList  from '@/emr/laboratory/details/RequestList.vue';
import EMRRadiologyDetailRequestList from '@/emr/radiology/details/RequestList.vue';
export default {
    compoents:{
        EMRAdmissionDetailRequestList, EMRConsultantDetailQueueList, EMRLaboratoryDetailRequestList, EMRRadiologyDetailRequestList
    },
    data(){
        return {
            loading:false,
            nokForm: new Form({
                id:'',
                name:'',
                relationship:'',
                address:'',
                email:'',
                phone:'',
            }),
        }
    },
    methods:{
        updateNextofKin(){
            this.nokForm.post('/api/hrms/nok')
            .then(response =>{
                this.$swal.fire({icon: 'success', title: 'The Next of Kin details has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;
            });        
        },
        
    },
    mounted() {},
    props:{
        editMode: Boolean,
        nok: Object,
    },
    watch:{
        nok(){
            this.nokForm.fill(this.nok);
        }
    }
}
</script>