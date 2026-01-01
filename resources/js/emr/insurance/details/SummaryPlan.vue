<template>
    <section>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Plan Summary</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover table-stripped">
                    <tbody>
                        <tr>
                            <td>Name</td>
                            <td colspan="2">{{ plan.name }}</td>
                        </tr>
                        <tr>
                            <td>Provider</td>
                            <td colspan="2">{{ plan.provider != null ? plan.provider.name : 'Inactive/No Provider' }}</td>
                        </tr>
                        <tr>
                            <td>Patients</td>
                            <td colspan="2">{{ plan.patients != null ? plan.patients.length : 0 }}</td>
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
        return {
        }
    },
    mounted() {
        Fire.$on('updatedPlan', response => {
            this.closeModal();
        })
    },
    methods: {
        branchAllocation(){
            this.editMode = false;
            Fire.$emit('planBranchUpdate', this.plan);
            $('#planBranchModal').modal('show');
        },
        closeModal(){
            $('#planBranchModal').modal('hide');
        }
    },
    props: {
        plan: Object,
    }
}
</script>