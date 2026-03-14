<template>
<section class="overlay-wrapper p-0">
    <strong><i class="fas fa-book mr-1"></i> Asset Name</strong>
    <p class="text-muted">{{ asset.name }}</p>
    <hr>
    <strong><i class="fas fa-map-marker-alt mr-1"></i> Category</strong>
    <p class="text-muted">{{ asset.category != null ? asset.category.name : 'N/A' }}</p>
    <hr>

    <strong><i class="fas fa-pencil-alt mr-1"></i> Location</strong>
    <p class="text-muted">{{ asset.location != null ? asset.location.name : 'N/A' }}</p>
    <hr />

    <strong><i class="fas fa-user-circle mr-1"></i> Assigned To:</strong>
    <p class="text-muted">{{ asset.assignedUser != null ? FullName(asset.assignedUser) : 'Not Assigned to a User' }}</p>
    <hr />

    <strong><i class="fas fa-cash-register mr-1"></i> Purchase Value:</strong>
    <p class="text-muted">{{ currency(asset.purchase_value)}}</p>
    <hr />

    <strong><i class="fas fa-user-circle mr-1"></i> Current Value:</strong>
    <p class="text-muted">{{ current_value}}</p>
    <span class="text-sm text-muted"> (Calculated based on depreciation rate of {{ asset.depreciation_rate }}% from the {{ ExcelDate(asset.acquisition_date) }})</span>
    <hr />
    
    <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>
    <p class="text-muted" v-html="asset.description"></p>

</section>
</template>
<script>
import EquipmentFormAsset from '@/equipments/forms/Asset.vue';
export default {
    components:{
        EquipmentFormAsset,
    },
    computed:{
        current_value(){
            if (this.asset.acquisition_date == null || this.asset.purchase_value == null || this.asset.depreciation_rate == null){
                return 'N/A';
            }
            let acquisitionDate = new Date(this.asset.acquisition_date);
            let currentDate = new Date();
            let yearsDiff = currentDate.getFullYear() - acquisitionDate.getFullYear();
            let depreciationAmount = (this.asset.purchase_value * (this.asset.depreciation_rate / 100)) * yearsDiff;
            let currentValue = this.asset.purchase_value - depreciationAmount;
            console.log(depreciationAmount)
            return currentValue > 0 ? this.currency(currentValue) : this.currency(0);
        },
    },
    data(){
        return  {
            loading: false,
        }
    },
    emits: ['refreshAssetDetail'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createAsset(){
            this.loading = true;
            this.assetData.post('/api/equipments/assets')
            .then( () =>{
                this.$emit('refreshAssetForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Branch Account detail has been captured',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;    
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/equipments/assets/initials')
            .then(response =>{
                this.asset_types = response.data.asset_types;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Branch Account Form not loaded successfully',})
            });
            this.loading = false;
        },
        updateAsset(){
            this.loading = true;
            this.assetData.put('/api/equipments/assets/'+this.assetData.id)
            .then(() => {
                this.$emit('refreshAssetForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Asset detail has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;            
        },
    },
    props:{
        asset: Object,
        source: String,
    },
    watch:{
        asset(){
        }
    }
}
</script>