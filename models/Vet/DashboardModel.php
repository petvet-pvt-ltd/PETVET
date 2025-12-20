<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/AppointmentsModel.php';
require_once __DIR__ . '/MedicalRecordsModel.php';
require_once __DIR__ . '/PrescriptionsModel.php';
require_once __DIR__ . '/VaccinationsModel.php';

class DashboardModel extends BaseModel
{
    private int $vetId;
    private int $clinicId;

    public function __construct(int $vetId, int $clinicId)
    {
        parent::__construct();
        $this->vetId = $vetId;
        $this->clinicId = $clinicId;
    }

    public function getDashboardData(): array
    {
        $appointmentsModel   = new AppointmentsModel();
        $medicalRecordsModel = new MedicalRecordsModel();
        $prescriptionsModel  = new PrescriptionsModel();
        $vaccinationsModel   = new VaccinationsModel();

        $all = $appointmentsModel->getAllAppointmentsForVet($this->vetId, $this->clinicId);

        // Put ongoing first (if exists)
        $ongoing = null;
        $rest = [];

        foreach ($all as $a) {
            if (($a['status'] ?? '') === 'ongoing' && !$ongoing) {
                $ongoing = $a;
            } else {
                $rest[] = $a;
            }
        }

        if ($ongoing) {
            $rest = array_values(array_filter($rest, fn($a) => $a['id'] != $ongoing['id']));
        }

        return [
            'appointments'   => $ongoing ? array_merge([$ongoing], $rest) : $rest,
            'medicalRecords' => $medicalRecordsModel->getMedicalRecordsForVet($this->vetId, $this->clinicId),
            'prescriptions'  => $prescriptionsModel->getPrescriptionsForVet($this->vetId, $this->clinicId),
            'vaccinations'   => $vaccinationsModel->getVaccinationsForVet($this->vetId, $this->clinicId),
        ];
    }
}
