<?php
class WPPG_DisplayImportJobs {
	
	private $cfg     = null;
	
	function __construct() {
			
		$this->cfg = new WPPG_Config();
		
	}
	
 	// Import Jobs Logic (List Display)
 	
	function init() {
	    // process actions	
	    if ( isset($_REQUEST['wppgaction']) ) {
		    switch ( $_REQUEST['wppgaction'] ) {
			    default : // initial display
				    $this->dispImportJobs();
			    case "deleteimportjobsubmitted" :
				    $this->deleteImportJob();
			    break;
		    }
	    } else {
		    $this->dispImportJobs();
	    }
	}

	function dispImportJobs() {
		global $wpdb;
		
		$page_links = "";

		$search_term = isset( $_REQUEST['s'] ) ? esc_html(trim($_REQUEST['s'])) : "";
		
		$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;
			
		if ( empty($pagenum) )
		       $pagenum = 1;
		   
		$per_page  = $this->cfg->list_per_page;
		$start_query = (($pagenum - 1) * $per_page);
		if ($start_query == 1) { $start_query = 0; }
		
		$main_query = $wpdb->prepare("SELECT * FROM " . $this->cfg->tbl_import . "
					       WHERE blog_id = %d
					         AND site_id = %d", 
					  	 $this->cfg->blog_id, $this->cfg->site_id);
		
		if ( $search_term )
		   $main_query .= $wpdb->prepare(" AND name LIKE '%s'", "%" . $search_term . "%");

		$main_query .= " GROUP BY id ORDER BY name";
		
		$import_results = $wpdb->get_results($main_query);
		$num_pages      = ceil($wpdb->num_rows / $per_page);
		$count_posts    = $wpdb->num_rows;
		
		$main_query    .= $wpdb->prepare(" LIMIT %d,%d",$start_query,$per_page);
		
		$import_results = $wpdb->get_results($main_query);
		
		?>
		<div class="wrap">
			<?php screen_icon('edit-pages'); ?>
			<h2>wpPostGen : List of Import Jobs <a href="<?php echo $this->cfg->add_import_job_url; ?>" class="button add-new-h2">Add Import Job</a>
				<?php
				if ( $search_term )
	               printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html($search_term) );
				?>
			</h2>
			
			<div id="message" class="updated"></div>
			
			<form id="imports-filter" action="<?php echo $this->cfg->import_job_url; ?>" method="post">
            
                <p class="search-box">
	           <label class="screen-reader-text" for="post-search-input">Search Imports:</label>
	           <input type="text" id="post-search-input" name="s" value="<?php echo $search_term; ?>" />
	           <input type="submit" name="sButton" id="search-submit" class="button" value="Search Import Jobs"  />
	        </p>
            
            <?php if ($import_results) { ?>
            <div class="tablenav">
            <?php
               $page_links = paginate_links( array(
	              'base' => add_query_arg( 'paged', '%#%' ),
	              'format' => '',
	              'prev_text' => __('&laquo;'),
	              'next_text' => __('&raquo;'),
		      'show_all'  => true,
	              'total' => $num_pages,
	              'current' => $pagenum,
		      'add_args' => array( 's' => $search_term )
               ));
            if ($page_links) { ?>
	        <div class="tablenav-pages">
	        <?php
	        
	        $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
			        			number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
					        	number_format_i18n( min( $pagenum * $per_page, $count_posts ) ),
						        number_format_i18n( $count_posts ),
						        $page_links
						       );
	        echo $page_links_text;
	        ?>
	        </div>
	        <?php } ?>
	        </div>
	        <?php } ?>
	        <br class="clear" /><br />
		<?php wp_nonce_field('n_deleteImportJob','n_importAction'); ?>
				<table class="wp-list-table widefat fixed posts" cellspacing="0">
					<thead>
						<tr>
							<th scope="col" class="manage-column column-name sortable desc">&nbsp;&nbsp;Import Job Label</th>
							<th scope="col" class="manage-column column-importfile">Import File</th>
							<th scope="col" class="manage-column column-numposts">Posts Created</th>
							<th scope="col" class="manage-column column-dateimported">Date Imported</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th scope="col" class="manage-column column-name sortable desc">&nbsp;&nbsp;Import Job Label</th>
							<th scope="col" class="manage-column column-importfile">Import File</th>
							<th scope="col" class="manage-column column-numposts">Posts Created</th>
							<th scope="col" class="manage-column column-dateimported">Date Imported</th>
						</tr>
					</tfoot>
					
					<tbody id="the-list" class="list:importjobs">
						<?php
						
						if ($import_results) :
							
							$class = 'alternate';
							$i = 0;
							
							foreach($import_results as $import_result ) :
								$i++;
								$class = ( $class == 'alternate' ) ? '' : 'alternate';
								$upload_post = get_post($import_result->upload_id);
								
								$meta_key   = 'wppgimport';
								$meta_value = $import_result->id;
								$pwKey      = "tmp" . $meta_value;
								
								$sql = $wpdb->prepare("SELECT count(DISTINCT pm.post_id)
									FROM " . $wpdb->postmeta . " pm
									JOIN " . $wpdb->posts . " p ON (p.ID = pm.post_id)
								       WHERE pm.meta_key   = %s
									 AND pm.meta_value = %s
								       ", $meta_key, $meta_value);
								
								$count = $wpdb->get_var($sql);
								
								if ($count == 0) {
									$sql = $wpdb->prepare("SELECT count(DISTINCT id)
									FROM " . $wpdb->posts . "
								       WHERE post_password LIKE %s
								       ", "%" . $pwKey . "%");
									
									$count = $wpdb->get_var($sql);
								}
					    ?>
								<tr id="importjob-<?php echo $import_result->id; ?>" class="<?php echo $class; ?>">
									<td class="name column-name">
										<strong><?php echo esc_html($import_result->name); ?></strong>
										<br />
										<div class="row-actions">
											<span class="delete"><a class="delete-importjob" id="removelink-<?php echo $import_result->id; ?>" href="#">Delete Permanently</a></span>
										</div>
										<div id="loading-<?php echo $import_result->id; ?>"></div>
									</td>
									<td><?php echo esc_html($upload_post->post_title); ?></td>
									<td><?php echo $count; ?></td>
									<td><?php echo esc_html($import_result->date_imported); ?></td>
								</tr>
						<?php 
							endforeach;
						else :	
						?>
							<tr id="importjobs-none" class="">
								<td colspan="4">
									<?php
									 if ($search_term) {
									?>
										Sorry, your search query returned 0 results.
									<?php
									 } else {
									?>
										You have not added any Import Jobs yet. Please click Add Import Job.
									<?php
									 }
									 ?>
									</td>
							</tr>
						<?php 
						endif;
						?>
					</tbody>
				</table>
				<div class="tablenav">
				<?php
					if ( $page_links )
						echo "<div class='tablenav-pages'>$page_links_text</div>";
				?>
				</div>
				</form>
				<br class="clear" />
			
		</div>
		<?php
	}
	
	function deleteImportJob() {
		
		global $wpdb;
		
		$jsonVARS = array();
		
		if (!isset($_REQUEST['importId'])) {
			$jsonVARS['wppgError'] = "REQUIRED PARAMETERS NOT SET";
			echo json_encode($jsonVARS);
			die();
		}
		
		$importId              = $_REQUEST['importId'];
		$jsonVARS['wppgError'] = "success";
		$numToDelete           = ($this->cfg->data_per_page * 2);
		
		if (!isset($_REQUEST['n_importAction']) || !wp_verify_nonce($_REQUEST['n_importAction'], 'n_deleteImportJob')) {
			$jsonVARS['wppgError']        = "ACCESS DENIED";
			echo json_encode($jsonVARS);
			die();
		}
				
		$wppgSelectedImport = $wpdb->get_results($wpdb->prepare("SELECT *
					 FROM " . $this->cfg->tbl_import . " 
					WHERE id      = %d
					  AND site_id = %d
					  AND blog_id = %d LIMIT 1",
					  intval($importId), intval($this->cfg->site_id), intval($this->cfg->blog_id)));
		
		if ($wppgSelectedImport) :
		
			$meta_key   = 'wppgimport';
			$meta_value = $importId;
			$pwKey      = "tmp" . $importId;
			
			$wppgImportedPosts = $wpdb->get_results($wpdb->prepare("SELECT p.ID as post_id
										FROM " . $wpdb->postmeta . " pm
										JOIN " . $wpdb->posts . " p ON (p.ID = pm.post_id)
									       WHERE pm.meta_key   = %s
										 AND pm.meta_value = %s LIMIT %d",
										 $meta_key, $meta_value, $numToDelete));
			
			$wppgImportedPostsByTmps = $wpdb->get_results($wpdb->prepare("SELECT id as post_id
										FROM " . $wpdb->posts . "
									       WHERE post_password LIKE %s LIMIT %d",
									       "%" . $pwKey . "%", $numToDelete));
				
			if ($wppgImportedPosts) :
				foreach ($wppgImportedPosts as $wppgImportedPost) {
					if ($wppgImportedPost->post_id > 0) {
						wp_delete_post( $wppgImportedPost->post_id, true );
					}
				}
			elseif($wppgImportedPostsByTmps) :
				foreach ($wppgImportedPostsByTmps as $wppgImportedPostsByTmp) {
					if ($wppgImportedPostsByTmp->post_id > 0) {
						wp_delete_post( $wppgImportedPostsByTmp->post_id, true );
					}
				}
			endif;
		
			$wppgMorePosts = $wpdb->get_results($wpdb->prepare("SELECT p.ID as post_id
										FROM " . $wpdb->postmeta . " pm
										JOIN " . $wpdb->posts . " p ON (p.ID = pm.post_id)
									       WHERE pm.meta_key   = %s
										 AND pm.meta_value = %s LIMIT 1",
										 $meta_key, $meta_value));
			
			$wppgMoreImportedPostsByTmps = $wpdb->get_results($wpdb->prepare("SELECT id as post_id
										FROM " . $wpdb->posts . "
									       WHERE post_password LIKE %s LIMIT 1",
									       "%" . $pwKey . "%"));
			
			if ($wppgMorePosts || $wppgMoreImportedPostsByTmps) :
			
				$jsonVARS['wppgReturn'] = "";

			else :

				$wpdb->query($wpdb->prepare("DELETE FROM " . $this->cfg->tbl_datamap . "
						WHERE site_id   = %d
						  AND blog_id   = %d
						  AND import_id = %d",
						  intval($this->cfg->site_id), intval($this->cfg->blog_id), intval($importId)));
							
				$wpdb->query($wpdb->prepare("DELETE FROM " . $this->cfg->tbl_import . "
						WHERE site_id   = %d
						  AND blog_id   = %d
						  AND id        = %d",
						  intval($this->cfg->site_id), intval($this->cfg->blog_id), intval($importId)));
				
				$jsonVARS['wppgReturn'] = "done";
			endif;
		endif;

		echo json_encode($jsonVARS);
		
		die();
		
	}
}
?>