<template>
<section class="overlay-wrapper p-0">
    <div class="card-body">
        <strong><i class="fas fa-book mr-1"></i> Bank</strong>
        <p class="text-muted">{{branch_account.bank != null ? branch_account.bank.name : 'N/A'}}</p>
        <hr>

        <strong><i class="fas fa-map-marker-alt mr-1"></i> Account Name</strong>
        <p class="text-muted">{{ branch_account.account_name }}</p>
        <hr>

        <strong><i class="fas fa-map-marker-alt mr-1"></i> Account Number</strong>
        <p class="text-muted">{{ branch_account.account_number }}</p>
        <hr>

        <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

        <p class="text-muted">
            <span class="tag tag-danger">UI Design</span>
            <span class="tag tag-success">Coding</span>
            <span class="tag tag-info">Javascript</span>
            <span class="tag tag-warning">PHP</span>
            <span class="tag tag-primary">Node.js</span>
        </p>

        <hr>

        <strong><i class="far fa-file-alt mr-1"></i> Description</strong>

        <p class="text-muted">{{ branch_account.description }}</p>
    </div>
</section>
</template>