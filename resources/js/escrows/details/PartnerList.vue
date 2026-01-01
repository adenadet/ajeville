<template>
    <div class="modal fade" id="partnerModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{editMode ? 'Update Item:'+partner.item_code : 'Create'}} Partner</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EscrowFormPartner :partner.sync="partner" :editMode.sync="editMode"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="transactionModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Start Transaction</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EscrowFormTransaction :editMode="editMode" :partner.sync="partner" :transaction="{}"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card-header bg-dark">
        <h3 class="card-title">Partners</h3>
        <div class="card-tools">
            <button class="btn btn-tool btn-sm" @click="addPartner"><i class="fa fa-plus"></i></button>
            <button class="btn btn-tool btn-sm" v-if="style == 'table'" @click="switchStyle('grid')"><i class="fa fa-table" title="Grid View"></i></button>
            <button class="btn btn-tool btn-sm" v-if="style == 'grid'" @click="switchStyle('table')"><i class="fa fa-list" title="Table View"></i></button>
            <button class="btn btn-tool btn-sm"><i class="fa fa-download"></i></button>
        </div>
    </div>
    <div v-if="style == 'table'" class="card-body table-responsive p-0" style="height: 600px;">
        <table class="table table-hover table-striped table-head-fixed text-nowrap">
            <thead>
                <tr>
                    <th>Unique ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Since</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="partners.length >= 1">
                <tr v-for="partner in partners">
                    <td class="text-dark">
                        <img :src="partner.image != null ? '/img/profile/'+partner.image : '/img/profile/default.png'" :alt="partner.unique_id" :title="partner.unique_id" class="img-circle mr-2" style="width: 20px;">
                        <span v-html="partner.unique_id"></span>
                    </td>
                    <td class="text-dark">{{ FullName(partner) }} </td>
                    <td class="text-dark">{{ partner.email}}</td>
                    <td class="text-dark">{{ partner.phone}}</td>
                    <td class="text-dark">{{ ExcelDate(partner.created_at) }}</td>
                    <td class="text-dark">
                        <button class="nav-link btn btn-sm btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <router-link :to="'./partners/'+partner.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Partner Profile</button></router-link>
                            <button class="dropdown-item btn btn-block btn-sm"  @click="addReview(partner)"><i class="fa fa-tags mr-1 text-success"></i> View Transactions</button>
                            <button class="dropdown-item btn btn-block btn-sm" @click="updatePartner(partner)"><i class="fa fa-edit mr-1 text-warning"></i> Update Partner</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr><td colspan="8">No Partner has been created</td></tr>
            </tbody>
        </table>
    </div>
    <div v-if="style == 'grid'" class="card-body">
        <div class="row">
            <div class="col-md-4 mt-3" v-for="partner in partners">
                <div class="card bg-light">
                    <div class="card-header text-muted border-bottom-0 row">
                        <h3 class="card-title col-md-9">Partner Detail</h3>
                        <div class="card-tools col-md-3">
                            <button class="btn btn-sm btn-tool text-white" data-toggle="dropdown" type="button">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right ">
                                <router-link :to="'./partners/'+partner.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Partner Profile</button></router-link>
                                <button class="dropdown-item btn btn-block btn-sm"  @click="addReview(partner)"><i class="fa fa-tags mr-1 text-success"></i> View Transactions</button>
                                <button class="dropdown-item btn btn-block btn-sm" @click="updatePartner(partner)"><i class="fa fa-edit mr-1 text-warning"></i> Update Partner</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-7">
                            <h2 class="lead"><b>{{ FullName(partner) }} </b></h2>
                            <p class="text-muted text-sm"><b>Since: </b> {{ExcelDate(partner.created_at)}} </p>
                            <ul class="ml-4 mb-0 fa-ul text-muted">
                                <li class="small"><span class="fa-li"><i class="fa fa-envelope"></i></span> Email: {{ partner.email }}</li>
                                <li class="small"><span class="fa-li"><i class="fa fa-phone"></i></span> Phone #: {{ partner.phone }}</li>
                            </ul>
                            </div>
                            <div class="col-5 text-center">
                            <img :src="'/img/profile/'+partner.image" alt="" class="img-circle img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-right">
                            <button type="button" class="btn btn-sm bg-teal"><i class="fa fa-tags"></i> View Transactions</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            loading: false,
            partner: {},
            style: 'grid',
        }
    },
    methods:{
        addPartner(){
            this.loading = true;
            this.editMode = false;
            this.partner = {};
            $('#partnerModal').modal('show');
            this.loading = false; 
        },
        deactivatePartner(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This partner will no longer be available to people who visit your page",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, deactivate it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/escrows/partners/'+id)
                    .then(response=>{
                        this.$swal.fire('Deactivated!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        startTransaction(partner){
            this.loading = true;
            this.editMode = false;
            this.partner = partner;
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        switchStyle(text){
            this.style = text;
        },
        updatePartner(partner){
            alert(partner.details);
            this.loading = true;
            this.editMode = true;
            this.partner = partner;
            $('#partnerModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        partners: Array,
        source: String,
    },
    watch:{}
}
</script>