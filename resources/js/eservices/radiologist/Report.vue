<template>
<section class="content-header pt-0">
    <div class="row clearfix">
        <div class="col-md-12">
            <EServiceDetailAppointment :appointment="report" source="radiologist"/>
        </div>
    </div>
</section>
</template>

<script>
import Form from 'vform';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
export default {
    data(){
        return {
            editMode: false,
            report: {}, 
            report: {},
            findings: [],    
        }
    },
    methods:{
        editReport(){
            //this.$Progress.start();
            this.editMode = true;
            //Fire.$emit('reportDataFill', this.report);
            $('#reportModal').modal('show');
            //this.$Progress.finish();
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/emr/radiologists/'+this.$route.params.id)
            .then(response =>{
                this.reportReload(response);
                this.loading = false;
                toast.fire({icon: 'success', title: 'Report was loaded successfully',});
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({icon: 'error', title: 'Report was not loaded successfully',})
            });
        },
        reportReload(response){
            this.findings = response.data.findings;
            this.report = response.data.report;
        },
    },
    mounted() {
        this.getAllInitials();    
    }
}
</script>