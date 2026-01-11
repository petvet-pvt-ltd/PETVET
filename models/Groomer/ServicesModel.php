<?php
require_once __DIR__ . '/../BaseModel.php';

class GroomerServicesModel extends BaseModel {
    
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
     * Get all services for a groomer
     */
    public function getAllServices($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    id,
                    name,
                    description,
                    price,
                    duration,
                    for_dogs,
                    for_cats,
                    available,
                    created_at,
                    updated_at
                FROM groomer_services
                WHERE user_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert tinyint to boolean for frontend
            foreach ($services as &$service) {
                $service['for_dogs'] = (bool)$service['for_dogs'];
                $service['for_cats'] = (bool)$service['for_cats'];
                $service['available'] = (bool)$service['available'];
                $service['price'] = (float)$service['price'];
            }
            
            return $services;
        } catch (PDOException $e) {
            error_log("Error fetching groomer services: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Add a new service
     */
    public function addService($data) {
        try {
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
            
            // Insert service
            $stmt = $this->pdo->prepare("
                INSERT INTO groomer_services 
                (provider_profile_id, user_id, name, description, price, duration, for_dogs, for_cats, available) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");
            
            $stmt->execute([
                $profileId,
                $data['user_id'],
                $data['name'],
                $data['description'] ?? '',
                $data['price'],
                $data['duration'] ?? null,
                $data['for_dogs'] ? 1 : 0,
                $data['for_cats'] ? 1 : 0
            ]);
            
            return [
                'success' => true,
                'message' => 'Service added successfully',
                'service_id' => $this->pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            error_log("Error adding groomer service: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to add service: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update an existing service
     */
    public function updateService($serviceId, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE groomer_services 
                SET name = ?, 
                    description = ?, 
                    price = ?, 
                    duration = ?, 
                    for_dogs = ?, 
                    for_cats = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND user_id = ?
            ");
            
            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? '',
                $data['price'],
                $data['duration'] ?? null,
                $data['for_dogs'] ? 1 : 0,
                $data['for_cats'] ? 1 : 0,
                $serviceId,
                $data['user_id']
            ]);
            
            if ($result && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Service updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Service not found or no changes made'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error updating groomer service: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update service: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete a service
     */
    public function deleteService($serviceId, $userId) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM groomer_services 
                WHERE id = ? AND user_id = ?
            ");
            
            $result = $stmt->execute([$serviceId, $userId]);
            
            if ($result && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Service deleted successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Service not found or already deleted'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error deleting groomer service: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete service: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Toggle service availability
     */
    public function toggleAvailability($serviceId, $userId) {
        try {
            // First get current availability
            $stmt = $this->pdo->prepare("
                SELECT available 
                FROM groomer_services 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$serviceId, $userId]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$service) {
                return [
                    'success' => false,
                    'message' => 'Service not found'
                ];
            }
            
            // Toggle availability
            $newAvailability = !$service['available'];
            $stmt = $this->pdo->prepare("
                UPDATE groomer_services 
                SET available = ?, 
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND user_id = ?
            ");
            
            $result = $stmt->execute([$newAvailability ? 1 : 0, $serviceId, $userId]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Service availability updated',
                    'available' => $newAvailability
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update availability'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error toggling service availability: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to toggle availability: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get a single service by ID
     */
    public function getServiceById($serviceId, $userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * 
                FROM groomer_services 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$serviceId, $userId]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($service) {
                $service['for_dogs'] = (bool)$service['for_dogs'];
                $service['for_cats'] = (bool)$service['for_cats'];
                $service['available'] = (bool)$service['available'];
                $service['price'] = (float)$service['price'];
            }
            
            return $service;
        } catch (PDOException $e) {
            error_log("Error fetching service by ID: " . $e->getMessage());
            return null;
        }
    }
}
