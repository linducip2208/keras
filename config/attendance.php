<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kebijakan mock-GPS / fake location
    |--------------------------------------------------------------------------
    | true = request dengan is_mock_gps=true DITOLAK (422). Set false hanya
    | untuk tenant demo/testing via env ATTENDANCE_ALLOW_MOCK_GPS=true.
    */
    'reject_mock_gps' => env('ATTENDANCE_REJECT_MOCK_GPS', true),

    /*
    |--------------------------------------------------------------------------
    | Upload selfie clock-in
    |--------------------------------------------------------------------------
    */
    'photo_disk' => env('ATTENDANCE_PHOTO_DISK', 'local'),
    'photo_dir' => 'attendance-photos',
    'photo_max_kb' => 4096,
];
