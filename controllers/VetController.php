<?php
//controllers
require_once __DIR__ . '/BaseController.php';

//Models
require_once __DIR__ . '/../models/Vet/AppointmentsModel.php';
require_once __DIR__ . '/../models/Vet/MedicalRecordsModel.php';
require_once __DIR__ . '/../models/Vet/PrescriptionsModel.php';
require_once __DIR__ . '/../models/Vet/VaccinationsModel.php';
require_once __DIR__ . '/../models/Vet/DashboardModel.php';

class VetController extends BaseController {
    
    public function dashboard() {
        $model = new DashboardModel();
        $dashboardData = $model->fetchDashboardData();
        
        // Pass the data in the format expected by the view
        $data = [
            'dashboardData' => $dashboardData,
            'vetName' => $_SESSION['user_name'] ?? 'Dr. Smith'
        ];
        
        $this->view('vet', 'dashboard', $data);
    }

    public function appointments() {
        $model = new AppointmentsModel();
        $appointmentsData = $model->fetchAppointments();
        
        $data = [
            'appointments' => $appointmentsData['appointments'],
            'medicalRecords' => $appointmentsData['medicalRecords'],
            'prescriptions' => $appointmentsData['prescriptions'],
            'vaccinations' => $appointmentsData['vaccinations']
        ];
        
        $this->view('vet', 'appointments', $data);
    }

    public function medicalRecords() {
        $model = new MedicalRecordsModel();
        $data = $model->fetchMedicalRecordsData();
        
        $this->view('vet', 'medical-records', $data);
    }

    public function prescriptions() {
        $model = new PrescriptionsModel();
        $data = $model->fetchPrescriptionsData();
        
        $this->view('vet', 'prescriptions', $data);
    }

    public function vaccinations() {
        $model = new VaccinationsModel();
        $data = $model->fetchVaccinationsData();
        
        $this->view('vet', 'vaccinations', $data);
    }
}
?>