<?php
session_start();
$_SESSION['current_role'] = 'sitter';
$pageTitle = "Bookings";
$currentPage = "bookings";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PetVet Sitter</title>
    <link rel="stylesheet" href="../../public/css/sidebar/sidebar.css">
    <link rel="stylesheet" href="../../public/css/shared/role-switcher.css">
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

        .booking-filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 16px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 600;
        }

        .filter-btn.active {
            background: #17a2b8;
            color: white;
            border-color: #17a2b8;
        }

        .bookings-grid {
            display: grid;
            gap: 20px;
        }

        .booking-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            border-left: 4px solid #17a2b8;
        }

        .booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .booking-title {
            font-size: 20px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 5px;
        }

        .booking-date {
            color: #17a2b8;
            font-weight: 600;
            font-size: 14px;
        }

        .booking-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .detail-icon {
            font-size: 16px;
        }

        .booking-description {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #495057;
            font-size: 14px;
            line-height: 1.5;
        }

        .booking-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
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
            background: #17a2b8;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-outline {
            background: transparent;
            color: #17a2b8;
            border: 1px solid #17a2b8;
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

            .booking-header {
                flex-direction: column;
                gap: 10px;
            }

            .booking-details {
                grid-template-columns: 1fr;
            }

            .booking-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>🤗 Pet Sitter</h2>
        </div>

        <?php include '../shared/role-switcher.php'; ?>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item active">
                <a href="bookings.php" class="nav-link">
                    <span class="nav-icon">📅</span>
                    <span class="nav-text">Bookings</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="pets.php" class="nav-link">
                    <span class="nav-icon">🐾</span>
                    <span class="nav-text">Pet Profiles</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="services.php" class="nav-link">
                    <span class="nav-icon">⭐</span>
                    <span class="nav-text">Services</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="availability.php" class="nav-link">
                    <span class="nav-icon">⏰</span>
                    <span class="nav-text">Availability</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="reviews.php" class="nav-link">
                    <span class="nav-icon">⭐</span>
                    <span class="nav-text">Reviews</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="earnings.php" class="nav-link">
                    <span class="nav-icon">💰</span>
                    <span class="nav-text">Earnings</span>
                </a>
            </li>
        </ul>
    </nav>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1>Bookings</h1>
                <p>Manage your pet sitting appointments and requests</p>
            </div>
        </div>

        <div class="booking-filters">
            <div class="filter-btn active">All Bookings (23)</div>
            <div class="filter-btn">Pending (5)</div>
            <div class="filter-btn">Confirmed (12)</div>
            <div class="filter-btn">Completed (6)</div>
        </div>

        <div class="bookings-grid">
            <div class="booking-card">
                <div class="booking-header">
                    <div>
                        <div class="booking-title">Luna & Shadow - Dog Walking</div>
                        <div class="booking-date">Today, March 20, 2025</div>
                    </div>
                    <div class="booking-status status-confirmed">Confirmed</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span>Maria Garcia</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span>9:00 AM - 10:00 AM</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">📍</span>
                        <span>Central Park Area</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">💰</span>
                        <span>$25</span>
                    </div>
                </div>
                <div class="booking-description">
                    Two friendly Golden Retrievers need their daily walk. They're well-trained and love meeting other dogs at the park.
                </div>
                <div class="booking-actions">
                    <button class="btn btn-success">Mark Complete</button>
                    <button class="btn btn-outline">Contact Owner</button>
                </div>
            </div>

            <div class="booking-card">
                <div class="booking-header">
                    <div>
                        <div class="booking-title">Whiskers - Pet Sitting</div>
                        <div class="booking-date">March 20-22, 2025</div>
                    </div>
                    <div class="booking-status status-confirmed">Confirmed</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span>Tom Wilson</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span>12:00 PM - 6:00 PM</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">🐱</span>
                        <span>Cat</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">💰</span>
                        <span>$180 (3 days)</span>
                    </div>
                </div>
                <div class="booking-description">
                    Indoor cat needs daily feeding, litter cleaning, and companionship while owner is away on business trip.
                </div>
                <div class="booking-actions">
                    <button class="btn btn-primary">View Details</button>
                    <button class="btn btn-outline">Contact Owner</button>
                </div>
            </div>

            <div class="booking-card">
                <div class="booking-header">
                    <div>
                        <div class="booking-title">Buddy - Dog Walking</div>
                        <div class="booking-date">Today, March 20, 2025</div>
                    </div>
                    <div class="booking-status status-pending">Pending</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span>Lisa Chen</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span>4:00 PM - 5:00 PM</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">🐕</span>
                        <span>Beagle</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">💰</span>
                        <span>$20</span>
                    </div>
                </div>
                <div class="booking-description">
                    Young Beagle needs exercise and socialization. First time client - please review special instructions.
                </div>
                <div class="booking-actions">
                    <button class="btn btn-success">Accept</button>
                    <button class="btn btn-outline">Decline</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>