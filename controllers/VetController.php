<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../config/auth_helper.php';

require_once __DIR__ . '/../models/Vet/AppointmentsModel.php';
require_once __DIR__ . '/../models/Vet/MedicalRecordsModel.php';
require_once __DIR__ . '/../models/Vet/PrescriptionsModel.php';
require_once __DIR__ . '/../models/Vet/VaccinationsModel.php';
require_once __DIR__ . '/../models/Vet/DashboardModel.php';

class VetController extends BaseController
{
    private AppointmentsModel $appointmentsModel;
    private MedicalRecordsModel $medicalRecordsModel;
    private PrescriptionsModel $prescriptionsModel;
    private VaccinationsModel $vaccinationsModel;

    public function __construct()
    {
        // ✅ Enforce auth + role
        requireLogin('/PETVET/index.php?module=guest&page=login');
        requireRole('vet', '/PETVET/index.php');

        // ✅ Enforce clinic context
        // If you added requireClinic() helper, use it:
        if (function_exists('requireClinic')) {
            requireClinic('/PETVET/index.php?module=guest&page=home');
        } else {
            if (empty($_SESSION['clinic_id'])) {
                header('Location: /PETVET/index.php?module=guest&page=home');
                exit;
            }
        }

        // ✅ Init models once
        $this->appointmentsModel   = new AppointmentsModel();
        $this->medicalRecordsModel = new MedicalRecordsModel();
        $this->prescriptionsModel  = new PrescriptionsModel();
        $this->vaccinationsModel   = new VaccinationsModel();
    }

    /**
     * Get current vet context safely
     */
    private function vetContext(): array
    {
        $vetId = $_SESSION['user_id'] ?? null;
        $clinicId = $_SESSION['clinic_id'] ?? null;

        if (empty($vetId) || empty($clinicId)) {
            // Don’t die. Redirect safely.
            header('Location: /PETVET/index.php?module=guest&page=login');
            exit;
        }

        return [
            'id' => (int)$vetId,
            'clinic_id' => (int)$clinicId
        ];
    }

    /* DASHBOARD PAGE */
    public function dashboard()
    {
        $vet = $this->vetContext();

        $dashboardModel = new DashboardModel($vet['id'], $vet['clinic_id']);
        $dashboardData  = $dashboardModel->getDashboardData();

        $this->view('vet', 'dashboard', [
            'dashboardData' => $dashboardData
        ]);
    }

    /* APPOINTMENTS PAGE */
    public function appointments()
    {
        $vet = $this->vetContext();

        $ongoing   = $this->appointmentsModel->getOngoingAppointmentForVet($vet['id'], $vet['clinic_id']);
        $upcoming  = $this->appointmentsModel->getUpcomingAppointmentsForVet($vet['id'], $vet['clinic_id']);
        $completed = $this->appointmentsModel->getCompletedAppointments($vet['id'], $vet['clinic_id']);
        $cancelled = $this->appointmentsModel->getCancelledAppointments($vet['id'], $vet['clinic_id']);

        // These are “all records by vet” (not per appointment). Keep if your UI needs it.
        $medicalRecords = $this->medicalRecordsModel->getMedicalRecordsForVet($vet['id'], $vet['clinic_id']);
        $prescriptions  = $this->prescriptionsModel->getPrescriptionsForVet($vet['id'], $vet['clinic_id']);
        $vaccinations   = $this->vaccinationsModel->getVaccinationsForVet($vet['id'], $vet['clinic_id']);


        $this->view('vet', 'appointments', compact(
            'ongoing',
            'upcoming',
            'completed',
            'cancelled',
            'medicalRecords',
            'prescriptions',
            'vaccinations'
        ));
    }

    /* MEDICAL RECORDS PAGE */
    public function medicalRecords()
    {
        $vet = $this->vetContext();

        $appointments   = $this->appointmentsModel->getAllAppointmentsForVet($vet['id'], $vet['clinic_id']);
        $medicalRecords = $this->medicalRecordsModel->getMedicalRecordsForVet($vet['id'], $vet['clinic_id']);
        $prescriptions  = $this->prescriptionsModel->getPrescriptionsForVet($vet['id'], $vet['clinic_id']);
        $vaccinations   = $this->vaccinationsModel->getVaccinationsForVet($vet['id'], $vet['clinic_id']);

        $this->view('vet', 'medical-records', compact(
            'appointments',
            'medicalRecords',
            'prescriptions',
            'vaccinations'
        ));
    }

    /* PRESCRIPTIONS PAGE */
    public function prescriptions()
    {
        $vet = $this->vetContext();

        $appointments  = $this->appointmentsModel->getAllAppointmentsForVet($vet['id'], $vet['clinic_id']);
        $prescriptions = $this->prescriptionsModel->getPrescriptionsForVet($vet['id'], $vet['clinic_id']);

        $this->view('vet', 'prescriptions', compact(
            'appointments',
            'prescriptions'
        ));
    }

    /* VACCINATIONS PAGE */
    public function vaccinations()
    {
        $vet = $this->vetContext();

        $appointments = $this->appointmentsModel->getAllAppointmentsForVet($vet['id'], $vet['clinic_id']);
        $vaccinations = $this->vaccinationsModel->getVaccinationsForVet($vet['id'], $vet['clinic_id']);

        $this->view('vet', 'vaccinations', compact(
            'appointments',
            'vaccinations'
        ));
    }
}
