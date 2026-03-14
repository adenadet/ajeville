<template>
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label fw-bold">Customer Type</label>
                            <select class="form-control" v-model="posData.customer_type">
                                <option value="">--Select Patient Type--</option>
                                <option value="returning">Returning Patient</option>
                                <option value="walkin">Walk In</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8" v-if="posData.customer_type === 'returning'">
                        <div class="form-group">
                            <label class="form-label">Select Patient</label>
                            <select class="form-control" v-model="posData.patient_id">
                                <option value="">Select Patient</option>
                                <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patientName(patient) }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8" v-if="posData.customer_type === 'walkin'">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" id="patient_first_name" name="patient_first_name" v-model="posData.patient.first_name">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="patient_last_name" name="patient_last_name" v-model="posData.patient.last_name">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-label">Phone number</label>
                                <input type="text" class="form-control" id="patient_phone" name="patient_phone" v-model="posData.patient.phone">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" id="patient_email" name="patient_email" v-model="posData.patient.email">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-info mb-4">
                    <div class="card-header">
                        <div class="form-check">
                        <input class="form-check-input"
                                type="checkbox"
                                v-model="posData.has_prescription">
                        <label class="form-check-label fw-bold">
                            Patient has Prescription
                        </label>
                        </div>
                    </div>

                    <div v-if="posData.has_prescription" class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Doctor Name</label>
                                <input type="text" class="form-control" v-model="posData.doctor_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prescription Date</label>
                                <input type="date" class="form-control" v-model="posData.prescription_date">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Prescription Notes</label>
                            <QuillEditor theme="snow" content-type="html" class="form-control" rows="3" v-model:content="posData.prescription_notes"></QuillEditor>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Drugs</h3>
                        <div class="card-tools">
                            <button class="btn btn-default btn-sm"@click="addItem">+ Add Drug</button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <!--table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Drug</th>
                                    <th width="120">Qty</th>
                                    <th width="150">Price</th>
                                    <th width="150">Total</th>
                                    <th width="80"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in posData.items" :key="index">
                                    <td>
                                        <select class="form-control" v-model="item.drug_id" @change="setPrice(index)">
                                            <option value="">Select Drug</option>
                                            <option v-for="drug in drugs"
                                                    :key="drug.id"
                                                    :value="drug.id">
                                                {{ drug.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" v-model.number="item.quantity" @input="calculateTotal(index)">
                                    </td>
                                    <td><input type="number" class="form-control" step="0.01" v-model.number="item.price" @input="calculateTotal(index)">
                                    </td>
                                    <td>{{ item.total.toFixed(2) }}</td>
                                    <td><button class="btn btn-danger btn-sm" @click="removeItem(index)"><i class="fa fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table-->

                        <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Package Type</th>
                                <th>Package Qty</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in posData.items" :key="index">
                                <td>
                                    <input class="form-control" v-model="item.item_name" @input="searchItems(index, item.item_name)" list="item-list"/>
                                    <datalist id="item-list"><option v-for="i in searchedItems" :key="i.id" :value="i.name">{{ i.name }}</option></datalist>
                                </td>
                                <td><input class="form-control" type="number" v-model.number="item.quantity" @input="updateTotal(index)" /></td>
                                <td>
                                    <select class="form-control" v-model="item.package_id">
                                        <option v-for="p in packageTypes" :key="p.id" :value="p.id">{{ p.name }}</option>
                                    </select>
                                </td>
                                <td><input class="form-control" type="number" v-model.number="item.package_quantity" /></td>
                                <td><div class="form-control" v-html.number="item.unit_price"></div></td>
                                <td>{{ currency(item.unit_price * item.package_quantity * item.quantity) }}</td>
                                <td><button class="btn btn-danger btn-sm" @click.prevent="removeItem(index)">×</button></td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>

            <div class="card bg-light">
                <div class="card-body text-end">
                    <h4>Total: ₦ {{ grandTotal.toFixed(2) }}</h4>
                </div>
            </div>
            <div class="text-end mt-3">
                <button class="btn btn-primary"
                        @click="submitOrder">
                    Save Order
                </button>
            </div>

          </div>
        </div>
      </div>
</section>
</template>

<script>
export default {
    data() {
        return {
            patients: [],
            drugs: [],
            packageTypes: [],
            posData: new Form({
                customer_type: "returning",
                items: [],
                walkin_name: "",
                walkin_phone: "",
                has_prescription: false,
                doctor_name: "",
                prescription_date: "",
                prescription_notes: "",
                patient_id: "",
                patient: {},
            }),
            searchedItems: [],
        }
    },

    computed: {
        grandTotal() {
        return this.posData.items.reduce((sum, item) => sum + item.total, 0)
        }
    },

    methods: {
        addItem() {
            this.posData.items.push({
                item_id: "",
                quantity: 1,
                price: 0,
                total: 0
            })
        },
        calculateTotal(index) {
            const item = this.posData.items[index]
            item.total = item.quantity * item.price
        },
        removeItem(index) {
            this.posData.items.splice(index, 1)
        },
        async searchItems(index, query) {
            if (query.length < 3) return;
            const res = await axios.get(`/api/inventory/items/quick_search?q=${query}&plan_id=${this.plan_id}`);
            this.searchedItems = res.data.items;
            const selected = res.data.items.find(i => i.name === query);
            if (selected) {
                this.posData.items[index].item_id = selected.id;
                this.posData.items[index].unit_price = selected.price;
            }
        },
        setPrice(index) {
            const drug = this.drugs.find(d => d.id === this.posData.items[index].drug_id)
            if (drug) {
                this.posData.items[index].price = drug.price
                this.calculateTotal(index)
            }
        },
        submitOrder() {
            console.log("Submitting order", this.form)
            // axios.post('/api/pharmacy/orders', this.form)
        }
    }
    }
</script>
