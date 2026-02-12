<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fixed Header Table</h3>

                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                            <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overlay-wrapper card-body table-responsive p-0" style="height: 600px;">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(panel, index) in panels.data" :key="panel.id">
                                <td>{{ addOne(index) }}</td>
                                <td>{{ panel.name}}</td>
                                <td>
                                    <span class="badge badge-success" v-if="panel.status == 1">Active</span>
                                    <span class="badge badge-danger" v-else>Inactive</span>
                                </td>
                                <td v-html="readMore(panel.description, 50, '...')" :title="panel.description"></td>
                                <td>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="panels.per_page != null ? panels.per_page : 52" :records="panels.total != null ? panels.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            editMode: false,
            loading: false,
            query: '',
            panel: {},
            panels: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        closeModals(){

        },
        getInitials() {
            axios.get('/api/emr/laboratory/panel_investigations?page='+this.current_page+'&query='+this.query)
            .then(response => {
                this.closeModals();
                this.refreshPage(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Panel Investigations did not load successfully',});
            });
        },
        refreshPage(response) {
            this.panels = response.data.panels;
        },
    },
    props: {}
}
</script>