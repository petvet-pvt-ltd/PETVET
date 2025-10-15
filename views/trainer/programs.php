<?php
session_start();
$_SESSION['current_role'] = 'trainer';
$pageTitle = "Training Programs";
$currentPage = "programs";
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
        }

        .programs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .program-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            border-left: 4px solid #28a745;
        }

        .program-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .program-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .program-icon {
            font-size: 32px;
        }

        .program-title {
            font-size: 20px;
            font-weight: 700;
            color: #212529;
        }

        .program-description {
            color: #6c757d;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .program-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 18px;
            font-weight: 700;
            color: #28a745;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
        }

        .program-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #28a745;
            color: white;
        }

        .btn-primary:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            color: #28a745;
            border: 1px solid #28a745;
        }

        .btn-outline:hover {
            background: #28a745;
            color: white;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .programs-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Training Programs</h1>
            <p>Manage your training programs and services</p>
        </div>

        <div class="programs-grid">
            <div class="program-card">
                <div class="program-header">
                    <div class="program-icon">🎯</div>
                    <div class="program-title">Basic Obedience</div>
                </div>
                <div class="program-description">
                    Foundation training covering sit, stay, come, heel, and down commands. Perfect for puppies and dogs new to training.
                </div>
                <div class="program-stats">
                    <div class="stat">
                        <div class="stat-number">12</div>
                        <div class="stat-label">Active Clients</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Sessions/Week</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">$80</div>
                        <div class="stat-label">Per Session</div>
                    </div>
                </div>
                <div class="program-actions">
                    <button class="btn btn-primary">Manage</button>
                    <button class="btn btn-outline">Edit Program</button>
                </div>
            </div>

            <div class="program-card">
                <div class="program-header">
                    <div class="program-icon">🏃</div>
                    <div class="program-title">Agility Training</div>
                </div>
                <div class="program-description">
                    Advanced training for obstacle courses, jumps, tunnels, and competitive agility. For energetic and athletic dogs.
                </div>
                <div class="program-stats">
                    <div class="stat">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Active Clients</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">6</div>
                        <div class="stat-label">Sessions/Week</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">$120</div>
                        <div class="stat-label">Per Session</div>
                    </div>
                </div>
                <div class="program-actions">
                    <button class="btn btn-primary">Manage</button>
                    <button class="btn btn-outline">Edit Program</button>
                </div>
            </div>

            <div class="program-card">
                <div class="program-header">
                    <div class="program-icon">🧠</div>
                    <div class="program-title">Behavior Modification</div>
                </div>
                <div class="program-description">
                    Specialized training for behavioral issues like aggression, separation anxiety, excessive barking, and fearfulness.
                </div>
                <div class="program-stats">
                    <div class="stat">
                        <div class="stat-number">6</div>
                        <div class="stat-label">Active Clients</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">4</div>
                        <div class="stat-label">Sessions/Week</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">$150</div>
                        <div class="stat-label">Per Session</div>
                    </div>
                </div>
                <div class="program-actions">
                    <button class="btn btn-primary">Manage</button>
                    <button class="btn btn-outline">Edit Program</button>
                </div>
            </div>

            <div class="program-card">
                <div class="program-header">
                    <div class="program-icon">👥</div>
                    <div class="program-title">Group Classes</div>
                </div>
                <div class="program-description">
                    Socialization and basic training in a group setting. Great for puppies and dogs that benefit from social learning.
                </div>
                <div class="program-stats">
                    <div class="stat">
                        <div class="stat-number">15</div>
                        <div class="stat-label">Active Clients</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">3</div>
                        <div class="stat-label">Classes/Week</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">$45</div>
                        <div class="stat-label">Per Session</div>
                    </div>
                </div>
                <div class="program-actions">
                    <button class="btn btn-primary">Manage</button>
                    <button class="btn btn-outline">Edit Program</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>