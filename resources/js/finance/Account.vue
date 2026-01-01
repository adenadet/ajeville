<template>
    <div>
        <h2>Chart of Accounts</h2>
        <form @submit.prevent="createAccount">
            <input v-model="form.code" placeholder="Code" required />
            <input v-model="form.name" placeholder="Name" required />
            <select v-model="form.type"><option v-for="type in types" :key="type">{{ type }}</option></select>
            <button type="submit">Add Account</button>
        </form>

        <ul>
            <li v-for="account in accounts" :key="account.id">
                {{ account.code }} - {{ account.name }} ({{ account.type }})
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: { code: '', name: '', type: 'Asset' },
            accounts: [],
            types: ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense']
        };
    },
    mounted() {
        this.fetchAccounts();
    },
    methods: {
        async fetchAccounts() {
          const res = await fetch('/api/accounts');
          this.accounts = await res.json();
        },
        async createAccount() {
            await fetch('/api/accounts', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(this.form),
            });
            this.form = { code: '', name: '', type: 'Asset' };
            this.fetchAccounts();
        },
    }
};
</script>