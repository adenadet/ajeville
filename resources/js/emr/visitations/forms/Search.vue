<template>
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Select Service</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>Service Type</label>
            <select class="form-control" v-model="selectedServiceType" @change="fetchCategories">
                <option value="">-- Select Service Type --</option>
                <option v-for="type in serviceTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
            </select>
        </div>
        <div class="form-group" v-if="categories.length">
            <label>Category</label>
            <select class="form-control" v-model="selectedCategory" @change="fetchServices">
                <option value="">-- Select Category --</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
            </select>
        </div>
        <div class="form-group" v-if="services.length">
            <label>Service</label>
            <select class="form-control" v-model="selectedService">
                <option value="">-- Select Service --</option>
                <option v-for="service in services" :key="service.id" :value="service.id"> {{ service.name }}</option>
            </select>
        </div>
        <div class="form-group">
            <label>Item</label>
            <select class="form-control" v-model="selectedService">
                <option value="">-- Select Item --</option>
                <option v-for="item in items" :key="item.id" :value="item.id"> {{ item.name }}</option>
            </select>
        </div>
        <button class="btn btn-primary" @click="emitService">Add Service</button>
    </div>
</div>
</template>

<script>
export default {
    data() {
        return {
            serviceTypes: [],
            categories: [],
            services: [],

            selectedServiceType: '',
            selectedCategory: '',
            selectedService: ''
        }
    },

    mounted() {
        this.getInitials();
    },

    methods: {
        getInitials() {
            axios.get('/api/emr/settings/services/initials')
                .then(response => {
                    this.categories = response.data.categories;
                    this.services = response.data.services;
                    this.serviceTypes = response.data.service_types;
                });
        },

        fetchCategories() {
            this.selectedCategory = '';
            this.selectedService = '';
            this.categories = [];
            this.services = [];

            if (!this.selectedServiceType) return;

            axios.get(`/api/emr/settings/services/categories/${this.selectedServiceType}`)
                .then(response => {
                    this.categories = response.data.categories;
                    this.services = response.data.services
                });
        },

        fetchServices() {
            this.selectedService = '';
            this.services = [];

            if (!this.selectedCategory) return;

            axios.get(`${this.apiBase}/categories/${this.selectedCategory}/services`)
                .then(response => {
                    this.services = response.data;
                });
        },

        emitService() {
            if (!this.selectedService) return;

            const selected = this.services.find(
                s => s.id === this.selectedService
            );

            this.$emit('service-selected', selected);
        }
    }
}
</script>