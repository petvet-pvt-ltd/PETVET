<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$module = 'sitter';
$GLOBALS['currentPage'] = 'pets.php';
$GLOBALS['module'] = 'sitter';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pets - PetVet</title>
<link rel="stylesheet" href="/PETVET/public/css/sitter/dashboard.css">
<style>
.pets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

.pet-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 5px solid #17a2b8;
    position: relative;
    overflow: hidden;
}

.pet-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.pet-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, transparent 100%);
    border-radius: 0 0 0 100%;
}

.pet-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}

.pet-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    flex-shrink: 0;
}

.pet-title-section {
    flex: 1;
}

.pet-name {
    font-size: 22px;
    font-weight: 700;
    color: #212529;
    margin-bottom: 5px;
}

.pet-type-badge {
    display: inline-block;
    padding: 4px 12px;
    background: #e7f6f8;
    color: #17a2b8;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.pet-details {
    display: grid;
    gap: 12px;
    margin-bottom: 20px;
}

.pet-detail-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: #495057;
}

.detail-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 6px;
    font-size: 14px;
}

.detail-label {
    font-weight: 600;
    color: #6c757d;
    min-width: 80px;
}

.detail-value {
    font-weight: 500;
    color: #212529;
}

.pet-dates {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 15px;
    border-radius: 10px;
    margin-top: 15px;
}

.dates-header {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
}

.dates-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.date-item {
    text-align: center;
}

.date-label {
    font-size: 11px;
    color: #6c757d;
    margin-bottom: 4px;
}

.date-value {
    font-size: 14px;
    font-weight: 700;
    color: #17a2b8;
}

.date-arrow {
    font-size: 20px;
    color: #17a2b8;
}

.no-pets-message {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.no-pets-message .icon {
    font-size: 80px;
    margin-bottom: 20px;
    opacity: 0.3;
}

.no-pets-message p {
    font-size: 18px;
    color: #6c757d;
    margin: 0;
}

@media (max-width: 768px) {
    .main-content {
        padding-top: 80px;
    }
    
    .pets-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>
<?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>
<main class="main-content">
<div class="dashboard-header">
<h1>Current Pets</h1>
<p>Pets in your care</p>
</div>

<?php if (!empty($currentPets)): ?>
<div class="pets-grid">
    <?php foreach ($currentPets as $pet): ?>
    <div class="pet-card">
        <div class="pet-header">
            <div class="pet-icon">
                <?php echo $pet['type'] == 'Dog' ? '🐕' : '🐱'; ?>
            </div>
            <div class="pet-title-section">
                <div class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></div>
                <span class="pet-type-badge"><?php echo htmlspecialchars($pet['type']); ?></span>
            </div>
        </div>
        
        <div class="pet-details">
            <div class="pet-detail-row">
                <div class="detail-icon">🎯</div>
                <span class="detail-label">Breed:</span>
                <span class="detail-value"><?php echo htmlspecialchars($pet['breed']); ?></span>
            </div>
            <div class="pet-detail-row">
                <div class="detail-icon">🎂</div>
                <span class="detail-label">Age:</span>
                <span class="detail-value"><?php echo $pet['age']; ?> years old</span>
            </div>
            <div class="pet-detail-row">
                <div class="detail-icon">👤</div>
                <span class="detail-label">Owner:</span>
                <span class="detail-value"><?php echo htmlspecialchars($pet['owner']); ?></span>
            </div>
        </div>
        
        <div class="pet-dates">
            <div class="dates-header">Sitting Period</div>
            <div class="dates-content">
                <div class="date-item">
                    <div class="date-label">Check-in</div>
                    <div class="date-value"><?php echo date('M d', strtotime($pet['check_in'])); ?></div>
                </div>
                <div class="date-arrow">→</div>
                <div class="date-item">
                    <div class="date-label">Check-out</div>
                    <div class="date-value"><?php echo date('M d, Y', strtotime($pet['check_out'])); ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="no-pets-message">
    <div class="icon">🐾</div>
    <p>No pets currently in care</p>
</div>
<?php endif; ?>
</main>
</body>
</html>
