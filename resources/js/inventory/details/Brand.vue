<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="brand.id == null"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <strong><i class="fas fa-book mr-1"></i> Name</strong>
    <p class="text-muted">{{ brand.name }}</p>
    <hr>
    <strong><i class="fas fa-map-marker-alt mr-1"></i> Status</strong>
    <p class="text-muted">{{ firstUp(brand.status) }}</p>
    <hr>
    <strong><i class="fas fa-pencil-alt mr-1"></i> Description</strong>
    <span class="text-muted" v-html="brand.description"></span>
    <hr>
</section>
</template>
<script>
export default {
    data(){
        return  {};
    },
    emits: ['itemReload'],
    props:{
        brand: Object,
    },
}
</script>