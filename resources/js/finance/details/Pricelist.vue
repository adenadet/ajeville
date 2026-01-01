<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-striped table-bordered">
        <tbody>
            <tr>
                <td>Name: </td>
                <td colspan="3">{{ price_list.name }} <span class="text-muted">[{{ price_list.unique_id }}]</span></td>
            </tr>
            <tr>
                <td>Type: </td><td>{{ price_list.type_name }}</td>
                <td>Status: </td><td>{{ price_list.status == 1 ? 'Active' : 'Inactive' }}</td>
            </tr>
            <tr>
                <td colspan="4" v-html="price_list.description"></td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            loading: false,
        }
    },
    mounted() {},
    methods: {},
    props:{
        source: String,
        price_list: {type: Object, default: () => {},}
    }
}
</script>