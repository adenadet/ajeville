<template>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>Name</th>
                <th>Unique ID</th>
                <th>Patient/Vendor</th>
                <th>Category</th>
                <th>Sub Category</th>
                <th>Uploaded By</th>
                <th>Date</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="documents.length > 0">
            <tr v-for="document in documents" :key="document.id">
                <td>{{document.name}}</td>
                <td>{{ document.unique_id }}</td>
                <td>{{ document.patient != null ? PatientName(document.patient.user) : (document.vendor != null ? document.vendor.name : '')}}</td>
                <td>{{ document.category != null ? document.category.name : '' }}</td>
                <td>{{ document.sub_category != null ? document.sub_category.name : '' }}</td>
                <td>{{ document.creater != null ? FullName(document.creater.user) : '' }}</td>
                <td>{{ ExcelDate(document.updated_at) }}</td>
                <td>{{ document.status }}</td>
                <td></td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="9" class="text-center">No records found</td>
            </tr>
        </tbody>
    </table>
</template>
<script>
export default {
    data() {
        return {
            editMode: true,
        }
    },
    mounted() {},
    methods: {
        pay_from_wallet(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "Debit patient's wallet for transaction!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/lms/courses/'+id)
                    .then(response=>{
                    Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        }
    },
    props: {
        actionable: String,
        documents: Array,
        source: String,
    }
}
</script>