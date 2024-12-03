<?php

return [
    'encryption_key' => env('JOBS_ENCRYPTION_KEY', 'default-jobs-key'),
    'storage_path' => storage_path('app/jobs'),
];
