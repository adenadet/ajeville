<template>
<section class="overlay-wrapper">
    <table class="table table-bordered table-stripped">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Procedure</th>
                <th>ASA</th>
                <th>Anesthesia</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="cases.length > 0">
            <tr v-for="item in cases" :key="item.id">
                <td>{{ item.patient.name }}</td>
                <td>{{ item.procedure.name }}</td>
                <td>{{ item.asa_class }}</td>
                <td>{{ item.anesthesia_type }}</td>
                <td>
                    <span class="badge bg-info">{{ item.status_label }}</span>
                </td>
                <td>
                    <router-link
                        :to="`/emr/anesthesia/cases/${item.id}`"
                        class="btn btn-sm btn-primary">
                        Open
                    </router-link>
                </td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <td colspan="6">No Request Cases meets your requirements</td>
            </tr>
        </tbody>
    </table>
</section>
</template>

<script>
export default {
    data() {
        return {
            case: {},
            loading: false,
        }
    },
    emits:['refreshCaseList'],
    methods: {
        async getCases() {
            const res = await axios.get('/api/emr/anesthesia/cases')
            this.cases = res.data
        }
    },
    mounted() {
        //this.getCases()
    },
    props:{
        cases: Array,
    }

}
</script>
