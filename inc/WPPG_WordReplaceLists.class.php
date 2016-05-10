<?php
class WPPG_WordReplaceLists {
	
	private $cfg     = null;
	
	function __construct() {
			
		$this->cfg = new WPPG_Config();
		
	}
	
 	// Word Replace Logic (List Display)
 	
	function init() {
	    // process actions	
	    if ( isset($_REQUEST['wppgaction']) ) {
		    switch ( $_REQUEST['wppgaction'] ) {
			    default : // initial display
				    $this->dispReplaceLists();
			    case "editlistform" :
				    $this->editReplaceListForm();
				break;
			    case "editlist" :
				    $this->editReplaceList();
				break;
			    case "editwordreplaceform" :
				    $this->editWordReplaceForm();
				break;
			    case "editwordreplace" :
				    $this->editWordReplace();
				break;
			    case "dispwordreplace" :
				    $this->dispWordReplace();
				break;
			
		    }
	    } else {
		    $this->dispReplaceLists();
	    }
	}

	function dispReplaceLists(&$wppgmsg = "") {
		
		global $wpdb;
		
		$page_links  = "";

		$search_term = isset( $_REQUEST['s'] ) ? esc_html(trim($_REQUEST['s'])) : "";
		
		$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;
			
		if ( empty($pagenum) )
		       $pagenum = 1;
		   
		$per_page         = $this->cfg->list_per_page;
		$start_query      = (($pagenum - 1) * $per_page);
		if ($start_query == 1) { $start_query = 0; }
						 
		$main_query = $wpdb->prepare("SELECT f.*, COUNT(r.id) AS rtcount FROM " . $this->cfg->tbl_format . " AS f
						LEFT JOIN " . $this->cfg->tbl_replacetxt . " AS r ON r.format_id = f.id
					       WHERE f.blog_id = %d
					         AND f.site_id = %d", 
					  	 $this->cfg->blog_id, $this->cfg->site_id);
		
		if ( $search_term )
		   $main_query .= $wpdb->prepare(" AND name LIKE '%s'", "%" . $search_term . "%");

		$main_query .= " GROUP BY f.id ORDER BY f.name";
		
		$query_results  = $wpdb->get_results($main_query);
		$num_pages      = ceil($wpdb->num_rows / $per_page);
		$count_posts    = $wpdb->num_rows;
		
		$main_query .= $wpdb->prepare(" LIMIT %d,%d",$start_query,$per_page);
		
		$query_results  = $wpdb->get_results($main_query);
		
		?>
		<div class="wrap">
			<?php screen_icon('edit-pages'); ?>
			<h2>wpPostGen : Word Replace Lists <a href="<?php echo $this->cfg->word_replace_url; ?>&wppgaction=editlistform" class="button add-new-h2">Add Word Replace List</a>
				<?php
				if ( $search_term )
	               printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', $search_term);
				?>
			</h2>
			
			<?php if ($wppgmsg) { ?>
				<div id="wppgmessage" class="updated"><?php echo $wppgmsg; ?></div>
			<?php } ?>
			<div id="message" class="updated"></div>
			
			<form id="query-filter" action="<?php echo $this->cfg->word_replace_url; ?>" method="post">
            
                <p class="search-box">
	           <label class="screen-reader-text" for="post-search-input">Search Word Replace Lists:</label>
	           <input type="text" id="post-search-input" name="s" value="<?php echo $search_term; ?>" />
	           <input type="submit" name="sButton" id="search-submit" class="button" value="Search Word Replace Lists"  />
	        </p>
            <?php if ($query_results) { ?>
            <div class="tablenav">
		<br/>
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
			        			number_format_i18n( ( $pagenum - 1 ) * $per_page + 1),
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
		<?php wp_nonce_field('n_replaceList','n_nonceAction'); ?>
				<table class="wp-list-table widefat fixed posts" cellspacing="0">
					<thead>
						<tr>
							<th scope="col" class="manage-column column-name sortable desc">&nbsp;&nbsp;Word Replace Label</th>
							<th scope="col" class="manage-column column-numlists">Word Replace Entries</th>
							<th scope="col" class="manage-column column-action">&nbsp;</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th scope="col" class="manage-column column-name sortable desc">&nbsp;&nbsp;Word Replace Label</th>
							<th scope="col" class="manage-column column-numlists">Word Replace Entries</th>
							<th scope="col" class="manage-column column-action">&nbsp;</th>
						</tr>
					</tfoot>
					
					<tbody id="the-list" class="list:dbrows">
						<?php
						
						if ($query_results) :
							
							$class = 'alternate';
							$i = 0;
							
							foreach($query_results as $query_result ) :
								$i++;
								$class = ( $class == 'alternate' ) ? '' : 'alternate';
								
					    ?>
								<tr id="dbrow-<?php echo $query_result->id; ?>" class="<?php echo $class; ?>">
									<td class="name column-name">
										<strong><?php echo esc_html($query_result->name); ?></strong>
										<br />
										<div class="row-actions">
											<span class="edit"><a class="edit-row" id="editlink-<?php echo $query_result->id; ?>" href="<?php echo $this->cfg->word_replace_url; ?>&wppgaction=editlistform&rowId=<?php echo $query_result->id; ?>">Edit</a></span> | <span class="delete"><a class="delete-row" id="removelink-<?php echo $query_result->id; ?>" href="#">Delete Permanently</a></span>
										</div>
										<div id="loading-<?php echo $query_result->id; ?>"></div>
									</td>
									<td><?php echo $query_result->rtcount; ?></td>
									<td><?php if ($query_result->rtcount == 0) { ?>
										<a href="<?php echo $this->cfg->word_replace_url; ?>&wppgaction=editwordreplaceform&wppgformatid=<?php echo $query_result->id; ?>" id="addentry-<?php echo $query_result->id; ?>">Add Word Replace Entries</a>
										<?php
									} else {
									?>
										<a href="<?php echo $this->cfg->word_replace_url; ?>&wppgaction=dispwordreplace&wppgformatid=<?php echo $query_result->id; ?>" id="viewentry-<?php echo $query_result->id; ?>">View Word Replace Entries</a>
									<?php
									} ?>
									</td>
								</tr>
						<?php 
							endforeach;
						else :	
						?>
							<tr id="dbrow-none" class="">
								<td colspan="3">
									<?php
									 if ($search_term) {
									?>
										Sorry, your search query returned 0 results.
									<?php
									 } else {
									?>
										You have not added any Word Replace Lists yet. Please click Add Word Replace List.
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
	
	function editReplaceListForm() {
		global $wpdb;
		
		$rowId = isset($_REQUEST['rowId']) ? intval($_REQUEST['rowId']) : 0;

		if ($rowId > 0) {
			$mpresult = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $this->cfg->tbl_format . " WHERE id = %d AND site_id = %d AND blog_id = %d LIMIT 1", intval($rowId), intval($this->cfg->site_id), intval($this->cfg->blog_id)));
		}
?>
		<div class="wrap">
			<?php screen_icon('edit-pages'); ?>
			<h2>wpPostGen : Add Word Replace List <a href="<?php echo $this->cfg->word_replace_url; ?>" class="button add-new-h2">Return to Word Replace Lists</a>		
		        <br /><br />
			<form method="post" action="<?php echo $this->cfg->word_replace_url; ?>">
			<?php wp_nonce_field('n_replaceList','n_nonceAction'); ?>	    	
			<input type="hidden" name="rowId" value="<?php echo $rowId; ?>">
			<input type="hidden" name="wppgaction" value="editlist">

			<div id="poststuff" class="metabox-holder">
				<div id="col-container">
				<div id="col-left">
				<div class="col-wrap">
					<div class="meta-box-sortabless">
						<div class="postbox">
							<h3 class="hndle"><span>Word Replace List Name</span></h3>
							<div class="inside">
								<p>A name to give this Word Replace List so that you can identify it easily during the import process.</p>
								<input type="text" name="rowName" value="<?php if (isset($mpresult)) { echo $mpresult->name; } ?>" />
							</div>
						</div>
					</div>
					<p class="submit">
					    <a href="<?php echo $this->cfg->word_replace_url; ?>" class="button-secondary">Return to Word Replace Lists</a>&nbsp;
					    <input type="submit" class="button-primary" value="Save Changes" />
					</p>
				</div>
				</div>
				</div>
			</div>
		    </form>
		    <br class="clear" />
		</div>
<?php
	}
	
	function editReplaceList() {
		
		global $wpdb;
		
		$rowId        = isset($_REQUEST['rowId']) ? intval($_REQUEST['rowId']) : 0;
		$rowName      = isset($_REQUEST['rowName']) ? $_REQUEST['rowName'] : "";
		
		if (!isset($_REQUEST['n_nonceAction']) || !wp_verify_nonce($_REQUEST['n_nonceAction'], 'n_replaceList')) {
			wp_die("ACCESS DENIED");
		}
		
		if ($rowId > 0) {
			$wpdb->update( $this->cfg->tbl_format,
			   array( 'name'    => $rowName),
			   array( 'id'      => intval($rowId),
				  'site_id' => intval($this->cfg->site_id),
				  'blog_id' => intval($this->cfg->blog_id)),
			   array( '%s' ), array( '%d','%d','%d' ) );
		} else {
			$wpdb->insert( $this->cfg->tbl_format, 
			   array( 'id'              => 'NULL',
			 	  'name'            => $rowName,
				  'site_id'         => intval($this->cfg->site_id),
				  'blog_id'         => intval($this->cfg->blog_id)),
			   array( '%d', '%s', '%d', '%d' ) );
		}
		
		if ($rowId == 0) {
			$wppgmsg = "Successfully added Word Replace List!";
		} else {
			$wppgmsg = "Successfully updated Word Replace List!";
		}
		$this->dispReplaceLists($wppgmsg);
	}
	
	function deleteReplaceList() {
		
		global $wpdb;
		
		$rowId        = isset($_REQUEST['rowId']) ? intval($_REQUEST['rowId']) : 0;
		$jsonVARS['wppgError'] = "success";
		
		if (!isset($_REQUEST['n_nonceAction']) || !wp_verify_nonce($_REQUEST['n_nonceAction'], 'n_replaceList')) {
			$jsonVARS['wppgError']        = "ACCESS DENIED";
			echo json_encode($jsonVARS);
			die();
		}
		
		$wppgSelectedRows = $wpdb->get_results($wpdb->prepare("SELECT *
					 FROM " . $this->cfg->tbl_format . " 
					WHERE id      = %d
					  AND site_id = %d
					  AND blog_id = %d LIMIT 1",
					  intval($rowId),intval($this->cfg->site_id),intval($this->cfg->blog_id)));

		if ($wppgSelectedRows) :
		
			$wpdb->query($wpdb->prepare("DELETE FROM " . $this->cfg->tbl_replacetxt . "
							WHERE site_id   = %d
							  AND blog_id   = %d
							  AND format_id = %d",
							  intval($this->cfg->site_id), intval($this->cfg->blog_id), intval($rowId)
							  ));
			
			$wpdb->query($wpdb->prepare("DELETE FROM " . $this->cfg->tbl_format . "
							WHERE site_id   = %d
							  AND blog_id   = %d
							  AND id        = %d",
							  intval($this->cfg->site_id), intval($this->cfg->blog_id), intval($rowId)
							  ));
		endif;

		echo json_encode($jsonVARS);
		
		die();
		
	}
	
	/******************************* CODE FOR WORD REPLACE ENTRIES *********************************/

	function dispWordReplace(&$wppgmsg = "",&$wppgformatid = 0) {
		
		global $wpdb;
		
		$page_links  = "";

	        $wppgformatid = isset( $_REQUEST['wppgformatid'] ) ? intval($_REQUEST['wppgformatid']) : $wppgformatid;
		$search_term  = isset( $_REQUEST['s'] ) ? esc_html(trim($_REQUEST['s'])) : "";
		
		$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;
			
		if ( empty($pagenum) )
		       $pagenum = 1;
		   
		$per_page         = $this->cfg->list_per_page;
		$start_query      = (($pagenum - 1) * $per_page);
		if ($start_query == 1) { $start_query = 0; }
						 
		$main_query = $wpdb->prepare("SELECT * FROM " . $this->cfg->tbl_replacetxt . "
					       WHERE blog_id   = %d
					         AND site_id   = %d
						 AND format_id = %d",
					  	 $this->cfg->blog_id, $this->cfg->site_id, $wppgformatid);
		
		if ( $search_term )
		   $main_query .= $wpdb->prepare(" AND rtext LIKE '%s'", "%" . $search_term . "%");

		$main_query .= " GROUP BY id ORDER BY rtext";
		
		$query_results  = $wpdb->get_results($main_query);
		$num_pages      = ceil($wpdb->num_rows / $per_page);
		$count_posts    = $wpdb->num_rows;
		
		$main_query .= $wpdb->prepare(" LIMIT %d,%d",$start_query,$per_page);
		
		$query_results  = $wpdb->get_results($main_query);
		
		$mpresult = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $this->cfg->tbl_format . " WHERE id = %d AND site_id = %d AND blog_id = %d LIMIT 1", intval($wppgformatid),intval($this->cfg->site_id), intval($this->cfg->blog_id)));
		
		?>
		<div class="wrap">
			<?php screen_icon('edit-pages'); ?>
			<h2>wpPostGen : Word Replace Entries for <?php echo $mpresult->name; ?> <a href="<?php echo $this->cfg->word_replace_url; ?>&wppgaction=editwordreplaceform&wppgformatid=<?php echo $wppgformatid; ?>" class="button add-new-h2">Add Word Replace Entry</a> <a href="<?php echo $this->cfg->word_replace_url; ?>" class="button add-new-h2">Return to Word Replace Lists</a>
				<?php
				if ( $search_term )
	               printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', $search_term);
				?>
			</h2>
			
			<?php if ($wppgmsg) { ?>
				<div id="wppgmessage" class="updated"><?php echo $wppgmsg; ?></div>
			<?php } ?>
			<div id="message" class="updated"></div>
			
			<form id="query-filter" action="<?php echo $this->cfg->word_replace_url; ?>" method="post">
			<input type="hidden" name="wppgaction" value="dispwordreplace">
			<input type="hidden" name="wppgformatid" value="<?php echo $wppgformatid; ?>">
                <p class="search-box">
	           <label class="screen-reader-text" for="post-search-input">Search Word Replace Lists:</label>
	           <input type="text" id="post-search-input" name="s" value="<?php echo $search_term; ?>" />
	           <input type="submit" name="sButton" id="search-submit" class="button" value="Search Word Replace Lists"  />
	        </p>
            <?php if ($query_results) { ?>
            <div class="tablenav">
		<br/>
            <?php
               $page_links = paginate_links( array(
	              'base' => add_query_arg( 'paged', '%#%' ),
	              'format' => '',
	              'prev_text' => __('&laquo;'),
	              'next_text' => __('&raquo;'),
		      'show_all'  => true,
	              'total' => $num_pages,
	              'current' => $pagenum,
		      'add_args' => array( 's'            => $search_term,
					   'wppgaction'   => 'dispwordreplace',
					   'wppgformatid' => $wppgformatid )
               ));
            if ($page_links) { ?>
	        <div class="tablenav-pages">
	        <?php
	        
	        $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
			        			number_format_i18n( ( $pagenum - 1 ) * $per_page + 1),
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
		<?php wp_nonce_field('n_replaceList','n_nonceAction'); ?>
				<table class="wp-list-table widefat fixed posts" cellspacing="0">
					<thead>
						<tr>
							<th scope="col" class="manage-column column-name sortable desc">&nbsp;&nbsp;Words to Replace</th>
							<th scope="col" class="manage-column column-numlists">Replace With</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th scope="col" class="manage-column column-name sortable desc">&nbsp;&nbsp;Words to Replace</th>
							<th scope="col" class="manage-column column-numlists">Replace With</th>
						</tr>
					</tfoot>
					
					<tbody id="the-list" class="list:dbrows">
						<?php
						
						if ($query_results) :
							
							$class = 'alternate';
							$i = 0;
							
							foreach($query_results as $query_result ) :
								$i++;
								$class = ( $class == 'alternate' ) ? '' : 'alternate';
								
					    ?>
								<tr id="dbrow-<?php echo $query_result->id; ?>" class="<?php echo $class; ?>">
									<td class="name column-name">
										<strong><?php echo esc_html($query_result->rtext); ?></strong>
										<br />
										<div class="row-actions">
											<span class="edit"><a class="edit-row" id="editlink-<?php echo $query_result->id; ?>" href="<?php echo $this->cfg->word_replace_url; ?>&wppgaction=editwordreplaceform&rowId=<?php echo $query_result->id; ?>&wppgformatid=<?php echo $wppgformatid; ?>">Edit</a></span> | <span class="delete"><a class="delete-entry" id="removelink-<?php echo $query_result->id; ?>-<?php echo $wppgformatid; ?>" href="#">Delete</a></span>
										</div>
										<div id="loading-<?php echo $query_result->id; ?>"></div>
									</td>
									<td><?php echo esc_html($query_result->rwith); ?></td>
								</tr>
						<?php 
							endforeach;
						else :	
						?>
							<tr id="dbrow-none" class="">
								<td colspan="2">
									<?php
									 if ($search_term) {
									?>
										Sorry, your search query returned 0 results.
									<?php
									 } else {
									?>
										You have not added any Word Replace Entries yet. Please click Add Word Replace Entry.
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
	
	function editWordReplaceForm() {
		global $wpdb;
		
		$rowId    = isset($_REQUEST['rowId']) ? intval($_REQUEST['rowId']) : 0;
		$formatId = isset($_REQUEST['wppgformatid']) ? intval($_REQUEST['wppgformatid']) : 0;

		if ($rowId > 0) {
			$mpresult = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $this->cfg->tbl_replacetxt . " WHERE id = %d AND site_id = %d AND blog_id = %d AND format_id = %d LIMIT 1", intval($rowId), intval($this->cfg->site_id), intval($this->cfg->blog_id),intval($formatId)));
		}
?>
		<div class="wrap">
			<?php screen_icon('edit-pages'); ?>
			<h2>wpPostGen : Add Word Replace Entry <a href="<?php echo $this->cfg->word_replace_url; ?>&wppgaction=dispwordreplace&wppgformatid=<?php echo $formatId; ?>" class="button add-new-h2">Return to Word Replace Entries</a>		
			<br /><br />
			<form method="post" action="<?php echo $this->cfg->word_replace_url; ?>">
			<?php wp_nonce_field('n_replaceList','n_nonceAction'); ?>	    	
			<input type="hidden" name="rowId" value="<?php echo $rowId; ?>">
			<input type="hidden" name="wppgformatid" value="<?php echo $formatId; ?>">
			<input type="hidden" name="wppgaction" value="editwordreplace">
			<div id="poststuff" class="metabox-holder">
				<div id="col-container">
				<div id="col-left">
				<div class="col-wrap">
					<div class="meta-box-sortabless">
						<div class="postbox">
							<h3 class="hndle"><span>Words to Replace</span></h3>
							<div class="inside">
								<p>The words or phrases (separated by comma) that you want to be replaced with the values added to "Replace With" below. When each entry is encountered, a random word from the "Replace With" will replace it in the column data during import.</p>
								<input type="text" name="rText" value="<?php if (isset($mpresult)) { echo $mpresult->rtext; } ?>" size="75" />
							</div>
						</div>
					</div>				
					
					<div class="meta-box-sortabless">
						<div class="postbox">
							<h3 class="hndle"><span>Replace With</span></h3>
							<div class="inside">
								<p>The words or phrases (separated by comma) that will be randomly selected to replace the words / phrases above in the column data during import.</p>
								<input type="text" name="rWith" value="<?php if (isset($mpresult)) { echo $mpresult->rwith; } ?>" size="75" />
							</div>
						</div>
					</div>	
			<p class="submit">
			    <a href="<?php echo $this->cfg->word_replace_url; ?>&wppgaction=dispwordreplace&wppgformatid=<?php echo $formatId; ?>" class="button-secondary">Return to Word Replace Entries</a>&nbsp;
			    <input type="submit" class="button-primary" value="Save Changes" />
			</p>
				</div>
				</div>
				</div>
			</div>
		    </form>
		    <br class="clear" />
		</div>
<?php
	}
	
	function editWordReplace() {
		
		global $wpdb;
		
		$rowId        = isset($_REQUEST['rowId']) ? intval($_REQUEST['rowId']) : 0;
		$formatId     = isset($_REQUEST['wppgformatid']) ? intval($_REQUEST['wppgformatid']) : 0;
		$rText        = isset($_REQUEST['rText']) ? $_REQUEST['rText'] : "";
		$rWith        = isset($_REQUEST['rWith']) ? $_REQUEST['rWith'] : "";
		
		if (!isset($_REQUEST['n_nonceAction']) || !wp_verify_nonce($_REQUEST['n_nonceAction'], 'n_replaceList')) {
			wp_die("ACCESS DENIED");
		}
		
		if ($rowId > 0) {
			$wpdb->update( $this->cfg->tbl_replacetxt,
			   array( 'rtext'     => $rText,
				  'rwith'     => $rWith),
			   array( 'id'        => intval($rowId),
				  'site_id'   => intval($this->cfg->site_id),
				  'blog_id'   => intval($this->cfg->blog_id),
				  'format_id' => intval($formatId)),
			   array( '%s','%s' ), array( '%d','%d','%d','%d' ) );
		} else {
			$wpdb->insert( $this->cfg->tbl_replacetxt, 
			   array( 'id'        => 'NULL',
			 	  'rtext'     => $rText,
				  'rwith'     => $rWith,
				  'site_id'   => intval($this->cfg->site_id),
				  'blog_id'   => intval($this->cfg->blog_id),
				  'format_id' => intval($formatId)),
			   array( '%d', '%s', '%s', '%d', '%d', '%d' ) );
		}
		
		if ($rowId == 0) {
			$wppgmsg = "Successfully added Word Replace Entry!";
		} else {
			$wppgmsg = "Successfully updated Word Replace Entry!";
		}
		$this->dispWordReplace($wppgmsg,$formatId);
	}
	
	function deleteWordReplace() {
		
		global $wpdb;
		
		$rowId    = isset($_REQUEST['rowId']) ? intval($_REQUEST['rowId']) : 0;
		$formatId = isset($_REQUEST['wppgformatid']) ? intval($_REQUEST['wppgformatid']) : 0;
		
		$jsonVARS['wppgError'] = "success";
		
		if (!isset($_REQUEST['n_nonceAction']) || !wp_verify_nonce($_REQUEST['n_nonceAction'], 'n_replaceList')) {
			$jsonVARS['wppgError']        = "ACCESS DENIED";
			echo json_encode($jsonVARS);
			die();
		}
		
		$wpdb->query($wpdb->prepare("DELETE FROM " . $this->cfg->tbl_replacetxt . "
						WHERE id        = %d
						  AND site_id   = %d
						  AND blog_id   = %d
						  AND format_id = %d",
						  intval($rowId),intval($this->cfg->site_id), intval($this->cfg->blog_id), intval($formatId)
						  ));
		
		echo json_encode($jsonVARS);
		
		die();
		
	}
}
?>