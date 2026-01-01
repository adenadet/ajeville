<template>
<section class="overlay-wrapper">
    <table class="table table-head-fixed table-stripped text-nowrap">
        <thead>
            <tr>
                <th>Contact</th>
                <th>Company</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody v-if="contacts.length > 0">
            <tr v-for="contact in contacts">
                <td>{{ contact.title }} {{ contact.first_name }} {{ contact.last_name }}</td>
                <td>{{ contact.company != null ? contact.company.name : '' }}</td>
                <td>{{contact.phone}} {{ contact.alt_phone != null ? ' | '+contact.alt_phone : '' }}</td>
                <td>{{contact.email}}</td>
                <td v-html="contact.description"></td>
                <td>
                    <span v-if="contact.status == 1" class="badge badge-primary">Active</span>
                    <span v-else class="badge badge-grey">Inactive</span>
                </td>
            </tr>   
        </tbody>
        <tbody v-else>
            <tr><td colspan="6" class="text-center">No contacts found.</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            contact: {},
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
        }
    },
    emits:['contactReload'],
    mounted() {},
    methods: {
        closeModal() {
            $('#contactFormModal').modal('hide');
        },
        deactivateContact(id) {
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                if (result.value) {
                    this.form.delete('/api/crm/contacts/'+id)
                    .then(response => {
                        this.$emit('contactReload', response);
                        this.$swal.fire('Deleted!', 'Contact has been deactivated.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        refreshPage(){
            this.$emit('contactReload');
            this.closeModal();
        },
        updateContact(contact){
            this.loading = true;
            this.contact = contact;
            this.editMode = true;
            $('#contactFormModal').modal('show');
            this.loading = false;
        }
    },
    props:{
        contacts: Array,
        source: String,
    },
    watch:{
        contacts(){}
    },
}
</script>