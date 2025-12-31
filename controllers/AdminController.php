<?php
require_once __DIR__ . '/BaseController.php';

// Admin Models
require_once __DIR__ . '/../models/Admin/DashboardModel.php';
require_once __DIR__ . '/../models/Admin/ManageUsersModel.php';
require_once __DIR__ . '/../models/Admin/AppointmentsModel.php';
require_once __DIR__ . '/../models/Admin/MedicalRecordsModel.php';
require_once __DIR__ . '/../models/Admin/FinancePanelModel.php';

class AdminController extends BaseController {
    public function dashboard() {
        // Dashboard now loads data via JavaScript API
        $this->view('admin', 'dashboard', []);
    }

    public function manageUsers() {
        $model = new ManageUsersModel();
        $data = $model->fetchUsersData();
        $this->view('admin', 'manage-users', $data);
    }

    public function manageUsersByRole() {
        $this->view('admin', 'manage-users-by-role');
    }

    public function manageClinics() {
        $this->view('admin', 'manage-clinics');
    }

    public function reports() {
        $this->view('admin', 'reports');
    }

    public function appointments() {
        $model = new AppointmentsModel();
        $data = $model->fetchAppointmentsData();
        $this->view('admin', 'appointments', $data);
    }

    public function medicalRecords() {
        $model = new MedicalRecordsModel();
        $data = $model->fetchMedicalRecordsData();
        $this->view('admin', 'medical-records', $data);
    }

    public function financePanel() {
        $model = new FinancePanelModel();
        $data = $model->fetchFinanceData();
        $this->view('admin', 'finance-panel', $data);
    }

    // These views have static content, no models needed
    public function petListings() {
        $this->view('admin', 'pet-listings-modern');
    }

    public function settings() {
        $this->view('admin', 'settings');
    }

    public function lostFound() {
        $this->view('admin', 'lost-found');
    }
}
?>