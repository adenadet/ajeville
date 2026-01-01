<template>
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12"><LoanDetailSummary /></div>
                <div class="col-md-12"><LoanDetailCPM /></div>
                <div class="col-md-12"><LoanDetailFiles source="view"/></div>
                <div class="col-md-12"><LoanDetailCheckList /></div>
                <div class="col-md-12"><LoanDetailGuarantors /> </div>
                <div class="col-md-12"><LoanDetailRepayments source="risk" /> </div>
                <div class="col-md-12"><LoanDetailCreditScore source="risk" /></div>
                <div class="col-md-12"><LoanDetailConfirmations /> </div>
            </div>
        </div>
    </div>
</template>