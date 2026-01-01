<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-5">
            Put Maintenance Detail Here
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    Movement History
                </div>
                <div class="card-body table-responsive p-0 ovelay-wrapper">
                    <div class=""></div>
                    <FinanceDetailAssetList :assets="assets.data" @refreshAssetList="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>

</section>
</template>