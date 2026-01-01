<template>
<section class="card">
    <div class="card-header">
        <h3 class="card-title">Approval Matrix</h3>
    </div>
    <div class="card-body">
        <div class="timeline timeline-inverse">
            <div v-for="action in actions.data" :key="action.id">
                <i class="fa fa-sticky-note"></i>
                <div class="timeline-item">
                    <span class="time"><i class="far fa-clock"></i> {{ action.created_at | excelDate }}</span>
                    <h3 class="timeline-header"><a href="#">{{ action.creator | FullName }}</a> sent {{ action.summary }}</h3>
                    <div class="timeline-body" v-if="action.description != null" v-html="action.description"></div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>