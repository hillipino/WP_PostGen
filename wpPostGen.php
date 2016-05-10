<?php
/*
 * Plugin Name: wpPostGen
 * Version: 1.0
 * Plugin URI: https://github.com/hillipino
 * Description: Dynamic importing of csv file dumps from affiliates / partners to create WordPress posts. 
 * Author: wpWares
 * Author URI: http://www.hillipino.com
 * License: GPL3
 * 
 */

require("inc/WPPG_Config.class.php");

register_activation_hook( __FILE__, 'wppgSetup' );

add_action( 'init', 'wppgFrontEnd' );
add_action( 'plugins_loaded', 'wppgCore' );

function wppgSetup() {
	//if ( is_admin() ) {
		require("inc/WPPG_Setup.class.php");
		$wppg_setup = new WPPG_Setup();
	//}
}

function wppgCore() {
	//if ( is_admin() ) {
		require("inc/WPPG_DisplayImportJobs.class.php");
		require("inc/WPPG_AddImportJob.class.php");
		require("inc/WPPG_WordReplaceLists.class.php");
		require("inc/WPPG_Core.class.php");
		$wppg_core = new WPPG_Core();
	//}
}

function wppgFrontEnd() {
	
	require("inc/WPPG_FrontEnd.class.php");
	$wppg_frontend = new WPPG_FrontEnd();

}
?>