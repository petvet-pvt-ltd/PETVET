<?php
// Central configuration for Clinic Manager module
return [
    // Timezone used across overview and scheduling
    'timezone' => 'Asia/Colombo',

    // Length of a single appointment slot in minutes
    'slot_duration_minutes' => 20,

    // Daily slot start times (24h HH:MM)
    // Note: Mock data generator uses these; real data would come from DB
    'slots' => ['09:00', '10:30', '13:00', '15:30'],
];
