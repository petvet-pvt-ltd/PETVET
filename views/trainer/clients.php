<?php
// Session is already started in index.php
$_SESSION['current_role'] = 'trainer';
$pageTitle = "Client Pets";
$currentPage = "clients";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PetVet Trainer</title>
    <link rel="stylesheet" href="../../public/css/sidebar/sidebar.css">
    <style>
        .main-content {
            margin-left: 280px;
            padding: 30px;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .page-header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .clients-list {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .client-item {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            transition: background 0.2s ease;
        }

        .client-item:hover {
            background: #f8f9fa;
        }

        .client-item:last-child {
            border-bottom: none;
        }

        .client-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .pet-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .client-info {
            flex: 1;
        }

        .pet-name {
            font-size: 18px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 4px;
        }

        .owner-name {
            color: #6c757d;
            font-size: 14px;
        }

        .client-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: #495057;
        }

        .progress-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .progress-title {
            font-weight: 600;
            color: #212529;
            margin-bottom: 10px;
        }

        .progress-bar {
            background: #e9ecef;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 12px;
            color: #6c757d;
        }

        .client-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-primary {
            background: #28a745;
            color: white;
        }

        .btn-outline {
            background: transparent;
            color: #28a745;
            border: 1px solid #28a745;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-tab {
            padding: 8px 16px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 600;
        }

        .filter-tab.active {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }

            .client-meta {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1>Client Pets</h1>
                <p>Manage your training clients and their progress</p>
            </div>
            <button class="btn btn-primary">Add New Client</button>
        </div>

        <div class="filter-tabs">
            <div class="filter-tab active">All Clients (24)</div>
            <div class="filter-tab">Active Training (18)</div>
            <div class="filter-tab">Completed (6)</div>
        </div>

        <div class="clients-list">
            <div class="client-item">
                <div class="client-header">
                    <div class="pet-avatar">🐕</div>
                    <div class="client-info">
                        <div class="pet-name">Max</div>
                        <div class="owner-name">Owner: John Smith</div>
                    </div>
                </div>
                <div class="client-meta">
                    <div class="meta-item">
                        <span>🎯</span>
                        <span>Basic Obedience</span>
                    </div>
                    <div class="meta-item">
                        <span>📅</span>
                        <span>Started: Mar 15, 2025</span>
                    </div>
                    <div class="meta-item">
                        <span>⏰</span>
                        <span>Next: Today 2:00 PM</span>
                    </div>
                </div>
                <div class="progress-section">
                    <div class="progress-title">Training Progress</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 75%"></div>
                    </div>
                    <div class="progress-text">75% Complete - Excellent progress with sit and stay commands</div>
                </div>
                <div class="client-actions">
                    <button class="btn btn-primary">View Progress</button>
                    <button class="btn btn-outline">Schedule Session</button>
                </div>
            </div>

            <div class="client-item">
                <div class="client-header">
                    <div class="pet-avatar">🐕‍🦺</div>
                    <div class="client-info">
                        <div class="pet-name">Bella</div>
                        <div class="owner-name">Owner: Sarah Johnson</div>
                    </div>
                </div>
                <div class="client-meta">
                    <div class="meta-item">
                        <span>🏃</span>
                        <span>Agility Training</span>
                    </div>
                    <div class="meta-item">
                        <span>📅</span>
                        <span>Started: Feb 28, 2025</span>
                    </div>
                    <div class="meta-item">
                        <span>⏰</span>
                        <span>Next: Tomorrow 4:00 PM</span>
                    </div>
                </div>
                <div class="progress-section">
                    <div class="progress-title">Training Progress</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 90%"></div>
                    </div>
                    <div class="progress-text">90% Complete - Ready for advanced obstacle courses</div>
                </div>
                <div class="client-actions">
                    <button class="btn btn-primary">View Progress</button>
                    <button class="btn btn-outline">Schedule Session</button>
                </div>
            </div>

            <div class="client-item">
                <div class="client-header">
                    <div class="pet-avatar">🐶</div>
                    <div class="client-info">
                        <div class="pet-name">Rocky</div>
                        <div class="owner-name">Owner: Mike Davis</div>
                    </div>
                </div>
                <div class="client-meta">
                    <div class="meta-item">
                        <span>🧠</span>
                        <span>Behavior Modification</span>
                    </div>
                    <div class="meta-item">
                        <span>📅</span>
                        <span>Started: Mar 10, 2025</span>
                    </div>
                    <div class="meta-item">
                        <span>⏰</span>
                        <span>Next: Wed 6:00 PM</span>
                    </div>
                </div>
                <div class="progress-section">
                    <div class="progress-title">Training Progress</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 45%"></div>
                    </div>
                    <div class="progress-text">45% Complete - Showing improvement in aggressive behaviors</div>
                </div>
                <div class="client-actions">
                    <button class="btn btn-primary">View Progress</button>
                    <button class="btn btn-outline">Schedule Session</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>