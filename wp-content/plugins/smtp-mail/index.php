<?php
/*
Plugin Name: SMTP Mail
Plugin URI: http://photoboxone.com
Description: SMTP settings, mail function, send test, save data ( phpmailer ). It is very easy to configure and fast.
Author: Photoboxone
Author URI: http://photoboxone.com
Version: 1.2.13
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('ABSPATH') or die();

function smtpmail_index()
{
	return __FILE__;
}

require( dirname(__FILE__). '/includes/functions.php');

if( is_admin() ) {
	
	smtpmail_include('setting.php');

	smtpmail_include('notices.php'); 
	
} else {
	
	smtpmail_include('site.php');
	
}