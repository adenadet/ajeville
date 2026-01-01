<template>
<section>
    <div class="content">
        This is working fine
        <div v-if="activeTab === 'dashboard'">
            <h1 style="margin-bottom: 20px;">Facility Management Dashboard</h1>
            
            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Space Utilization</div>
                        <div class="card-icon" style="background: var(--primary);">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    <div class="card-value">78%</div>
                    <div class="card-footer">+5% from last month</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Active Work Orders</div>
                        <div class="card-icon" style="background: var(--info);">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                    <div class="card-value">24</div>
                    <div class="card-footer">8 high priority</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Energy Consumption</div>
                        <div class="card-icon" style="background: var(--success);">
                            <i class="fas fa-bolt"></i>
                        </div>
                    </div>
                    <div class="card-value">3,245 kWh</div>
                    <div class="card-footer">-12% from last month</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Maintenance Costs</div>
                        <div class="card-icon" style="background: var(--warning);">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="card-value">$12,580</div>
                    <div class="card-footer">On budget</div>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="grid-2">
                <div class="chart-container">
                    <div class="chart-title">Space Utilization by Department</div>
                    <canvas id="spaceChart" width="400" height="250"></canvas>
                </div>
                
                <div class="chart-container">
                    <div class="chart-title">Energy Consumption Trend</div>
                    <canvas id="energyChart" width="400" height="250"></canvas>
                </div>
            </div>
            
            <!-- Recent Activities and Work Orders -->
            <div class="grid-2">
                <div class="recent-activities">
                    <div class="chart-title">Recent Activities</div>
                    
                    <div class="activity-item">
                        <div class="activity-icon" style="background: var(--info);">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">Preventive Maintenance Scheduled</div>
                            <div class="activity-time">HVAC System #5 • 2 hours ago</div>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon" style="background: var(--success);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">Work Order #245 Completed</div>
                            <div class="activity-time">Plumbing Issue • 5 hours ago</div>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon" style="background: var(--warning);">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">High Energy Consumption Alert</div>
                            <div class="activity-time">Building A • Yesterday</div>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon" style="background: var(--primary);">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">Office Space Reallocated</div>
                            <div class="activity-time">Marketing Department • 2 days ago</div>
                        </div>
                    </div>
                </div>
                
                <div class="work-orders">
                    <div class="chart-title">Recent Work Orders</div>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#267</td>
                                <td>Electrical</td>
                                <td>High</td>
                                <td><span class="status status-open">Open</span></td>
                            </tr>
                            <tr>
                                <td>#266</td>
                                <td>HVAC</td>
                                <td>Medium</td>
                                <td><span class="status status-in-progress">In Progress</span></td>
                            </tr>
                            <tr>
                                <td>#265</td>
                                <td>Plumbing</td>
                                <td>Low</td>
                                <td><span class="status status-completed">Completed</span></td>
                            </tr>
                            <tr>
                                <td>#264</td>
                                <td>Structural</td>
                                <td>High</td>
                                <td><span class="status status-in-progress">In Progress</span></td>
                            </tr>
                            <tr>
                                <td>#263</td>
                                <td>General</td>
                                <td>Medium</td>
                                <td><span class="status status-completed">Completed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Other tabs content would go here -->
        <div v-if="activeTab !== 'dashboard'" style="padding: 40px; text-align: center;">
            <h2>{{ tabNames[activeTab] }} Management</h2>
            <p style="margin-top: 20px; color: var(--gray);">
                This feature would be implemented in the full application with Laravel backend API and Vue.js frontend components.
            </p>
            <div style="margin-top: 40px; font-size: 64px; color: var(--light-gray);">
                <i :class="tabIcons[activeTab]"></i>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            activeTab: 'dashboard',
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