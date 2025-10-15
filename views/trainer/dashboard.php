<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'trainer';
$GLOBALS['currentPage'] = 'dashboard.php';
$GLOBALS['module'] = 'trainer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Dashboard - PetVet</title>
    <link rel="stylesheet" href="/PETVET/public/css/trainer/dashboard.css">
    <style>
        /* Trainer Dashboard Specific Styles */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .dashboard-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .dashboard-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #28a745;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #28a745;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 14px;
            font-weight: 600;
        }

        .dashboard-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .content-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .recent-activity {
            list-style: none;
            padding: 0;
        }

        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            background: #e3f2fd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: #212529;
            margin-bottom: 4px;
        }

        .activity-time {
            font-size: 12px;
            color: #6c757d;
        }

        .upcoming-sessions {
            list-style: none;
            padding: 0;
        }

        .session-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 4px solid #28a745;
        }

        .session-time {
            font-weight: 700;
            color: #28a745;
            font-size: 14px;
        }

        .session-pet {
            font-weight: 600;
            color: #212529;
            margin: 4px 0;
        }

        .session-type {
            font-size: 12px;
            color: #6c757d;
        }

        /* Mobile responsive */
        @media (max-width: 1200px) {
            .dashboard-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header {
                padding: 20px;
            }
            
            .dashboard-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>

    <main class="main-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Welcome, Trainer!</h1>
            <p class="dashboard-subtitle">Manage your training sessions and clients</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🐕</div>
                <div class="stat-number"><?php echo $stats['total_clients']; ?></div>
                <div class="stat-label">Total Clients</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-number"><?php echo $stats['active_sessions']; ?></div>
                <div class="stat-label">Active Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-number"><?php echo $stats['completed_sessions']; ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-number">$<?php echo number_format($stats['monthly_earnings']); ?></div>
                <div class="stat-label">Monthly Earnings</div>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="content-section">
                <h2 class="section-title">⏰ Upcoming Sessions</h2>
                <ul class="upcoming-sessions">
                    <?php foreach ($upcomingSessions as $session): ?>
                    <li class="session-item">
                        <div class="session-time"><?php echo htmlspecialchars($session['time']); ?> - <?php echo htmlspecialchars($session['date']); ?></div>
                        <div class="session-pet"><?php echo htmlspecialchars($session['pet_name']); ?> - <?php echo htmlspecialchars($session['session_type']); ?></div>
                        <div class="session-type">Client: <?php echo htmlspecialchars($session['client_name']); ?></div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="content-section">
                <h2 class="section-title">👥 Recent Clients</h2>
                <ul class="recent-activity">
                    <?php foreach ($recentClients as $client): ?>
                    <li class="activity-item">
                        <div class="activity-icon">�</div>
                        <div class="activity-content">
                            <div class="activity-title"><?php echo htmlspecialchars($client['name']); ?> - <?php echo htmlspecialchars($client['pet']); ?></div>
                            <div class="activity-time">Joined: <?php echo date('M d, Y', strtotime($client['joined'])); ?></div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </main>
</body>
</html>