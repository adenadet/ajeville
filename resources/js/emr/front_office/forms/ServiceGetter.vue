<template>
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">Service Selection</h3>
        </div>

        <form @submit.prevent="sendOut">
            <div class="card-body">

                <!-- Service Type -->
                <div class="form-group">
                    <label class="required">Service Type</label>
                    <select
                        class="form-control"
                        v-model="itemForm.service_type_id"
                        @change="onServiceTypeChange"
                    >
                        <option value="">-- Select Service Type --</option>
                        <option
                            v-for="type in service_types"
                            :key="type.id"
                            :value="type.id"
                        >
                            {{ type.name }}
                        </option>
                    </select>
                </div>

                <!-- Optional Sub Category -->
                <div class="form-group" v-if="showSubCategory">
                    <label>Sub Category</label>
                    <model-list-select
                        class="form-control"
                        :list="availableCategories"
                        v-model="itemForm.category_id"
                        option-value="id"
                        option-text="name"
                        placeholder="Select Sub Category"
                        @input="onCategoryChange"
                    />
                </div>

                <!-- Price List -->
                <div class="form-group" v-if="source === 'price_list'">
                    <label>Service Name</label>
                    <input
                        type="text"
                        class="form-control"
                        v-model="itemForm.item_name"
                        placeholder="Enter service name"
                    />
                </div>

                <!-- Patient Services (Multiple) -->
                <div class="form-group" v-if="source === 'patient_service'">
                    <label>Services</label>
                    <multiselect
                        v-model="itemForm.items"
                        :options="filteredItems"
                        :multiple="true"
                        :taggable="true"
                        :close-on-select="false"
                        :clear-on-select="false"
                        :custom-label="itemName"
                        track-by="id"
                        placeholder="Select services"
                        @tag="addTag"
                    />
                </div>

                <!-- Single Service -->
                <div class="form-group" v-else>
                    <label>Service / Item</label>
                    <model-list-select
                        class="form-control"
                        :list="filteredItems"
                        v-model="itemForm.item_id"
                        option-value="id"
                        :custom-text="itemName"
                        placeholder="Select Service"
                    />
                </div>

            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    Submit
                </button>
            </div>
        </form>
    </section>
</template>

<script>
import { ModelListSelect } from 'vue-search-select';

export default {
    components: { ModelListSelect },

    props: {
        source: { type: String, default: '' }
    },

    emits: ['ServiceFinderExtract'],

    data() {
        return {
            loading: false,

            itemForm: new Form({
                service_type_id: '',
                category_id: '',
                item_id: '',
                item_name: '',
                items: []
            }),

            service_types: []
        };
    },

    computed: {
        selectedServiceType() {
            return this.service_types.find(
                s => s.id === this.itemForm.service_type_id
            ) || null;
        },

        availableCategories() {
            return this.selectedServiceType?.categories || [];
        },

        showSubCategory() {
            return this.availableCategories.length > 0;
        },

        filteredItems() {
            if (!this.selectedServiceType) return [];

            let services = this.selectedServiceType.services || [];

            if (this.itemForm.category_id) {
                services = services.filter(
                    s => s.category_id === this.itemForm.category_id
                );
            }

            return services;
        }
    },

    mounted() {
        this.loadInitials();
    },

    methods: {
        loadInitials() {
            this.loading = true;

            axios.get('/api/emr/hims/services/initials')
                .then(({ data }) => {
                    this.service_types = data.service_types || [];
                })
                .catch(() => {
                    this.$toast.fire({
                        icon: 'error',
                        title: 'Failed to load services'
                    });
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        onServiceTypeChange() {
            this.itemForm.category_id = '';
            this.itemForm.item_id = '';
            this.itemForm.items = [];
        },

        onCategoryChange() {
            this.itemForm.item_id = '';
            this.itemForm.items = [];
        },

        addTag(newTag) {
            this.itemForm.items.push({
                name: newTag,
                is_custom: true
            });
        },

        itemName(service) {
            if (!service) return '';

            if (service.item) {
                return `${service.item.name} [${service.item.unique_id}]`;
            }

            return service.name || 'Unknown Service';
        },

        sendOut() {
            this.$emit('ServiceFinderExtract', this.itemForm);
            this.itemForm.reset();
        }
    }
};
</script>
