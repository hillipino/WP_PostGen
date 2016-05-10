jQuery(document).ready(function($) {

    $("#displayImport-").show().siblings().hide();
    
	var prevImportFile    = "";
	var datamapTabVisited = "";
	var prevImportDelim   = "";
	
	$.widget( "ui.combobox", {
		_create: function() {
			var self = this,
				select = this.element.hide(),
				selected = select.children( ":selected" ),
				value = selected.val() ? selected.text() : "";
			var input = this.input = $( '<input id="i_importFile" name="i_importFile">' )
				.insertAfter( select )
				.val( value )
				.autocomplete({
					delay: 0,
					minLength: 0,
					source: function( request, response ) {
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
						response( select.children( "option" ).map(function() {
							var text = $( this ).text();
							if ( this.value && ( !request.term || matcher.test(text) ) )
								return {
									label: text.replace(
										new RegExp(
											"(?![^&;]+;)(?!<[^<>]*)(" +
											$.ui.autocomplete.escapeRegex(request.term) +
											")(?![^<>]*>)(?![^&;]+;)", "gi"
										), "<strong>$1</strong>" ),
									value: text,
									realval: this.value,
									option: this
								};
						}) );
					},
					select: function( event, ui ) {
						
						if (prevImportFile != "" && prevImportFile != ui.item.realval && datamapTabVisited == "yes") {
							if (confirm('Changing the Import File will cause you to lose any changes made on the Data Mapping tab. Do you wish to continue?')) {
								datamapTabVisited = "";
							} else {
								return false;	
							}
						}
						
						if (!checkFileDelim()) {
							return false;
						}
						
						$("#displayImport-" + ui.item.realval).show().siblings().hide();
						$("#s_importFile").removeClass("ui-state-highlight");
						ui.item.option.selected = true;
						self._trigger( "selected", event, {
							item: ui.item.option
						});
						preloadImportFile(ui.item.realval);
						
					},
					change: function( event, ui ) {
						
						if ( !ui.item ) {
							var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
								valid = false;
							select.children( "option" ).each(function() {
								if ( $( this ).text().match( matcher ) ) {
									if (prevImportFile != "" && prevImportFile != ui.item.realval && datamapTabVisited == "yes") {
										if (confirm('Changing the Import File will cause you to lose any changes made on the Data Mapping tab. Do you wish to continue?')) {
											datamapTabVisited = "";
										} else {
											return false;	
										}
									}
						
									if (!checkFileDelim()) {
										return false;
									}
									$("#s_importFile").removeClass("ui-state-highlight");
									this.selected = valid = true;
									$("#displayImport-" + $(this).val()).show().siblings().hide();
									preloadImportFile($(this).val());
									
									return false;
								}
							});
							if ( !valid ) {
								// remove invalid value, as it didn't match anything
								$("#displayImport-").show().siblings().hide();
								$( this ).val( "" );
								select.val( "" );
								input.data( "autocomplete" ).term = "";
								return false;
							}
						}
					}
				})
				.blur(function() {
					if ($("#i_importFile").val() == "") {
  						$("#displayImport-").show().siblings().hide();
  					}
				})
				.addClass( "ui-widget ui-widget-content ui-corner-left" );
			
			input.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.label + "</a>" )
					.appendTo( ul );
			};

			this.button = $( "<button name='btnImportFile' id='btnImportFile' type='button'>&nbsp;</button>" )
				.attr( "tabIndex", -1 )
				.attr( "title", "Show All Items" )
				.insertAfter( input )
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				})
				.removeClass( "ui-corner-all" )
				.addClass( "ui-corner-right ui-button-icon" )
				.click(function() {
					// close if already visible
					if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
						input.autocomplete( "close" );
						return;
					}

					// pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
					input.focus();
				});
		},

		destroy: function() {
			this.input.remove();
			this.button.remove();
			this.element.show();
			$.Widget.prototype.destroy.call( this );
		}
	});
	
	// Tab Processing
	var fnValid = true;
		
	$( "#tabs" ).tabs({
		cache: false,
		show: function(event, ui) {
			if (ui.index == 1) {
				callDataMapTab();
			}
		},
		select: function(event, ui) {
			fnValid = checkFileDelim();
            
			if (fnValid) {
				fnValid = checkTabValid(ui);
			}
			
			if (fnValid) {
				$("#importjobaddform").submit();
			}
			return fnValid;
			
		},
		ajaxOptions: {
			cache: false,
			async: false
		}
	});
    
	wppgInit();
			
	function wppgInit() {
		$( "#s_importFile" ).combobox();
		$( "#tabs" ).tabs( "option", "disabled", [1,2] );
		$( "#s_fileDelim" ).focus();
		$( "#wppgDatePicker" ).datepicker({
			defaultDate: $("#d_beginDate").val(),
			altField: '#d_beginDate'
		});
					
		$("#importjobaddform").validate({
			debug: false,
			onkeyup: false,
			onfocusout: false,
			submitHandler: function(form) {
   				// do other stuff for a valid form
				//form.submit();
				fnValid = true;
			},
			invalidHandler: function(form, validator) {
				fnValid = false;
			},
			highlight: function(input) {
				$(input).addClass("ui-state-highlight");
			},
			unhighlight: function(input) {
				$(input).removeClass("ui-state-highlight");
			},
			showErrors: function(errorMap, errorList) {
				var errors = this.numberOfInvalids();
			    if (errors) {
			        alert(this.errorList[0].message);
			        this.errorList[0].element.focus(); //Set Focus
			        if (this.settings.highlight) {
			        	this.settings.highlight.call(this, this.errorList[0].element, this.settings.errorClass, this.settings.validClass);
			        }
				    
			    }
		        if (this.settings.unhighlight) {
		            for (var i = 0, elements = this.validElements(); elements[i]; ++i) {
		                this.settings.unhighlight.call(this, elements[i],
		                    this.settings.errorClass, this.settings.validClass);
		            }
		        }
			},
			rules: {
				s_fileDelim: "required",
				i_importName: "required",
				s_postAuthor: "required",
				d_beginDate: {required: true, date: true},
				i_postInterval: {required: true, number: true},
				i_postsPerInterval: {required: true, number: true}
			},
			messages: {
				s_fileDelim: "You must select a File Delimiter before choosing an Import File. Please try again.",
				i_importName: "You must enter an Import File label. Please try again.",
				s_postAuthor: "You must select a Post Author. Please try again.",
				d_beginDate: {required: "You must enter a Beginning Post Date. Please try again.", date: "You must enter a valid date for Beginning Post Date. Please try again."},
				i_postInterval: {required: "You must enter a Post Interval. Please try again.", number: "You must enter a number for Post Interval. Please try again."},
				i_postsPerInterval: {required: "You must enter a Posts Per Interval. Please try again.", number: "You must enter a number for Posts Per Interval. Please try again."}
			}			
		});
		
	        // tab 1 buttons
		$("#tabs-1 #wizard-nav #btnNext").live('click', function() {
			
			$("#tabs").tabs('enable', 1);
			$("#tabs").tabs('select', 1);
	    	
			if ($("#tabs").tabs('option', 'selected') == 0) {
				$( "#tabs" ).tabs( "option", "disabled", [1,2] );
			} 
		});
		
		// tab 2 buttons
		$("#tabs-2 #wizard-nav #btnNext").live('click', function() {
			$("#tabs").tabs('enable', 2);
			$("#tabs").tabs('select', 2);
		});
		
		$("#tabs-2 #wizard-nav #btnPrev").live('click', function() {
			$("#tabs").tabs('select', 0); 
		});
		
		// tab 3 buttons
		$("#tabs-3 #wizard-nav #btnPrev").live('click', function() {
			$("#tabs").tabs('select', 1); 
		});
		
		$("#tabs-3 #wizard-nav #btnSubmit").live('click', function() {
			$( "#tabs" ).tabs( "option", "disabled", [0,1,2] );
			$("#tabs-3 #wizard-nav #btnPrev").hide();
			$("#tabs-3 #wizard-nav #btnSubmit").hide();
			finishImport();
		});
		
		$('#s_fileDelim').change(function() {
			    if (prevImportDelim != "" && prevImportDelim != $('#s_fileDelim').val() && datamapTabVisited == "yes") {
				    if (confirm('Changing the Import File Delimiter will reset the Import File and cause you to lose any changes made on the Data Mapping tab. Do you wish to continue?')) {
					    datamapTabVisited = "";
					    $("#i_importFile").val('');
					    $("#displayImport-").show().siblings().hide();
				    } else {
					    $('#s_fileDelim').val(prevImportDelim);
					    return false;	
				    }
			    } else {
				    $("#i_importFile").val('');
				    $("#displayImport-").show().siblings().hide();
			    }
		});
		
		$("#message").hide();
		
		$(".delete-importjob").live('click', function() {
			
			$("#wppgmessage").empty().hide();
			$("#message").empty().hide();
			
			var selectId      = this.id;
			var selectIdArray = selectId.split('-');
			var inputId       = selectIdArray[1];
			var removeError   = false;
			var importRemoved = false;
			var dataRequest = ({action: "deleteImportJob",
					    n_importAction: $("#n_importAction").val(),
					    importId: inputId,
					    seed: Math.random()});
			
			$.ajaxSetup ({  
				cache: false,
				async: false,
				error:function(x,e){
					if(x.status==0){
						alert('You are offline!!\n Please Check Your Network.');
					}else if(x.status==404){
						alert('Requested URL not found.');
					}else if(x.status==500){
						alert('Internel Server Error.');
					}else if(e=='parsererror'){
						alert('Error.\nParsing JSON Request failed.');
					}else if(e=='timeout'){
						alert('Request Time out.');
					}else {
						alert('Unknow Error.\n'+x.responseText);
					}
				}
			});
				
                        var answer = confirm( 'You are about to delete an Import Job and all related posts.\n\n  \'Cancel\' to stop, \'OK\' to delete.' );
			
			if (answer) {
				
				while(!importRemoved && !removeError){
  
					$("#loading-"+inputId).empty().append('<img src="' + wppgJsVars.pluginUrl + '/images/loader.gif">');
				
					$.get(ajaxurl, dataRequest, function(data){
						
						if (data.wppgError != "success") {
							removeError = true;
							divContent = '<p class="error">' + data.wppgError + '</p>';
							$("#rowactions-"+inputId).empty().append( divContent );
						} else {
							importRemoved = (data.wppgReturn == "done") ? true : false;
							
							if (importRemoved) {
								$("#message").show().append('<p>The import job and all posts associated with it have been removed successfully!</p>');
								$('#importjob-'+inputId).remove();
							}
						}
						
					}, "json");
				}
				
				return true;
			
			} else { return false; }
		});
		
		$(".delete-row").live('click', function() {
			
			$("#wppgmessage").empty().hide();
			$("#message").empty().hide();
			
			var selectId      = this.id;
			var selectIdArray = selectId.split('-');
			var inputId       = selectIdArray[1];
				
			var dataRequest = ({action: "deleteReplaceList",
					    n_nonceAction: $("#n_nonceAction").val(),
					    rowId: inputId,
					    seed: Math.random()});
			
			$.ajaxSetup ({  
				cache: false,
				async: false,
				error:function(x,e){
					if(x.status==0){
						alert('You are offline!!\n Please Check Your Network.');
					}else if(x.status==404){
						alert('Requested URL not found.');
					}else if(x.status==500){
						alert('Internel Server Error.');
					}else if(e=='parsererror'){
						alert('Error.\nParsing JSON Request failed.');
					}else if(e=='timeout'){
						alert('Request Time out.');
					}else {
						alert('Unknow Error.\n'+x.responseText);
					}
				}
			});
				
                        var answer = confirm( 'You are about to delete a Word Replace List and all related word replace entries.\n\n  \'Cancel\' to stop, \'OK\' to delete.' );
			
			if (answer) {
				
				$("#loading-"+inputId).empty().append('<img src="' + wppgJsVars.pluginUrl + '/images/loader.gif">');
			
				$.get(ajaxurl, dataRequest, function(data){
					
					if (data.wppgError != "success") {
						divContent = '<p class="error">' + data.wppgError + '</p>';
						$("#rowactions-"+inputId).empty().append( divContent );
					} else {
						$("#message").show().append('<p>The word replace list and all entries associated with it have been removed successfully!</p>');
						$('#dbrow-'+inputId).remove();
					}
					
				}, "json");
				
				return true;
			
			} else { return false; }
		});
		
		$(".delete-entry").live('click', function() {
			
			$("#wppgmessage").empty().hide();
			$("#message").empty().hide();
			
			var selectId      = this.id;
			var selectIdArray = selectId.split('-');
			var inputId       = selectIdArray[1];
			var formatId      = selectIdArray[2];
				
			var dataRequest = ({action: "deleteWordReplace",
					    n_nonceAction: $("#n_nonceAction").val(),
					    rowId: inputId,
					    wppgformatid: formatId,
					    seed: Math.random()});
			
			$.ajaxSetup ({  
				cache: false,
				async: false,
				error:function(x,e){
					if(x.status==0){
						alert('You are offline!!\n Please Check Your Network.');
					}else if(x.status==404){
						alert('Requested URL not found.');
					}else if(x.status==500){
						alert('Internel Server Error.');
					}else if(e=='parsererror'){
						alert('Error.\nParsing JSON Request failed.');
					}else if(e=='timeout'){
						alert('Request Time out.');
					}else {
						alert('Unknow Error.\n'+x.responseText);
					}
				}
			});
				
                        var answer = confirm( 'You are about to delete a Word Replace Entry.\n\n  \'Cancel\' to stop, \'OK\' to delete.' );
			
			if (answer) {
				
				$("#loading-"+inputId).empty().append('<img src="' + wppgJsVars.pluginUrl + '/images/loader.gif">');
			
				$.get(ajaxurl, dataRequest, function(data){
					
					if (data.wppgError != "success") {
						divContent = '<p class="error">' + data.wppgError + '</p>';
						$("#rowactions-"+inputId).empty().append( divContent );
					} else {
						$("#message").show().append('<p>The word replace entry has been removed successfully!</p>');
						$('#dbrow-'+inputId).remove();
					}
					
				}, "json");
				
				return true;
			
			} else { return false; }
		});		
	}
	
	function preloadImportFile(fileId) {
		var fileDelim   = $("#s_fileDelim").val();
		var dataRequest = ({action: "preloadImportFile",
				    fileId: fileId,
				    fileDelim: fileDelim,
				    n_importAction: $("#n_importAction").val(),
				    seed: Math.random()});
		var divContent  = "";
		
		$.ajaxSetup ({  
			cache: false,
			async: false
		});
		
		$("#h_importRows").val("");
		$("#preloadImportFile-" + fileId).html('<p><img src="' + wppgJsVars.pluginUrl + '/images/loader.gif"></p>');
		
		$.get(ajaxurl, dataRequest, function(data){
		
			if (data.wppgError != "success") {
				divContent = '<p class="error">' + data.wppgError + '</p>';
				$("#preloadImportFile-" + fileId).empty().append( divContent );
			} else {
				$("#h_importRows").val(data.h_importRows);
				divContent  = '<p><strong>Rows Detected</strong> : ' + data.h_importRows + '</p>';
	        	        divContent += '<p><strong>Columns Detected</strong> : ' + data.h_importCols + '</p>';
				$("#preloadImportFile-" + fileId).empty().append( divContent );
			}
			
		}, "json");
		
		return true;
	}
	
	function checkFileDelim() {
		var fileDelim = $("#s_fileDelim").val();
									
		if (fileDelim == "") {
			$("#displayImport-").show().siblings().hide();
			//$("#i_importFile").val() = "";
			$("#s_fileDelim").addClass("ui-state-highlight");
			$("#s_fileDelim").focus();
			alert('You must select a File Delimiter before choosing an Import File. Please try again.');
			return false;
		} else {
			$("#s_fileDelim").removeClass("ui-state-highlight");
		}
		return true;
	}
	
	function checkTabValid(ui) {
		
		var foundImportSel = false;
		var errorMsg       = "";

		switch ($("#tabs").tabs('option', 'selected')) {
			case 0:
				if ($("#i_importFile").val() == "") {
					$("#i_importFile").addClass("ui-state-highlight");
					$("#i_importFile").focus();
					errorMsg = "You must type or select an existing Import File. Please try again.";	
				} else {
					$("#i_importFile").removeClass("ui-state-highlight");
					$("#s_importFile").children( "option" ).each(function() {
						if ( $( this ).text() == $("#i_importFile").val()) {
							if ($("#preloadImportFile-" + $(this).val()).html().indexOf("ERROR LOADING FILE :: PLEASE TRY AGAIN",0) >= 0) {
								$("#s_importFile").addClass("ui-state-highlight");
								$("#s_importFile").focus();
								errorMsg = "The Import File you chose was not properly loaded. Please choose another or try again.";
								foundImportSel = false;
							} else {
								$("#s_importFile").removeClass("ui-state-highlight");
								foundImportSel = true;
							}							
						}
					});
					if (!foundImportSel) {
						
						if (errorMsg == "") {
							$("#s_importFile").addClass("ui-state-highlight");
							$("#s_importFile").focus();
							errorMsg = "You must type or select an existing Import File. Please try again.";
						}
						
					} else {
						$("#s_importFile").removeClass("ui-state-highlight");
						return true;
					}
				}
				break;
			case 1:
				
				$('input[id^="i_col_to"]').each(function() {
					var selectId      = this.id;
					var selectIdArray = selectId.split('-');
					var inputId       = selectIdArray[1];
					
					if ($("#s_requiredCol-" + inputId).val() == "1" && this.value == "") {
						errorMsg = "Column is marked required. Cannot be left blank... please try again.";
						$("#" + selectId).focus();
					}
				});
				break;
		}
		if (errorMsg) {
			alert(errorMsg);
			return false;
		}
		return true;
	}	

	function callDataMapTab() {
		
		var fileDelim = $("#s_fileDelim").val();
		var fileId    = $("#s_importFile").val();
		
		var dataRequest = ({action: "callDataMapTab", 
				    fileId: fileId,
				    fileDelim: fileDelim,
				    n_importAction: $("#n_importAction").val(),
				    seed: Math.random()});

		if (prevImportFile != fileId || prevImportDelim != fileDelim) {
			
			prevImportFile  = fileId;
			prevImportDelim = fileDelim;
        	
			$.ajaxSetup ({  
				cache: false,
				async: false
			});  
			
			$("#tabs-2").html('<p><img src="' + wppgJsVars.pluginUrl + '/images/loader.gif"></p>');
			
			$("#tabs-2").load(ajaxurl + " #datamap-response", dataRequest, function(response, status, xhr) {
		    	if (status == "error") {
					var msg = "Sorry but there was an error: ";
					$("#tabs-2").html(msg + xhr.status + " " + xhr.statusText);
				} else {
					initDataMapTab();
				}
			});
			
			datamapTabVisited = "yes";
		}
		
		return true;
	}
	
	function initDataMapTab() {
		
		var numKey = 0;
		var numVal = 0;
		
		/* column relationship select events */
		$('select[id^="s_col_type"]').live('change', function() {

			var selectId      = this.id.replace("s_col_type","i_col_to");
			var selectIdArray = selectId.split('-');
			var inputId       = selectIdArray[0] + "-" + selectIdArray[1];
			var selVal        = $(this).val();
			
			$("#"+inputId).val(selVal);
			$('select[id^="s_genTags"]').change();
		});
		
		$("#s_customPostType").live('change', function() {
			
			$('div[id^="customTagTaxonomyDiv"]').hide();
			$("#customTagTaxonomyDiv-"+$(this).val()).show();
			
			$("#r_mapType1").attr("checked", "checked");
			$("#r_mapType1").click();
		});
		/*
		$('select[id^="s_genTags"]').live('change', function() {
			
			var selectId      = $(this).attr('id');
			var selectIdArray = selectId.split('-');
			var inputId       = selectIdArray[1];
			var postType      = $("#s_customPostType").val();
			
			if (postType != "post" && postType != "page" && $(this).attr('value') == "1") {
				$('div[id^="tagTaxonomyDiv-'+inputId+'"]').hide();
				$("#tagTaxonomyDiv-"+inputId+"-"+postType).show().siblings().hide();
			} else {
				$('div[id^="tagTaxonomyDiv-'+inputId+'"]').hide();
			}
			
		});
		*/
		/* widget type select events */
		$('select[id^="s_widgetType"]').live('change', function() {
			var selectId      = this.id;
			var selectIdArray = selectId.split('-');
			var inputId       = selectIdArray[1];
			
			if ($(this).val() == "genflv" || $(this).val() == "genimg") {
				$("#widgetTypeOptions-"+inputId).show();
			} else {
				$("#widgetTypeOptions-"+inputId).hide();
			}
		});

		/* add new custom field */
		$("#btnAddCustom").live('click', function() {
			$("#list-table").show();
						
			$("#the-list").append('<tr><td id="newmetaleft" class="left"><input type="text" id="i_metaKeyInput-'+numKey+'" name="i_metaKeyInput-'+numKey+'" value="'+$("#i_metaKeyInput").val()+'" /></td><td><textarea id="i_metaValueInput-'+numVal+'" name="i_metaValueInput-'+numVal+'" rows="2" cols="25">'+$("#i_metaValueInput").val()+'</textarea></td></tr>');
			$("#i_metaKeyInput").val('');
			$("#i_metaValueInput").val('');
			numKey = numKey + 1;
			numVal = numVal + 1;
		});
		
		$("#btnClearCustom").live('click', function() {
			$("#the-list").empty();
			$("#list-table").hide();
		});
				
		/* theme mapping event for wp tube */
		
		$('input[id^="r_themeMap"]').live('click', function() {
			
			var mapVal = this.value;
			/* hide non-selected column rel boxes */
			$('select[id^="s_col_type"]').each(function() {
				
				var selectId      = this.id;
				var selectIdArray = selectId.split('-');
				var inputId       = selectIdArray[1];
									
				if (this.id.indexOf('-' + mapVal) >= 0) {
					$(this).show();
			        
					/* pre-fill i_col_to entries for this theme */
					var selectNum = 0;
					$("#" + selectId + " option").each(function() {
						if (this.value != "") {
							if (selectNum == inputId) {
								$("#i_col_to-" + inputId).val(this.value);
							}
							
							selectNum = selectNum + 1;
						}
					});
				} else {
					$(this).hide();
				}
			});
			$("#dataMapDisplay").empty().append( '<span class="error">Successfully loaded import dataMap</span>');
		});
		
		/* theme mapping for previous imports */
		$('select[id^="s_otherImport"]').live('change', function() {
			var dispMsg    = "";
			var divContent = "";
			
			$.get(ajaxurl, { action: "prevImportSelect",
					 importID: $("#s_otherImport").val(),
					 n_importAction: $("#n_importAction").val(),
				         seed: Math.random() },
			function(data){
				
				if (data.wppgError == "success") {
					
					$("#the-list").empty();
					$("#list-table").hide();
					
					$.each(data.dataMap, function(i,dataMap){
						
						$("#i_col_to-" + i).val(dataMap.col_to.replace(/\\/gi, ""));
						$("#i_col_prepend-" + i).val(dataMap.col_prepend.replace(/\\/gi, ""));
						$("#i_col_append-" + i).val(dataMap.col_append.replace(/\\/gi, ""));
						$("#s_widgetType-" + i).val(dataMap.widget_type);
						$("#s_widgetType-" + i).change();
						$("#i_opt_width-" + i).val(dataMap.opt_width);
						$("#i_opt_height-" + i).val(dataMap.opt_height);
						$("#i_opt_class-" + i).val(dataMap.opt_class);
						$("#s_wordReplace-" + i).val(dataMap.format_id);
						$("#s_requiredCol-" + i).val(dataMap.col_required);
						$("#s_genTags-" + i).val(dataMap.generate_tags);
						
					});
					$.each(data.customDataMap, function(x,customDataMap){
						$("#list-table").show();
						$("#the-list").append('<tr><td id="newmetaleft" class="left"><input type="text" id="i_metaKeyInput-pre-'+x+'" name="i_metaKeyInput-pre-'+x+'" value="'+customDataMap.col_prepend.replace(/\\/gi, "")+'" /></td><td><textarea id="i_metaValueInput-pre-'+x+'" name="i_metaValueInput-pre-'+x+'" rows="2" cols="25">'+customDataMap.col_append.replace(/\\/gi, "")+'</textarea></td></tr>');
					});
					
					$.each(data.postRandNum, function(y,postRandNum){
						$("#i_post_randnum_col").val(postRandNum.col_to);
						$("#i_post_randnum_floor").val(postRandNum.col_prepend);
						$("#i_post_randnum_ceil").val(postRandNum.col_append);
					});
					
					$.each(data.postTags, function(z,postTags){
						$("#i_post_tags").val(postTags.col_append);
					});
					
					$("#dataMapDisplay").empty().append( '<span class="error">Successfully loaded import dataMap: <strong>' + $("#s_otherImport option:selected").text() + '</strong></span>');
				} else {
					divContent = '<p class="error">' + data.wppgError + '</p>';
					$("#dataMapDisplay").empty().append( divContent );
				}
				
			}, "json");
		});
		
		/* click events for map types */
		$("#r_mapType1").live('click', function() {
			
			$('select[id^="s_col_type"]').each(function() {
				if (this.id.indexOf('-' + $("#s_customPostType").val()) >= 0) {
					$(this).change();
					$(this).show();
				} else {
					$(this).hide();
				}
			});
			
			$("#" + $("#r_mapType1").val()).show().siblings().hide();
		});
		$("#r_mapType2").live('click', function() {
			$("input:radio[name=r_themeMap]:first").click();
			$("#" + $("#r_mapType2").val()).show().siblings().hide();
		});
		$("#r_mapType3").live('click', function() {
			$("#s_otherImport").change();
			$("#" + $("#r_mapType3").val()).show().siblings().hide();
		});
		
		/* default invoke events */
		
		$("#r_mapType1").click();
		$('select[id^="s_widgetType"]').change();
		$("#s_customPostType").change();
	}
	
	function finishImport() {
		var pageNums     = parseInt(Number($("#h_importRows").val()) / Number(wppgJsVars.dataPerPage),10);
		var fileDelim    = $("#s_fileDelim").val();
		var fileId       = $("#s_importFile").val();
		var importName   = $("#i_importName").val();
		var totalAdded   = 0;
		var totalSkipped = 0;
		var divContent   = "";
		var importId     = "";
		var newBeginDate = "";
		var failedImport = false;
		
		if (pageNums < 1) {
			pageNums = 1;
		}
		
		var processPage = 0;
		
		for (processPage = 1; processPage <= pageNums; processPage++) {
			
			var wppgStartRow  = $("#h_importStartRow").val();
			var dataSerial    = $("#importjobaddform").serialize();			
			var dataRequest   = dataSerial + "&action=finishImport&importId="+importId+"&newBeginDate="+newBeginDate+"&fileId="+fileId+"&fileDelim="+fileDelim+"&pageNums="+pageNums+"&currPage="+processPage+"&seed="+Math.random();
			var nextStartRow  = Number(wppgStartRow) + Number(wppgJsVars.dataPerPage);
		
			$("#importProgressBar").empty();
			
			$.ajaxSetup ({  
				cache: false,
				async: false,
				error:function(x,e){
					$("#importProgressBar").empty();
					$("#importProgressBar").append('<p>&nbsp;</p>');
					$("#importProgressBar").append('<p class="error"><strong>## IMPORT ENCOUNTERED ERRORS - READ BELOW ##</strong></p>');
					$("#importProgressBar").append('<p>There may have been posts created as a result of this failed import job. It is recommended that you view the import job using the wpPostGen plugin menu and decide if you wish to remove the import and all associated posts. If you continue to have issues, please do not hesitate to contact our support by visiting <a href="http://wpwares.com" target="_blank">wpWares.com</a></p>');
					failedImport = true;
				}
			});

			$("#importProgressBar").append('<p>&nbsp;</p>');					
			$("#importProgressBar").append('<p>Page <strong>' + processPage + '</strong> of <strong>' + pageNums + '</strong></p>');
			$("#importProgressBar").append('<p>Total posts to process: <strong>' + $("#h_importRows").val() + '</strong></p>');
			$("#importProgressBar").append('<span id="loaderImage"><p><img src="' + wppgJsVars.pluginUrl + '/images/loader.gif"></p></span>');
			
			$.get(ajaxurl, dataRequest, function(data){
				
				if (data.wppgError != "success") {
					divContent  = '<p>&nbsp;</p>';
					divContent += '<p class="error">' + data.wppgError + '</p>';
					$("#importProgressBar").empty().append( divContent );
				} else {
					totalAdded   = totalAdded + data.addedRows;
					totalSkipped = totalSkipped + data.skippedRows;
					
					importId     = data.importId;
					newBeginDate = data.newBeginDate;
				}
				
			}, "json");
			
			if (failedImport) {
				break;
			}
			
			$("#h_importStartRow").val(nextStartRow);			
		}
		
		if (!failedImport) {
			$("#loaderImage").hide();
			$("#importProgressBar").append('<p>&nbsp;</p>');
			$("#importProgressBar").append('<p class="success">## IMPORT COMPLETE ##</p>');
			$("#importProgressBar").append('<p class="success">Import Label Name: <strong>' + importName + '</strong></p>');
			$("#importProgressBar").append('<p class="success">Total Posts Created: <strong>' + totalAdded + '</strong></p>');
			$("#importProgressBar").append('<p class="success">Total Lines Skipped: <strong>' + totalSkipped + '</strong></p>');
			return true;
		} else {
			return false;
		}
	}
	
	$("#s_postAuthor option").attr("selected","selected");
});
