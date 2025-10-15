<?php
session_start();
$_SESSION['current_role'] = 'breeder';
$pageTitle = "Available Pets";
$currentPage = "available-pets";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PetVet Breeder</title>
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

        .pets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .pet-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            border-left: 4px solid #fd7e14;
        }

        .pet-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .pet-image {
            height: 200px;
            background: linear-gradient(135deg, #fd7e14, #e55a4e);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
        }

        .pet-content {
            padding: 20px;
        }

        .pet-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }

        .pet-name {
            font-size: 20px;
            font-weight: 700;
            color: #212529;
        }

        .pet-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
        }

        .status-reserved {
            background: #fff3cd;
            color: #856404;
        }

        .pet-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
        }

        .detail-label {
            color: #6c757d;
        }

        .detail-value {
            font-weight: 600;
            color: #212529;
        }

        .pet-description {
            color: #495057;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .pet-price {
            font-size: 24px;
            font-weight: 700;
            color: #fd7e14;
            margin-bottom: 15px;
        }

        .pet-actions {
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
            flex: 1;
            text-align: center;
        }

        .btn-primary {
            background: #fd7e14;
            color: white;
        }

        .btn-outline {
            background: transparent;
            color: #fd7e14;
            border: 1px solid #fd7e14;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #495057;
        }

        .filter-group select {
            padding: 6px 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
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

            .pets-grid {
                grid-template-columns: 1fr;
            }

            .filter-row {
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
                <h1>Available Pets</h1>
                <p>Manage your available puppies and breeding announcements</p>
            </div>
            <button class="btn btn-primary">Add New Listing</button>
        </div>

        <div class="filter-section">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Breed</label>
                    <select>
                        <option>All Breeds</option>
                        <option>Golden Retriever</option>
                        <option>Labrador</option>
                        <option>German Shepherd</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Status</label>
                    <select>
                        <option>All Status</option>
                        <option>Available</option>
                        <option>Reserved</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Age</label>
                    <select>
                        <option>All Ages</option>
                        <option>8-12 weeks</option>
                        <option>12-16 weeks</option>
                        <option>16+ weeks</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Gender</label>
                    <select>
                        <option>All</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="pets-grid">
            <div class="pet-card">
                <div class="pet-image">🐕</div>
                <div class="pet-content">
                    <div class="pet-header">
                        <div class="pet-name">Golden Puppy #1</div>
                        <div class="pet-status status-available">Available</div>
                    </div>
                    <div class="pet-details">
                        <div class="detail-row">
                            <span class="detail-label">Breed:</span>
                            <span class="detail-value">Golden Retriever</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Gender:</span>
                            <span class="detail-value">Male</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Age:</span>
                            <span class="detail-value">10 weeks</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Color:</span>
                            <span class="detail-value">Golden</span>
                        </div>
                    </div>
                    <div class="pet-description">
                        Beautiful golden retriever puppy with excellent temperament. Parents are both champion bloodlines with health clearances.
                    </div>
                    <div class="pet-price">$1,200</div>
                    <div class="pet-actions">
                        <button class="btn btn-primary">View Details</button>
                        <button class="btn btn-outline">Edit</button>
                    </div>
                </div>
            </div>

            <div class="pet-card">
                <div class="pet-image">🐕‍🦺</div>
                <div class="pet-content">
                    <div class="pet-header">
                        <div class="pet-name">Golden Puppy #2</div>
                        <div class="pet-status status-reserved">Reserved</div>
                    </div>
                    <div class="pet-details">
                        <div class="detail-row">
                            <span class="detail-label">Breed:</span>
                            <span class="detail-value">Golden Retriever</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Gender:</span>
                            <span class="detail-value">Female</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Age:</span>
                            <span class="detail-value">10 weeks</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Color:</span>
                            <span class="detail-value">Light Golden</span>
                        </div>
                    </div>
                    <div class="pet-description">
                        Sweet female golden retriever puppy. Very social and loves children. Reserved for the Johnson family.
                    </div>
                    <div class="pet-price">$1,200</div>
                    <div class="pet-actions">
                        <button class="btn btn-primary">View Details</button>
                        <button class="btn btn-outline">Edit</button>
                    </div>
                </div>
            </div>

            <div class="pet-card">
                <div class="pet-image">🐶</div>
                <div class="pet-content">
                    <div class="pet-header">
                        <div class="pet-name">Labrador Puppy #1</div>
                        <div class="pet-status status-available">Available</div>
                    </div>
                    <div class="pet-details">
                        <div class="detail-row">
                            <span class="detail-label">Breed:</span>
                            <span class="detail-value">Labrador</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Gender:</span>
                            <span class="detail-value">Male</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Age:</span>
                            <span class="detail-value">12 weeks</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Color:</span>
                            <span class="detail-value">Yellow</span>
                        </div>
                    </div>
                    <div class="pet-description">
                        Energetic yellow labrador puppy. Great with kids and other dogs. Parents have excellent hunting bloodlines.
                    </div>
                    <div class="pet-price">$1,000</div>
                    <div class="pet-actions">
                        <button class="btn btn-primary">View Details</button>
                        <button class="btn btn-outline">Edit</button>
                    </div>
                </div>
            </div>

            <div class="pet-card">
                <div class="pet-image">🐕‍🦺</div>
                <div class="pet-content">
                    <div class="pet-header">
                        <div class="pet-name">German Shepherd #1</div>
                        <div class="pet-status status-available">Available</div>
                    </div>
                    <div class="pet-details">
                        <div class="detail-row">
                            <span class="detail-label">Breed:</span>
                            <span class="detail-value">German Shepherd</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Gender:</span>
                            <span class="detail-value">Female</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Age:</span>
                            <span class="detail-value">14 weeks</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Color:</span>
                            <span class="detail-value">Black & Tan</span>
                        </div>
                    </div>
                    <div class="pet-description">
                        Intelligent German Shepherd puppy with strong protective instincts. Both parents are working line with health clearances.
                    </div>
                    <div class="pet-price">$1,500</div>
                    <div class="pet-actions">
                        <button class="btn btn-primary">View Details</button>
                        <button class="btn btn-outline">Edit</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>