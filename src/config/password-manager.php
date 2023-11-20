<?php

return [
    'enable'              => env('PASSWORD_MANAGER_EANBLE', false),
    'cache_minutes'       => env('PASSWORD_MANAGER_EXPIRY_TIME', 0),
    'check_old_password'  => env('PASSWORD_MANAGER_CHECK_OLD', false),
    'user_model'          => env("PASSWORD_MANAGER_USER_MODEL", "App\Models\User")
];