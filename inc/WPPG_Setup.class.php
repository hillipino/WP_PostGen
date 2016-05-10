<?php
class WPPG_Setup {
	
	private $cfg = null;
	
	function __construct() {
		$this->cfg = new WPPG_Config();
		$this->dbInstall();
	}
	
	function dbInstall() {
		global $wpdb;
		
		require_once ($this->cfg->wp_upgrade_inc);
		
		if($wpdb->get_var("show tables like '" . $this->cfg->tbl_import . "'") != $this->cfg->tbl_import) {

			$sql = "CREATE TABLE " . $this->cfg->tbl_import . " (
			id            bigint(20)    NOT NULL AUTO_INCREMENT,
			blog_id       bigint(20)    NOT NULL,
			site_id       bigint(20)    NOT NULL,
			name          VARCHAR(128)  NOT NULL,
			upload_id     bigint(20)    NOT NULL,
			status        VARCHAR(32)   NOT NULL,
			numposts      mediumint(9)  NOT NULL,
			date_imported DATETIME      NOT NULL,
			file_delim    VARCHAR(32)   NOT NULL,
			start_date    DATETIME      NOT NULL,
			per_interval  INT(4)        NOT NULL,
			post_interval INT(4)        NOT NULL,
			interval_type VARCHAR(32)   NOT NULL,
			randomize_csv INT(1)        NOT NULL,
			UNIQUE KEY id (id)
			);";

			dbDelta($sql);

			add_option($this->cfg->db_opt_name, $this->cfg->db_version);

		}
		
		
		if($wpdb -> get_var("show tables like '" . $this->cfg->tbl_datamap . "'") != $this->cfg->tbl_datamap) {

			$sql = "CREATE TABLE " . $this->cfg->tbl_datamap . " (
			id            bigint(20)    NOT NULL AUTO_INCREMENT,
			blog_id       bigint(20)    NOT NULL,
			site_id       bigint(20)    NOT NULL,
			import_id     bigint(20)    NOT NULL,
			format_id     bigint(20)    NOT NULL,
			widget_type   VARCHAR(128)  NULL,
			col_type      VARCHAR(128)  NOT NULL,
			col_from      bigint(20)    NOT NULL,
			col_to        VARCHAR(128)  NULL,
			opt_width     VARCHAR(10)   NULL,
			opt_height    VARCHAR(10)   NULL,
			opt_class     VARCHAR(128)  NULL,
			col_required  INT(1)        NOT NULL,
			generate_tags INT(1)        NOT NULL,
			col_append    LONGTEXT      NULL,
			col_prepend   LONGTEXT      NULL
			UNIQUE KEY id (id)
			);";

			dbDelta($sql);

		}

		if($wpdb -> get_var("show tables like '" . $this->cfg->tbl_format . "'") != $this->cfg->tbl_format) {

			$sql = "CREATE TABLE " . $this->cfg->tbl_format . " (
			id           bigint(20)   NOT NULL AUTO_INCREMENT,
			blog_id      bigint(20)   NOT NULL,
			site_id      bigint(20)   NOT NULL,
			name         VARCHAR(128) NOT NULL,
			UNIQUE KEY id (id)
			);";

			dbDelta($sql);

		}

		if($wpdb -> get_var("show tables like '" . $this->cfg->tbl_replacetxt . "'") != $this->cfg->tbl_replacetxt) {

			$sql = "CREATE TABLE " . $this->cfg->tbl_replacetxt . " (
			id        bigint(20)   NOT NULL AUTO_INCREMENT,
			blog_id   bigint(20)   NOT NULL,
			site_id   bigint(20)   NOT NULL,
			format_id bigint(20)   NOT NULL,
			rtext     LONGTEXT     NOT NULL,
			rwith     LONGTEXT     NOT NULL,
			UNIQUE KEY id (id)
			);";

			dbDelta($sql);

		}
	}
}
?>