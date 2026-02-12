<template>
<section>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Services</h3>
                <div class="card-tools">
                    <div class="input-group" style="width: 350px;">
                        <input type="text" name="table_search" v-model="query" class="form-control mr-1" placeholder="Search">
                        <div class="input-group-append">
                            <select class="form-control ml-1" v-model="type">
                                <option value="all">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <button type="button" class="btn btn-default ml-1" @click="getAllInitials"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 600px">
                <EMRAdmissionDetailServiceList :services="services.data" @refreshServiceList="getAllInitials" />
            </div>
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="services.per_page != null ? services.per_page : 52" :records="services.total != null ? services.total : 550" ></pagination>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            departments: [],
            editMode: false,
            loading: false,
            query: '',
            services: {data: [], total: 0},
            service_types: [],
            specialties: [],
            statuses: ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled', 'no_show'],
            type: 'active',
        }
    },
    methods: {
        getAllInitials(){
            this.loading = true
            axios.get('/api/emr/admissions/services?type='+this.type+'&query='+this.query+'&page='+this.current_page)
            .then(res => {
                this.services = res.data.services;
            })
            .finally(() => {
                this.loading = false
            })
        },
        statusClass(status) {
            return {
                'bg-secondary': status === 'pending',
                'bg-primary': status === 'confirmed',
                'bg-success': status === 'checked_in',
                'bg-dark': status === 'completed',
                'bg-danger': status === 'cancelled',
                'bg-warning': status === 'no_show'
            }
        },
        viewAppointment(a) {
            this.$router.push(`/appointments/${a.id}`)
        },
    },
    mounted() {
        this.getAllInitials()
        //this.fetchDepartments()
        //this.fetchAppointments()
    },
    watch: {
        filters: {
            deep: true,
            handler() {
                this.getAllInitials();
            }
        }
    }
}
</script>