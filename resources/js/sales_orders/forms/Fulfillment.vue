<template>
    <div>
        <h2 class="text-xl font-bold mb-4">Fulfill Order: {{ order.unique_id }}</h2>

        <div v-for="item in order.order_items" :key="item.id" class="mb-6 border p-4 rounded">
            <h3 class="font-semibold text-lg">{{ item.item.name }} (Qty: {{ item.quantity }})</h3>

            <div v-for="batch in availableBatches(item.item_id)" :key="batch.id" class="flex gap-2 items-center mt-2">
                <span>Batch ID: {{ batch.batch_id }} | Balance: {{ batch.balance }}</span>
                <input type="number" v-model.number="form[item.id][batch.id]" min="0" :max="batch.balance" class="border p-1 w-24"/>
            </div>
        </div>

        <button @click="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Submit Fulfillment</button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            fulfillmentData: new Form({
                fulfillments: []
            }),
        }
    },
    mounted() {
        //this.getInitials();
    },
    methods: {
        initializeForm() {
            this.order.order_items.forEach(item => {
                this.form[item.id] = {};
                this.batches
                    .filter(batch => batch.item_id === item.item_id)
                    .forEach(batch => {
                        this.form[item.id][batch.id] = 0;
                    });
            });
        },

        collectFulfillments() {
            this.fulfillmentData.fulfillments = [];

            Object.entries(this.form).forEach(([itemId, batches]) => {
                Object.entries(batches).forEach(([batchId, qty]) => {
                    if (qty > 0) {
                        this.fulfillmentData.fulfillments.push({
                            so_item_id: itemId,
                            batch_id: batchId,
                            quantity: qty
                        });
                    }
                });
            });
        },

        submitFulfillment() {
            this.$Progress.start();
            this.collectFulfillments();

            this.fulfillmentData.post('/fulfillments')
            .then(() => {
                this.$toast.fire({
                    icon: 'success',
                    title: 'Fulfillment submitted successfully!',
                });
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Failed to submit fulfillment',
                });
            })
            .finally(() => {
                this.loading = false;
            });
        }
    },
    props: {
        batches: Array,
        order: Object,
    }
}
const form = ref({})

// Pre-fill form with zeros
props.order.order_items.forEach(item => {
  form.value[item.id] = {}
  props.batches
    .filter(b => b.item_id === item.item_id)
    .forEach(batch => {
      form.value[item.id][batch.id] = 0
    })
})

const availableBatches = (itemId) => props.batches.filter(b => b.item_id === itemId)

const submit = () => {
  const fulfillments = []
  for (const [itemId, batches] of Object.entries(form.value)) {
    for (const [batchId, qty] of Object.entries(batches)) {
      if (qty > 0) {
        fulfillments.push({
          so_item_id: itemId,
          batch_id: batchId,
          quantity: qty,
        })
      }
    }
  }

  router.post('/fulfillments', { fulfillments }, {
    onSuccess: () => alert('Fulfillment submitted!')
  })
}
</script>
