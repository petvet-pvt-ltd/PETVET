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
    <style>
        .main-content {
            margin-left: 280px;
            padding: 30px;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .page-header {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            color: white;
            margin: 0 0 5px 0;
            font-size: 28px;
            font-weight: 700;
        }

        .page-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-size: 16px;
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
                padding-top: 80px;
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
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1>Bookings</h1>
                <p>Manage your pet sitting appointments and requests</p>
            </div>
        </div>

        <div class="booking-filters">
            <div class="filter-btn active" data-filter="pending" onclick="filterBookings('pending')">
                Pending (<?php echo count($pendingBookings); ?>)
            </div>
            <div class="filter-btn" data-filter="confirmed" onclick="filterBookings('confirmed')">
                Confirmed (<?php echo count($activeBookings); ?>)
            </div>
            <div class="filter-btn" data-filter="completed" onclick="filterBookings('completed')">
                Completed (<?php echo count($completedBookings); ?>)
            </div>
        </div>

        <div class="bookings-grid" id="bookingsGrid">
            <!-- Pending Bookings -->
            <?php foreach ($pendingBookings as $booking): ?>
            <div class="booking-card" data-status="pending">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($booking['pet_name']); ?> - <?php echo htmlspecialchars($booking['service_type']); ?></div>
                        <div class="booking-date"><?php echo date('M d, Y', strtotime($booking['start_date'])); ?><?php if ($booking['start_date'] != $booking['end_date']): ?> - <?php echo date('M d, Y', strtotime($booking['end_date'])); ?><?php endif; ?></div>
                    </div>
                    <div class="booking-status status-pending">Pending</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($booking['owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span><?php echo htmlspecialchars($booking['start_time']); ?> - <?php echo htmlspecialchars($booking['end_time']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon"><?php echo $booking['pet_type'] == 'Dog' ? '�' : '🐱'; ?></span>
                        <span><?php echo htmlspecialchars($booking['pet_breed']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">�</span>
                        <span><?php echo htmlspecialchars($booking['location']); ?></span>
                    </div>
                </div>
                <div class="booking-description">
                    <?php echo htmlspecialchars($booking['special_notes']); ?>
                </div>
                <div class="booking-actions">
                    <button class="btn btn-success">Accept</button>
                    <button class="btn btn-outline">Decline</button>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Confirmed Bookings -->
            <?php foreach ($activeBookings as $booking): ?>
            <div class="booking-card" data-status="confirmed" style="display: none;">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($booking['pet_name']); ?> - <?php echo htmlspecialchars($booking['service_type']); ?></div>
                        <div class="booking-date"><?php echo date('M d, Y', strtotime($booking['start_date'])); ?><?php if ($booking['start_date'] != $booking['end_date']): ?> - <?php echo date('M d, Y', strtotime($booking['end_date'])); ?><?php endif; ?></div>
                    </div>
                    <div class="booking-status status-confirmed">Confirmed</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($booking['owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span><?php echo htmlspecialchars($booking['start_time']); ?> - <?php echo htmlspecialchars($booking['end_time']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon"><?php echo $booking['pet_type'] == 'Dog' ? '🐕' : '🐱'; ?></span>
                        <span><?php echo htmlspecialchars($booking['pet_breed']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">�</span>
                        <span><?php echo htmlspecialchars($booking['location']); ?></span>
                    </div>
                </div>
                <div class="booking-description">
                    <?php echo htmlspecialchars($booking['special_notes']); ?>
                </div>
                <div class="booking-actions">
                    <button class="btn btn-success">Mark Complete</button>
                    <button class="btn btn-outline">Contact Owner</button>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Completed Bookings -->
            <?php foreach ($completedBookings as $booking): ?>
            <div class="booking-card" data-status="completed" style="display: none;">
                <div class="booking-header">
                    <div>
                        <div class="booking-title"><?php echo htmlspecialchars($booking['pet_name']); ?> - <?php echo htmlspecialchars($booking['service_type']); ?></div>
                        <div class="booking-date"><?php echo date('M d, Y', strtotime($booking['start_date'])); ?><?php if ($booking['start_date'] != $booking['end_date']): ?> - <?php echo date('M d, Y', strtotime($booking['end_date'])); ?><?php endif; ?></div>
                    </div>
                    <div class="booking-status status-completed">Completed</div>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span><?php echo htmlspecialchars($booking['owner_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">⏰</span>
                        <span><?php echo htmlspecialchars($booking['start_time']); ?> - <?php echo htmlspecialchars($booking['end_time']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon"><?php echo $booking['pet_type'] == 'Dog' ? '🐕' : '🐱'; ?></span>
                        <span><?php echo htmlspecialchars($booking['pet_breed']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">�</span>
                        <span><?php echo htmlspecialchars($booking['location']); ?></span>
                    </div>
                </div>
                <div class="booking-description">
                    <?php echo htmlspecialchars($booking['special_notes']); ?>
                </div>
                <div class="booking-actions">
                    <button class="btn btn-outline">View Details</button>
                    <button class="btn btn-outline">Contact Owner</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        function filterBookings(status) {
            // Get all booking cards
            const cards = document.querySelectorAll('.booking-card');
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            const activeButton = document.querySelector(`[data-filter="${status}"]`);
            if (activeButton) {
                activeButton.classList.add('active');
            }
            
            // Show/hide cards based on status
            cards.forEach(card => {
                if (card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Initialize: show pending bookings by default
        document.addEventListener('DOMContentLoaded', function() {
            filterBookings('pending');
        });
    </script>
</body>
</html>