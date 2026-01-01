<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Laboratory Result Templates</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(template, index) in templates.data" :key="template.id">
                                    <td>{{index | addOne}}</td>
                                    <td>{{template.name}}</td>
                                    <td>{{template.status == 1 ? 'Active' : 'Inactive'}}</td>
                                    <td>{{template.description}}</td>
                                    <td></td>
                                </tr>    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{editMode ? 'Edit Template: ' : 'New Template'}}</h3>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            editMode: true,
            form: new Form({}),
            templates: {},
            template: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials(page = 1){

        },
        callPatient(consultation){
            Swal.fire({
                title: 'Are you sure?',
                text: "A notification would be sent to the patient",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, call Patient!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.get('/api/emr/consultations/consultants/call_patient/'+consultation.id)
                    .then(response=>{
                        Swal.fire('Notified!', 'Patient has been notified.', 'success');
                        //Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
    },
    props: {
    }
}
</script>