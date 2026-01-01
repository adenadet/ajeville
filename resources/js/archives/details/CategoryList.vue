<template>
<table class="table table-head-fixed text-nowrap">
    <thead>
        <tr>
            <th>Name</th>
            <th>Unique ID</th>
            <th>Short</th>
            <th>File Locations</th>
            <th>Last Update</th>
            <th>Updated By</th>
            <th></th>
        </tr>
    </thead>
    <tbody v-if="categories.length > 0">
        <tr v-for="category in categories" :key="category.id">
            <td>{{category.name}}</td>
            <td>{{ category.unique_id }}</td>
            <td>{{ category.short }}</td>
            <td>{{ category.location }}</td>
            <td>{{ ExcelDate(category.updated_at) }}</td>
            <td>{{ FullName(category.updater) }}</td>
            <td></td>
        </tr>
    </tbody>
    <tbody v-else>
        <tr>
            <td colspan="5" class="text-center">No records found</td>
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
    mounted() {
        
    },
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
        categories: Array,
        source: String,
    }
}
</script>