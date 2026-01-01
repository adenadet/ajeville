<template>
<section class="overlay-wrapper p-0 m-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Modules</h3>
            <div class="card-tools"><button class="btn btn-sm btn-success" @click="addModules" :disabled="loading"><i class="fas fa-plus mr-1"></i> Add Modules</button></div>
        </div>
        <div class="card-body table-responsive p-0" style="height: 500px;">
        </div>
    </div>
</section>
</template>