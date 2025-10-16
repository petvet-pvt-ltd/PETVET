<?php
// This view expects data from BreederController::breedingPets()
// Variables available: $breedingPets

if (!isset($breedingPets)) $breedingPets = [];

$module = 'breeder';
$currentPage = 'breeding-pets.php';
$GLOBALS['module'] = $module;
$GLOBALS['currentPage'] = $currentPage;
require_once dirname(__DIR__, 2) . '/config/config.php';
$pageTitle = "Breeding Pets";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PetVet Breeder</title>
    <link rel="stylesheet" href="<?= asset('css/breeder/breeding-pets.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../shared/sidebar/sidebar.php'; ?>

    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Breeding Pets</h1>
                <p>Manage your breeding pets available for breeding services</p>
            </div>
            <button class="btn btn-primary" onclick="showAddPetModal()">
                <span class="btn-icon">+</span> Add New Breeding Pet
            </button>
        </div>

        <!-- Pets Table -->
        <div class="table-container">
            <table class="pets-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Breed</th>
                        <th>Gender</th>
                        <th>Date of Birth</th>
                        <th>Age</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($breedingPets)): ?>
                        <?php foreach ($breedingPets as $pet): ?>
                        <tr data-pet-id="<?php echo $pet['id']; ?>">
                            <td>
                                <div class="pet-photo">
                                    <?php if (!empty($pet['photo'])): ?>
                                        <img src="<?php echo htmlspecialchars($pet['photo']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                                    <?php else: ?>
                                        <div class="pet-photo-placeholder">
                                            <?php echo $pet['gender'] == 'Male' ? '🐕' : '🐕'; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($pet['name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($pet['breed']); ?></td>
                            <td>
                                <span class="gender-badge gender-<?php echo strtolower($pet['gender']); ?>">
                                    <?php echo htmlspecialchars($pet['gender']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($pet['dob'])); ?></td>
                            <td><?php echo $pet['age']; ?></td>
                            <td>
                                <label class="toggle-switch">
                                    <input type="checkbox" 
                                           <?php echo $pet['is_active'] ? 'checked' : ''; ?>
                                           onchange="togglePetStatus(<?php echo $pet['id']; ?>, this.checked)">
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="status-text"><?php echo $pet['is_active'] ? 'Active' : 'Inactive'; ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon-action" onclick="showEditPetModal(<?php echo $pet['id']; ?>)" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                        </svg>
                                    </button>
                                    <button class="btn-icon-action delete" onclick="showDeleteConfirmation(<?php echo $pet['id']; ?>, '<?php echo htmlspecialchars($pet['name'], ENT_QUOTES); ?>')" title="Delete">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-data">
                                <div class="no-data-message">
                                    <div class="no-data-icon">🐾</div>
                                    <p>No breeding pets added yet</p>
                                    <button class="btn btn-primary" onclick="showAddPetModal()">Add Your First Pet</button>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Add/Edit Pet Modal -->
    <div id="petModal" class="modal-overlay">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Breeding Pet</h3>
                <button class="modal-close" onclick="closePetModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="petForm" enctype="multipart/form-data">
                    <input type="hidden" id="petId" name="pet_id">
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="petPhoto">Pet Photo</label>
                            <div class="photo-upload">
                                <div class="photo-preview" id="photoPreview">
                                    <span class="photo-placeholder">📷</span>
                                </div>
                                <input type="file" id="petPhoto" name="photo" accept="image/*" onchange="previewPhoto(event)">
                                <label for="petPhoto" class="photo-upload-btn">Choose Photo</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="petName">Pet Name *</label>
                            <input type="text" id="petName" name="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="petBreed">Breed *</label>
                            <input type="text" id="petBreed" name="breed" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="petGender">Gender *</label>
                            <select id="petGender" name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="petDob">Date of Birth *</label>
                            <input type="date" id="petDob" name="dob" class="form-control" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="petDescription">Description (Optional)</label>
                            <textarea id="petDescription" name="description" class="form-control" rows="3" placeholder="Add any additional information about the pet..."></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label class="checkbox-label">
                                <input type="checkbox" id="petActive" name="is_active" checked>
                                <span>Active (Available for breeding)</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closePetModal()">Cancel</button>
                <button class="btn btn-primary" onclick="savePet()">Save Pet</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h3>Confirm Deletion</h3>
                <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="delete-warning">
                    <div class="warning-icon">⚠️</div>
                    <p>Are you sure you want to delete <strong id="deletePetName"></strong>?</p>
                    <p class="warning-text">This action cannot be undone.</p>
                </div>
                <input type="hidden" id="deletePetId">
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDelete()">Delete Pet</button>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const breedingPetsData = <?php echo json_encode($breedingPets); ?>;
    </script>
    <script src="<?= asset('js/breeder/breeding-pets.js') ?>"></script>
</body>
</html>
