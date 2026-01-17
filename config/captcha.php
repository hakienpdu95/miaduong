<?php

return [
    'secret' => env('RECAPTCHA_SECRET_KEY'),
    'sitekey' => env('RECAPTCHA_SITE_KEY'),
    'version' => 'v3',
    'options' => [
        'timeout' => 30,
        'threshold' => 0.6, // Ngưỡng điểm tối thiểu
    ],
];