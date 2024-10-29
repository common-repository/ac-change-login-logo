<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
 die;
}
require_once plugin_dir_path(__FILE__) . 'config.php';
delete_option(AC_LOGIN_LOGO_ID);
