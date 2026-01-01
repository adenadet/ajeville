<template>
    <div>
        <h2>Create Journal Entry</h2>
        <form @submit.prevent="submitEntry">
            <input v-model="form.date" type="date" required />
            <textarea v-model="form.description" placeholder="Description" required></textarea>

            <div v-for="(line, i) in form.lines" :key="i">
                <select v-model="line.account_id">
                <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                    {{ acc.name }} ({{ acc.code }})
                </option>
                </select>
                <input v-model.number="line.debit" placeholder="Debit" />
                <input v-model.number="line.credit" placeholder="Credit" />
            </div>

            <button type="button" @click="addLine">Add Line</button>
            <button type="submit">Post Entry</button>
        </form>

        <h3>Journal Entries</h3>
        <ul>
        <li v-for="entry in entries" :key="entry.id">
            <strong>{{ entry.date }}</strong>: {{ entry.description }}
            <ul>
            <li v-for="line in entry.lines" :key="line.id">
                {{ line.account.name }} - Dr: {{ line.debit }} | Cr: {{ line.credit }}
            </li>
            </ul>
        </li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                date: '',
                description: '',
                lines: [{ account_id: '', debit: 0, credit: 0 }],
            },
            accounts: [],
            entries: [],
        };
    },
    mounted() {
        this.loadAccounts();
        this.loadEntries();
    },
    methods: {
        addLine() {
            this.form.lines.push({ account_id: '', debit: 0, credit: 0 });
        },
        async loadAccounts() {
        const res = await fetch('/api/accounts');
        this.accounts = await res.json();
        },
        async loadEntries() {
        const res = await fetch('/api/journal-entries');
        this.entries = await res.json();
        },
        async submitEntry() {
        await fetch('/api/journal-entries', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(this.form),
        });
        this.form = {
            date: '',
            description: '',
            lines: [{ account_id: '', debit: 0, credit: 0 }],
        };
        this.loadEntries();
        },
    },
};
</script>