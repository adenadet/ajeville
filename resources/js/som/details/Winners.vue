<template>
<div class="card card-primary">
    <div class="card-header">Staffs of the Month</div>
    <div class="card-body overlay-dark">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-10 offset-1">
                <carousel :items-to-show="1.5">
                    <CarouselSlide v-for="slide in items" :key="slide.id">
                        <div class="card m-3">
                            <div class="card-header bg-primary"><h5>{{ slide.branch ? slide.branch.name : 'Branch' }} - {{ ExcelMonthYear(month.month) }}</h5> </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8 offset-2 text-center">
                                        <img :src="(slide.user) && (slide.user.image) ? '/img/profile/'+slide.user.image : '/img/profile/default.png'" class="img-fluid" />
                                        <h5 class="">{{ FullName(slide.user)}}</h5>
                                        <p>{{slide.user.department.name}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CarouselSlide>

                    <template #addons>
                        <CarouselNavigation />
                        <CarouselPagination />
                    </template>
                </carousel>
            </div>
        </div>
    </div>
</div>
</template>
<script>
import Form from 'vform';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
export default {
    data(){
        return  {
            items: [],
            loading: false,
            staff_months: {},
        }
    },
    mounted() {},
    methods:{
        refresh(response){
            this.staff_months = response.data.som_months;
            this.items = response.data.staff_months;
        },
        showSOM(id){
            axios.get('/api/som/winners/'+id).then(response =>{
                this.refresh(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Winners were loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Winners were not loaded successfully',
                })
            });            
        },
    },
    props:{branch: Object, editMode: Boolean, users: Array, month: Object,},
    watch:{
        month(){
            if(this.month != null){
                this.items = this.month.winners;
            }
        }
    }
}
</script>
