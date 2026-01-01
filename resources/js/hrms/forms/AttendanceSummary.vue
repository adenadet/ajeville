<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">Create Attendance Summary</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="font-semibold">Start Date</label>
                <input type="date" v-model="startDate" class="input w-full" />
            </div>
            <div>
                <label class="font-semibold">End Date</label>
                <input type="date" v-model="endDate" class="input w-full" />
            </div>
            <div>
                <label class="font-semibold">Department (optional)</label>
                <select v-model="departmentId" class="input w-full">
                    <option value="">-- Select --</option>
                    <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                </select>
            </div>
            <div>
                <label class="font-semibold">Team (optional)</label>
                <select v-model="teamId" class="input w-full">
                    <option value="">-- Select --</option>
                    <option v-for="t in teams" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
            </div>
            <div>
                <label class="font-semibold">Employee ID (optional)</label>
                <input type="number" v-model="employeeId" class="input w-full" />
            </div>
        </div>

        <div>
            <h3 class="text-xl font-semibold mb-2">Shift Assignment Calendar</h3>
            <div v-if="calendar.length" class="space-y-4">
                <div v-for="(week, i) in calendar" :key="i" class="grid grid-cols-7 gap-2">
                    <div v-for="day in week" :key="day.date" class="border p-2 rounded bg-gray-50">
                        <div class="font-bold text-sm">{{ formatDate(day.date) }}</div>
                        <select v-model="day.shift_id" class="mt-1 w-full text-sm">
                            <option value="">-- Select Shift --</option>
                            <option v-for="shift in shifts" :key="shift.id" :value="shift.id">{{ shift.name }} ({{ shift.start_time }} - {{ shift.end_time }})</option>
                        </select>
                    </div>
                </div>
            </div>
            <div v-else class="text-gray-500">Please select Start and End dates above.</div>
        </div>
        <div class="mt-6 border-t pt-4 text-right">
            <button @click="submitForm" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Submit Summary
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import dayjs from 'dayjs';

export default {
    data() {
        return {
            //startDate: today.startOf('month').format('YYYY-MM-DD'),
            //endDate: today.endOf('month').format('YYYY-MM-DD'),
            departmentId: '',
            teamId: '',
            employeeId: '',
            shifts: [],
            departments: [],
            teams: [],
            calendar: []
        };
    },
    watch: {
        startDate: 'generateCalendar',
        endDate: 'generateCalendar'
    },
    methods: {
        async getInitials() {
            const [shiftsRes, deptRes, teamRes] = await Promise.all([
                axios.get('/api/shifts'),
                axios.get('/api/departments'),
                axios.get('/api/teams')
            ]);
            this.shifts = shiftsRes.data;
            this.departments = deptRes.data;
            this.teams = teamRes.data;
            },
            formatDate(date) {
            return dayjs(date).format('ddd, MMM D');
        },
        generateCalendar() {
            if (!this.startDate || !this.endDate) {
                this.calendar = [];
                return;
            }

            const start = dayjs(this.startDate);
            const end = dayjs(this.endDate);
            const days = [];

            let current = start;
            while (current.isBefore(end) || current.isSame(end)) {
                days.push({
                date: current.format('YYYY-MM-DD'),
                shift_id: ''
                });
                current = current.add(1, 'day');
            }

            const calendar = [];
            let week = [];

            days.forEach((day, index) => {
                week.push(day);
                if ((index + 1) % 7 === 0 || index === days.length - 1) {
                calendar.push(week);
                week = [];
                }
            });

            this.calendar = calendar;
        },
        async submitForm() {
            const payload = {
                start_date: this.startDate,
                end_date: this.endDate,
                department_id: this.departmentId,
                team_id: this.teamId,
                employee_id: this.employeeId,
                schedule: this.calendar.flat().filter(day => day.shift_id)
            };

            try {
                await axios.post('/api/attendance/schedule', payload);
                alert('Attendance Summary Created Successfully!');
            } 
            catch (error) {
                console.error(error);
                alert('Something went wrong.');
            }
        }
    },
    mounted() {
        this.getInitials();
    }
};
</script>

<!--style scoped>
.input {
    @apply border border-gray-300 rounded p-2;
}
</style-->
