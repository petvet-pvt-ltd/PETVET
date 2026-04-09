<?php
require_once __DIR__ . '/../BaseModel.php';

class TrainerSettingsModel extends BaseModel {
    
    public function getProfile($trainerId) {
        $trainerId = (int)$trainerId;
        if ($trainerId <= 0) {
            return ['avatar' => '/PETVET/public/images/emptyProfPic.png'];
        }

        try {
            $stmt = $this->pdo->prepare("SELECT id, first_name, last_name, email, phone, address, avatar FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$trainerId]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$u) {
                return ['avatar' => '/PETVET/public/images/emptyProfPic.png'];
            }

            return [
                'id' => (int)$u['id'],
                'name' => trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')),
                'email' => $u['email'] ?? '',
                'phone' => $u['phone'] ?? '',
                'address' => $u['address'] ?? '',
                'avatar' => !empty($u['avatar']) ? $u['avatar'] : '/PETVET/public/images/emptyProfPic.png'
            ];
        } catch (Throwable $e) {
            error_log('TrainerSettingsModel getProfile error: ' . $e->getMessage());
            return ['avatar' => '/PETVET/public/images/emptyProfPic.png'];
        }
    }

    public function getPreferences($trainerId) {
        return [];
    }
}
