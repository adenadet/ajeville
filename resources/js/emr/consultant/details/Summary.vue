<template>
<section class="container-fluid">
    <div class="row">
        <div class="col-md-12 table-responsive p-0">
            <table class="table table-hover text-nowrap" v-if="patient != null">
                <tbody>
                    <tr>
                      <td>Name</td><td colspan="3">{{patient | fullName}}</td>
                      <td>Hospital ID</td><td>{{ patient.unique_id}}</td>
                    </tr>
                    <tr>
                      <td>Sex</td><td>{{patient.user.sex}}</td><td>Age</td><td>{{ patient.user.dob | age}}</td><td>Age</td><td>{{ patient.user.dob | age}}</td>
                    </tr>
                    <tr>
                        <td>Allergies</td>
                        <td colspan="5">
                            
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
</section>
</template>
<script>
export default {
    data() {
        return {}
    },
    mounted() {},
    methods: {},
    props: {
        consultation: Object,
        patient: Object,
        editMode: Boolean,
        visit: Object,
    },
}
</script>