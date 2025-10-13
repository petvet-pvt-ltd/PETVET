<?php
require_once __DIR__ . '/../BaseModel.php';
require_once __DIR__ . '/ClinicData.php';
class VetsModel extends BaseModel {
    public function fetchVetsData(): array {
        return ClinicData::getVets();
    }
}
?>