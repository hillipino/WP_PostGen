<?php
class WPPG_Core {
	
	private $cfg                     = null;
	private $wppgAddImportjob        = null;
	private $wppgDisplayImportJobs   = null;
	private $wppgWordReplaceLists    = null;
	
	function __construct() {
			
		$this->cfg                     = new WPPG_Config();
		$this->wppgAddImportjob        = new WPPG_AddImportJob();
		$this->wppgDisplayImportJobs   = new WPPG_DisplayImportJobs();
		$this->wppgWordReplaceLists    = new WPPG_WordReplaceLists();
		
		add_filter('upload_mimes', array(&$this, 'addUploadMimes'));
		add_action('admin_init',   array(&$this, 'headInstall'));
		add_action('admin_menu',   array(&$this, 'navInstall'));
	}
	
	function addUploadMimes($mimes) {
	    $mimes = array_merge($mimes, array(
	        'csv' => 'text/csv'
	    ));
	    return $mimes;
	}
	
	function navInstall() {
		
		add_menu_page('wpPostGen', 'wpPostGen', 'publish_posts', $this->cfg->main_slug, array(&$this, "home") );
		add_submenu_page($this->cfg->main_slug, '', '', 'publish_posts', $this->cfg->main_slug, array(&$this, "home") );
		
		if ($this->cfg->plugin_debug) {
			add_submenu_page($this->cfg->main_slug, '*Debug Info*', '*Debug Info*', 'publish_posts', $this->cfg->debug_slug, array(&$this, "debug") );
		}
		add_submenu_page($this->cfg->main_slug, 'Import Jobs', 'Import Jobs', 'publish_posts', $this->cfg->import_job_slug, array(&$this->wppgDisplayImportJobs, "init"));
		add_submenu_page($this->cfg->main_slug, 'Add Import Job', 'Add Import Job', 'publish_posts', $this->cfg->add_import_job_slug, array(&$this->wppgAddImportjob, "init"));
				
		add_submenu_page($this->cfg->main_slug, 'Word Replace Lists', 'Word Replace Lists', 'publish_posts', $this->cfg->word_replace_slug, array(&$this->wppgWordReplaceLists, "init"));
		
		add_action('admin_print_styles', array(&$this, 'adminStyles'));
		add_action('admin_print_scripts', array(&$this, 'adminScripts'));
	}
	
	function headInstall() {
		
		wp_register_style( 'wppgJQueryStyleSheet', $this->cfg->plugin_url . '/css/jquery-ui-custom.css', false, "1.8.11" );
		wp_register_style( 'wppgStyleSheet', $this->cfg->plugin_url . '/css/wppg.css', false, "1.0" );
		
		wp_register_script( 'jquery-ui-custom', $this->cfg->plugin_url . '/js/jquery-ui.min.js', array('jquery'), "1.8.14" );
		wp_register_script( 'wppgJs', $this->cfg->plugin_url . '/js/wppg.js', array('jquery'), "1.0" );
		wp_register_script( 'jquery-validation', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js', array('jquery'), "1.11.1" );

		// ajax actions
		if (isset($_REQUEST['action'])) {
			switch ( $_REQUEST['action'] ) {
				case 'prevImportSelect' :
					add_action('wp_ajax_prevImportSelect', array(&$this->wppgAddImportjob, 'prevImportSelect'), 1, 1);
					break;
				case 'preloadImportFile' :
					add_action('wp_ajax_preloadImportFile', array(&$this->wppgAddImportjob, 'preloadImportFile'), 1, 1);
					break;
				case 'callDataMapTab' :
					add_action('wp_ajax_callDataMapTab', array(&$this->wppgAddImportjob, 'callDataMapTab'), 1, 1);
					break;
				case 'finishImport' :
					add_action('wp_ajax_finishImport', array(&$this->wppgAddImportjob, 'finishImport'), 1, 1);
					break;
				case 'deleteImportJob' :
					add_action('wp_ajax_deleteImportJob', array(&$this->wppgDisplayImportJobs, 'deleteImportJob'), 1, 1);
					break;
				case 'deleteReplaceList' :
					add_action('wp_ajax_deleteReplaceList', array(&$this->wppgWordReplaceLists, 'deleteReplaceList'), 1, 1);
					break;
				case 'deleteWordReplace' :
					add_action('wp_ajax_deleteWordReplace', array(&$this->wppgWordReplaceLists, 'deleteWordReplace'), 1, 1);
					break;
				
			}
		}
	}
	
	function adminStyles() {
	    if (isset($_REQUEST['page']) && $_REQUEST['page'] == $this->cfg->add_import_job_slug) {
		    wp_enqueue_style( 'wppgJQueryStyleSheet' );
		    wp_enqueue_style( 'wppgStyleSheet' );
	    }
	}
	
	function adminScripts() {
		    wp_enqueue_script( 'jquery-validation' );
		    wp_enqueue_script( 'jquery-ui-custom' );
		    wp_enqueue_script( 'wppgJs' );
		    wp_localize_script( 'wppgJs', 'wppgJsVars', array(
							    'pluginUrl' => $this->cfg->plugin_url,
							    'addImportJobUrl' => $this->cfg->add_import_job_url,
							    'dataPerPage'     => $this->cfg->data_per_page
							    )
				       );
	}

	function home() {
		?>
		<div class="wrap">
			<?php screen_icon('themes'); ?>
			<h2>wpPostGen : Welcome</h2>
			<p>Thank you for installing wpPostGen!</p>
			<p>--> Developed and released August 2011 by <a href="http://wpwares.com">wpWares</a></p>
			<p>&nbsp;</p>
			<pre>
				<?php $readmefile=fopen($this->cfg->plugin_dir . "/README","r") or exit("Unable to open readme file!"); 
				while(!feof($readmefile))
				{
					echo htmlentities(fgets($readmefile));
				}
				fclose($readmefile);
				?>
			</pre>
		</div>
		<?php
	}
	
	function debug() {
		?>
		<div class="wrap">
			<?php screen_icon('tools'); ?>
			<h2>wpPostGen : Debug Information</h2>
			<h3>wpPostGen Settings</h3>
			<div class="message updated">
				<p><strong>Database Option Name:</strong> <?php echo $this->cfg->db_opt_name; ?></p>
				<p><strong>Database Version:</strong> <?php echo $this->cfg->db_version; ?></p>
				<p><strong>Import Jobs Table Name:</strong> <?php echo $this->cfg->tbl_import; ?></p>
				<p><strong>Format Text Table Name:</strong> <?php echo $this->cfg->tbl_format; ?></p>
				<p><strong>Replace Text Table Name:</strong> <?php echo $this->cfg->tbl_replacetxt; ?></p>
				<p><strong>Data Map Table Name:</strong> <?php echo $this->cfg->tbl_datamap; ?></p>
				<p><strong>Imports to Show Per Page:</strong> <?php echo $this->cfg->list_per_page; ?></p>
			</div>
			<h3>Blog Settings</h3>
			<div class="message updated">
				<p><strong>Current Blog ID:</strong> <?php echo $this->cfg->blog_id; ?></p>
				<p><strong>Current Site ID:</strong> <?php echo $this->cfg->site_id; ?></p>
				<p><strong>Plugin Folder:</strong> <?php echo $this->cfg->plugin_dir; ?></p>
				<p><strong>Plugin Url:</strong> <?php echo $this->cfg->plugin_url; ?></p>
				<p><strong>Database Upgrade Include Path:</strong> <?php echo $this->cfg->wp_upgrade_inc; ?></p>
				<p><strong>Admin Main Url:</strong> <?php echo $this->cfg->main_url; ?></p>
				<p><strong>Admin Debug Url:</strong> <?php echo $this->cfg->debug_url; ?></p>
			</div>
			<h3>Mime Types</h3>
			<div class="message updated">
				<?php
					foreach(get_allowed_mime_types() as $ext => $type)
					{
				?>
				<p><strong><?php echo $ext; ?>:</strong> <?php echo $type; ?></p>
				<?php
					}
				?>
			</div>
		</div>
		<?php
	}
}
?>
