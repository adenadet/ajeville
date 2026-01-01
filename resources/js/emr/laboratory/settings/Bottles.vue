<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Laboratory Bottles</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(bottle, index) in bottles.data" :key="bottle.id">
                                    <td>{{index | addOne}}</td>
                                    <td>{{bottle.name}}</td>
                                    <td>{{bottle.status == 1 ? 'Active' : 'Inactive'}}</td>
                                    <td>{{bottle.description}}</td>
                                    <td></td>
                                </tr>    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <LaboratoryFormBottle :editMode="editMode" />
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            bottles: {},
            bottle: {},
            editMode: true,
            form: new Form({}),
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials(page = 1){
            axios.get('/api/emr/laboratory/bottles?page='+page)
            .then(response => {
                this.bottles = response.data.bottles;
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Your bottles did not loaded successfully',})
            });
        },
    },
    props: {
    }
}
</script>