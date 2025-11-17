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
| Cron Auto Logger Hook
| -------------------------------------------------------------------------
| Automatically detects and logs cron job executions
|
*/
$hook['post_controller_constructor'] = array(
    'class'    => 'Cron_auto_logger',
    'function' => 'check_cron_execution',
    'filename' => 'Cron_auto_logger.php',
    'filepath' => 'hooks'
);
