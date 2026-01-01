<template>
    <div class="card-body table-responsive p-0" style="height: 600px;">
        <table class="table table-head-fixed text-nowrap">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Name</th>
                    <th>Unique ID</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Last Updater</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="price_lists.length > 0">
                <tr v-for="(price_list, index) in price_lists" :key="price_list.id">
                    <td>{{ addOne(index) }}</td>
                    <td>{{ price_list.name }}</td>
                    <td>{{ price_list.unique_id}}</td>
                    <td v-html="readMore(price_list.description, 50, '...')"></td>
                    <td>{{ price_list.branch != null ? price_list.branch.name : "No Branch Assigned" }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-tool" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars text-dark"></i></button>
                        <div class="dropdown-menu">
                            <router-link class="btn btn-block dropdown-item" :to="'/emr/operations/price_lists/'+price_list.id"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link>
                            <button class="btn btn-block dropdown-item" @click="deleteBranch(branch.id)"><i class="fa fa-trash mr-1 text-danger"></i> Deactivate Price List</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="6" class="text-center">No Price List Found</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    data() {
        return {
            price_list:{},
            
        }
    },
    emits:['refreshPage'],
    mounted() {
        //this.getInitials();
    },
    methods: {
        refreshPage() {
            this.$emit('refreshPage');
        },
        updatePlan(){},
    },
    props: {
        price_lists: Array,
        source: String,
    },
    watch:{}
}
</script>