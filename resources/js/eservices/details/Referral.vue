<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-12" v-if="appointment.patient != null && appointment != null && referral != null">
                <div class="invoice p-3 mb-3" style="font-size:2rem !important ">
                    <div class="row">
                        <div class="col-12">
                            <img class="img-fluid" :src="'/img/background/SNH_logo.png'" />
                            <h3 class="text-center"  style="font-size:4rem !important; font-weight:bold; "> PATIENT REFERRAL FORM</h3>
                        </div>
                    </div>
             
                    <div class="row invoice-info">
                        <div class="col-sm-7 invoice-col">
                            <address style="font-size:2rem !important">
                                REF: <u><b>{{ appointment.patient != null ? FullName(appointment.patient) : '' }}</b></u><br>
                                SNH No: <u><b>{{ appointment.unique_id }}</b></u><br>
                                Date of Birth: <u><b>{{ appointment.patient != null ? ExcelDate(appointment.patient.dob) : 'Loading'  }}</b></u><br>
                            </address>
                        </div>
                        <div class="col-sm-5 invoice-col">
                            <address style="font-size:2rem !important; text-align: right;">
                                Date:  <u><b>{{ referral != null ? ExcelDate( referral.updated_at ) : ''}}</b></u><br>
                            </address>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12"  style="font-size:2rem !important ">
                            <h3 style="font-size:3rem !important; font-weight: bold;">MEDICAL HISTORY</h3>
                            <div class="" v-html="treatFont(referral.details)"></div>
                            <div class="">
                                <strong>Yours Sincerely,<br />
                                For: St. Nicholas Hospital, </strong><br />

                                <img :src="referral.created_by == 56 ? '/img/consents/rusman.png' : (referral.created_by == 57 ? '/img/consents/rabudah.png' :(referral.created_by == 55 ? '/img/consents/bsalami.png' :(referral.created_by == 331 ? '/img/consents/mnwachukwu.png' :'/img/consents/bsalami.png')))" class="img-fluid" />
                                <br /><strong>Dr. {{FullName(referral.creator)}}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12" v-else>
                Loading
            </div>
        </div>
    </section>
</template>
<script>
export default{
    props:{
        appointment: Object,
        referral: Object,
    },
}
</script>
