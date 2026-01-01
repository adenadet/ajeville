<template>
<section class="content">
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Department Details</h3>
            <!--div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
            </div-->
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1" style="height: 500px; overflow-y: auto">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch" v-for="employee in department.employees">
                            <div class="card bg-light">
                                <div class="card-header text-muted">{{ employee.designation != null ? employee.designation.name : 'Staff' }}</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-7">
                                            <h2 class="lead"><b>{{ FullName(employee.user) }}</b></h2>
                                        </div>
                                        <div class="col-5 text-center">
                                            <img :src="employee.user != null ? '/img/profile/'+employee.user.image :'/img/profile/default.png'" alt="" class="img-circle img-fluid">
                                        </div>
                                        <div class="col-12">
                                            <ul class="ml-4 mb-0 fa-ul text-muted">
                                                <li class="small"><span class="fa-li"><i class="fa fa-envelope"></i></span> Email Address: {{employee.user != null ? employee.user.email : ''}}</li>
                                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone: {{employee.user != null ? employee.user.phone : ''}}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-right">
                                        <a href="#" class="btn btn-sm bg-teal">
                                        <i class="fas fa-comments"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-primary">
                                        <i class="fas fa-user"></i> View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2">
                    <h3 class="text-primary"><i class="fas fa-paint-brush"></i> {{ department.name }}</h3>
                    <p class="text-muted" v-html="department.description"></p>
                    <br>
                    <div class="text-muted">
                        <p class="text-sm">Head of Department
                            <b class="d-block">{{ department.hod != null && department.hod.user != null ? FullName(department.hod.user) : 'Old Staff' }}</b>
                        </p>
                        <p class="text-sm">Email
                            <b class="d-block">{{ department.email }}</b>
                        </p>
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
            department: {},
            departments: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        deleteDepartment(id){
            alert("Working")
        },
        getInitials() {
            this.loading = true;
            axios.get('/api/operations/departments/'+this.$route.params.id).then(response => {
                this.loading = false;
                this.refreshPage(response);
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'The departments did not load successfully',
                })
            });
        },
        refreshPage(response) {this.department = response.data.department;},
        updatePlan(){},
    },
    props: {}
}
</script>