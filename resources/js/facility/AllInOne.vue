<template>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facility Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: var(--dark);
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 20px;
            background: var(--primary);
            text-align: center;
        }
        
        .sidebar-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .sidebar-menu {
            padding: 10px 0;
        }
        
        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .menu-item.active {
            background: var(--primary);
            border-left: 4px solid var(--success);
        }
        
        .menu-item i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }
        
        .search-bar {
            display: flex;
            align-items: center;
            background: var(--light);
            border-radius: 30px;
            padding: 8px 15px;
            width: 300px;
        }
        
        .search-bar input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            margin-left: 10px;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
        }
        
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        
        /* Content Area Styles */
        .content {
            padding: 30px;
            overflow-y: auto;
            flex: 1;
        }
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .card-title {
            font-size: 16px;
            font-weight: 500;
            color: var(--gray);
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        
        .card-value {
            font-size: 28px;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .card-footer {
            border-top: 1px solid var(--light-gray);
            padding-top: 10px;
            font-size: 14px;
            color: var(--gray);
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .recent-activities {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
            color: white;
        }
        
        .activity-details {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .activity-time {
            font-size: 12px;
            color: var(--gray);
        }
        
        .work-orders {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .table th {
            background: var(--light);
            font-weight: 500;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-open {
            background: rgba(230, 57, 70, 0.1);
            color: var(--danger);
        }
        
        .status-in-progress {
            background: rgba(72, 149, 239, 0.1);
            color: var(--info);
        }
        
        .status-completed {
            background: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        @media (max-width: 992px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h2, .menu-item span {
                display: none;
            }
            
            .menu-item {
                justify-content: center;
            }
            
            .menu-item i {
                margin-right: 0;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .search-bar {
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="container">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-header">
                    <h2>Facility Manager</h2>
                </div>
                <div class="sidebar-menu">
                    <div class="menu-item active" @click="setActiveTab('dashboard')">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </div>
                    <div class="menu-item" @click="setActiveTab('space')">
                        <i class="fas fa-building"></i>
                        <span>Space Management</span>
                    </div>
                    <div class="menu-item" @click="setActiveTab('maintenance')">
                        <i class="fas fa-tools"></i>
                        <span>Maintenance</span>
                    </div>
                    <div class="menu-item" @click="setActiveTab('assets')">
                        <i class="fas fa-laptop-house"></i>
                        <span>Asset Management</span>
                    </div>
                    <div class="menu-item" @click="setActiveTab('energy')">
                        <i class="fas fa-bolt"></i>
                        <span>Energy Management</span>
                    </div>
                    <div class="menu-item" @click="setActiveTab('sustainability')">
                        <i class="fas fa-leaf"></i>
                        <span>Sustainability</span>
                    </div>
                    <div class="menu-item" @click="setActiveTab('projects')">
                        <i class="fas fa-tasks"></i>
                        <span>Capital Projects</span>
                    </div>
                    <div class="menu-item" @click="setActiveTab('lease')">
                        <i class="fas fa-file-contract"></i>
                        <span>Lease & Vendors</span>
                    </div>
                    <div class="menu-item" @click="setActiveTab('reports')">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports & Analytics</span>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Header -->
                <div class="header">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search...">
                    </div>
                    <div class="user-profile">
                        <img src="https://randomuser.me/api/portraits/men/41.jpg" alt="User">
                        <div>
                            <div>John Smith</div>
                            <div style="font-size: 12px; color: var(--gray);">Facility Manager</div>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="content">
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
            </div>
        </div>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                activeTab: 'dashboard',
                tabNames: {
                    space: 'Space',
                    maintenance: 'Maintenance',
                    assets: 'Asset',
                    energy: 'Energy',
                    sustainability: 'Sustainability',
                    projects: 'Capital Project',
                    lease: 'Lease & Vendor',
                    reports: 'Report & Analytics'
                },
                tabIcons: {
                    space: 'fas fa-building',
                    maintenance: 'fas fa-tools',
                    assets: 'fas fa-laptop-house',
                    energy: 'fas fa-bolt',
                    sustainability: 'fas fa-leaf',
                    projects: 'fas fa-tasks',
                    lease: 'fas fa-file-contract',
                    reports: 'fas fa-chart-bar'
                }
            },
            methods: {
                setActiveTab(tab) {
                    this.activeTab = tab;
                }
            },
            mounted() {
                // Space Utilization Chart
                const spaceCtx = document.getElementById('spaceChart').getContext('2d');
                new Chart(spaceCtx, {
                    type: 'bar',
                    data: {
                        labels: ['HR', 'IT', 'Marketing', 'Operations', 'Finance', 'R&D'],
                        datasets: [{
                            label: 'Utilization Rate (%)',
                            data: [85, 60, 75, 90, 65, 80],
                            backgroundColor: [
                                'rgba(67, 97, 238, 0.7)',
                                'rgba(76, 201, 240, 0.7)',
                                'rgba(72, 149, 239, 0.7)',
                                'rgba(247, 37, 133, 0.7)',
                                'rgba(58, 12, 163, 0.7)',
                                'rgba(230, 57, 70, 0.7)'
                            ],
                            borderColor: [
                                'rgb(67, 97, 238)',
                                'rgb(76, 201, 240)',
                                'rgb(72, 149, 239)',
                                'rgb(247, 37, 133)',
                                'rgb(58, 12, 163)',
                                'rgb(230, 57, 70)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });
                
                // Energy Consumption Chart
                const energyCtx = document.getElementById('energyChart').getContext('2d');
                new Chart(energyCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                        datasets: [{
                            label: 'Energy Consumption (kWh)',
                            data: [4200, 3950, 4100, 3800, 3700, 3500, 3245],
                            fill: false,
                            borderColor: 'rgb(76, 201, 240)',
                            tension: 0.1,
                            pointBackgroundColor: 'rgb(76, 201, 240)',
                            pointRadius: 5
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: false
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
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