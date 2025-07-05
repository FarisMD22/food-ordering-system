<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodieDelight Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
<section class="admin-dashboard">
    <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="admin-brand">
                <h1>🍽️ FoodieDelight Admin</h1>
                <p>Psychology-Enhanced Food Ordering Dashboard</p>
            </div>
            <div class="admin-user">
                <span class="admin-welcome">Welcome, <strong>Admin User</strong></span>
                <div class="last-updated">
                    Last updated: <span id="lastUpdated">3:45 PM</span>
                </div>
            </div>
        </div>

        <div class="admin-layout">
            <!-- Sidebar Navigation -->
            <div class="admin-sidebar">
                <nav class="admin-nav">
                    <a href="#dashboard" class="nav-item active" data-section="dashboard">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                    <a href="#orders" class="nav-item" data-section="orders">
                        <span class="nav-icon">📦</span>
                        Orders
                    </a>
                    <a href="#psychology" class="nav-item" data-section="psychology">
                        <span class="nav-icon">🧠</span>
                        Psychology
                    </a>
                    <a href="#users" class="nav-item" data-section="users">
                        <span class="nav-icon">👥</span>
                        Users
                    </a>
                    <a href="#menu" class="nav-item" data-section="menu">
                        <span class="nav-icon">🍽️</span>
                        Menu
                    </a>
                    <a href="#analytics" class="nav-item" data-section="analytics">
                        <span class="nav-icon">📈</span>
                        Analytics
                    </a>
                </nav>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h4>Quick Actions</h4>
                    <button class="quick-btn" onclick="refreshDashboard()">
                        🔄 Refresh Data
                    </button>
                    <button class="quick-btn" onclick="exportData()">
                        📥 Export Data
                    </button>
                    <button class="quick-btn" onclick="showAddMenuItem()">
                        ➕ Add Menu Item
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="admin-main">
                <!-- Dashboard Section -->
                <div class="admin-section active" id="dashboard-section">
                    <div class="section-header">
                        <h2>📊 Real-Time Dashboard</h2>
                        <p>Live statistics and key metrics</p>
                    </div>

                    <!-- Key Metrics Cards -->
                    <div class="metrics-grid">
                        <div class="metric-card orders-card">
                            <div class="metric-icon">📦</div>
                            <div class="metric-content">
                                <h3>Today's Orders</h3>
                                <div class="metric-value" id="ordersToday">247</div>
                                <div class="metric-change">+12% from yesterday</div>
                            </div>
                        </div>

                        <div class="metric-card revenue-card">
                            <div class="metric-icon">💰</div>
                            <div class="metric-content">
                                <h3>Revenue Today</h3>
                                <div class="metric-value revenue-amount" id="revenueToday" title="$8,492.50">$8.49K</div>
                                <div class="metric-change">+8% from yesterday</div>
                            </div>
                        </div>

                        <div class="metric-card users-card">
                            <div class="metric-icon">👥</div>
                            <div class="metric-content">
                                <h3>Active Users</h3>
                                <div class="metric-value" id="activeUsers">523</div>
                                <div class="metric-change">+5% from last hour</div>
                            </div>
                        </div>

                        <div class="metric-card psychology-card">
                            <div class="metric-icon">🧠</div>
                            <div class="metric-content">
                                <h3>Avg Psychology Score</h3>
                                <div class="metric-value">87.3%</div>
                                <div class="metric-change">Engagement rate</div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="charts-row">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h3>📈 Revenue Trend (30 Days)</h3>
                                <div class="chart-controls">
                                    <button class="chart-btn" onclick="updateChart('7d')">7D</button>
                                    <button class="chart-btn active" onclick="updateChart('30d')">30D</button>
                                    <button class="chart-btn" onclick="updateChart('90d')">90D</button>
                                </div>
                            </div>
                            <div class="chart-placeholder">
                                <canvas id="revenueChart" width="400" height="200"></canvas>
                            </div>
                        </div>

                        <div class="chart-container">
                            <div class="chart-header">
                                <h3>🧠 Psychology Triggers</h3>
                            </div>
                            <div class="psychology-triggers-summary">
                                <div class="trigger-item">
                                    <span class="trigger-name">Scarcity</span>
                                    <div class="trigger-bar">
                                        <div class="trigger-fill" style="width: 85%"></div>
                                    </div>
                                    <span class="trigger-value">85%</span>
                                </div>
                                <div class="trigger-item">
                                    <span class="trigger-name">Social Proof</span>
                                    <div class="trigger-bar">
                                        <div class="trigger-fill" style="width: 92%"></div>
                                    </div>
                                    <span class="trigger-value">92%</span>
                                </div>
                                <div class="trigger-item">
                                    <span class="trigger-name">Urgency</span>
                                    <div class="trigger-bar">
                                        <div class="trigger-fill" style="width: 78%"></div>
                                    </div>
                                    <span class="trigger-value">78%</span>
                                </div>
                                <div class="trigger-item">
                                    <span class="trigger-name">Color</span>
                                    <div class="trigger-bar">
                                        <div class="trigger-fill" style="width: 89%"></div>
                                    </div>
                                    <span class="trigger-value">89%</span>
                                </div>
                                <div class="trigger-item">
                                    <span class="trigger-name">Sensory</span>
                                    <div class="trigger-bar">
                                        <div class="trigger-fill" style="width: 91%"></div>
                                    </div>
                                    <span class="trigger-value">91%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="activity-section">
                        <h3>🔔 Recent Activity</h3>
                        <div class="activity-feed" id="activityFeed">
                            <div class="activity-item">
                                <span class="activity-icon">📦</span>
                                <span class="activity-text">New order #1247 received</span>
                                <span class="activity-time">2 minutes ago</span>
                            </div>
                            <div class="activity-item">
                                <span class="activity-icon">👤</span>
                                <span class="activity-text">New user registered</span>
                                <span class="activity-time">5 minutes ago</span>
                            </div>
                            <div class="activity-item">
                                <span class="activity-icon">🧠</span>
                                <span class="activity-text">Psychology trigger effectiveness updated</span>
                                <span class="activity-time">10 minutes ago</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Section -->
                <div class="admin-section" id="orders-section">
                    <div class="section-header">
                        <h2>📦 Order Management</h2>
                        <p>Monitor and manage customer orders</p>
                    </div>

                    <div class="orders-filters">
                        <div class="filter-group">
                            <label>Status:</label>
                            <select id="statusFilter" onchange="filterOrders()">
                                <option value="all">All Orders</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="preparing">Preparing</option>
                                <option value="ready">Ready</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Date:</label>
                            <select id="dateFilter" onchange="filterOrders()">
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                    </div>

                    <div class="orders-table-container">
                        <table class="orders-table">
                            <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Psychology Score</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody id="ordersTableBody">
                            <tr class="order-row" data-order-id="1247">
                                <td class="order-id">#1247</td>
                                <td class="customer-info">
                                    <div class="customer-name">John Doe</div>
                                    <div class="customer-email">john@example.com</div>
                                </td>
                                <td class="order-items">3 items</td>
                                <td class="order-total">$34.50</td>
                                <td class="psychology-score">
                                    <span class="score-badge">8.7/10</span>
                                </td>
                                <td class="order-status">
                                    <select class="status-select" onchange="updateOrderStatus(1247, this.value)">
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="preparing" selected>Preparing</option>
                                        <option value="ready">Ready</option>
                                        <option value="delivered">Delivered</option>
                                    </select>
                                </td>
                                <td class="order-time">2:15 PM</td>
                                <td class="order-actions">
                                    <button class="action-btn view-btn" onclick="viewOrderDetails(1247)">
                                        👁️ View
                                    </button>
                                </td>
                            </tr>
                            <tr class="order-row" data-order-id="1246">
                                <td class="order-id">#1246</td>
                                <td class="customer-info">
                                    <div class="customer-name">Jane Smith</div>
                                    <div class="customer-email">jane@example.com</div>
                                </td>
                                <td class="order-items">2 items</td>
                                <td class="order-total">$18.50</td>
                                <td class="psychology-score">
                                    <span class="score-badge">9.2/10</span>
                                </td>
                                <td class="order-status">
                                    <select class="status-select" onchange="updateOrderStatus(1246, this.value)">
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="preparing">Preparing</option>
                                        <option value="ready" selected>Ready</option>
                                        <option value="delivered">Delivered</option>
                                    </select>
                                </td>
                                <td class="order-time">2:10 PM</td>
                                <td class="order-actions">
                                    <button class="action-btn view-btn" onclick="viewOrderDetails(1246)">
                                        👁️ View
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Psychology Section -->
                <div class="admin-section" id="psychology-section">
                    <div class="section-header">
                        <h2>🧠 Psychology Analytics</h2>
                        <p>Behavioral triggers and conversion insights</p>
                    </div>

                    <div class="psychology-overview">
                        <div class="psychology-stats">
                            <div class="psych-stat">
                                <h4>Total Triggers Used</h4>
                                <div class="stat-number">2,847</div>
                            </div>
                            <div class="psych-stat">
                                <h4>Successful Conversions</h4>
                                <div class="stat-number">2,394</div>
                            </div>
                            <div class="psych-stat">
                                <h4>Average Engagement</h4>
                                <div class="stat-number">87.3%</div>
                            </div>
                        </div>

                        <div class="trigger-effectiveness">
                            <h4>Trigger Effectiveness</h4>
                            <div class="effectiveness-item">
                                <div class="trigger-info">
                                    <span class="trigger-icon">⚡</span>
                                    <span class="trigger-label">Scarcity</span>
                                </div>
                                <div class="effectiveness-bar">
                                    <div class="effectiveness-fill" style="width: 85%"></div>
                                </div>
                                <span class="effectiveness-value">85%</span>
                            </div>
                            <div class="effectiveness-item">
                                <div class="trigger-info">
                                    <span class="trigger-icon">👥</span>
                                    <span class="trigger-label">Social Proof</span>
                                </div>
                                <div class="effectiveness-bar">
                                    <div class="effectiveness-fill" style="width: 92%"></div>
                                </div>
                                <span class="effectiveness-value">92%</span>
                            </div>
                            <div class="effectiveness-item">
                                <div class="trigger-info">
                                    <span class="trigger-icon">🔥</span>
                                    <span class="trigger-label">Urgency</span>
                                </div>
                                <div class="effectiveness-bar">
                                    <div class="effectiveness-fill" style="width: 78%"></div>
                                </div>
                                <span class="effectiveness-value">78%</span>
                            </div>
                            <div class="effectiveness-item">
                                <div class="trigger-info">
                                    <span class="trigger-icon">🎨</span>
                                    <span class="trigger-label">Color</span>
                                </div>
                                <div class="effectiveness-bar">
                                    <div class="effectiveness-fill" style="width: 89%"></div>
                                </div>
                                <span class="effectiveness-value">89%</span>
                            </div>
                            <div class="effectiveness-item">
                                <div class="trigger-info">
                                    <span class="trigger-icon">👅</span>
                                    <span class="trigger-label">Sensory</span>
                                </div>
                                <div class="effectiveness-bar">
                                    <div class="effectiveness-fill" style="width: 91%"></div>
                                </div>
                                <span class="effectiveness-value">91%</span>
                            </div>
                        </div>
                    </div>

                    <div class="psychology-insights">
                        <h4>🔍 Key Insights</h4>
                        <div class="insights-list">
                            <div class="insight-card">
                                <h5>Scarcity Psychology</h5>
                                <p>Limited quantity messages increase conversion by 85%</p>
                            </div>
                            <div class="insight-card">
                                <h5>Social Proof Impact</h5>
                                <p>Customer testimonials show 92% effectiveness</p>
                            </div>
                            <div class="insight-card">
                                <h5>Color Psychology</h5>
                                <p>Appetite-stimulating colors achieve 89% engagement</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Section -->
                <div class="admin-section" id="users-section">
                    <div class="section-header">
                        <h2>👥 User Management</h2>
                        <p>Customer statistics and psychology profiles</p>
                    </div>

                    <div class="user-stats-grid">
                        <div class="user-stat-card">
                            <h4>Total Users</h4>
                            <div class="user-stat-number">1,247</div>
                            <div class="user-stat-change">+87 this month</div>
                        </div>
                        <div class="user-stat-card">
                            <h4>Active Users</h4>
                            <div class="user-stat-number">523</div>
                            <div class="user-stat-change">Last 30 days</div>
                        </div>
                        <div class="user-stat-card">
                            <h4>Avg Order Value</h4>
                            <div class="user-stat-number">$34.38</div>
                            <div class="user-stat-change">Per customer</div>
                        </div>
                    </div>

                    <!-- User Psychology Breakdown -->
                    <div class="psychology-breakdown">
                        <h4>Psychology Profile Distribution</h4>
                        <div class="psychology-charts">
                            <div class="psychology-chart">
                                <h5>Appetite Profiles</h5>
                                <div class="profile-bars">
                                    <div class="profile-bar">
                                        <span class="profile-label">🏠 Comfort Lovers</span>
                                        <div class="profile-bar-fill" style="width: 45%"></div>
                                        <span class="profile-percentage">45%</span>
                                    </div>
                                    <div class="profile-bar">
                                        <span class="profile-label">🌶️ Adventurous</span>
                                        <div class="profile-bar-fill" style="width: 30%"></div>
                                        <span class="profile-percentage">30%</span>
                                    </div>
                                    <div class="profile-bar">
                                        <span class="profile-label">🥗 Health Conscious</span>
                                        <div class="profile-bar-fill" style="width: 15%"></div>
                                        <span class="profile-percentage">15%</span>
                                    </div>
                                    <div class="profile-bar">
                                        <span class="profile-label">🍰 Indulgent</span>
                                        <div class="profile-bar-fill" style="width: 10%"></div>
                                        <span class="profile-percentage">10%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Section -->
                <div class="admin-section" id="menu-section">
                    <div class="section-header">
                        <h2>🍽️ Menu Management</h2>
                        <p>Manage menu items and psychology attributes</p>
                    </div>

                    <div class="menu-stats">
                        <div class="menu-stat">
                            <span class="stat-label">Total Items</span>
                            <span class="stat-value">45</span>
                        </div>
                        <div class="menu-stat">
                            <span class="stat-label">Available</span>
                            <span class="stat-value">42</span>
                        </div>
                        <div class="menu-stat">
                            <span class="stat-label">Featured</span>
                            <span class="stat-value">8</span>
                        </div>
                        <div class="menu-stat">
                            <span class="stat-label">Avg Psychology Score</span>
                            <span class="stat-value">8.7</span>
                        </div>
                    </div>

                    <div class="menu-actions">
                        <button class="admin-btn primary" onclick="showAddMenuItem()">
                            ➕ Add New Item
                        </button>
                        <button class="admin-btn secondary" onclick="exportMenuData()">
                            📥 Export Menu Data
                        </button>
                        <button class="admin-btn secondary" onclick="bulkUpdateItems()">
                            🔄 Bulk Update
                        </button>
                    </div>

                    <!-- Popular Items -->
                    <div class="popular-items">
                        <h4>📈 Most Popular Items</h4>
                        <div class="popular-list">
                            <div class="popular-item">
                                <span class="popular-rank">#1</span>
                                <span class="popular-name">Margherita Pizza</span>
                                <span class="popular-orders">47 orders</span>
                            </div>
                            <div class="popular-item">
                                <span class="popular-rank">#2</span>
                                <span class="popular-name">Chicken Burger</span>
                                <span class="popular-orders">32 orders</span>
                            </div>
                            <div class="popular-item">
                                <span class="popular-rank">#3</span>
                                <span class="popular-name">Caesar Salad</span>
                                <span class="popular-orders">28 orders</span>
                            </div>
                            <div class="popular-item">
                                <span class="popular-rank">#4</span>
                                <span class="popular-name">Pepperoni Pizza</span>
                                <span class="popular-orders">25 orders</span>
                            </div>
                            <div class="popular-item">
                                <span class="popular-rank">#5</span>
                                <span class="popular-name">Fish & Chips</span>
                                <span class="popular-orders">22 orders</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Section -->
                <div class="admin-section" id="analytics-section">
                    <div class="section-header">
                        <h2>📈 Advanced Analytics</h2>
                        <p>Detailed performance metrics and trends</p>
                    </div>

                    <div class="analytics-content">
                        <div class="analytics-summary">
                            <h4>30-Day Summary</h4>
                            <div class="summary-stats">
                                <div class="summary-stat">
                                    <span class="summary-label">Total Orders</span>
                                    <span class="summary-value">1,247</span>
                                </div>
                                <div class="summary-stat">
                                    <span class="summary-label">Total Revenue</span>
                                    <span class="summary-value">$42,850.00</span>
                                </div>
                                <div class="summary-stat">
                                    <span class="summary-label">Avg Order Value</span>
                                    <span class="summary-value">$34.38</span>
                                </div>
                            </div>
                        </div>

                        <div class="analytics-charts">
                            <div class="analytics-chart">
                                <h5>Daily Revenue Trend</h5>
                                <div class="chart-placeholder">
                                    <canvas id="analyticsChart" width="600" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* ========================================
       CSS CUSTOM PROPERTIES
    ======================================== */
    :root {
        /* Colors */
        --primary-red: #e74c3c;
        --primary-red-dark: #c0392b;
        --primary-red-light: #f5b7b1;

        --secondary-blue: #3498db;
        --secondary-green: #27ae60;
        --secondary-purple: #9b59b6;

        --neutral-50: #f8fafc;
        --neutral-100: #f1f5f9;
        --neutral-200: #e2e8f0;
        --neutral-300: #cbd5e1;
        --neutral-400: #94a3b8;
        --neutral-500: #64748b;
        --neutral-600: #475569;
        --neutral-700: #334155;
        --neutral-800: #1e293b;
        --neutral-900: #0f172a;

        --white: #ffffff;
        --success: #22c55e;
        --warning: #f59e0b;
        --error: #ef4444;

        /* Typography */
        --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        --font-size-xs: 0.75rem;
        --font-size-sm: 0.875rem;
        --font-size-base: 1rem;
        --font-size-lg: 1.125rem;
        --font-size-xl: 1.25rem;
        --font-size-2xl: 1.5rem;
        --font-size-3xl: 1.875rem;
        --font-size-4xl: 2.25rem;

        --font-weight-light: 300;
        --font-weight-normal: 400;
        --font-weight-medium: 500;
        --font-weight-semibold: 600;
        --font-weight-bold: 700;

        --line-height-tight: 1.25;
        --line-height-normal: 1.5;
        --line-height-relaxed: 1.75;

        /* Spacing */
        --space-1: 0.25rem;
        --space-2: 0.5rem;
        --space-3: 0.75rem;
        --space-4: 1rem;
        --space-5: 1.25rem;
        --space-6: 1.5rem;
        --space-8: 2rem;
        --space-10: 2.5rem;
        --space-12: 3rem;
        --space-16: 4rem;

        /* Borders */
        --border-radius-sm: 0.375rem;
        --border-radius: 0.5rem;
        --border-radius-lg: 0.75rem;
        --border-radius-xl: 1rem;
        --border-radius-large: 0.75rem;

        /* Shadows */
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);

        /* Transitions */
        --transition-fast: 150ms ease-in-out;
        --transition-normal: 300ms ease-in-out;
        --transition-slow: 500ms ease-in-out;
        --transition-smooth: all 0.3s ease;

        /* Gradients */
        --gradient-appetite: linear-gradient(135deg, #e74c3c 0%, #ff6b35 100%);

        /* Text Colors */
        --text-color: #334155;
    }

    /* ========================================
       RESET & BASE STYLES
    ======================================== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: var(--font-family);
        font-size: var(--font-size-base);
        line-height: var(--line-height-normal);
        color: var(--text-color);
        background-color: var(--neutral-50);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* ========================================
       ADMIN DASHBOARD LAYOUT
    ======================================== */
    .admin-dashboard {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 0;
    }

    .admin-container {
        max-width: 100%;
        margin: 0;
        padding: 0;
    }

    .admin-header {
        background: #f8f8fc; /* Dark background like your nav */
        padding: var(--space-4) var(--space-8);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .admin-brand h1 {
        font-size: var(--font-size-2xl);
        margin-bottom: var(--space-1);
        color: #e74c3c; /* Solid color instead of gradient */
        font-weight: var(--font-weight-bold);
    }

    .admin-brand h1 {
        font-size: var(--font-size-2xl);
        margin-bottom: var(--space-1);
        color: #e74c3c; /* Keep red like FoodieDelight brand */
        font-weight: var(--font-weight-bold);
    }

    .admin-brand p {
        color: #bdc3c7; /* Light gray for dark background */
        font-size: var(--font-size-sm);
        margin: 0;
        font-weight: 500;
    }

    .admin-welcome {
        display: block;
        font-weight: var(--font-weight-semibold);
        color: #334155; /* Darker color */
        margin-bottom: var(--space-1);
    }

    .last-updated {
        font-size: var(--font-size-xs);
        color: #64748b; /* Better contrast than neutral-500 */
        font-weight: 500;
    }

    .admin-layout {
        display: grid;
        grid-template-columns: 250px 1fr;
        height: calc(100vh - 80px);
    }

    .admin-sidebar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: var(--space-8) var(--space-4);
        border-right: 1px solid rgba(0, 0, 0, 0.1);
        overflow-y: auto;
    }

    .admin-nav {
        margin-bottom: var(--space-8);
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        padding: var(--space-3) var(--space-4);
        margin-bottom: var(--space-2);
        border-radius: var(--border-radius);
        color: var(--text-color);
        text-decoration: none;
        transition: var(--transition-smooth);
        font-weight: var(--font-weight-medium);
    }

    .nav-item:hover {
        background: rgba(231, 76, 60, 0.1);
        color: var(--primary-red);
        transform: translateX(5px);
    }

    .nav-item.active {
        background: var(--primary-red);
        color: var(--white);
        box-shadow: var(--shadow-md);
    }

    .nav-icon {
        font-size: var(--font-size-lg);
        width: 20px;
        text-align: center;
    }

    .quick-actions {
        border-top: 1px solid var(--neutral-200);
        padding-top: var(--space-6);
    }

    .quick-actions h4 {
        margin-bottom: var(--space-4);
        color: var(--text-color);
        font-size: var(--font-size-xs);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: var(--font-weight-semibold);
    }

    .quick-btn {
        display: block;
        width: 100%;
        padding: var(--space-2);
        margin-bottom: var(--space-2);
        background: var(--neutral-50);
        border: 1px solid var(--neutral-200);
        border-radius: var(--border-radius);
        font-size: var(--font-size-xs);
        cursor: pointer;
        transition: var(--transition-smooth);
        color: var(--text-color);
    }

    .quick-btn:hover {
        background: var(--primary-red);
        color: var(--white);
        border-color: var(--primary-red);
    }

    .admin-main {
        padding: var(--space-8);
        overflow-y: auto;
        background: rgba(255, 255, 255, 0.05);
    }

    .admin-section {
        display: none;
    }

    .admin-section.active {
        display: block;
    }

    .section-header {
        margin-bottom: var(--space-8);
    }

    .section-header h2 {
        font-size: var(--font-size-3xl);
        color: var(--white);
        margin-bottom: var(--space-2);
        font-weight: var(--font-weight-bold);
    }

    .section-header p {
        color: rgba(255, 255, 255, 0.8);
        font-size: var(--font-size-lg);
    }

    /* ========================================
       METRICS GRID
    ======================================== */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--space-6);
        margin-bottom: var(--space-12);
    }

    .metric-card {
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
        display: flex;
        align-items: center;
        gap: var(--space-6);
        transition: var(--transition-smooth);
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .metric-icon {
        font-size: var(--font-size-4xl);
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .orders-card .metric-icon {
        background: rgba(52, 152, 219, 0.1);
    }

    .revenue-card .metric-icon {
        background: rgba(39, 174, 96, 0.1);
    }

    .users-card .metric-icon {
        background: rgba(155, 89, 182, 0.1);
    }

    .psychology-card .metric-icon {
        background: rgba(231, 76, 60, 0.1);
    }

    .metric-content {
        flex: 1;
        min-width: 0; /* Allow content to shrink */
    }

    .metric-content h3 {
        font-size: var(--font-size-sm);
        color: var(--neutral-500);
        margin-bottom: var(--space-2);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: var(--font-weight-semibold);
    }

    .metric-value {
        font-size: var(--font-size-3xl);
        font-weight: var(--font-weight-bold);
        color: var(--text-color);
        margin-bottom: var(--space-1);
        line-height: var(--line-height-tight);
        word-break: break-word; /* Handle long numbers */
    }

    .revenue-amount {
        font-size: clamp(1.5rem, 4vw, 1.875rem); /* Responsive font size */
        cursor: help; /* Indicate tooltip on hover */
    }

    .metric-change {
        font-size: var(--font-size-xs);
        color: var(--success);
        font-weight: var(--font-weight-semibold);
    }

    /* ========================================
       CHARTS ROW
    ======================================== */
    .charts-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: var(--space-8);
        margin-bottom: var(--space-12);
    }

    .chart-container {
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-6);
    }

    .chart-header h3 {
        color: var(--text-color);
        margin: 0;
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
    }

    .chart-controls {
        display: flex;
        gap: var(--space-2);
    }

    .chart-btn {
        padding: var(--space-1) var(--space-3);
        background: var(--neutral-50);
        border: 1px solid var(--neutral-200);
        border-radius: 15px;
        cursor: pointer;
        font-size: var(--font-size-xs);
        transition: var(--transition-smooth);
        color: var(--text-color);
    }

    .chart-btn.active,
    .chart-btn:hover {
        background: var(--primary-red);
        color: var(--white);
        border-color: var(--primary-red);
    }

    .chart-placeholder {
        width: 100%;
        height: 200px;
        position: relative;
    }

    .chart-placeholder canvas {
        max-width: 100%;
        height: 200px !important;
    }

    .psychology-triggers-summary {
        display: flex;
        flex-direction: column;
        gap: var(--space-4);
    }

    .trigger-item {
        display: flex;
        align-items: center;
        gap: var(--space-4);
    }

    .trigger-name {
        font-size: var(--font-size-sm);
        color: var(--text-color);
        min-width: 100px;
        font-weight: var(--font-weight-medium);
    }

    .trigger-bar {
        flex: 1;
        height: 8px;
        background: var(--neutral-100);
        border-radius: 4px;
        overflow: hidden;
    }

    .trigger-fill {
        height: 100%;
        background: var(--primary-red);
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    .trigger-value {
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        color: var(--primary-red);
        min-width: 40px;
        text-align: right;
    }

    /* ========================================
       ACTIVITY SECTION
    ======================================== */
    .activity-section {
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .activity-section h3 {
        margin-bottom: var(--space-6);
        color: var(--text-color);
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
    }

    .activity-feed {
        display: flex;
        flex-direction: column;
        gap: var(--space-4);
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: var(--space-4);
        padding: var(--space-4);
        background: var(--neutral-50);
        border-radius: var(--border-radius);
        transition: var(--transition-smooth);
    }

    .activity-item.new {
        background: rgba(231, 76, 60, 0.1);
        border-left: 4px solid var(--primary-red);
    }

    .activity-icon {
        font-size: var(--font-size-lg);
        width: 30px;
        text-align: center;
    }

    .activity-text {
        flex: 1;
        color: var(--text-color);
        font-size: var(--font-size-sm);
    }

    .activity-time {
        font-size: var(--font-size-xs);
        color: var(--neutral-500);
    }

    /* ========================================
       ORDERS SECTION
    ======================================== */
    .orders-filters {
        display: flex;
        gap: var(--space-8);
        margin-bottom: var(--space-8);
        background: var(--white);
        padding: var(--space-6);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }

    .filter-group label {
        font-weight: var(--font-weight-semibold);
        color: var(--text-color);
        font-size: var(--font-size-sm);
    }

    .filter-group select {
        padding: var(--space-2);
        border: 1px solid var(--neutral-200);
        border-radius: var(--border-radius);
        background: var(--white);
        color: var(--text-color);
        font-size: var(--font-size-sm);
    }

    .orders-table-container {
        background: var(--white);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
    }

    .orders-table th {
        background: var(--neutral-50);
        padding: var(--space-4);
        text-align: left;
        font-weight: var(--font-weight-semibold);
        color: var(--text-color);
        border-bottom: 1px solid var(--neutral-200);
        font-size: var(--font-size-sm);
    }

    .orders-table td {
        padding: var(--space-4);
        border-bottom: 1px solid var(--neutral-100);
        vertical-align: middle;
        font-size: var(--font-size-sm);
    }

    .order-row:hover {
        background: rgba(231, 76, 60, 0.05);
    }

    .order-id {
        font-weight: var(--font-weight-semibold);
        color: var(--primary-red);
    }

    .customer-name {
        font-weight: var(--font-weight-semibold);
        color: var(--text-color);
        margin-bottom: var(--space-1);
    }

    .customer-email {
        font-size: var(--font-size-xs);
        color: var(--neutral-500);
    }

    .score-badge {
        background: var(--primary-red);
        color: var(--white);
        padding: var(--space-1) var(--space-2);
        border-radius: 12px;
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
    }

    .status-select {
        padding: var(--space-1) var(--space-2);
        border: 1px solid var(--neutral-200);
        border-radius: var(--border-radius);
        background: var(--white);
        font-size: var(--font-size-xs);
        color: var(--text-color);
    }

    .action-btn {
        padding: var(--space-1) var(--space-3);
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-size: var(--font-size-xs);
        transition: var(--transition-smooth);
        font-weight: var(--font-weight-medium);
    }

    .view-btn {
        background: #17a2b8;
        color: var(--white);
    }

    .view-btn:hover {
        background: #138496;
    }

    /* ========================================
       PSYCHOLOGY SECTION
    ======================================== */
    .psychology-overview {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: var(--space-8);
        margin-bottom: var(--space-8);
    }

    .psychology-stats {
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .psych-stat {
        margin-bottom: var(--space-6);
    }

    .psych-stat h4 {
        font-size: var(--font-size-sm);
        color: var(--neutral-500);
        margin-bottom: var(--space-2);
        text-transform: uppercase;
        font-weight: var(--font-weight-semibold);
    }

    .psych-stat .stat-number {
        font-size: var(--font-size-3xl);
        font-weight: var(--font-weight-bold);
        color: var(--primary-red);
    }

    .trigger-effectiveness {
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .trigger-effectiveness h4 {
        margin-bottom: var(--space-6);
        color: var(--text-color);
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
    }

    .effectiveness-item {
        display: flex;
        align-items: center;
        gap: var(--space-4);
        margin-bottom: var(--space-4);
    }

    .trigger-info {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        min-width: 150px;
    }

    .trigger-icon {
        font-size: var(--font-size-lg);
    }

    .trigger-label {
        font-size: var(--font-size-sm);
        color: var(--text-color);
        font-weight: var(--font-weight-medium);
    }

    .effectiveness-bar {
        flex: 1;
        height: 8px;
        background: var(--neutral-100);
        border-radius: 4px;
        overflow: hidden;
    }

    .effectiveness-fill {
        height: 100%;
        background: var(--primary-red);
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    .effectiveness-value {
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        color: var(--primary-red);
        min-width: 40px;
        text-align: right;
    }

    .psychology-insights {
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .psychology-insights h4 {
        margin-bottom: var(--space-6);
        color: var(--text-color);
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
    }

    .insights-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--space-4);
    }

    .insight-card {
        background: var(--neutral-50);
        padding: var(--space-6);
        border-radius: var(--border-radius);
        border-left: 4px solid var(--primary-red);
    }

    .insight-card h5 {
        margin-bottom: var(--space-2);
        color: var(--text-color);
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
    }

    .insight-card p {
        color: var(--neutral-600);
        font-size: var(--font-size-sm);
        margin: 0;
    }

    /* ========================================
       USERS SECTION
    ======================================== */
    .user-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--space-6);
        margin-bottom: var(--space-8);
    }

    .user-stat-card {
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
        text-align: center;
    }

    .user-stat-card h4 {
        font-size: var(--font-size-sm);
        color: var(--neutral-500);
        margin-bottom: var(--space-4);
        text-transform: uppercase;
        font-weight: var(--font-weight-semibold);
    }

    .user-stat-number {
        font-size: var(--font-size-3xl);
        font-weight: var(--font-weight-bold);
        color: var(--primary-red);
        margin-bottom: var(--space-2);
    }

    .user-stat-change {
        font-size: var(--font-size-xs);
        color: var(--success);
        font-weight: var(--font-weight-medium);
    }

    .psychology-breakdown {
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .psychology-breakdown h4 {
        margin-bottom: var(--space-6);
        color: var(--text-color);
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
    }

    .psychology-chart h5 {
        margin-bottom: var(--space-4);
        color: var(--text-color);
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
    }

    .profile-bars {
        display: flex;
        flex-direction: column;
        gap: var(--space-4);
    }

    .profile-bar {
        display: flex;
        align-items: center;
        gap: var(--space-4);
    }

    .profile-label {
        min-width: 150px;
        font-size: var(--font-size-sm);
        color: var(--text-color);
        font-weight: var(--font-weight-medium);
    }

    .profile-bar-fill {
        height: 8px;
        background: var(--primary-red);
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    .profile-percentage {
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        color: var(--primary-red);
        min-width: 40px;
        text-align: right;
    }

    /* ========================================
       MENU SECTION
    ======================================== */
    .menu-stats {
        display: flex;
        gap: var(--space-8);
        margin-bottom: var(--space-8);
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .menu-stat {
        text-align: center;
        flex: 1;
    }

    .stat-label {
        display: block;
        font-size: var(--font-size-xs);
        color: var(--neutral-500);
        margin-bottom: var(--space-2);
        text-transform: uppercase;
        font-weight: var(--font-weight-semibold);
    }

    .stat-value {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--primary-red);
    }

    .menu-actions {
        display: flex;
        gap: var(--space-4);
        margin-bottom: var(--space-8);
    }

    .admin-btn {
        padding: var(--space-3) var(--space-6);
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-weight: var(--font-weight-semibold);
        transition: var(--transition-smooth);
        font-size: var(--font-size-sm);
    }

    .admin-btn.primary {
        background: var(--primary-red);
        color: var(--white);
    }

    .admin-btn.primary:hover {
        background: var(--primary-red-dark);
        transform: translateY(-2px);
    }

    .admin-btn.secondary {
        background: var(--neutral-500);
        color: var(--white);
    }

    .admin-btn.secondary:hover {
        background: var(--neutral-600);
    }

    .popular-items {
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .popular-items h4 {
        margin-bottom: var(--space-6);
        color: var(--text-color);
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
    }

    .popular-list {
        display: flex;
        flex-direction: column;
        gap: var(--space-4);
    }

    .popular-item {
        display: flex;
        align-items: center;
        gap: var(--space-4);
        padding: var(--space-4);
        background: var(--neutral-50);
        border-radius: var(--border-radius);
    }

    .popular-rank {
        font-weight: var(--font-weight-bold);
        color: var(--primary-red);
        min-width: 30px;
        font-size: var(--font-size-base);
    }

    .popular-name {
        flex: 1;
        color: var(--text-color);
        font-weight: var(--font-weight-medium);
    }

    .popular-orders {
        font-size: var(--font-size-xs);
        color: var(--neutral-500);
        font-weight: var(--font-weight-medium);
    }

    /* ========================================
       ANALYTICS SECTION
    ======================================== */
    .analytics-content {
        display: flex;
        flex-direction: column;
        gap: var(--space-8);
    }

    .analytics-summary {
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .analytics-summary h4 {
        margin-bottom: var(--space-6);
        color: var(--text-color);
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
    }

    .summary-stats {
        display: flex;
        gap: var(--space-8);
    }

    .summary-stat {
        text-align: center;
        flex: 1;
    }

    .summary-label {
        display: block;
        font-size: var(--font-size-xs);
        color: var(--neutral-500);
        margin-bottom: var(--space-2);
        text-transform: uppercase;
        font-weight: var(--font-weight-semibold);
    }

    .summary-value {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--primary-red);
    }

    .analytics-charts {
        background: var(--white);
        padding: var(--space-8);
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .analytics-chart h5 {
        margin-bottom: var(--space-6);
        color: var(--text-color);
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
    }

    /* ========================================
       NOTIFICATIONS
    ======================================== */
    .admin-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: var(--space-4) var(--space-6);
        border-radius: var(--border-radius);
        color: var(--white);
        font-weight: var(--font-weight-semibold);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        z-index: 2000;
        font-size: var(--font-size-sm);
    }

    .admin-notification.show {
        transform: translateX(0);
    }

    .admin-notification.success {
        background: var(--success);
    }

    .admin-notification.error {
        background: var(--error);
    }

    .admin-notification.info {
        background: #17a2b8;
    }

    /* ========================================
       RESPONSIVE DESIGN
    ======================================== */
    @media (max-width: 1024px) {
        .admin-layout {
            grid-template-columns: 200px 1fr;
        }

        .charts-row {
            grid-template-columns: 1fr;
        }

        .psychology-overview {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .admin-layout {
            grid-template-columns: 1fr;
            height: auto;
        }

        .admin-sidebar {
            display: none;
        }

        .admin-main {
            padding: var(--space-4);
        }

        .metrics-grid {
            grid-template-columns: 1fr;
        }

        .summary-stats {
            flex-direction: column;
            gap: var(--space-4);
        }

        .menu-stats {
            flex-direction: column;
            gap: var(--space-4);
        }

        .orders-filters {
            flex-direction: column;
            gap: var(--space-4);
        }

        .menu-actions {
            flex-direction: column;
        }

        .orders-table-container {
            overflow-x: auto;
        }

        .orders-table {
            min-width: 600px;
        }
    }

    @media (max-width: 480px) {
        .admin-header {
            flex-direction: column;
            gap: var(--space-4);
            text-align: center;
        }

        .metric-card {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<script>
    // Global admin variables
    let currentSection = 'dashboard';
    let refreshInterval;

    document.addEventListener('DOMContentLoaded', function() {
        initializeAdminDashboard();
        startAutoRefresh();
        setupNavigation();
        setupRealTimeUpdates();
    });

    function initializeAdminDashboard() {
        // Track admin dashboard load
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('admin_dashboard_loaded', {
                admin_id: 'demo_admin',
                timestamp: Date.now()
            });
        }

        // Initialize charts
        initializeCharts();

        // Setup real-time data updates
        updateDashboardData();
    }

    function setupNavigation() {
        const navItems = document.querySelectorAll('.nav-item');
        const sections = document.querySelectorAll('.admin-section');

        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                const targetSection = this.dataset.section;

                // Update active nav item
                navItems.forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');

                // Update active section
                sections.forEach(section => section.classList.remove('active'));
                document.getElementById(targetSection + '-section').classList.add('active');

                currentSection = targetSection;

                // Track section view
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('admin_section_viewed', {
                        section: targetSection,
                        admin_id: 'demo_admin'
                    });
                }
            });
        });
    }

    function startAutoRefresh() {
        // Refresh dashboard data every 30 seconds
        refreshInterval = setInterval(() => {
            updateDashboardData();
            updateLastUpdatedTime();
        }, 30000);
    }

    function updateDashboardData() {
        // Simulate real-time data updates
        const metrics = document.querySelectorAll('.metric-value');
        metrics.forEach(metric => {
            if (metric.id) {
                if (metric.id === 'revenueToday') {
                    // Handle revenue with special formatting
                    const currentTitle = metric.getAttribute('title') || '$8,492.50';
                    const currentValue = parseFloat(currentTitle.replace(/[$,]/g, ''));
                    const variation = Math.random() * 1000 - 500;
                    const newValue = Math.max(1000, currentValue + variation);

                    // Update both display and full value
                    metric.textContent = formatCurrency(newValue, true);
                    metric.setAttribute('title', formatCurrency(newValue, false));
                } else {
                    const currentText = metric.textContent;
                    const currentValue = parseInt(currentText.replace(/[^0-9]/g, ''));
                    const variation = Math.floor(Math.random() * 10) - 5;

                    if (currentValue + variation > 0) {
                        let newValue = currentValue + variation;
                        if (currentText.includes('%')) {
                            metric.textContent = newValue + '%';
                        } else {
                            metric.textContent = newValue.toLocaleString();
                        }
                    }
                }

                // Add animation effect
                metric.style.transform = 'scale(1.1)';
                metric.style.color = 'var(--primary-red)';
                setTimeout(() => {
                    metric.style.transform = 'scale(1)';
                    metric.style.color = '';
                }, 200);
            }
        });
    }

    function formatCurrency(amount, compact = false) {
        if (compact) {
            if (amount >= 1000000) {
                return '$' + (amount / 1000000).toFixed(1) + 'M';
            } else if (amount >= 1000) {
                return '$' + (amount / 1000).toFixed(1) + 'K';
            }
        }
        return '$' + amount.toLocaleString();
    }

    function updateLastUpdatedTime() {
        document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString([], {
            hour: 'numeric',
            minute: '2-digit'
        });
    }

    // Chart data generation functions
    function generateChartRevenueData(days = 30) {
        const labels = [];
        const values = [];
        const today = new Date();

        for (let i = days - 1; i >= 0; i--) {
            const date = new Date(today);
            date.setDate(date.getDate() - i);

            if (days <= 7) {
                labels.push(date.toLocaleDateString('en-US', { weekday: 'short' }));
            } else if (days <= 30) {
                labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
            } else {
                if (i % 3 === 0) {
                    labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                    values.push(Math.max(1000, Math.round(8000 + Math.random() * 4000 - 2000)));
                }
                continue;
            }

            const baseRevenue = 8000;
            const variation = Math.random() * 4000 - 2000;
            const weekendMultiplier = (date.getDay() === 0 || date.getDay() === 6) ? 1.3 : 1;
            values.push(Math.max(1000, Math.round((baseRevenue + variation) * weekendMultiplier)));
        }

        return { labels, values };
    }

    function generateChartAnalyticsData() {
        const labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        const orders = [45, 52, 38, 67, 73, 89, 95];
        const revenue = [3200, 4100, 2800, 5200, 5800, 7100, 8500];
        return { labels, orders, revenue };
    }

    function updateChartData(period) {
        if (window.revenueChartInstance) {
            let days = 30;
            if (period === '7d') days = 7;
            if (period === '90d') days = 90;

            const newData = generateChartRevenueData(days);
            window.revenueChartInstance.data.labels = newData.labels;
            window.revenueChartInstance.data.datasets[0].data = newData.values;
            window.revenueChartInstance.update('active');
        }
    }

    function initializeCharts() {
        // Initialize revenue chart with Chart.js
        const revenueChart = document.getElementById('revenueChart');
        if (revenueChart) {
            const ctx = revenueChart.getContext('2d');
            const revenueData = generateChartRevenueData();

            window.revenueChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: revenueData.labels,
                    datasets: [{
                        label: 'Daily Revenue',
                        data: revenueData.values,
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#e74c3c',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#e74c3c',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: $' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { family: 'Inter' } }
                        },
                        y: {
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                color: '#64748b',
                                font: { family: 'Inter' },
                                callback: function(value) {
                                    return '$' + (value / 1000).toFixed(1) + 'K';
                                }
                            }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });
        }

        // Initialize analytics chart with Chart.js
        const analyticsChart = document.getElementById('analyticsChart');
        if (analyticsChart) {
            const ctx = analyticsChart.getContext('2d');
            const analyticsData = generateChartAnalyticsData();

            window.analyticsChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: analyticsData.labels,
                    datasets: [{
                        label: 'Orders',
                        data: analyticsData.orders,
                        backgroundColor: 'rgba(52, 152, 219, 0.8)',
                        borderColor: '#3498db',
                        borderWidth: 1
                    }, {
                        label: 'Revenue ($)',
                        data: analyticsData.revenue,
                        backgroundColor: 'rgba(231, 76, 60, 0.8)',
                        borderColor: '#e74c3c',
                        borderWidth: 1,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { font: { family: 'Inter' }, color: '#64748b' }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { family: 'Inter' } }
                        },
                        y: {
                            type: 'linear', display: true, position: 'left',
                            grid: { color: '#f1f5f9' },
                            ticks: { color: '#64748b', font: { family: 'Inter' } }
                        },
                        y1: {
                            type: 'linear', display: true, position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: {
                                color: '#64748b',
                                font: { family: 'Inter' },
                                callback: function(value) {
                                    return '$' + (value / 1000).toFixed(1) + 'K';
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    function refreshDashboard() {
        // Show loading state
        const quickBtn = event.target;
        const originalText = quickBtn.textContent;
        quickBtn.textContent = '🔄 Refreshing...';
        quickBtn.disabled = true;

        // Update all data
        updateDashboardData();
        updateLastUpdatedTime();

        // Track refresh action
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('admin_manual_refresh', {
                section: currentSection,
                admin_id: 'demo_admin'
            });
        }

        // Reset button after 2 seconds
        setTimeout(() => {
            quickBtn.textContent = originalText;
            quickBtn.disabled = false;
        }, 2000);
    }

    function updateOrderStatus(orderId, newStatus) {
        // Show loading state for the select
        const selectElement = event.target;
        selectElement.disabled = true;

        // Simulate API call
        setTimeout(() => {
            showAdminNotification('Order status updated successfully', 'success');

            // Track status change
            if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                window.FoodieDelight.PsychologyEngine.trackEvent('admin_order_status_changed', {
                    order_id: orderId,
                    new_status: newStatus,
                    admin_id: 'demo_admin'
                });
            }

            selectElement.disabled = false;
        }, 1000);
    }

    function viewOrderDetails(orderId) {
        // Track order details view
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('admin_order_details_viewed', {
                order_id: orderId,
                admin_id: 'demo_admin'
            });
        }

        alert(`Order #${orderId} details would open here. This could be a modal with full order information, psychology analysis, customer details, etc.`);
    }

    function filterOrders() {
        const statusFilter = document.getElementById('statusFilter').value;
        const dateFilter = document.getElementById('dateFilter').value;

        // Track filter usage
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('admin_orders_filtered', {
                status_filter: statusFilter,
                date_filter: dateFilter,
                admin_id: 'demo_admin'
            });
        }

        console.log(`Filtering orders by status: ${statusFilter}, date: ${dateFilter}`);
    }

    function exportData() {
        // Track export action
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('admin_data_exported', {
                section: currentSection,
                admin_id: 'demo_admin'
            });
        }

        showAdminNotification('Data export started. Download will begin shortly.', 'info');

        setTimeout(() => {
            showAdminNotification('Export completed successfully!', 'success');
        }, 3000);
    }

    function showAddMenuItem() {
        // Track add menu item action
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('admin_add_menu_item_clicked', {
                admin_id: 'demo_admin'
            });
        }

        alert('Add Menu Item modal would open here. This could include fields for name, description, price, psychology attributes, etc.');
    }

    function exportMenuData() {
        // Track menu export
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('admin_menu_exported', {
                admin_id: 'demo_admin'
            });
        }

        showAdminNotification('Menu data export started', 'info');
    }

    function bulkUpdateItems() {
        alert('Bulk update modal would open here for updating multiple menu items at once.');
    }

    function updateChart(period) {
        const chartBtns = document.querySelectorAll('.chart-btn');
        chartBtns.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        // Track chart period change
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('admin_chart_period_changed', {
                period: period,
                admin_id: <?php echo $_SESSION['user_id']; ?>
            });
        }

        // ADD THIS LINE:
        updateChartData(period);

        console.log(`Updating chart for period: ${period}`);
    }
    function showAdminNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `admin-notification ${type}`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    function setupRealTimeUpdates() {
        // Setup periodic updates for activity feed
        setInterval(() => {
            if (Math.random() > 0.7) {
                addRandomActivity();
            }
        }, 45000);
    }

    function addRandomActivity() {
        const activities = [
            { icon: '📦', text: 'New order received', time: 'Just now' },
            { icon: '👤', text: 'New user registered', time: 'Just now' },
            { icon: '🧠', text: 'Psychology trigger activated', time: 'Just now' },
            { icon: '💰', text: 'Payment completed', time: 'Just now' }
        ];

        const activity = activities[Math.floor(Math.random() * activities.length)];
        const activityFeed = document.getElementById('activityFeed');

        const activityItem = document.createElement('div');
        activityItem.className = 'activity-item new';
        activityItem.innerHTML = `
                <span class="activity-icon">${activity.icon}</span>
                <span class="activity-text">${activity.text}</span>
                <span class="activity-time">${activity.time}</span>
            `;

        activityFeed.insertBefore(activityItem, activityFeed.firstChild);

        // Keep only latest 10 activities
        if (activityFeed.children.length > 10) {
            activityFeed.removeChild(activityFeed.lastChild);
        }
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
</script>
</body>
</html>