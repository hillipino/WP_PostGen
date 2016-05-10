<?php
class WPPG_FrontEnd {
	
	private $cfg     = null;
	
	function __construct() {
			
		$this->cfg = new WPPG_Config();
		
		//wp_register_script( 'flowPlayer', $this->cfg->plugin_url . '/js/flowplayer.js', "", "3.2.6" );
		//wp_enqueue_script('flowPlayer');
	}
}
?>