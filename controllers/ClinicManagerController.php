<?php
//controllers
require_once __DIR__ . '/BaseController.php';

//Models
require_once __DIR__ . '/../models/ClinicManager/AppointmentsModel.php';
require_once __DIR__ . '/../models/ClinicManager/VetsModel.php';
require_once __DIR__ . '/../models/ClinicManager/ShopModel.php';
require_once __DIR__ . '/../models/ClinicManager/OverviewModel.php';
require_once __DIR__ . '/../models/ClinicManager/ReportsModel.php';
require_once __DIR__ . '/../models/ClinicManager/StaffModel.php';

class ClinicManagerController extends BaseController {
    public function overview() {
        $model = new OverviewModel();
        $overviewData = $model->fetchOverviewData();
        
        // Get clinic_id for logged-in clinic manager
        $userId = $_SESSION['user_id'] ?? 0;
        $pdo = db();
        $stmt = $pdo->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $clinicId = $stmt->fetchColumn() ?: 1;
        
        // Get all staff members for the edit modal
        $staffModel = new StaffModel();
        $allStaff = $staffModel->all($clinicId);
        
        $data = [
            'kpis' => $overviewData['kpis'],
            'appointments' => $overviewData['appointments'],
            'ongoingAppointments' => $overviewData['ongoingAppointments'] ?? [],
            'staff' => $overviewData['staff'],
            'allStaff' => $allStaff,
            'badgeClasses' => $overviewData['badgeClasses']
        ];
        $this->view('clinic_manager', 'overview', $data);
    }

    public function appointments() {
        $model = new AppointmentsModel();
        $appointments = $model->fetchAppointments();

        // Build unique vet name list from schedule
        $vetNamesSet = [];
        foreach ($appointments as $date => $list) {
            foreach ($list as $appt) {
                if (!empty($appt['vet'])) {
                    $vetNamesSet[$appt['vet']] = true;
                }
            }
        }
        $vetNames = array_keys($vetNamesSet);
        sort($vetNames);

        // Apply filter if provided (GET param 'vet')
        $selectedVet = $_GET['vet'] ?? 'all';
        if ($selectedVet !== 'all') {
            foreach ($appointments as $date => $list) {
                $filtered = array_filter($list, function($a) use ($selectedVet) {
                    return $a['vet'] === $selectedVet; 
                });
                if ($filtered) {
                    $appointments[$date] = array_values($filtered);
                } else {
                    unset($appointments[$date]);
                }
            }
        }

        $data = [
            'appointments' => $appointments,
            'vetNames' => $vetNames,
            'selectedVet' => $selectedVet
        ];
        $this->view('clinic_manager', 'appointments', $data);
    }

    public function vets() {
        // Get the current clinic manager's clinic_id
        $currentUserId = $_SESSION['user_id'] ?? 0;
        
        // Get clinic_id from clinic_manager_profiles
        $stmt = db()->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
        $stmt->execute([$currentUserId]);
        $profile = $stmt->fetch();
        
        if (!$profile) {
            // If no clinic found, show error or redirect
            die("Error: Clinic manager profile not found.");
        }
        
        $clinicId = $profile['clinic_id'];
        
        $model = new VetsModel();
        $data = [
            'vets' => $model->fetchVetsData($clinicId),
            'pending' => $model->fetchPendingRequests($clinicId)
        ];
        $this->view('clinic_manager', 'vets', $data);
    }

    public function staff() {
        $model = new StaffModel();
        
        // Get clinic manager's clinic ID
        $userId = currentUserId();
        $pdo = db();
        $stmt = $pdo->prepare("SELECT clinic_id FROM clinic_manager_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $clinicId = $stmt->fetchColumn();
        
        if (!$clinicId) {
            $clinicId = 1; // Fallback
        }
        
        $data = [
            'staff' => $model->all($clinicId)
        ];
        $this->view('clinic_manager', 'staff', $data);
    }

    public function shop() {
        $model = new ShopModel();
        $data = [
            'products' => $model->fetchShopData(),
            'orders' => $model->fetchPendingOrders()
        ];
        $this->view('clinic_manager', 'shop', $data);
    }

    public function reports(){
        $model = new ReportsModel();

        // rangeMode comes from query: week | month | year | custom
        $mode = $_GET['range'] ?? 'week';

        // Compute from/to for each mode
        if ($mode === 'custom' && !empty($_GET['from']) && !empty($_GET['to'])) {
            $from = $_GET['from'];
            $to   = $_GET['to'];
        } elseif ($mode === 'month') {
            $from = date('Y-m-01');
            $to   = date('Y-m-t');
        } elseif ($mode === 'year') {
            $from = date('Y-01-01');
            $to   = date('Y-12-31');
        } else { // week (default)
            $from = date('Y-m-d', strtotime('monday this week'));
            $to   = date('Y-m-d', strtotime('sunday this week'));
            $mode = 'week';
        }

        // Call the model with the computed range and mode
        $vm = $model->getReport($from, $to, $mode);

        // Add the mode so the view can style active toggle & titles
        $vm['rangeMode'] = $mode;

        // Render
        $this->view('clinic_manager', 'reports', $vm);
    }

    public function settings() {
        require_once __DIR__ . '/../models/ClinicManager/SettingsModel.php';
        $settingsModel = new ClinicManagerSettingsModel();
        
        // Get current user ID from session
        $currentUserId = $_SESSION['user_id'] ?? null;
        
        if (!$currentUserId) {
            header('Location: /PETVET/index.php?module=guest&page=login');
            exit;
        }
        
        $data = [
            'profile' => $settingsModel->getManagerProfile($currentUserId),
            'clinic' => $settingsModel->getClinicData($currentUserId),
            'prefs' => $settingsModel->getPreferences($currentUserId),
            'weeklySchedule' => $settingsModel->getWeeklySchedule($currentUserId),
            'blockedDays' => $settingsModel->getBlockedDays($currentUserId)
        ];
        
        $this->view('clinic_manager', 'settings', $data);
    }

}
?>