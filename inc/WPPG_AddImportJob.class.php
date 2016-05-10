<?php
class WPPG_AddImportJob {
	
	private $cfg     = null;
	
	function __construct() {
			
		$this->cfg = new WPPG_Config();

	}
		
	// Import Jobs Logic (Add)
	
	function init() {
		
		$this->addImportJobForm();

	}
	
	function addImportJobForm() {
		global $wpdb;
		$beginDate   = "";
		$beginHour   = 0;
		$beginMinute = 0;
		
		$postargs = array(
		    'numberposts'     => 1,
		    'orderby'         => 'post_date',
		    'order'           => 'DESC',
		    'post_type'       => 'post' );
		
		$myposts = get_posts( $postargs );
		
		foreach( $myposts as $lastpost ) {
			$beginDate   = date('m/d/Y',strtotime($lastpost->post_date));
			$beginHour   = date('H',strtotime($lastpost->post_date));
			$beginMinute = date('i',strtotime($lastpost->post_date));
		}
		
		if ($beginDate == "") { $beginDate = date('m/d/Y'); }
		
		// add import form
		?>
		<div class="wrap nosubsub">
			<?php screen_icon('edit-pages'); ?>
			<h2>wpPostGen : Add New Import Job</h2>
						
			<form id="importjobaddform" action="">
				
				<?php wp_nonce_field('n_addImportJob','n_importAction'); ?>
				<div id="poststuff" class="metabox-holder">	
					<div id="tabs">
						<ul>
							<li><a href="#tabs-1">Options</a></li>						
							<li><a href="#tabs-2">Data Mapping</a></li>
							<li><a href="#tabs-3">Finalize Import</a></li>
						</ul>
						
						<div id="tabs-1">
							<div id="tabmsg" class="tabupdated"><p>In this step, define settings that will be used to create posts from your import file selected below. When finished, choose the Next button at the bottom to continue to the next step (Data Mapping).</p></div>
							<div id="col-container">
								<div id="col-right">
									<div class="col-wrap">
										<div class="meta-box-sortabless">
											<div class="postbox">
												<h3 class="hndle"><span>Beginning Post Date</span></h3>
												<div class="inside">
													<p>A date where the generated posts will begin.</p>
													<input type="text" name="d_beginDate" id="d_beginDate" style="width:200px" value="<?php echo $beginDate; ?>" />&nbsp;&nbsp;@&nbsp;&nbsp;
													<select name="s_beginTimeHour" id="s_beginTimeHour" style="width:50px">
													<?php
														$num = 0; 

 														while ( $num <= 23 )	{
															if ($num == $beginHour) {
													?>
														<option selected="selected"><?php echo sprintf("%02d",$num); ?></option>
													<?php
																
															} else {
													?>
														<option><?php echo sprintf("%02d",$num); ?></option>
													<?php
															}
															
															$num++;
														}
													?>
													</select> : 
													<select name="s_beginTimeMin" id="s_beginTimeMin" style="width:50px">
													<?php
														$num = 0; 

 														while ( $num <= 59 )	{
															if ($num == $beginMinute) {
													?>
														<option selected="selected"><?php echo sprintf("%02d",$num); ?></option>
													<?php
																
															} else {
													?>
														<option><?php echo sprintf("%02d",$num); ?></option>
													<?php
															}
															
															$num++;
														}
													?>
													</select>
													<div id="wppgDatePicker"></div>
												</div>
											</div>
										</div>
										<div class="meta-box-sortabless">
											<div class="postbox">
												<h3 class="hndle"><span>Post Interval</span></h3>
												<div class="inside">
													<p>The posts will be generated every so many days/hours/minutes you specify above for the entire import file contents. Example: Every 3 Days</p>
													<p><input type="radio" name="r_postInterval_type" id="r_postInterval_type5" value="month" />Months <input type="radio" name="r_postInterval_type" id="r_postInterval_type4" value="week" />Weeks <input type="radio" tabindex="6" name="r_postInterval_type" id="r_postInterval_type1" value="day" checked="checked" />Days <input type="radio" name="r_postInterval_type" id="r_postInterval_type2" value="hour" />Hours <input type="radio" name="r_postInterval_type" id="r_postInterval_type3" value="minute" />Minutes</p>
													<p><input type="text" name="i_postInterval" tabindex="7" id="i_postInterval" style="width:200px" value="3" /></p>
												</div>
											</div>
										</div>
										<div class="meta-box-sortabless">
											<div class="postbox">
												<h3 class="hndle"><span>Posts Per Interval</span></h3>
												<div class="inside">
													<p>The number of posts that will be generated based on the interval specified above. Example: 2 Posts Every 3 Days</p>
													<p><input type="text" name="i_postsPerInterval" tabindex="8" id="i_postsPerInterval" value="2" style="width:200px" /></p>
												</div>
											</div>
										</div>
										<div class="meta-box-sortabless">
											<div class="postbox">
												<h3 class="hndle"><span>Randomize Import</span></h3>
												<div class="inside">
													<p>Yes/No to randomize the entries taken from the Import File to generate posts.</p>
													<p><input type="radio" name="r_randomizeCSV" tabindex="9" id="r_randomizeCSV1" value="1" checked="checked" />Yes <input type="radio" name="r_randomizeCSV" id="r_randomizeCSV2" value="0" />No</p>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div id="col-left">
									<div class="col-wrap">
										<div class="meta-box-sortabless">
											<div class="postbox">
												<h3 class="hndle"><span>Import File</span></h3>
												<div class="inside">
													<p>Select the file delimiter used in the Import File.</p>
													<p>
													<select id="s_fileDelim" name="s_fileDelim" tabindex="1" style="width:200px">
														<option value="" selected="selected">Please choose one ...</option>
														<option value="pipe">Pipe : data1|data2</option>
														<option value="comma">Comma : data1,data2</option>
														<option value="semicolon">Semicolon : data1;data2</option>
														<option value="tab">Tab : data1	data2</option>
													</select>
													</p>
													<p><?php echo 'Below is a list of files from the <a href="upload.php">Media Library</a>. You may type / select an existing import title or you may add a new upload in the <a href="upload.php">Media Library</a>. When finished, return to this page to see your new file listed.'; ?></p>
													<p>
														<select id="s_importFile" name="s_importFile" tabindex="2" style="width:200px">
															<option value="" selected="selected">Select an Import File ...</option>
														<?php 
														query_posts( 'post_type=attachment&post_status=inherit&post_mime_type=text/csv&nopaging=true' );
														while ( have_posts() ) : the_post();
														?>
														    <option value="<?php esc_attr(the_ID()); ?>"><?php esc_html(the_title()); ?></option>
														<?php
														endwhile; 
														?>
														</select>
														
													</p>
													<input type="hidden" name="h_importRows" id="h_importRows" value="" />
													<div id="importDivs">
															<div id="displayImport-">
															</div>
														<?php
														rewind_posts(); 
														query_posts( 'post_type=attachment&post_status=inherit&post_mime_type=text/csv' );
														while ( have_posts() ) : the_post();
														?>
															<div id="displayImport-<?php esc_attr(the_ID()); ?>" style="display: none;">
																<p><strong>Uploaded</strong> : <?php echo get_the_date(); ?></p> 
																<p><strong>Description</strong> : <?php echo get_the_content(); ?></p>
																<div id="preloadImportFile-<?php esc_attr(the_ID()); ?>"></div>
															</div>
														<?php
														endwhile; 
														wp_reset_query();
														?>
													</div>
												</div>
											</div>
										</div>
										<div class="meta-box-sortabless">
											<div class="postbox">
												<h3 class="hndle"><span>Import Job Label</span></h3>
												<div class="inside">
													<p>A name to give this import for later reference. Example: August News Feed</p>
													<input type="text" id="i_importName" name="i_importName" tabindex="3" maxlength="128" value="" style="width:200px" />
												</div>
											</div>
										</div>
										<div class="meta-box-sortabless">
											<div class="postbox">
												<h3 class="hndle"><span>Post Author</span></h3>
												<div class="inside">
													<p>Select the author(s) you wish to create the posts for. If multiple are chosen, they will randomly be chosen during the import process for each post.</p>
													<?php $selecthtml = wp_dropdown_users(array('echo' => '0', 'name' => 's_postAuthor[]', 'id' => 's_postAuthor', 'who' => 'authors'));
														$selecthtml = str_replace('<select', '<select style=\'width:200px\' multiple=\'multiple\' tabindex=\'4\' size=\'5\'', $selecthtml);
														echo $selecthtml;
													 ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
	
							<span style="clear:both;"></span>
							<div id="wizard-nav"><input id="btnNext" class="button-primary" type="button" name="btnNext" value="Next" /></div>
							                  
						</div>
						<div id="tabs-2">
							
						</div>
						<div id="tabs-3">
							
							<div id="tabmsg" class="tabupdated">
								<p>If you have finished setting up this Import Job, you may choose submit below to start the actual importing / creating of the data in the appropriate WordPress Post Type you have chosen.</p>
								<p>Please do not interrupt the import process... If you do accidentally interrupt the process and wish to remove all posts associated to this Import Job, please visit the Import Jobs list.</p>
								<p>Import Job progress will be displayed below ... <strong>(can take several minutes for large imports)</strong></p>
							</div>
							
							<div id="importProgress">
								<input type="hidden" name="h_importStartRow" id="h_importStartRow" value="0" />
								<div id="importProgressBar">
									
								</div>
							</div>
							
							<span style="clear:both;"></span><div id="wizard-nav"><input id="btnPrev" class="button-secondary" type="button" name="btnPrev" value="Previous" />&nbsp;<input id="btnSubmit" class="button-primary" type="button" name="btnSubmit" value="Submit" /></div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}

	function callDataMapTab() {
		
		global $wpdb;
		$jsonVARS = array();
		
		if (!isset($_REQUEST['fileId']) || !isset($_REQUEST['fileDelim'])) {
			$jsonVARS['wppgError'] = "REQUIRED PARAMETERS NOT SET";
			echo json_encode($jsonVARS);
			die();
		}
		
		$importFile     = get_attached_file( $_REQUEST['fileId'] );
		$wppgfiledelim  = $_REQUEST['fileDelim'];
		$wppgfiledelim  = $this->cfg->importFileDelim[$wppgfiledelim];
		
		$wppgnumcols       = 0;
		$wppgnumrows       = 0;
		$wppgErrorMsg      = "";
		$jsonVARS['wppgError'] = "success";
		
		if (!isset($_REQUEST['n_importAction']) || !wp_verify_nonce($_REQUEST['n_importAction'], 'n_addImportJob')) {
			$jsonVARS['wppgError']        = "ACCESS DENIED";
			echo json_encode($jsonVARS);
			die();
		}
		
		$wordReplaceHTML = '<option value="None" slected="selected">None</option>';

		$mp_formats = $wpdb->prepare("SELECT f.*, COUNT(r.id) AS rtcount FROM " . $this->cfg->tbl_format . " AS f
						LEFT JOIN " . $this->cfg->tbl_replacetxt . " AS r ON r.format_id = f.id
					       WHERE f.blog_id = %d
					         AND f.site_id = %d
						 AND r.id   > 0 GROUP BY f.id ORDER BY f.name", 
					  	 intval($this->cfg->blog_id), intval($this->cfg->site_id));
		
		$mp_formats = $wpdb->get_results($mp_formats);

		foreach ($mp_formats as $mp_format) {
			$wordReplaceHTML .= '<option value="' . $mp_format->id . '">' . $mp_format->name . '</option>';
		}


		$wppgDispSelectedImports = $wpdb->get_results($wpdb->prepare("SELECT *
					 FROM " . $this->cfg->tbl_import . " 
					WHERE site_id = %d
					  AND blog_id = %d",
					  intval($this->cfg->site_id), intval($this->cfg->blog_id)));

		$preImportSelectHTML = "";
		
		if ($wppgDispSelectedImports) {
			foreach ($wppgDispSelectedImports as $wppgDispSelectedImport) {
				$wppgDispDataMaps = $wpdb->get_results($wpdb->prepare("SELECT *
							 FROM " . $this->cfg->tbl_datamap . " 
							WHERE site_id   = %d
							  AND blog_id   = %d
							  AND import_id = %d LIMIT 1",
							  intval($this->cfg->site_id), intval($this->cfg->blog_id), intval($wppgDispSelectedImport->id)));
				
				if ($wppgDispDataMaps) {
					$preImportSelectHTML .= '<option value="' . $wppgDispSelectedImport->id . '">' . $wppgDispSelectedImport->name . '</option>';
				}
			}
		}
			
		$wppgdefaultcols = explode(",",$this->cfg->db_columns);
		
?>
		<div id="datamap-response">
			<div id="poststuff" class="metabox-holder">
				<div id="post-body">
					<div id="post-body-content">
						<div id="tabmsg" class="tabupdated"><p>In this step, you will link a column of data from your Import File with a WordPress column name and/or a custom taxonomy / field. Please fill-in each Column Relationship below for the corresponding data element in your import file. <strong>All fields below are Optional</strong>, however, leaving a Column Relationship blank will skip that data during the import. When finished, choose the Next button at the bottom to continue to the next step (Finalize Import).</p></div>
						<div id="datamap-options" class="stuffbox">
							<h3>Pre-Defined Data Mappings</h3>
							<div class="inside">
								<p>Choose the WordPress Post Type you wish to assign your imported data for and then you may select a Pre-Loaded Theme Map or Copy from Previous Import to help you map your data. You may also enter your own relationships for each Import column below...</p>
								<p>&nbsp;</p>
								<p>
									<label for="s_customPostType"><strong><u>WordPress Post Type</u></strong></label>
								</p>
								<p>
									<select name="s_customPostType" id="s_customPostType">
<?php
									$post_types=get_post_types('','names');
									$taxhtml   = null;
									foreach ($post_types as $post_type ) {
								      if ($post_type != "attachment" && $post_type != "mediapage" && $post_type != "revision" && $post_type != "nav_menu_item") {
								      	$taxonomies = get_object_taxonomies($post_type);
									  	echo '<option value="'.$post_type.'">'. $post_type. '</option>';
										foreach ($taxonomies as $taxonomy ) {
											$taxhtml[$post_type] .= '<option value="' . $taxonomy . '">' . $taxonomy . '</option>';
										}	
									  }
									}
?>										

									</select>
								</p>
								<div id="customTagTaxonomyWrapper">
										<?php
										foreach ( $taxhtml as $taxkey => $taxval ) {
											if ($taxkey == "post" || $taxkey == "page") continue;
										?>
										<div id="customTagTaxonomyDiv-<?php echo $taxkey; ?>">
											<p><strong><u>Tag Taxonomy</u></strong></p>
											<p><select name="s_customTagTaxonomy-<?php echo $taxkey; ?>" id="s_customTagTaxonomy-<?php echo $taxkey; ?>">
												<?php echo $taxval; ?>
											</select></p>
										</div>
										<?php } ?>
								</div>
								<p><strong><u>Choose a Map Type</u></strong></p>
								<p> 
									<input type="radio" name="r_mapType" id="r_mapType1" value="mapNone" checked="checked" /> None<br/> 
									<input type="radio" name="r_mapType" id="r_mapType2" value="mapTypeTheme" /> Pre-Loaded Theme Maps<br/>
									<?php if ($preImportSelectHTML != "") { ?>
									<input type="radio" name="r_mapType" id="r_mapType3" value="mapTypePreviousImport" /> Copy from Previous Import
									<?php } ?>
								</p>
								<div id="mapTypes">									
									<div id="mapNone">
										
									</div>
									<div id="mapTypeTheme">
										<p><strong><u>Pre-Loaded Theme Maps</u></strong></p>
										<p>
											<?php
											$numPreloadMaps = count($this->cfg->preloadMapNames);
											
											for ($np = 0; $np < $numPreloadMaps; $np++) {
												echo $this->cfg->preloadMapNames[$np];
												echo ' <input type="radio" name="r_themeMap" id="r_themeMap-' . $np . '" value="' . str_replace(" ","",$this->cfg->preloadMapNames[$np]) . '" />';
											}
											?>
										</p>
									</div>

									<div id="mapTypePreviousImport">
										<p>
											<label for="s_otherImport"><strong><u>Copy from Previous Import</u></strong></label>
										</p>
										<p>
											<select name="s_otherImport" id="s_otherImport">
<?php
												echo $preImportSelectHTML;
?>
											</select>
										</p>										
									</div>
									<div id="dataMapDisplay"></div>
								</div>
							</div>
						</div>					
<?php
		if (($fhandle = fopen($importFile, "r")) !== FALSE) {
			
		    while (($coldata = fgetcsv($fhandle, 0, $wppgfiledelim, "\"")) !== FALSE) {
				
		        $wppgnumcols = count($coldata);
		        $wppgnumrows++;

        		for ($c=0; $c < $wppgnumcols; $c++) {

?>
						<div id="datamap-<?php echo $c; ?>" class="stuffbox">
							<h3><label for="t_col_from-<?php echo $c; ?>"><?php echo 'DataMap Column ' . ($c + 1); ?></label></h3>
							<div class="inside">
								<div id="wppgcols">
									<div class="wppgcol1">
										<textarea name="t_col_from-<?php echo $c; ?>" id="t_col_from-<?php echo $c; ?>" cols="45" rows="10" disabled><?php echo esc_html($coldata[$c]); ?></textarea>
									</div>
									<div class="wppgcol2">
										<p><strong><u>Column Relationship</u></strong></p>
										<p><input type="text" id="i_col_to-<?php echo $c; ?>" name="i_col_to-<?php echo $c; ?>" value="" />
											<?php
											for ($np = 0; $np < $numPreloadMaps; $np++) {
												$numPreloadCols = count($this->cfg->preloadMapCols[$this->cfg->preloadMapNames[$np]]);
												
												echo '<select name="s_col_type-' . $c . '-' . str_replace(" ","",$this->cfg->preloadMapNames[$np]) . '" id="s_col_type-' . $c . '-' . str_replace(" ","",$this->cfg->preloadMapNames[$np]) . '">';
												echo '<option value="">' . $this->cfg->preloadMapNames[$np] . ' Column List ...</option>';
												echo '<option value=""></option>';
												echo '<option value="">Custom Field</option>';
												echo '<option value=""></option>';
												echo '<option value="">--------------------</option>';
												for ($zp = 0; $zp < $numPreloadCols; $zp++) {
													echo '<option value="' . $this->cfg->preloadMapCols[$this->cfg->preloadMapNames[$np]][$zp] . '">' . $this->cfg->preloadMapCols[$this->cfg->preloadMapNames[$np]][$zp] . '</option>';
												}
												
												echo '</select>';
											}
											
											foreach ($post_types as $post_type ) {
										      if ($post_type != "attachment" && $post_type != "mediapage" && $post_type != "revision" && $post_type != "nav_menu_item") {
										      	$taxonomies = get_object_taxonomies($post_type);
										      	echo '<select name="s_col_type-' . $c . '-' . $post_type . '" id="s_col_type-' . $c . '-' . $post_type . '">';
													echo '<option value="">(' . $post_type . ') Post Type Column List ...</option>';
													echo '<option value=""></option>';
													echo '<option value="">Custom Field</option>';
													echo '<option value=""></option>';
													echo '<option value="">---- * Default *----</option>';
												foreach ($wppgdefaultcols as $wppgdefaultcol ) {
													echo '<option value="' . $wppgdefaultcol . '">' . $wppgdefaultcol . '</option>';
												}	
													echo '<option value=""></option>';
													echo '<option value="">----Taxonomy/Meta----</option>';

													echo $taxhtml[$post_type];
												echo '</select>';
											  }
											}
											?>
										</p>
										<p><strong><u>Wrap Data</u></strong></p>
										<p>Before : <input type="text" id="i_col_prepend-<?php echo $c; ?>" name="i_col_prepend-<?php echo $c; ?>" value="" /></p>
										<p>&nbsp;&nbsp;&nbsp;After : <input type="text" id="i_col_append-<?php echo $c; ?>" name="i_col_append-<?php echo $c; ?>" value="" /></p>
									</div>
									<div class="wppgcol3">
										<p><strong><u>Use Widget</u></strong></p>
										<p><select name="s_widgetType-<?php echo $c; ?>" id="s_widgetType-<?php echo $c; ?>">
											<option value="none">None</option>
											<option value="genflv">Generate FLV Player HTML</option>
											<option value="genimg">Generate Image HTML</option>
										</select></p>
										<div id="widgetTypeOptions-<?php echo $c; ?>">
											<p><strong><u>Widget Options</u></strong></p>
											<p>&nbsp;Width : <input type="text" id="i_opt_width-<?php echo $c; ?>" name="i_opt_width-<?php echo $c; ?>" value="" /></p>
											<p>Height : <input type="text" id="i_opt_height-<?php echo $c; ?>" name="i_opt_height-<?php echo $c; ?>" value="" /></p>
											<p>Class Name : <input type="text" id="i_opt_class-<?php echo $c; ?>" name="i_opt_class-<?php echo $c; ?>" value="" /></p>
										</div>
									</div>
									<div class="wppgcol4">
										<p><strong><u>Use Replacement List</u></strong></p>
										<p><select name="s_wordReplace-<?php echo $c; ?>" id="s_wordReplace-<?php echo $c; ?>">
											<?php echo $wordReplaceHTML; ?>
										</select></p>
										<p><strong><u>Required Column</u></strong></p>
										<p><select name="s_requiredCol-<?php echo $c; ?>" id="s_requiredCol-<?php echo $c; ?>">
											<option value="0">No</option>
											<option value="1">Yes</option>
										</select></p>
										<p><strong><u>Generate Tags</u></strong></p>
										<p><select name="s_genTags-<?php echo $c; ?>" id="s_genTags-<?php echo $c; ?>">
											<option value="0">No</option>
											<option value="1">Yes</option>
										</select></p>
									</div>
								</div>
								<div style="clear:both;"></div>
							</div>
						</div>
<?php
				}
				break;
		    }
		    fclose($fhandle);

?>
						<div id="poststuff" class="metabox-holder">
							<div id="col-container">
							<div id="col-right">
							<div class="col-wrap">
								<div class="meta-box-sortabless">
									<div class="postbox">
										<h3 class="hndle"><span>Custom Post Tags</span></h3>
										<div class="inside">
											<p>Enter any custom post tags you wish to attach to each record in the import. (separated by comma)</p>
											<p><input type="text" id="i_post_tags" name="i_post_tags" value="" size="75" /></p>
										</div>
									</div>
								</div>				
							</div>
							</div>
							<div id="col-left">
							<div class="col-wrap">
								<div class="meta-box-sortabless">
									<div class="postbox">
										<h3 class="hndle"><span>Random Number Generator</span></h3>
										<div class="inside">
											<p>The random number generator allows you to create a custom field attached to each post during the import that will generate a random number. Enter a name for this custom field and then, enter the (floor) number that will be the lowest number generated. Then, enter the (ceiling) number that will be the highest number generated.</p>
											<p><strong><u>Column Name</u></strong>: <input type="text" id="i_post_randnum_col" name="i_post_randnum_col" value="" size="30" /></p>
											<p><strong><u>Floor (low) Number</u></strong>: <input type="text" id="i_post_randnum_floor" name="i_post_randnum_floor" value="" size="30" /></p>
											<p><strong><u>Ceiling (high) Number</u></strong>: <input type="text" id="i_post_randnum_ceil" name="i_post_randnum_ceil" value="" size="30" /></p>
										</div>
									</div>
								</div>				
							</div>
							</div>
							
							</div>
						</div>
						<br class="clear" />
						
						<div id="datamap-custom-fields" class="stuffbox">
							<h3><label for="custom-fields">Extra Data (Custom Fields)</label></h3>
							<div class="inside">
						        <div id="postcustomstuff">
									<table id="list-table" style="display: none;">
										<thead>
										<tr>
											<th class="left">Name</th>
											<th>Value</th>
										</tr>
										</thead>
										<tbody id="the-list" class="list:meta">
										
										</tbody>
									</table>
									<p>&nbsp;</p>
									<table id="newmeta">
										<thead>
											<tr>
												<th class="left"><label for="metakeyselect">Name</label></th>
												<th><label for="metavalue">Value</label></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td id="newmetaleft" class="left">
													<input type="text" id="i_metaKeyInput" name="i_metaKeyInput" value="" />
												</td>
												<td>
													<textarea id="i_metaValueInput" name="i_metaValueInput" rows="2" cols="25"></textarea>
												</td>
											</tr>
											<tr>
												<td colspan="2" class="submit">
													<input id="btnClearCustom" class="button-secondary" type="button" name="btnClearCustom" value="Clear Custom Fields" /><input id="btnAddCustom" class="button-secondary" type="button" name="btnAddCustom" value="Add Custom Field" />
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
<?php			
			
		}
?>
					</div>
				</div>
			</div>
			
			<span style="clear:both;"></span>
			<div id="wizard-nav">
				<input id="btnPrev" class="button-secondary" type="button" name="btnPrev" value="Previous" />
				<input id="btnNext" class="button-primary" type="button" name="btnNext" value="Next" />	
			</div>
				
		</div>	
<?php
		die();
	}

	function preloadImportFile() {
		
		$jsonVARS = array();
		
		if (!isset($_REQUEST['fileId']) || !isset($_REQUEST['fileDelim'])) {
			$jsonVARS['wppgError'] = "REQUIRED PARAMETERS NOT SET";
			echo json_encode($jsonVARS);
			die();
		}
		
		$importFile     = get_attached_file( $_REQUEST['fileId'] );
		$wppgfiledelim  = $_REQUEST['fileDelim'];
		$wppgfiledelim  = $this->cfg->importFileDelim[$wppgfiledelim];
		
		$wppgnumcols       = 0;
		$wppgnumrows       = 0;

		$jsonVARS['wppgError'] = "success";
		
		if (!isset($_REQUEST['n_importAction']) || !wp_verify_nonce($_REQUEST['n_importAction'], 'n_addImportJob')) {
			$jsonVARS['wppgError'] = "ACCESS DENIED";
			echo json_encode($jsonVARS);
			die();
		}
			
		if (($fhandle = fopen($importFile, "r")) !== FALSE) {
		    while (($rowData = fgetcsv($fhandle, 0, $wppgfiledelim, "\"")) !== FALSE) {
		        $wppgnumcols = count($rowData);
		        $wppgnumrows++;
		    }
		    fclose($fhandle);
		}
		
		if ($wppgnumrows == 0 || $wppgnumcols == 0) {
			$jsonVARS['wppgError'] = "ERROR LOADING FILE :: PLEASE TRY AGAIN";
		}
		
		$jsonVARS['h_importRows'] = $wppgnumrows;
		$jsonVARS['h_importCols'] = $wppgnumcols;

		echo json_encode($jsonVARS);
		die();
	}

	function prevImportSelect() {
		
		global $wpdb;
		$jsonVARS = array();
		
		if (!isset($_REQUEST['importID'])) {
			$jsonVARS['wppgError'] = "REQUIRED PARAMETERS NOT SET";
			echo json_encode($jsonVARS);
			die();
		}
		
		$jsonVARS['wppgError'] = "success";
		
		if (!isset($_REQUEST['n_importAction']) || !wp_verify_nonce($_REQUEST['n_importAction'], 'n_addImportJob')) {
			$jsonVARS['wppgError'] = "ACCESS DENIED";
			echo json_encode($jsonVARS);
			die();
		}
		
		$importID  = $_REQUEST['importID'];
		
		$wppgSelectedImport = $wpdb->get_results($wpdb->prepare("SELECT *
					 FROM " . $this->cfg->tbl_import . " 
					WHERE id      = %d
					  AND site_id = %d
					  AND blog_id = %d LIMIT 1",
					  intval($importID), intval($this->cfg->site_id), intval($this->cfg->blog_id)));

		if ($wppgSelectedImport) :
			foreach ($wppgSelectedImport as $wppgImport) {
				
				$wppgDataMaps = $wpdb->get_results($wpdb->prepare("SELECT *
							 FROM " . $this->cfg->tbl_datamap . " 
							WHERE site_id   = %d
							  AND blog_id   = %d
							  AND import_id = %d
							  AND col_type  = 'standard' ORDER BY col_from",
							  intval($this->cfg->site_id), intval($this->cfg->blog_id), intval($wppgImport->id)));
				
				$wppgCustomDataMaps = $wpdb->get_results($wpdb->prepare("SELECT *
							 FROM " . $this->cfg->tbl_datamap . " 
							WHERE site_id   = %d
							  AND blog_id   = %d
							  AND import_id = %d
							  AND col_type  = 'custom' ORDER BY col_from",
							  intval($this->cfg->site_id), intval($this->cfg->blog_id), intval($wppgImport->id)));
				
				$wppgPostTags = $wpdb->get_results($wpdb->prepare("SELECT *
							 FROM " . $this->cfg->tbl_datamap . " 
							WHERE site_id   = %d
							  AND blog_id   = %d
							  AND import_id = %d
							  AND col_type  = 'posttags' ORDER BY col_from",
							  intval($this->cfg->site_id), intval($this->cfg->blog_id), intval($wppgImport->id)));
				
				$wppgPostRandNum = $wpdb->get_results($wpdb->prepare("SELECT *
							 FROM " . $this->cfg->tbl_datamap . " 
							WHERE site_id   = %d
							  AND blog_id   = %d
							  AND import_id = %d
							  AND col_type  = 'postrandnum' ORDER BY col_from",
							  intval($this->cfg->site_id), intval($this->cfg->blog_id), intval($wppgImport->id)));
				
			}
		else :
		
			$jsonVARS['wppgError'] = "Unable to find Import ID.";
		
		endif;
		
		$jsonVARS['selectedImport'] = $wppgSelectedImport;
		$jsonVARS['dataMap']        = $wppgDataMaps;
		$jsonVARS['customDataMap']  = $wppgCustomDataMaps;
		$jsonVARS['postTags']       = $wppgPostTags;
		$jsonVARS['postRandNum']    = $wppgPostRandNum;
		echo json_encode($jsonVARS);
		
		die();
	}

	function wppgData() {
		$returnData              = array();
		
		foreach ($_REQUEST as $vField => $vValue) {
			$returnData[$vField] = $vValue;
		} 
		
		return $returnData;
	}
	
	function wppgDebug($label = "default") {
		
		return " | " . $label . " | " . (memory_get_peak_usage(true)/1024) . " | " . date('c');
	}
	
	function finishImport() {
		
		$jsonVARS = array();
				
		$wppgData          = $this->wppgData();
		$wppgData['file']  = get_attached_file( $wppgData['fileId'] );
		
		if (!isset($wppgData['fileId']) || !isset($wppgData['fileDelim']) || !isset($wppgData['file'])) {
			$jsonVARS['wppgError'] = "REQUIRED PARAMETERS NOT SET";
			echo json_encode($jsonVARS);
			die();
		}
		
		$wppgnumcols       = 0;
		$wppgnumrows       = 0;
		$wppgErrorMsg      = "";
		$wppgrowsprocessed = 0;
		$wppgendrows       = 0;
		$wppgdataperpage   = (int) $this->cfg->data_per_page;
		$wppgworkdata      = array();
		$wppgfiledelim     = $wppgData['fileDelim'];
		$wppgfiledelim     = $this->cfg->importFileDelim[$wppgfiledelim];
		
		if (!isset($_REQUEST['n_importAction']) || !wp_verify_nonce($_REQUEST['n_importAction'], 'n_addImportJob')) {
			$jsonVARS['wppgError'] = "ACCESS DENIED";
			echo json_encode($jsonVARS);
			die();
		}
		
		$wppgDebug = $this->wppgDebug('start');
		
		if (($fhandle = fopen($wppgData['file'], "r")) !== FALSE) {

			while (($coldata = fgetcsv($fhandle, 0, $wppgfiledelim, "\"")) !== FALSE) {

				$wppgnumcols = count($coldata);
				
				if ($wppgData['h_importStartRow'] > $wppgnumrows) {
					$wppgnumrows++;
					continue;
				}
				
				$wppgrowsprocessed++;
				
				if ($wppgrowsprocessed > $wppgdataperpage && $wppgData['currPage'] != $wppgData['pageNums']) {
					continue;
				}
				
				$wppgnumrows++;
				
				
				for ($c=0; $c < $wppgnumcols; $c++) {
					
					$wppgworkdata[($wppgnumrows - 1)][$c] = $coldata[$c];
					
				}
				
				$wppgendrows++;
			}
			fclose($fhandle);
		} else {
			$jsonVARS['wppgError'] = "UNABLE TO OPEN IMPORT FILE";
			echo json_encode($jsonVARS);
			die();
		}

		$wppgDebug .= $this->wppgDebug('after loop');
		
		$jsonVARS = $this->commitData($wppgworkdata,$wppgData);

		$jsonVARS['wppgDebug'] = $wppgDebug . $this->wppgDebug('after commitData');
		
		if (!isset($jsonVARS['wppgError'])) {
			$jsonVARS['wppgError'] = "success";
		}
		
		$jsonVARS['numRows'] = $wppgnumrows;
		$jsonVARS['endRows'] = $wppgendrows;
		echo json_encode($jsonVARS);
		die();
	}
	
	function commitData($importData,$formData) {
		
		global $wpdb;
		
		// Main Import Parameters
		$fileID             = $formData['fileId'];
		$beginDate          = $formData['d_beginDate'];
		$importName         = $formData['i_importName'];
		$postInterval       = $formData['i_postInterval'];
		$postsPerInterval   = $formData['i_postsPerInterval'];
		$postInterval_type  = $formData['r_postInterval_type'];
		$mapType            = $formData['r_mapType'];
		$beginTimeHour      = $formData['s_beginTimeHour'];
		$beginTimeMin       = $formData['s_beginTimeMin'];
		$customPostType     = $formData['s_customPostType'];
		$postAuthor         = $formData['s_postAuthor'];
		$randomCSV          = $formData['r_randomizeCSV'];
		$wppgfiledelim      = $formData['fileDelim'];
		$postTags           = $formData['i_post_tags'];
		$postTagsTaxonomy   = $formData['s_customTagTaxonomy-'.$customPostType];
		$postRandNumCol     = $formData['i_post_randnum_col'];
		$postRandNumFloor   = $formData['i_post_randnum_floor'];
		$postRandNumCeiling = $formData['i_post_randnum_ceil'];
		$import_id          = intval($formData['importId']);
		$valueSQL           = "";
		$jsonVARS          = array();
		$mkTimeVar         = mktime(intval($beginTimeHour), intval($beginTimeMin), 0, intval(date("m",strtotime($beginDate))), intval(date("d",strtotime($beginDate))), intval(date("Y",strtotime($beginDate))));
		$mpnumperinterval  = 1;
		
		if ($postTagsTaxonomy == "" || $customPostType == "post" || $customPostType == "page") {
			$postTagsTaxonomy = "post_tag";
		}
		
		if ($formData['newBeginDate'] != "") {
			$date     = date('Y-m-d H:i:s', strtotime($formData['newBeginDate']));	
		}
		
		$jsonVARS['addedRows']   = 0;
		$jsonVARS['addedSQL']    = 0;
		$jsonVARS['skippedRows'] = 0;
		$jsonVARS['tagStuff']    = "";
		$jsonVARS['returnMsg']   = "";
		
		if ($import_id > 0) {
			$jsonVARS['importId'] = $import_id;
		}
		
		$wppgdefaultcols   = explode(",",$this->cfg->db_columns);
		$my_post           = array();
		$my_post_meta      = array();

		// WPPG Specific Data Inserts - Only on page 1 of import process
		if ($formData['currPage'] == "1") {
		
			$dateImported = date('c');
			$startDate    = date('c',$mkTimeVar);
			
			$wpdb->insert(
				$this->cfg->tbl_import,
				array( 'id'            => NULL,
				       'site_id'       => intval($this->cfg->site_id),
				       'blog_id'       => intval($this->cfg->blog_id),
				       'name'          => $importName,
				       'upload_id'     => $fileID,
				       'status'        => 'started',
				       'numposts'      => 0,
				       'date_imported' => $dateImported,
				       'file_delim'    => $wppgfiledelim,
				       'start_date'    => $startDate,
				       'per_interval'  => $postsPerInterval,
				       'post_interval' => $postInterval,
				       'interval_type' => $postInterval_type,
				       'randomize_csv' => $randomCSV
				     ),
				array( '%d', '%d', '%d','%s','%d','%s','%d','%s','%s','%s','%d','%d','%s','%d' ) );
			
			if ($wpdb->insert_id == false) { $jsonVARS['wppgError'] = "Failed creating import record in the database. Cannot continue."; return $jsonVARS; } else { $import_id = $wpdb->insert_id; $jsonVARS['importId'] = $import_id; }
		}

		$numInputCols = 0;
		
		foreach ($formData as $vField => $vValue) {
			if (strlen(strstr($vField,"i_col_to-")) > 0) {
			   $numInputCols++;
			}
			if (strlen(strstr($vField,"i_metaKeyInput")) > 0) {
				$customField[] = $vValue;
			}
			if (strlen(strstr($vField,"i_metaValueInput")) > 0) {
				$customValue[] = $vValue;
			}
		}

		if ($randomCSV == "1") {
			shuffle($importData);
		}

		$numRows = count($importData);

		// cycle rows
		for ($currRow=0; $currRow < $numRows; $currRow++) {
			
			$numCols = count($importData[$currRow]);
			
			// if a row doesn't have the expected number of columns then skip it
			if ($numInputCols != $numCols) {
				$jsonVARS['skippedRows']++;
				continue;
			}
			
			$my_post      = null;
			
			if ($postAuthor) {
				shuffle($postAuthor);
			}
			
			if (!isset($date)) {
				$date     = date('Y-m-d H:i:s', $mkTimeVar);
			}
			
			$my_post_status = "publish";
						
			$date_gmt = get_gmt_from_date($date);
			
			if (strtotime($date_gmt) > strtotime(date('Y-m-d H:i:s'))) {
				$my_post_status = "future";
			}
			
			$my_post['post_type']      = $customPostType;
			$my_post['post_status']    = $my_post_status;
			$my_post['post_author']    = $postAuthor[0];
			$my_post['comment_status'] = "open";
			$my_post['ping_status']    = "open";
			$my_post['to_ping']        = "";
			$my_post['post_password']  = "tmp" . $import_id . "-" . $formData['currPage'] . $currRow;
			$my_post['post_title']     = "You did not assign post_title during import";
			$my_post['post_content']   = "You did not assign post_content during import";			
			$my_post['post_date']     = $date;
			$my_post['post_date_gmt'] = $date_gmt;
			
			// cycle columns
			
			$mpwords[$my_post['post_password']] = "";
				 
			for ($currCol=0; $currCol < $numCols; $currCol++) {
				
				$colData      = $importData[$currRow][$currCol];
				$embedID      = 0;
				$colName      = $formData['i_col_to-' . $currCol];
				$colRequired  = $formData['s_requiredCol-' . $currCol];
				$colAppend    = $formData['i_col_append-' . $currCol];
				$colPrepend   = $formData['i_col_prepend-' . $currCol];
				$optHeight    = $formData['i_opt_height-' . $currCol];
				$optWidth     = $formData['i_opt_width-' . $currCol];
				$optClass     = $formData['i_opt_class-' . $currCol];
				$generateTags = $formData['s_genTags-' . $currCol];
				$widgetType   = $formData['s_widgetType-' . $currCol];
				$wordReplace  = $formData['s_wordReplace-' . $currCol];
				
				if ($wordReplace == "None") { $formatID = 0; } else { $formatID = $wordReplace; }

				if ($currRow == 0 && $formData['currPage'] == "1") {
					$wpdb->insert(
						$this->cfg->tbl_datamap,
						array( 'id'            => NULL,
						       'site_id'       => intval($this->cfg->site_id),
						       'blog_id'       => intval($this->cfg->blog_id),
						       'import_id'     => intval($import_id),
						       'format_id'     => intval($formatID),
						       'widget_type'   => $widgetType,
						       'col_type'      => 'standard',
						       'col_from'      => intval($currCol),
						       'col_to'        => $colName,
						       'opt_width'     => $optWidth,
						       'opt_height'    => $optHeight,
						       'opt_class'     => $optClass,
						       'col_required'  => intval($colRequired),
						       'generate_tags' => intval($generateTags),
						       'col_append'    => $colAppend,
						       'col_prepend'   => $colPrepend
						     ),
						array( '%d', '%d', '%d','%d','%d','%s','%s','%d','%s','%s','%s','%s','%d','%d','%s','%s' ) );
				}

				if ($colName == "") { continue; }
						
				if ($colRequired == "1" && $colData == "") { $jsonVARS['skippedRows']++; continue 2; }
				
				if ($wordReplace != "None") {
					$colData = $this->doReplaceText($wordReplace,$colData);
				}
				
				if ($generateTags == "1") {
					if ($mpwords[$my_post['post_password']] == "") {
						$mpwords[$my_post['post_password']] = $colData;
					} else {
						$mpwords[$my_post['post_password']] .= $colData;
					}
				}
				
				$embedID = $import_id . $formData['currPage'] . $currRow . $currCol;
				
				if ($widgetType == "genflv") { $colData = $this->embedMovie($embedID,$colData,$optWidth,$optHeight,$optClass); }
				if ($widgetType == "genimg") { $colData = $this->embedImage($colData,$optWidth,$optHeight,$optClass); }
				
				if ($colPrepend != "") { $colData = $colPrepend . " " . $colData; }				
				if ($colAppend  != "") { $colData = $colData . " " . $colAppend; }
				
				if (in_array(strtolower($colName), $wppgdefaultcols)) {
					// this is in list of standard WordPress columns
					$my_post[strtolower($colName)]  = $colData;				
				} else {
					// not a standard column so need to add to meta / custom fields
					if (taxonomy_exists($colName)) {
						$my_post_taxonomies[$my_post['post_password']][strtolower($colName)] = $colData;
					} else {
						$my_post_meta[$my_post['post_password']][strtolower($colName)] = $colData;
					}
				}
			}
			
			// build the custom field / value pairs
			$customfieldnum = 0;
			foreach ($customField as $metaKey => $metaValue) {
				
				if ($customValue[$customfieldnum] != "") {
					
					$my_post_meta[$my_post['post_password']][strtolower($metaValue)] = $customValue[$customfieldnum];
					
					if ($currRow == 0 && $formData['currPage'] == "1") {
						$wpdb->insert(
							$this->cfg->tbl_datamap,
							array( 'id'            => NULL,
							       'site_id'       => intval($this->cfg->site_id),
							       'blog_id'       => intval($this->cfg->blog_id),
							       'import_id'     => intval($import_id),
							       'format_id'     => 0,
							       'widget_type'   => '',
							       'col_type'      => 'custom',
							       'col_from'      => intval($customfieldnum),
							       'col_to'        => '',
							       'opt_width'     => '',
							       'opt_height'    => '',
							       'opt_class'     => '',
							       'col_required'  => 0,
							       'generate_tags' => 0,
							       'col_append'    => $customValue[$customfieldnum],
							       'col_prepend'   => strtolower($metaValue)
							     ),
							array( '%d', '%d', '%d','%d','%d','%s','%s','%d','%s','%s','%s','%s','%d','%d','%s','%s' ) );
					}
				}
				$customfieldnum++;
			}
			
			if ($import_id > 0) {
				
				$valueSQL = $this->wppg_insert_post( $my_post, $valueSQL );
				$jsonVARS['addedSQL']++;
				
			} else {
				$jsonVARS['wppgError'] = "Failed creating import record in the database. Cannot continue.";
				return $jsonVARS;
			}
			
			// only change date if number of posts per day has been reached
			if ($mpnumperinterval == $postsPerInterval) {
				$datestr = "+" . $postInterval . " " . $postInterval_type;
				$date = strtotime($date . " " . $datestr);
				$date = date('Y-m-d H:i:s', $date);				
				$mpnumperinterval = 1;	
			} else { $mpnumperinterval++; }
		}
		
		$addRecords = $wpdb->query(rtrim($valueSQL,", "));
		$pwKey      = "tmp" . $import_id;
		
		if ($addRecords !== FALSE) {
			
			$wppgSelectedPosts = $wpdb->get_results($wpdb->prepare("SELECT *
						 FROM " . $wpdb->posts . " 
						WHERE post_password LIKE %s",
						  "%" . $pwKey . "%"));
	
			if ($wppgSelectedPosts) :
						
				$runAfter = 0;
				$postRow  = 0;
				
				foreach ($wppgSelectedPosts as $wppgSelectedPost) {
					
					$wppgpost = get_post($wppgSelectedPost->ID);
					$now = gmdate('Y-m-d H:i:59');
					if ( mysql2date('U', $wppgpost->post_date_gmt, false) > mysql2date('U', $now, false) )
                        $wppg_post_status = 'future';
					if ( mysql2date('U', $wppgpost->post_date_gmt, false) <= mysql2date('U', $now, false) )
                        $wppg_post_status = 'publish';
					
					wp_transition_post_status($wppg_post_status, "new", $wppgpost);
					
					$wppg_post_name = "";
					$wppg_post_name = wp_unique_post_slug($wppgpost->post_name, $wppgpost->ID, $wppgpost->post_status, $wppgpost->post_type, $wppgpost->post_parent);
					
					$myupdatepost = Array();
					$myupdatepost['ID'] = $wppgpost->ID;
					$myupdatepost['post_name'] = $wppg_post_name;
				    wp_update_post( $myupdatepost );
				    
					add_post_meta($wppgSelectedPost->ID, "wppgimport", $import_id, true);
										
					if (isset($my_post_taxonomies[$wppgSelectedPost->post_password])) {
												
						foreach($my_post_taxonomies[$wppgSelectedPost->post_password] as $mpcolname => $mpcoldata)
						{
							$mpcolname  = rtrim($mpcolname);							
							$mptagdata  = $mpcoldata;
							
							$mptagdata = @explode(",", $mptagdata);
							reset($mptagdata);
							
							$mptagdata = array_unique($mptagdata);
							
							$termIds = null;
							
							foreach($mptagdata as $mptagline) {
								$term_id = term_exists( $mptagline, $mpcolname );
								
								if ($term_id == 0) {
									$term_id = wp_insert_term( $mptagline, $mpcolname );
									if (is_wp_error( $term_id )) {
										$error_string = $term_id->get_error_message();
										error_log("WPPGerror: " . $error_string, 0);
									} else {
										if (is_taxonomy_hierarchical( $mpcolname )) {
											$termIds[] = $term_id['term_id'];
										} else {
											$termIds[] = $postTag;
										}
									}
								} else {
									if (is_taxonomy_hierarchical( $mpcolname )) {
										$termIds[] = $term_id['term_id'];
									} else {
										$termIds[] = $postTag;
									}
								}
							}
							
							if (isset($termIds)) {
								wp_set_post_terms( $wppgSelectedPost->ID, $termIds, $mpcolname, true );
								$mptagdata = null;
								unset($mptagdata);
							}
						}
					}
					
					if (isset($my_post_meta[$wppgSelectedPost->post_password])) {

						foreach($my_post_meta[$wppgSelectedPost->post_password] as $mpcolname => $mpcoldata)
						{
							$mpcatlist    = array();
							$mpcolname    = rtrim($mpcolname);
							
							if ($mpcolname == "category") {
								
								$mpcats = $mpcoldata;
								
								$mpcats = @explode(",", $mpcats);
								reset($mpcats);
								
								foreach($mpcats as $mpcatline) { 
									$mpcatlist[] = $mpcatline;
								}
								
								if (isset($mpcatlist)) {
									wp_create_categories($mpcatlist,$wppgSelectedPost->ID);
									$mpcatlist = null; $mpcats = null;
									unset($mpcatlist);
									unset($mpcats);
								}
								
							} else {
								add_post_meta($wppgSelectedPost->ID, $mpcolname, $mpcoldata, true);
							}
						}
					}
					
					// do random num generator
					if ($postRandNumCol != "") {
						$randNum = rand(intval($postRandNumFloor),intval($postRandNumCeiling));
						if ($postRow == 0 && $formData['currPage'] == "1") {
							$wpdb->insert(
								$this->cfg->tbl_datamap,
								array( 'id'            => NULL,
								       'site_id'       => intval($this->cfg->site_id),
								       'blog_id'       => intval($this->cfg->blog_id),
								       'import_id'     => intval($import_id),
								       'format_id'     => 0,
								       'widget_type'   => '',
								       'col_type'      => 'postrandnum',
								       'col_from'      => 0,
								       'col_to'        => $postRandNumCol,
								       'opt_width'     => '',
								       'opt_height'    => '',
								       'opt_class'     => '',
								       'col_required'  => 0,
								       'generate_tags' => 0,
								       'col_append'    => $postRandNumCeiling,
								       'col_prepend'   => $postRandNumFloor
								     ),
								array( '%d', '%d', '%d','%d','%d','%s','%s','%d','%s','%s','%s','%s','%d','%d','%s' ) );
						}
						
						add_post_meta($wppgSelectedPost->ID, $postRandNumCol, $randNum, true);	
					}
		
					// do custom tags
					if ($postTags != "") {
						if ($postRow == 0 && $formData['currPage'] == "1") {
							$wpdb->insert(
								$this->cfg->tbl_datamap,
								array( 'id'            => NULL,
								       'site_id'       => intval($this->cfg->site_id),
								       'blog_id'       => intval($this->cfg->blog_id),
								       'import_id'     => intval($import_id),
								       'format_id'     => 0,
								       'widget_type'   => '',
								       'col_type'      => 'posttags',
								       'col_from'      => 0,
								       'col_to'        => '',
								       'opt_width'     => '',
								       'opt_height'    => '',
								       'opt_class'     => '',
								       'col_required'  => 0,
								       'generate_tags' => 0,
								       'col_append'    => $postTags,
								       'col_prepend'   => ''
								     ),
								array( '%d', '%d', '%d','%d','%d','%s','%s','%d','%s','%s','%s','%s','%d','%d','%s' ) );
						}
						
						$postTagsArray = @explode(",", $postTags);
						reset($postTagsArray);
						
						$postTagsArray = array_unique($postTagsArray);
						$termIds = null;	
						foreach($postTagsArray as $postTag) {
							$term_id = term_exists( $postTag, $postTagsTaxonomy );
							
							if ($term_id == 0) {
								$term_id = wp_insert_term( $postTag, $postTagsTaxonomy );
								if (is_wp_error( $term_id )) {
									$error_string = $term_id->get_error_message();
									error_log("WPPGerror: " . $error_string, 0);
								} else {
									if (is_taxonomy_hierarchical( $postTagsTaxonomy )) {
										$termIds[] = $term_id['term_id'];
									} else {
										$termIds[] = $postTag;
									}
								}
							} else {
								if (is_taxonomy_hierarchical( $postTagsTaxonomy )) {
									$termIds[] = $term_id['term_id'];
								} else {
									$termIds[] = $postTag;
								}
							}
						}
						if (isset($termIds)) {
							wp_set_post_terms( $wppgSelectedPost->ID, $termIds, $postTagsTaxonomy, true );
						}
					}
		
					// do tags from column relationships
					if (isset($mpwords[$wppgSelectedPost->post_password])) {
						
						if ($mpwords[$wppgSelectedPost->post_password] != "") {
						
							$mpwords[$wppgSelectedPost->post_password]   = strip_tags($mpwords[$wppgSelectedPost->post_password]);
							$mpspecial = array(',', ')', '(', "'", '"', '<', '>', '.', '!', '?', '/', '_', '[', ']', ':', '+', '=', '#', '$', '&quot;', '&quot;s', '&copy;', '&gt;', '&lt;', '&nbsp;', '&trade;', '&reg;', ';', chr(10), chr(13), chr(9));
							$mpwords[$wppgSelectedPost->post_password]   = str_replace($mpspecial,"", $mpwords[$wppgSelectedPost->post_password]);
							//$mpwords   = split(" ",$mpwords);
							//reset($mpwords);
							
							if ($runAfter == 0) {
								require_once($this->cfg->plugin_dir . "/inc/WPPG_PostTagger.class.php");
								$mptagger = new WPPG_PostTagger($this->cfg->plugin_dir . "/" . $this->cfg->lexicon_fname);
								$runAfter++;
							}
							
							$this->buildTags($wppgSelectedPost->ID,$mpwords[$wppgSelectedPost->post_password],$mptagger,$postTagsTaxonomy);
						}
					}
					// end tags work //
				}
				
				$wpdb->query($wpdb->prepare("UPDATE " . $wpdb->posts . " SET post_password = %s WHERE post_password LIKE %s", "", "%" . $pwKey . "%"));
				
			endif;
		}
		
		if ($addRecords > 0) {
			$jsonVARS['skippedRows'] = $jsonVARS['addedSQL'] - $addRecords;
			$jsonVARS['addedRows']   = $addRecords;
		}
		
		$jsonVARS['newBeginDate'] = $date;
		$jsonVARS['addRecords']   = $addRecords;

		return $jsonVARS;
	}
	
	function buildTags($post_id = 0, $mpwords = "", $mptagger, $postTagsTaxonomy) {
		
		$prevTag    = "";
                $mpstrfull  = "";
		$mpaddtags  = array();
		$mptags     = array();
		$mpchartags = "";
		$returnMsg  = "";
				
		$mptags = $mptagger->tag($mpwords);
		
	        foreach($mptags as $mptag) {
			
			if (strlen(trim($mptag['token'])) <= 2 ||
			    in_array(trim(strtolower($mptag['token'])), $this->cfg->stopwords) ||
			    strpos($mptag['token'],".") !== false ||
			    (strpos($mptag['tag'],"JJ") === false &&
			     strpos($mptag['tag'],"NN") === false)) {
				$mpstrfull = "";
				continue;
			} else {
				if ($mpstrfull == "") {
					$mpstrfull = trim($mptag['token']);
				} else {
					$mpstrfull   = $mpstrfull . " " . trim($mptag['token']);
					$mpaddtags[] = str_replace(".","",trim($mpstrfull));
					$mpaddtags[] = str_replace(".","",trim($mptag['token']));
					$mpstrfull   = "";
				}
			}			
		}
		
		if ($mpstrfull != "") { $mpaddtags[] = str_replace(".","",trim($mpstrfull)); }
		
		$mpaddtags  = array_unique($mpaddtags);
		$termIds = null;					
		foreach($mpaddtags as $postTag) {
			$numtag++;
			
			$term_id = term_exists( $postTag, $postTagsTaxonomy );
			
			if ($term_id == 0) {
				$term_id = wp_insert_term( $postTag, $postTagsTaxonomy );
				if (is_wp_error( $term_id )) {
					$error_string = $term_id->get_error_message();
					error_log("WPPGerror: " . $error_string, 0);
				} else {
					if (is_taxonomy_hierarchical( $postTagsTaxonomy )) {
						$termIds[] = $term_id['term_id'];
					} else {
						$termIds[] = $postTag;
					}					
				}
			} else {
				if (is_taxonomy_hierarchical( $postTagsTaxonomy )) {
					$termIds[] = $term_id['term_id'];
				} else {
					$termIds[] = $postTag;
				}
			}
		}

		if (isset($termIds)) {
			wp_set_post_terms( $post_id, $termIds, $postTagsTaxonomy, true );
		}
		
		/*			
		$mpchartags = implode(",", $mpaddtags);
		
		if ($mpchartags != "") {
			wp_set_post_terms( $post_id, $mpchartags, &$postTagsTaxonomy, true );
		}
		*/	
		return $returnMsg;
		
	}
	
	function embedMovie($movieID = 0,$movieURL = "",$movieWidth = "500",$movieHeight = "350",$movieClass = "flashvideo") {
		
		$movieWidth  = ($movieWidth != "")  ? $movieWidth : "500";
		$movieHeight = ($movieHeight != "") ? $movieHeight : "350";
		$movieClass  = ($movieClass != "")  ? $movieClass : "flashvideo";
		/*
		$embedcode  = '<span id="video' . $movieID . '" class="' . $movieClass . '"><a href="http://get.adobe.com/flashplayer/">Get the Flash Player</a> to see this player.</span>';
		$embedcode .= '<script type="text/javascript">
				swfobject.embedSWF("' . $this->cfg->plugin_url . '/swf/flowplayer.swf", "video' . $movieID . '", "' . $movieWidth . '", "' . $movieHeight . '", "9.0.0", null, { 
					config: "{ clip: { url: \'' . $movieURL . '\', autoPlay: false }}"
				});
			       </script>';
		*/
		$embedcode = '<a href="' . $movieURL . '" style="display:block;width:' . $movieWidth . 'px;height:' . $movieHeight . 'px;" id="video' . $movieID . '"></a>';
		$embedcode .= '<script type="text/javascript">
				flowplayer("video' . $movieID . '", "' . $this->cfg->plugin_url . '/swf/flowplayer.swf", {
					clip:  {
					    url: "' . $movieURL . '",
					    autoPlay: false,
					    autoBuffering: true
					}
				});
				</script>';
		return $embedcode;
	}
	
	function embedImage($imageURL = "",$imageWidth = "",$imageHeight = "",$imageClass = "mtcsvthumb") {
		$imgWidthHTML  = "";
		$imgHeightHTML = "";
		
		if ($imageWidth  != "") { $imgWidthHTML  = 'width="' . $imageWidth . '"'; };
		if ($imageHeight != "") { $imgHeightHTML = 'height="' . $imageHeight . '"'; };
		
		$imgcode = '<img src="' . $imageURL . '" class="' . $imageClass .'" ' . $imgWidthHTML . ' ' . $imgHeightHTML . '>';
		return $imgcode;
	}
	
	function doReplaceText($mpformatid = 0,$mpval = "") {
		global $wpdb;
		
		if ($mpformatid == 0) { return $mpval; }

		$mpresult = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $this->cfg->tbl_format . "
							    WHERE id      = %d
							      AND site_id = %d
							      AND blog_id = %d LIMIT 1",
							      intval($mpformatid), intval($this->cfg->site_id), intval($this->cfg->blog_id)));	

		$mp_formats = $wpdb->get_results($wpdb->prepare("SELECT *
								FROM " . $this->cfg->tbl_replacetxt . " 
								WHERE format_id = %d
								  AND site_id   = %d
								  AND blog_id   = %d",
								  intval($mpresult->id), intval($this->cfg->site_id), intval($this->cfg->blog_id)));
		
		if ($mp_formats) {
			foreach ($mp_formats as $mp_format) {	
				
				$mprcount = 0;
				$i        = 0;
				
				$mprtext = explode(",", $mp_format->rtext);
				$mprwith = explode(",", $mp_format->rwith);
				
				foreach ($mprtext as $mpsingletext) {
					$mprcount = substr_count(strtoupper($mpval),strtoupper($mpsingletext));
	
					if ($mprcount == 0) { continue; }
					
					for ($i = 1; $i <= $mprcount; $i++) {
						
						$mpfind = "/" . $mpsingletext . "/i";
	
						shuffle($mprwith);
	
						$mpval = preg_replace($mpfind, $mprwith[0], $mpval, 1);
	
					}
				}
			}
		}
		
		return $mpval;
		// end replace text stuff
	}
	
	/********* MIMICK WP FUNCTIONS TO BUILD SQL INSTEAD OF INSERTING SINGLE POST **********/
	function wppg_insert_post($postarr, $inputSQL = "") {
		  global $wpdb, $wp_rewrite, $user_ID;
	  
		  $defaults = array('post_status' => 'draft', 'post_type' => 'post', 'post_author' => $user_ID,
		  'ping_status' => get_option('default_ping_status'), 'post_parent' => 0,
		  'menu_order' => 0, 'to_ping' =>  '', 'pinged' => '', 'post_password' => '',
		  'guid' => '', 'post_content_filtered' => '', 'post_excerpt' => '', 'import_id' => 0,
		  'post_content' => '', 'post_title' => '');
	  
		  $postarr = wp_parse_args($postarr, $defaults);
	  
		  unset( $postarr[ 'filter' ] );
	  
		  //$postarr = sanitize_post($postarr, 'db');
	  
		  // export array as variables
		  extract($postarr, EXTR_SKIP);
	  
		  // Are we updating or creating?
	  	  $previous_status = 'new';
	  
		  if ( !empty($post_category) )
			  $post_category = array_filter($post_category); // Filter out empty terms
	  
		  // Make sure we set a valid category.
	 	  if ( empty($post_category) || 0 == count($post_category) || !is_array($post_category) ) {
		  // 'post' requires at least one category.
		  if ( 'post' == $post_type && 'auto-draft' != $post_status )
			  $post_category = array( get_option('default_category') );
			  else
				  $post_category = array();
		  }
		  
		  if ( empty($post_author) )
			  $post_author = $user_ID;
	  
		  $post_ID = 0;
	  
		  // Create a valid post name.  Drafts and pending posts are allowed to have an empty
	  // post name.
	  if ( empty($post_name) ) {
		  if ( !in_array( $post_status, array( 'draft', 'pending', 'auto-draft' ) ) )
			  $post_name = sanitize_title($post_title);
		  else
			  $post_name = '';
	  } else {
			  $post_name = sanitize_title($post_name);
	  }
	  
		  $post_modified     = $post_date;
		  $post_modified_gmt = $post_date_gmt;
	  
	  if ( 'publish' == $post_status ) {
		  $now = gmdate('Y-m-d H:i:59');
		  if ( mysql2date('U', $post_date_gmt, false) > mysql2date('U', $now, false) )
			  $post_status = 'future';
	  } elseif( 'future' == $post_status ) {
		  $now = gmdate('Y-m-d H:i:59');
		  if ( mysql2date('U', $post_date_gmt, false) <= mysql2date('U', $now, false) )
			  $post_status = 'publish';
	  }
	  
	  if ( isset($to_ping) )
		  $to_ping = preg_replace('|\s+|', "\n", $to_ping);
	  else
		  $to_ping = '';
	  
		  if ( ! isset($pinged) )
			  $pinged = '';
	  
		  if ( isset($menu_order) )
			  $menu_order = (int) $menu_order;
		  else
			  $menu_order = 0;
	  
		  if ( !isset($post_password) || 'private' == $post_status )
		  $post_password = '';
	  
		  // expected_slashed (everything!)
	  $data = compact( array( 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_content_filtered', 'post_title', 'post_excerpt', 'post_status', 'post_type', 'comment_status', 'ping_status', 'post_password', 'post_name', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_parent', 'menu_order', 'guid' ) );
	  $data = apply_filters('wp_insert_post_data', $data, $postarr);
	  $data = stripslashes_deep( $data );
	  
	  if ( isset($post_mime_type) )
		  $data['post_mime_type'] = stripslashes( $post_mime_type ); // This isn't in the update
	  
	  if ($inputSQL == "") {
		  $postFields = array_keys( $data );
	  
		  $inputSQL = "INSERT INTO " . $wpdb->posts . " (`" . implode( '`,`', $postFields ) . "`) VALUES " . $wpdb->prepare($this->wppg_insert_replace_helper($data,$wpdb->field_types),$data);
		  } else {
			  $inputSQL = $inputSQL . $wpdb->prepare($this->wppg_insert_replace_helper($data,$wpdb->field_types),$data);
		  }
		  
		  return $inputSQL;
	}
	
	function wppg_insert_replace_helper( $data, $field_types ) {
	  
		 $fields = array_keys( $data );
		 $formatted_fields = array();
		 foreach ( $fields as $field ) {
					if ( isset( $this->field_types[$field] ) )
							  $form = $this->field_types[$field];
					else
							  $form = '%s';
		
						$formatted_fields[] = $form;
			 }
  
			 $sql = "('" . implode( "','", $formatted_fields ) . "'), ";
		 return $sql;
	}
}
?>