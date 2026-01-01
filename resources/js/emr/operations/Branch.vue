<template>
<section class="container-fluid">
    <div class="modal fade" id="branchFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Branch Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <OperationFormBranch :branch.sync="branch" :editMode="editMode" source="emr" @refreshBranchForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Branch Detail</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" @click="editBranch()"><i class="fas fa-edit"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                <i class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove">
                <i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
                    <OperationDetailBranch :branch.sync="branch" source="emr" />
                </div>
                <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2">
                    <h3 class="text-primary"><i class="fas fa-home"></i> {{ branch.name }} branch</h3>
                    <p class="text-muted">Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi, qui irure terr.</p>
                    <br>
                    <div class="text-muted">
                        <p class="text-sm">Address
                        <b class="d-block" v-html="branch.address"></b>
                        </p>
                    </div>

                    <h5 class="mt-5 text-muted">Price Lists</h5>
                        <ul class="list-unstyled">
                            <li><a href="" class="btn-link text-secondary"><i class="far fa-fw fa-file-word"></i> Functional-requirements.docx</a></li>
                            <li><a href="" class="btn-link text-secondary"><i class="far fa-fw fa-file-pdf"></i> UAT.pdf</a></li>
                            <li><a href="" class="btn-link text-secondary"><i class="far fa-fw fa-envelope"></i> Email-from-flatbal.mln</a></li>
                            <li><a href="" class="btn-link text-secondary"><i class="far fa-fw fa-image "></i> Logo.png</a></li>
                            <li><a href="" class="btn-link text-secondary"><i class="far fa-fw fa-file-word"></i> Contract-10_12_2014.docx</a></li>
                        </ul>
                    <div class="text-center mt-5 mb-3">
                        <!--router-link :to="'/emr/operations/branch_price_lists/'+$route.params.id" class="btn btn-sm btn-primary">See Price Lists</router-link>
                        <a href="#" class="btn btn-sm btn-warning">Report contact</a-->
                    </div>
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
            branch: {},
            branch: {},
            current_page: 1,
            loading: false,
            query: '',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        editBranch(branch){
            this.loading = true;
            this.branch = branch;
            this.editMode = true;
            $('#branchFormModal').modal('show');
            this.loading = false;
        },
        getInitials() {
            this.loading = true;
            axios.get('/api/operations/branches/'+this.$route.params.id).then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'The branches did not load successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {this.branch = response.data.branch;},
        updatePlan(){},
    },
    props: {}
}
</script>