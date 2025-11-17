<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

/*
| -------------------------------------------------------------------------
| Cron Monitor Hook
| -------------------------------------------------------------------------
| Automatically detects and logs all cron job executions
*/
$hook['post_controller_constructor'][] = array(
    'class'    => 'Cron_monitor',
    'function' => 'monitor',
    'filename' => 'Cron_monitor.php',
    'filepath' => 'hooks'
);
