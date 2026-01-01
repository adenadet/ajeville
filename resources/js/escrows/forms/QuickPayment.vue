<template>
    <div class="container payment-container overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-6 image-section d-none d-md-block"></div>

            <div class="col-md-6 payment-section d-flex flex-column justify-content-center">
                <img src="/img/logo/nairafy-horizontal-logo.png" class="img-fluid mb-3" />

                <div class="payment-header row">
                    <div class="company-info" v-if="transaction.buyer">
                        <div><strong>Name:</strong> {{ buyerName }}</div>
                        <div><strong>Email:</strong> {{ transaction.buyer.email }}</div>
                        <div><strong>Amount:</strong> {{ currency(transaction.amount) }}</div>
                    </div>
                    <div class="company-logo text-end" v-if="transaction.seller?.company != null">
                        <img :src="companyLogo" :alt="companyName" />
                        <br /><span v-html="companyName"></span>
                    </div>
                    <div class="company-logo text-end" v-else>
                        Pay: 
                        <br /><span>{{ sellerName }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-center">Make Payment to Nairafy</h3>
                </div>

                <div class="payment-buttons">
                    <!--AlatpayButton
                        class="btn btn-danger btn-block"
                        :apiKey="alatProd"
                        :businessId="alatKey"
                        :email="email"
                        :phoneNumber="phone"
                        :firstName="first_name"
                        :lastName="last_name"
                        :amount="Math.round((transaction.amount || amount) * 1.03)"
                        :transactionId="genRef('alat')"
                        :onTransaction="response => processPayment('alat', response)"
                        :onFailure="() => failedPayment('alatpay')">
                    {{ 'Pay ' + currency(amount) + ' with AlatPay' }}
                    </AlatpayButton-->

                    <!--button class="btn btn-warning text-white">Pay with QuickTeller</button-->

                    <paystack
                        class="btn btn-primary btn-block"
                        :publicKey="publicKey"
                        :email="transaction.buyer?.email"
                        :amount="paystackAmount"
                        :reference="genRef('paystack')"
                        :onSuccess="response => processPayment('paystack', response)"
                        :onCancel="() => failedPayment('paystack')"
                        :buttonText="'Pay ' + currency(transaction.amount) + ' with Paystack'"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import paystack from 'vue3-paystack'
import { Form } from 'vform'

export default {
    components: { paystack },
    data() {
        return {
            loading: false,
            reference_alat: null,
            reference_paystack: null,
            reference_quickteller: null,
            publicKey: 'pk_live_c2fded4469321ca5e78eeb29437b0e0be724daf4',
            publicKeyTest: 'pk_test_c07062fc3a1a099b9e85312f8b55841d996bd896',
            alatKey: 'ecea8c7f-3663-44c9-455b-08dcf53d02a7',
            alatProd: 'f230b3d136b24599a8db7c01e8afd51b',
            paymentData: new Form({
                channel: '',
                amount: 0,
                transaction_id: null,
                payment_transaction: null,
                payment_reference: null,
            }),
        }
    },
    emits: ['paymentFailed'],
    computed: {
        buyerName() {
            if (!this.transaction?.buyer) return ''
            return this.transaction.buyer.name || 
                   `${this.transaction.buyer.first_name || ''} ${this.transaction.buyer.last_name || ''}`.trim() || 
                   'Unknown Buyer'
        },
        sellerName() {
            if (!this.transaction?.seller) return ''
            return `${this.transaction.seller.first_name || ''} ${this.transaction.seller.last_name || ''}`.trim() || 
                   'Unknown Seller'
        },
        companyLogo() {
            return this.transaction?.seller?.company?.logo || '/img/logo/default.png'
        },
        companyName() {
            return this.transaction?.seller?.company?.name || 'Trial Company'
        },
        paystackAmount() {
            // Paystack expects amount in kobo (smallest currency unit)
            // Adding 3% fee: amount * 1.03 * 100
            return Math.round((this.transaction?.amount || 0) * 103)
        }
    },
    methods: {
        genRef(type) {
            const prefixMap = {
                alat: 'ALT_',
                paystack: 'PSK_',
                quickteller: 'QCK_',
            }
            const ref = (prefixMap[type] || 'TRX_') + new Date().valueOf()

            if (type === 'alat') this.reference_alat = ref
            else if (type === 'paystack') this.reference_paystack = ref
            else if (type === 'quickteller') this.reference_quickteller = ref

            return ref
        },
        processPayment(channel, response) {
            if (!this.transaction?.id) return

            this.loading = true
            this.paymentData.channel = channel
            this.paymentData.transaction_id = this.transaction.id
            this.paymentData.amount = this.transaction.amount || 0
            this.paymentData.payment_transaction = response?.reference || response?.trans || null
            this.paymentData.payment_reference =
                channel === 'paystack' ? this.reference_paystack :
                channel === 'quickteller' ? this.reference_quickteller :
                this.reference_alat

            this.paymentData.put(`/api/payments/quick_payments/${this.transaction.unique_code}`)
            .then(response => {
                console.log('Payment processed:', response.data);
                this.$swal.fire({
                    icon: 'success',
                    title: `Transaction "${this.transaction.unique_code}" received.`,
                    footer: channel === 'transfer'
                    ? 'Confirmation may take up to 24 hours.'
                    : 'Check your email for your receipt.',
                    showConfirmButton: false,
                    timer: 1500
                });
                if (window !== window.parent) {
                    window.parent.postMessage({
                        type: 'paymentSuccessful',
                        payload: response?.data || {}
                    }, '*')
                }
            })
            .catch(() => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!',
                })
            })
            .finally(() => {
                this.loading = false
            })
        },
        alatPayProcess(response) {
            this.processPayment('alat', response)
        },
        completePayment() {
            window.parent.postMessage({
                type: 'paymentSuccessful',
                payload: {
                transactionId: this.transaction?.id || null,
                amount: this.transaction?.amount || null,
                channel: 'quickteller'
                }
            }, '*');
        },
        failedPayment(method) {
            this.$emit('paymentFailed', method)
            this.$swal.fire({
                icon: 'error',
                title: `${method} payment failed`,
                text: 'Please try again later.',
            })
        },
    },
    mounted(){},
    props:{
        transaction: Object,
    },
    watch: {
        transaction() {
            this.paymentData.transaction_id = this.transaction?.id || null;
            this.paymentData.amount = this.transaction?.amount || 0;
        }
    }
}
</script>

<style scoped>
.payment-container {
    min-height: 70vh;
    display: flex;
}
.image-section {
    background: url('/img/background/nairafy_background.jpg') no-repeat center center;
    background-size: cover;
}
.payment-section {
    background: #f8f9fa;
    padding: 2rem;
}
.payment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}
.company-info {
    font-size: 1rem;
}
.company-logo img {
    height: 60px;
}
.payment-buttons .btn {
    width: 100%;
    margin-bottom: 1rem;
    padding: 1rem;
    font-size: 1.1rem;
}
</style>
