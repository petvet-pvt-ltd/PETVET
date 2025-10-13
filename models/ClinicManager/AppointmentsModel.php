<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/ClinicData.php';
class AppointmentsModel extends BaseModel {
    public function fetchAppointments(): array {
        $vets = ClinicData::getVets();
        $vetsById = [];
        foreach($vets as $v){ $vetsById[$v['id']] = $v; }
        $schedule = ClinicData::getAppointmentsSchedule();
        // Transform to include vet name for views
        $out = [];
        foreach($schedule as $date=>$items){
            foreach($items as $idx=>$ap){
                $vid = $ap['vet_id'];
                $items[$idx]['vet'] = $vetsById[$vid]['name'] ?? 'Unknown Vet';
            }
            $out[$date] = $items;
        }
        return $out;
    }
}


?>