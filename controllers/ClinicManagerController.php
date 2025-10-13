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
        $data = [
            'kpis' => $overviewData['kpis'],
            'appointments' => $overviewData['appointments'],
            'ongoingAppointments' => $overviewData['ongoingAppointments'] ?? [],
            'staff' => $overviewData['staff'],
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
        $model = new VetsModel();
        $data = [
            'vets' => $model->fetchVetsData()
        ];
        $this->view('clinic_manager', 'vets', $data);
    }

    public function staff() {
        $model = new StaffModel();
        $data = [
            'staff' => $model->all()
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
        // The settings view uses simulated arrays inside the view itself
        $this->view('clinic_manager', 'settings');
    }

}
?>