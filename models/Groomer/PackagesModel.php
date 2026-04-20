<?php
require_once __DIR__ . '/../BaseModel.php';

class GroomerPackagesModel extends BaseModel {
    
    // Retrieve groomer profile ID from database for given user
    private function getGroomerProfileId($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id 
                FROM service_provider_profiles 
                WHERE user_id = ? AND role_type = 'groomer'
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id'] : null;
        } catch (PDOException $e) {
            error_log("Error getting groomer profile ID: " . $e->getMessage());
            return null;
        }
    }
    
    // Fetch all packages for groomer with included services list
    public function getAllPackages($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    id,
                    name,
                    description,
                    original_price,
                    discounted_price,
                    discount_percent,
                    duration,
                    for_dogs,
                    for_cats,
                    available,
                    created_at,
                    updated_at
                FROM groomer_packages
                WHERE user_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Cast boolean and numeric fields to correct types
            foreach ($packages as &$package) {
                $package['for_dogs'] = (bool)$package['for_dogs'];
                $package['for_cats'] = (bool)$package['for_cats'];
                $package['available'] = (bool)$package['available'];
                $package['original_price'] = (float)$package['original_price'];
                $package['discounted_price'] = (float)$package['discounted_price'];
                $package['discount_percent'] = (float)$package['discount_percent'];
                
                // Load included services for each package
                $package['included_services'] = $this->getPackageServices($package['id']);
            }
            
            return $packages;
        } catch (PDOException $e) {
            error_log("Error fetching groomer packages: " . $e->getMessage());
            return [];
        }
    }
    
    // Get comma-separated service names for a specific package
    private function getPackageServices($packageId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT gs.name
                FROM groomer_package_services gps
                JOIN groomer_services gs ON gps.service_id = gs.id
                WHERE gps.package_id = ?
                ORDER BY gs.name
            ");
            $stmt->execute([$packageId]);
            $services = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            return implode(', ', $services);
        } catch (PDOException $e) {
            error_log("Error fetching package services: " . $e->getMessage());
            return '';
        }
    }
    
    // Get service IDs linked to a specific package
    private function getPackageServiceIds($packageId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT service_id
                FROM groomer_package_services
                WHERE package_id = ?
            ");
            $stmt->execute([$packageId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error fetching package service IDs: " . $e->getMessage());
            return [];
        }
    }
    
    // Create new package with services in transaction
    public function addPackage($data) {
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Get or create groomer profile if needed
            $profileId = $this->getGroomerProfileId($data['user_id']);
            
            if (!$profileId) {
                // Create profile if it doesn't exist
                $stmt = $this->pdo->prepare("
                    INSERT INTO service_provider_profiles 
                    (user_id, role_type, business_name, available) 
                    VALUES (?, 'groomer', '', 1)
                ");
                $stmt->execute([$data['user_id']]);
                $profileId = $this->pdo->lastInsertId();
            }
            
            // Insert package record
            $stmt = $this->pdo->prepare("
                INSERT INTO groomer_packages 
                (provider_profile_id, user_id, name, description, original_price, discounted_price, duration, for_dogs, for_cats, available) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");
            
            $stmt->execute([
                $profileId,
                $data['user_id'],
                $data['name'],
                $data['description'] ?? '',
                $data['original_price'],
                $data['discounted_price'],
                $data['duration'] ?? null,
                $data['for_dogs'] ? 1 : 0,
                $data['for_cats'] ? 1 : 0
            ]);
            
            $packageId = $this->pdo->lastInsertId();
            
            // Link services to package
            if (!empty($data['service_ids'])) {
                $this->updatePackageServices($packageId, $data['service_ids']);
            }
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Package added successfully',
                'package_id' => $packageId
            ];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error adding groomer package: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to add package: ' . $e->getMessage()
            ];
        }
    }
    
    // Update existing package and its linked services
    public function updatePackage($packageId, $data) {
        try {
            $this->pdo->beginTransaction();
            
            // Update package record
            $stmt = $this->pdo->prepare("
                UPDATE groomer_packages 
                SET name = ?, 
                    description = ?, 
                    original_price = ?,
                    discounted_price = ?, 
                    duration = ?, 
                    for_dogs = ?, 
                    for_cats = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND user_id = ?
            ");
            
            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? '',
                $data['original_price'],
                $data['discounted_price'],
                $data['duration'] ?? null,
                $data['for_dogs'] ? 1 : 0,
                $data['for_cats'] ? 1 : 0,
                $packageId,
                $data['user_id']
            ]);
            
            if ($result && $stmt->rowCount() > 0) {
                // Update service associations if provided
                if (isset($data['service_ids'])) {
                    $this->updatePackageServices($packageId, $data['service_ids']);
                }
                
                $this->pdo->commit();
                
                return [
                    'success' => true,
                    'message' => 'Package updated successfully'
                ];
            } else {
                $this->pdo->rollBack();
                return [
                    'success' => false,
                    'message' => 'Package not found or no changes made'
                ];
            }
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error updating groomer package: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update package: ' . $e->getMessage()
            ];
        }
    }
    
    // Replace package service associations
    private function updatePackageServices($packageId, $serviceIds) {
        try {
            // Remove old service relationships
            $stmt = $this->pdo->prepare("DELETE FROM groomer_package_services WHERE package_id = ?");
            $stmt->execute([$packageId]);
            
            // Add new service relationships
            if (!empty($serviceIds)) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO groomer_package_services (package_id, service_id) 
                    VALUES (?, ?)
                ");
                
                foreach ($serviceIds as $serviceId) {
                    $stmt->execute([$packageId, $serviceId]);
                }
            }
        } catch (PDOException $e) {
            error_log("Error updating package services: " . $e->getMessage());
            throw $e;
        }
    }
    
    // Remove package permanently with cascade delete on services
    public function deletePackage($packageId, $userId) {
        try {
            // Delete package (cascade delete handles package_services)
            $stmt = $this->pdo->prepare("
                DELETE FROM groomer_packages 
                WHERE id = ? AND user_id = ?
            ");
            
            $result = $stmt->execute([$packageId, $userId]);
            
            if ($result && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Package deleted successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Package not found or already deleted'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error deleting groomer package: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete package: ' . $e->getMessage()
            ];
        }
    }
    
    // Switch package availability status between available and unavailable
    public function toggleAvailability($packageId, $userId) {
        try {
            // Fetch current availability state
            $stmt = $this->pdo->prepare("
                SELECT available 
                FROM groomer_packages 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$packageId, $userId]);
            $package = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$package) {
                return [
                    'success' => false,
                    'message' => 'Package not found'
                ];
            }
            
            // Switch availability status
            $newAvailability = !$package['available'];
            $stmt = $this->pdo->prepare("
                UPDATE groomer_packages 
                SET available = ?, 
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND user_id = ?
            ");
            
            $result = $stmt->execute([$newAvailability ? 1 : 0, $packageId, $userId]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Package availability updated',
                    'available' => $newAvailability
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update availability'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error toggling package availability: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to toggle availability: ' . $e->getMessage()
            ];
        }
    }
    
    // Retrieve single package with all details and service IDs
    public function getPackageById($packageId, $userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM groomer_packages 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$packageId, $userId]);
            $package = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($package) {
                // Cast fields to proper types
                $package['for_dogs'] = (bool)$package['for_dogs'];
                $package['for_cats'] = (bool)$package['for_cats'];
                $package['available'] = (bool)$package['available'];
                $package['original_price'] = (float)$package['original_price'];
                $package['discounted_price'] = (float)$package['discounted_price'];
                $package['discount_percent'] = (float)$package['discount_percent'];
                // Load service names and IDs
                $package['included_services'] = $this->getPackageServices($packageId);
                $package['service_ids'] = $this->getPackageServiceIds($packageId);
            }
            
            return $package;
        } catch (PDOException $e) {
            error_log("Error fetching package by ID: " . $e->getMessage());
            return null;
        }
    }
}
