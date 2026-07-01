<?php

$config = require base_path('vendor/realrashid/sweet-alert/src/config/sweetalert.php');

$config['neverLoadJS'] = env('SWEET_ALERT_NEVER_LOAD_JS', true);

return $config;
