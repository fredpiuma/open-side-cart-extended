<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

delete_option('xsc_addons_options');
