<template>
<section class="overlay-wrapper p-0">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Uncollected Queue</h3>
            <div class="card-tools">
                <div class="input-group" style="width: 450px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        <select class="form-control ml-3">
                            <option value="1">Paid</option>
                            <option value="1">Unpaid</option>
                        </select>
                        <button class="btn btn-primary ml-3" type="button" @click="addRequest"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0" style="height: 500px;">
            <EMRRadiologyDetailRequestList :requests="requests.data" view="main" />
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getInitials" :per-page="requests.per_page != null ? requests.per_page : 52" :records="requests.total != null ? requests.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            editMode: true,
            requests: {data: []}
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addRequest(){
            this.editMode = false;
            this.request = {};
            //Fire.$emit('AppointmentDataFill', {});
            $('#requestFormModal').modal('show');
        },
        getInitials(page=1) {
            axios.get('/api/emr/radiology/requests?page='+page)
            .then(response => {
                this.refreshList(response)
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        refreshList(response) {
            this.requests = response.data.requests;
        }
    },
    props: {}
}
</script>