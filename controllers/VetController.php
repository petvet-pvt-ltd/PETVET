<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../config/auth_helper.php';

require_once __DIR__ . '/../models/Vet/AppointmentsModel.php';
require_once __DIR__ . '/../models/Vet/MedicalRecordsModel.php';
require_once __DIR__ . '/../models/Vet/PrescriptionsModel.php';
require_once __DIR__ . '/../models/Vet/VaccinationsModel.php';
require_once __DIR__ . '/../models/Vet/DashboardModel.php';
require_once __DIR__ . '/../models/Vet/SettingsModel.php';

class VetController extends BaseController
{
    private AppointmentsModel $appointmentsModel;
    private MedicalRecordsModel $medicalRecordsModel;
    private PrescriptionsModel $prescriptionsModel;
    private VaccinationsModel $vaccinationsModel;
    private bool $isSuspended = false;

    public function __construct()
    {
        // ✅ Enforce auth + role
        requireLogin('/PETVET/index.php?module=guest&page=login');
        requireRole('vet', '/PETVET/index.php');

        // ✅ Enforce clinic context
        if (empty($_SESSION['clinic_id'])) {
            header('Location: /PETVET/index.php?module=guest&page=home');
            exit;
        }

        // ✅ Suspension enforcement (settings + logout still allowed elsewhere)
        $this->isSuspended = isVetSuspended((int)($_SESSION['user_id'] ?? 0), (int)($_SESSION['clinic_id'] ?? 0));

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

    private function findAppointmentInList(array $appointments, int $appointmentId): ?array
    {
        foreach ($appointments as $appointment) {
            if ((int)($appointment['id'] ?? 0) === $appointmentId) {
                return $appointment;
            }
        }

        return null;
    }

    /* DASHBOARD PAGE */
    public function dashboard()
    {
        $vet = $this->vetContext();

        $dashboardData = [
            'appointments' => [],
            'medicalRecords' => [],
            'prescriptions' => [],
            'vaccinations' => [],
        ];

        if (!$this->isSuspended) {
            $dashboardModel = new DashboardModel($vet['id'], $vet['clinic_id']);
            $dashboardData  = $dashboardModel->getDashboardData();
        }

        $this->view('vet', 'dashboard', [
            'dashboardData' => $dashboardData,
            'isSuspended' => $this->isSuspended,
        ]);
    }

    /* APPOINTMENTS PAGE */
    public function appointments()
    {
        if ($this->isSuspended) {
            header('Location: /PETVET/index.php?module=vet&page=dashboard');
            exit;
        }
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
        if ($this->isSuspended) {
            header('Location: /PETVET/index.php?module=vet&page=dashboard');
            exit;
        }
        $vet = $this->vetContext();

        $appointments   = $this->appointmentsModel->getAllAppointmentsForVet($vet['id'], $vet['clinic_id']);
        $medicalRecords = $this->medicalRecordsModel->getMedicalRecordsForVet($vet['id'], $vet['clinic_id']);
        $prescriptions  = $this->prescriptionsModel->getPrescriptionsForVet($vet['id'], $vet['clinic_id']);
        $vaccinations   = $this->vaccinationsModel->getVaccinationsForVet($vet['id'], $vet['clinic_id']);

        $from = $_GET['from'] ?? '';
        $appointmentId = isset($_GET['appointment']) ? (int)$_GET['appointment'] : 0;

        if ($from === 'ongoing' && $appointmentId > 0) {
            $appointment = $this->findAppointmentInList($appointments, $appointmentId);

            if ($appointment) {
                $petId = (int)($appointment['pet_id'] ?? 0);
                $guestPetName = trim((string)($appointment['guest_pet_name'] ?? ''));
                $guestClientName = trim((string)($appointment['guest_client_name'] ?? ''));

                if ($petId > 0) {
                    $medicalRecords = $this->medicalRecordsModel->getMedicalRecordsByPetAcrossVets($petId);
                } elseif ($guestPetName !== '') {
                    $medicalRecords = $this->medicalRecordsModel->getMedicalRecordsByGuestPetAcrossVets($guestPetName, $guestClientName);
                }
            }
        }

        $this->view('vet', 'medical-records', compact(
            'appointments',
            'medicalRecords',
            'prescriptions',
            'vaccinations',
            'vet'
        ));
    }

    /* PRESCRIPTIONS PAGE */
    public function prescriptions()
    {
        if ($this->isSuspended) {
            header('Location: /PETVET/index.php?module=vet&page=dashboard');
            exit;
        }
        $vet = $this->vetContext();

        $appointments  = $this->appointmentsModel->getAllAppointmentsForVet($vet['id'], $vet['clinic_id']);
        $prescriptions = $this->prescriptionsModel->getPrescriptionsForVet($vet['id'], $vet['clinic_id']);

        $from = $_GET['from'] ?? '';
        $appointmentId = isset($_GET['appointment']) ? (int)$_GET['appointment'] : 0;

        if ($from === 'ongoing' && $appointmentId > 0) {
            $appointment = $this->findAppointmentInList($appointments, $appointmentId);

            if ($appointment) {
                $petId = (int)($appointment['pet_id'] ?? 0);
                $guestPetName = trim((string)($appointment['guest_pet_name'] ?? ''));
                $guestClientName = trim((string)($appointment['guest_client_name'] ?? ''));

                if ($petId > 0) {
                    $prescriptions = $this->prescriptionsModel->getPrescriptionsByPetAcrossVets($petId);
                } elseif ($guestPetName !== '') {
                    $prescriptions = $this->prescriptionsModel->getPrescriptionsByGuestPetAcrossVets($guestPetName, $guestClientName);
                }
            }
        }

        $this->view('vet', 'prescriptions', compact(
            'appointments',
            'prescriptions',
            'vet'
        ));
    }

    /* VACCINATIONS PAGE */
    public function vaccinations()
    {
        if ($this->isSuspended) {
            header('Location: /PETVET/index.php?module=vet&page=dashboard');
            exit;
        }
        $vet = $this->vetContext();

        $appointments = $this->appointmentsModel->getAllAppointmentsForVet($vet['id'], $vet['clinic_id']);
        $vaccinations = $this->vaccinationsModel->getVaccinationsForVet($vet['id'], $vet['clinic_id']);

        $from = $_GET['from'] ?? '';
        $appointmentId = isset($_GET['appointment']) ? (int)$_GET['appointment'] : 0;

        if ($from === 'ongoing' && $appointmentId > 0) {
            $appointment = $this->findAppointmentInList($appointments, $appointmentId);

            if ($appointment) {
                $petId = (int)($appointment['pet_id'] ?? 0);
                $guestPetName = trim((string)($appointment['guest_pet_name'] ?? ''));
                $guestClientName = trim((string)($appointment['guest_client_name'] ?? ''));

                if ($petId > 0) {
                    $vaccinations = $this->vaccinationsModel->getVaccinationsByPetAcrossVets($petId);
                } elseif ($guestPetName !== '') {
                    $vaccinations = $this->vaccinationsModel->getVaccinationsByGuestPetAcrossVets($guestPetName, $guestClientName);
                }
            }
        }

        $this->view('vet', 'vaccinations', compact(
            'appointments',
            'vaccinations',
            'vet'
        ));
    }

    /* SETTINGS PAGE */
    public function settings()
    {
        $settingsModel = new VetSettingsModel();
        $userId = currentUserId();
        
        $data = [
            'profile' => $settingsModel->getVetProfile($userId),
            'prefs' => $settingsModel->getPreferences($userId),
            'accountStats' => $settingsModel->getAccountStats($userId)
        ];
        
        $this->view('vet', 'settings', $data);
    }
}
