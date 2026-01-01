<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4"><LoanFormConfirm /></div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12"><LoanDetailSummary source="staff"/></div>
                    <div class="col-md-12"><LoanDetailCPM /></div>
                    <div class="col-md-12"><LoanDetailCheckList /></div>
                    <div class="col-md-12"><LoanDetailFiles source="account"/></div>
                    <div class="col-md-12"><LoanDetailGuarantors /> </div>
                    <div class="col-md-12"><LoanDetailCreditScore source="account" /></div>
                    <div class="col-md-12"><LoanDetailConfirmations /> </div>
                </div>
            </div>
        </div>
    </div>
</template>