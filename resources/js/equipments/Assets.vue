<template>
<section class="row" v-if="assets.length > 0">
    <div class="overlay-header">
        <h2>Assets</h2>
        <button @click="closeOverlay" class="btn btn-close">Close</button>
    </div>
</section>
</template>