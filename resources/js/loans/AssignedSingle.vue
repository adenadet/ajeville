<template>
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12"><LoanDetailSummary /></div>
                <div class="col-md-12"><LoanDetailCPM source="account"/></div>
                <div class="col-md-12"><LoanDetailFiles source="account"/></div>
                <div class="col-md-12"><LoanDetailCheckList source="account" /></div>
                <div class="col-md-12"><LoanDetailGuarantors /> </div>
                <div class="col-md-12"><LoanDetailRepayments /> </div>
                <div class="col-md-12"><LoanDetailCreditScore  source="account"/></div>
                <div class="col-md-12"><LoanDetailConfirmations /> </div>
            </div>
        </div>
    </div>
</template>