<?php

return [
    'login' => [
        'max_attempts' => (int) env('AUTH_LOGIN_MAX_ATTEMPTS', 5), // Ép kiểu thành int
        'lockout_seconds' => (int) env('AUTH_LOGIN_LOCKOUT_SECONDS', 300), // Ép kiểu thành int
    ],
];