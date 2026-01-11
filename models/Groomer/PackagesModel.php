<?php
require_once __DIR__ . '/../BaseModel.php';

class GroomerPackagesModel extends BaseModel {
    
    /**
     * Get the groomer's profile ID for a given user
     */
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
    
    /**
     * Get all packages for a groomer with their included services
     */
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
            
            // For each package, get the included services
            foreach ($packages as &$package) {
                $package['for_dogs'] = (bool)$package['for_dogs'];
                $package['for_cats'] = (bool)$package['for_cats'];
                $package['available'] = (bool)$package['available'];
                $package['original_price'] = (float)$package['original_price'];
                $package['discounted_price'] = (float)$package['discounted_price'];
                $package['discount_percent'] = (float)$package['discount_percent'];
                
                // Get included services
                $package['included_services'] = $this->getPackageServices($package['id']);
            }
            
            return $packages;
        } catch (PDOException $e) {
            error_log("Error fetching groomer packages: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get included services for a package (comma-separated names)
     */
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
    
    /**
     * Get service IDs for a package
     */
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
    
    /**
     * Add a new package
     */
    public function addPackage($data) {
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Get or create groomer profile
            $profileId = $this->getGroomerProfileId($data['user_id']);
            
            if (!$profileId) {
                // Create groomer profile if doesn't exist
                $stmt = $this->pdo->prepare("
                    INSERT INTO service_provider_profiles 
                    (user_id, role_type, business_name, available) 
                    VALUES (?, 'groomer', '', 1)
                ");
                $stmt->execute([$data['user_id']]);
                $profileId = $this->pdo->lastInsertId();
            }
            
            // Insert package
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
            
            // Insert package-service relationships
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
    
    /**
     * Update an existing package
     */
    public function updatePackage($packageId, $data) {
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
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
                // Update package-service relationships
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
    
    /**
     * Update package-service relationships
     */
    private function updatePackageServices($packageId, $serviceIds) {
        try {
            // Delete existing relationships
            $stmt = $this->pdo->prepare("DELETE FROM groomer_package_services WHERE package_id = ?");
            $stmt->execute([$packageId]);
            
            // Insert new relationships
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
    
    /**
     * Delete a package
     */
    public function deletePackage($packageId, $userId) {
        try {
            // The cascade delete will automatically remove package_services entries
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
    
    /**
     * Toggle package availability
     */
    public function toggleAvailability($packageId, $userId) {
        try {
            // First get current availability
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
            
            // Toggle availability
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
    
    /**
     * Get a single package by ID
     */
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
                $package['for_dogs'] = (bool)$package['for_dogs'];
                $package['for_cats'] = (bool)$package['for_cats'];
                $package['available'] = (bool)$package['available'];
                $package['original_price'] = (float)$package['original_price'];
                $package['discounted_price'] = (float)$package['discounted_price'];
                $package['discount_percent'] = (float)$package['discount_percent'];
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
