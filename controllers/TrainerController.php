<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Trainer/DashboardModel.php';
require_once __DIR__ . '/../models/Trainer/AppointmentsModel.php';
require_once __DIR__ . '/../models/Trainer/ClientsModel.php';
require_once __DIR__ . '/../models/Trainer/SettingsModel.php';

class TrainerController extends BaseController {

    public function dashboard() {
        $model = new TrainerDashboardModel();
        $trainerId = 1; // Mock trainer ID
        
        $data = [
            'stats' => $model->getStats($trainerId),
            'upcomingSessions' => $model->getUpcomingSessions($trainerId),
            'recentClients' => $model->getRecentClients($trainerId)
        ];
        
        $this->view('trainer', 'dashboard', $data);
    }

    public function appointments() {
        $model = new TrainerAppointmentsModel();
        $trainerId = 1; // Mock trainer ID
        
        $data = [
            'appointments' => $model->getAllAppointments($trainerId),
            'upcomingAppointments' => $model->getUpcomingAppointments($trainerId),
            'completedAppointments' => $model->getCompletedAppointments($trainerId)
        ];
        
        $this->view('trainer', 'appointments', $data);
    }

    public function clients() {
        $model = new TrainerClientsModel();
        $trainerId = 1; // Mock trainer ID
        
        $data = [
            'clients' => $model->getAllClients($trainerId),
            'activeClients' => $model->getActiveClients($trainerId),
            'clientStats' => $model->getClientStats($trainerId)
        ];
        
        $this->view('trainer', 'clients', $data);
    }

    public function settings() {
        $model = new TrainerSettingsModel();
        $trainerId = 1; // Mock trainer ID
        
        $data = [
            'profile' => $model->getProfile($trainerId),
            'preferences' => $model->getPreferences($trainerId)
        ];
        
        $this->view('trainer', 'settings', $data);
    }
}
