$(document).ready(function() {
	
	var is_mobile = false;
	$("input[name=album_path]").val(get_path);
	
	$('#title_album').keyup(function(e){
		
	    if(e.keyCode == 13)
	    {
		    e.preventDefault();
		    
	        $(".btn-create-album-ok").trigger("click");
	    }
	});
	
	$('#create-folder-modal').on('shown.bs.modal', function () {
		$("#create-folder-modal #title_album").focus();
	});
	
	$("#title_album").on("keydown",function search(e) {
		
	    if(e.keyCode == 13) {
		    e.preventDefault();
	        $(".btn-create-album-ok").trigger("click");
	    }
	    
	});
	
	if ($(window).width() < 1200){
        is_mobile = true;
    }
    
	$(window).resize(function() {
		
		if ($(window).width() < 1200){
	        is_mobile = true;
	    }
		
	});
    
    if(!is_mobile) {
	        
		var sidebar = $(".sidebar-block").stickySidebar({
		    topSpacing: 160,
		    bottomSpacing: 0
		});
	
	}
		
	// Add the content of the dashboard actions to the header menu
	$("#dash-acts-container").html($("#dashboard-actions").html());
	$(".top_header").css("height", "160px");
	
	var selected_files_ids = [];
	var select_node_init = false;
	
	/*
	// Detect when we scroll 
	var waypoint = new Waypoint({
		element: document.getElementById('dashboard-actions'),
		handler: function(direction) {
			
			if(direction == "up") {
				$(".dashboard-actions").removeClass("sticky");
			} else {
				$(".dashboard-actions").addClass("sticky");
			}
			
			
		}
	})
	*/
	
	$(".btn-edit-privacy").click(function(e) {
		
		e.preventDefault();
		
		$("#edit-privacy-modal").modal("show");
		
	});
	
	$(document).on("click", ".btn-share-album", function(e) {
		
		e.preventDefault();
		
		$("#share-album-modal").modal("show");
		
	});
	
	$(document).on("click", ".btn-update-privacy-ok", function(e) {
		
		e.preventDefault();
		
		var privacy_setting = $(".select_album_privacy").val();
		var privacy_password = $(".password_album_privacy").val();
		
		var that = $(this);
		that.prop("disabled", true);
		
		$(".alert-album-privacy").hide();
		
		$.ajax('ajax/update_album_privacy.php', {
	        method: "POST",
	        data: {
		        privacy_setting: privacy_setting,
		        privacy_password: privacy_password,
		        album_id: album_id,
		        is_dashboard: is_dashboard
	        },
	        dataType: "json",
	        success(data) {
		        
		        
		        that.prop("disabled", false);
		        
				if(data.error && data.error != "") {
					
					$(".alert-album-privacy").html(data.error).fadeIn();
					
				} else {
					
					$("#edit-privacy-modal").modal("hide");
					showSuccessToast("Your album privacy has been updated!");
					
				}
		        
	        },
	        error() {
		        that.prop("disabled", false);
		        
		        alert("Error while renaming this album...");
	        },
	    });
		
	});
	
	$(document).on("change", ".select_album_privacy", function() {
		
		var privacy_val = $(this).val();
		
		if(privacy_val == "2") {
			$(".from-group-album-password").addClass("active");
		} else {
			$(".from-group-album-password").removeClass("active");
		}
		
	});
	
	$(".btn-edit-album-name").click(function(e) {
		
		e.preventDefault();
		
		var album_type = $(this).attr("data-type");
		var album_name = $(".album_name").html();
		
		$("#rename-album-modal .input_edt_album_name").val($.trim(album_name));
		$(".btn-rename-album-ok").attr("data-type", album_type);
		$("#rename-album-modal").modal("show");
		
	});
	
	
	$(document).on("click", ".btn-rename-album-ok", function(e) {
		
		e.preventDefault();
		
		var new_album_name = $("input.input_edt_album_name").val();
		var album_type = $(this).attr("data-type");
		var that = $(this);
		
		$.ajax('ajax/rename_album.php', {
	        method: "POST",
	        data: {
		        album_title: new_album_name,
		        album_path: get_path,
		        album_type: album_type
	        },
	        success(data) {
		        
				if(data.error) {
					alert(data.error);
				} else {
					
					if(album_type == "normal") {
						
						var small_val = $(".jstree-clicked").find("span small").html();
						$(".jstree-clicked").find("span").html(new_album_name + " <small>"+ small_val +"</small>");
						
					} else {
						
						$("#jstree-folders").find(".jstree-container-ul").find("li").find("a").first().find("span").html(new_album_name);
						
					}
														
					$("span.album_name").html(new_album_name);
					
					$("#rename-album-modal").modal("hide");
					showSuccessToast("Your album name has been updated!");
				}
		        
	        },
	        error() {
		        that.prop("disabled", false);
		        
		        alert("Error while renaming this album...");
	        },
	    });
		
	});
	
	/*
		
		UNCOMMENT FOR ENABLING THE SORTABLE / DRAG & DROP
		
	$( ".row_files" ).sortable({
		revert: true,
		multiple:true,
		containment: "parent",
		snap:'.selector',
		cloneHelper:false,
		selected:'.selected_box',
		placeholder: "ui-state-highlight",
		beforeStart: function () {
            var $this = $(this);
            if (!$this.hasClass('selected_box')) {
                $this.siblings('.selected_box').removeClass('selected_box');
                $this.addClass('selected_box');
            }
        },
		over : function() {
		},
		start: function(event, ui) {
						
			
	        
		},
		sort: function (event, ui) {
		
		    
				
		},
		stop: function() {
			
		},
		receive: function(event, ui) {
	        var sourceList = ui.sender;
	        var targetList = $(this);
	    }
	});
	*/
	
	function moveSelected(ol, ot){
	    console.log("moving to: " + ol + ":" + ot);
	    selectedObjs.each(function(){
	        $this =$(this);
	        var p = $this.position();
	        var l = p.left;
	        var t = p.top;
	        console.log({id: $this.attr('id'), l: l, t: t});
	
	
	        $this.css('left', l+ol);
	        $this.css('top', t+ot);
	    })
	}
	
	/*
	$(document).on("click", ".btn-edit-album-name", function(e) {
		
		e.preventDefault();
		
		$("span.album_name").hide();
		$("input[name=album_name_edt]").show();
		$(this).html("<i class='fas fa-check'></i>");
		$(this).addClass("btn-save-album-name").removeClass("btn-edit-album-name");
		
	});
	*/
	

	
	$('.lazy').Lazy({
        scrollDirection: 'vertical',
        effect: 'fadeIn',
        visibleOnly: true,
        onError: function(element) {
            console.log('error loading ' + element.data('src'));
        },
        afterLoad: function() {
	        $("img.lazy").attr("style", "background-image: none;");
        }
    });
	
	$( ".btn-trash" ).droppable({
		tolerance: 'pointer',
        drop: function( event, ui ) {
	        
            dropped = $(this);
            var element = ui.draggable[0];
            var element_id = $(element).find(".file_container").attr("data-id");
        
			// Delete action
			var selected_file = [element_id];
			
			const formData = new FormData();
			formData.append('files_ids', JSON.stringify(selected_file));
			
			$(".alert-copy-files").hide();
			
			$.ajax('ajax/delete_files.php', {
		        method: "POST",
		        data: formData,
		        processData: false,
		        contentType: false,
		        success(data) {
			        			        
			        data = JSON.parse(data);
			        
			        if(data.error) {
				        
				        bootbox.alert("An error occurred while deleting these files... Please try again.");
				        
			        } else if(data.success) {
				        
				        element.remove();
				        
				        showSuccessToast("Your file has been moved to trash!");
				        
			        }
			        
		        },
		        error() {
			        that.prop("disabled", false);
			        
			        alert("Error while copying these files...");
		        },
		    });
            
        },
        over: function(event, ui) {
	        console.log("test");
        }
    });
	
	$(".file_container").each(function(i, elt) {
		
		var elt_url = $(elt).attr("data-url");
		
		$(elt).find(".shareIcons").jsSocials({
			showLabel: false,
		    showCount: false,
	        shares: [{ share: "twitter", label: "Twitter", logo: "fab fa-twitter", url: url + "/" + elt_url}, { share: "email", label: "Email", logo: "fas fa-envelope", url: url + "/" + elt_url}, { share: "facebook", label: "Facebook", logo: "fab fa-facebook", url: url + "/" + elt_url}, { share: "pinterest", label: "Pinterest", logo: "fab fa-pinterest", url: url + "/" + elt_url}, { share: "whatsapp", label: "WhatsApp", logo: "fab fa-whatsapp", url: url + "/" + elt_url}]
	    });
		
	});	
	
	var clipboard = new ClipboardJS('.btn-copy');
	
	clipboard.on('success', function(e) {
		

	    $(e.trigger).html("Copied!").prop("disabled", true);
	    
	    setTimeout(function() {
		    $(e.trigger).html("Copy").prop("disabled", false);
	    }, 2000);
	
	    e.clearSelection();
	});
	
	var clipboard_album = new ClipboardJS('.btn-copy-album-url');
	
	clipboard_album.on('success', function(e) {
		

	    $(e.trigger).html("Copied!").prop("disabled", true);
	    
	    setTimeout(function() {
		    $(e.trigger).html("Copy").prop("disabled", false);
	    }, 2000);
	
	    e.clearSelection();
	});
	
	
	$(".btn-change-sort").click(function(e) {
		
		e.preventDefault();
		
		if($(this).hasClass("active")) {
			return;
		}
		
		var sort_order = $(this).attr("data-sort");
		
		if(sort_order == "A-Z") {			
			
			$(".btn-change-sort.active").removeClass("active").find("i").addClass("far fa-square").removeClass("fas fa-check-square");
			$(".btn-change-sort[data-sort=A-Z]").addClass("active").find("i").addClass("fas fa-check-square").removeClass("far fa-square");
			
			$(".sorting_value").html("A-Z");
			
			$('.file_col_container').sort(function(a, b) {
				if ($(a).attr("data-name") < $(b).attr("data-name")) {
					return -1;
				} else {
					return 1;
				}
			}).appendTo('.row_files');
			
		} else if(sort_order == "Z-A") {			
			
			$(".btn-change-sort.active").removeClass("active").find("i").addClass("far fa-square").removeClass("fas fa-check-square");
			$(".btn-change-sort[data-sort=Z-A]").addClass("active").find("i").addClass("fas fa-check-square").removeClass("far fa-square");
			
			$(".sorting_value").html("Z-A");
			
			$('.file_col_container').sort(function(a, b) {
				if ($(a).attr("data-name") > $(b).attr("data-name")) {
					return -1;
				} else {
					return 1;
				}
			}).appendTo('.row_files');
			
		} else if(sort_order == "date-old") {			
			
			$(".btn-change-sort.active").removeClass("active").find("i").addClass("far fa-square").removeClass("fas fa-check-square");
			$(".btn-change-sort[data-sort=date-old]").addClass("active").find("i").addClass("fas fa-check-square ").removeClass("far fa-square");
			
			$(".sorting_value").html("Oldest to Newest");
			
			$('.file_col_container').sort(function(a, b) {
				if ($(a).attr("data-timestamp") < $(b).attr("data-timestamp")) {
					return -1;
				} else {
					return 1;
				}
			}).appendTo('.row_files');
			
		} else if(sort_order == "date-new") {			
			
			$(".btn-change-sort.active").removeClass("active").find("i").addClass("far fa-square").removeClass("fas fa-check-square");
			$(".btn-change-sort[data-sort=date-new]").addClass("active").find("i").addClass("fas fa-check-square ").removeClass("far fa-square");
			
			$(".sorting_value").html("Newest to Oldest");
			
			$('.file_col_container').sort(function(a, b) {
				if ($(a).attr("data-timestamp") > $(b).attr("data-timestamp")) {
					return -1;
				} else {
					return 1;
				}
			}).appendTo('.row_files');
			
		}
		
		$(".btn-change-sort").removeClass("active");		
		$(this).addClass("active");
		
	});
	
	var jstree_init = false;
	
	// For the sidebar
	$('#jstree-folders').jstree({
        'core': {
            'themes': {
                'name': 'proton',
                'responsive': true
            }
        }
	});
	
	$("#jstree-folders").bind('open_node.jstree', function (event, data) {
		
		if(jstree_init) {
				
			$(".jstree-anchor span").tooltipster({
				theme: 'tooltipster-light',
				side: 'right'
			});
		
			var time = 200;
			
			$.each(data.node.children, function(i, nd) {
				
				setTimeout( function() {
				
					var nd_dom = $("#"+nd+"");
					var span_dom = nd_dom.find("span");
					var data_path = span_dom.attr("data-path");
					var span_title = span_dom.attr("title");
															
					$.ajax('ajax/get_album_count.php', {
				        method: "POST",
				        dataType: "json",
				        data: {
					        data_path: data_path
				        },
				        success(data) {
					        
					        span_dom.find("small").first().html(data.album_count);
					        
					    } 
					});
				
				}, time);
				
				time += 200;
				
			});
		
		}
		
	});
	
	$("#jstree-folders").bind('ready.jstree', function (event, data) {
		
			
		if(get_path != "" && get_path != user_default_path) {
			
			// Open to find the good ID
			$(this).jstree("open_all");
			
			var span_path = $("span[data-path=\"" + get_path + "\"]");
			var closest_link = span_path.closest("a").addClass("jstree-clicked");
			var closest_node = span_path.closest("li");			
			var closest_id = closest_node.attr("id");
			
			// We have the good ID, we can close all
			$(this).jstree("close_all");
			
			jstree_init = true;
			
			// Finally we can select only the good one
			$(this).jstree("select_node", closest_id);
			
			select_node_init = true;
					
		} else {
			
			jstree_init = true;
						
			$(this).jstree("open_node", $("#jstree-folders .jstree-last"));
			
			select_node_init = true;
			
			
		}
		
		/*
		$(".jstree-anchor span").tooltipster({
			theme: 'tooltipster-light',
			side: 'right'
		});
		*/
				
		var album_title = $.trim($(".jstree-clicked span").first().html());
		
		if(album_title == "") {
			album_title = album_home_title;
		}
				
		$(".album_title h4 .album_name").html(album_title);
		$("#create-folder-modal .album_name").html(album_title);
		$(".album_title h4 .album_name").find("small").remove();
		$("#create-folder-modal .album_name").find("small").remove();

		
		var cleaned_album_name = $.trim($(".album_title h4 .album_name").html());
		
		$(".btn-download-album").attr("href", $(".btn-download-album").attr("href") + "&name=" + cleaned_album_name);
		$(".btn-download-album").removeClass("disabled");
		
		$(".btn-edit-album-name").css("display", "inline-block");
		$("input[name=album_name_edt]").val(cleaned_album_name);
		
		$(".album_title i.fa-spin").remove();
		
		$(".aside").fadeIn();
			
		$( ".jstree-anchor" ).droppable({
			tolerance: 'pointer',
	        drop: function( event, ui ) {
		        
				var dropped = $(this);
				var album_path = dropped.find("span").attr("data-path");
	            
				var element = ui.draggable[0];
				var element_id = $(element).find(".file_container").attr("data-id");
				
				// Delete action
				var selected_file = [element_id];
				var action = "move";
				
				const formData = new FormData();
				formData.append('files_ids', JSON.stringify(selected_file));
				formData.append('copy_path', album_path);
				formData.append('action', action);	
				
				var nb_files_album = parseInt(dropped.find("small").text());
				var nb_files_album_icrmt = nb_files_album+1;
				
				$.ajax('ajax/copy_files.php', {
			        method: "POST",
			        data: formData,
			        processData: false,
			        contentType: false,
			        success(data) {
				        				        
				        data = JSON.parse(data);
				        
				        if(data.error) {
					        
					        alert(data.error);
					        
				        } else if(data.success) {
					        
					        showSuccessToast("Your file has been moved to this album!");
							
							dropped.find("small").html(nb_files_album_icrmt);
					        					        
				        }
				        
			        },
			        error() {
				        that.prop("disabled", false);
				        
				        alert("Error while copying these files...");
			        },
			    });
		            
		    },
	        over: function(event, ui) {
		    	
		    	$(this).addClass("jstree-hovered");
		    	
	        },
	        out: function(event, ui) {
		    	
		    	$(this).removeClass("jstree-hovered");
		    	
	        }
	    });
		
	});
	
	$('#jstree-folders').on("select_node.jstree", function (node, selected, event) {
								
		if(jstree_init && select_node_init) {
			var selected_path = $(selected.node.text).attr("data-path");
			
			$.ajax('ajax/redirect_to_path.php', {
		        method: "POST",
		        dataType: "json",
		        data: {
			        selected_path: selected_path
		        },
		        success(data) {
			        
					if(data.error) {
						alert(data.error);
					} else if(data.success) {
						window.location = "dashboard.php?path=" + selected_path;
					}
			        
		        },
		        error() {		        
			        alert("Error while opening this album...");
		        },
		    });
	    }
		
	});
	
	
	
	// For the creation of a new album
	$('#jstree2-folders').jstree({
		'core': {
            'themes': {
                'name': 'proton',
                'responsive': true
            }
        }
	});
	$('#jstree2-folders').on("select_node.jstree", function (node, selected, event) {
		
		var selected_path = $(selected.node.text).attr("data-path");
		$("input[name=album_path]").val(selected_path);
		
	});
	
	/*
	$("#jstree2-folders").bind('ready.jstree', function (event, data) {
		
		
		$("input[name=album_path]").val(get_path);
		
		if(get_path != "" && get_path != user_default_path) {
			
			$(this).jstree("open_all");
			
			var span_path = $("span[data-path=\"" + get_path + "\"]");
			var closest_link = span_path.closest("a").addClass("jstree-clicked");
			var closest_ul = span_path.closest("ul");
			
			$(closest_ul).find("li").each(function(i, elt) {
				
				$(this).jstree("close_node", $(elt), function(evt) {console.log(evt)});
				
			});
		
		} else {
			
			$(this).jstree("open_node", $("#jstree2-folders .jstree-last"));
			
			
		}
		
	});
	*/
	
	// For the copy
	$('#jstree3-folders').jstree({
		'core': {
            'themes': {
                'name': 'proton',
                'responsive': true
            }
        }
	});
	$('#jstree3-folders').on("select_node.jstree", function (node, selected, event) {
		
		var selected_path = $(selected.node.text).attr("data-path");
		$("input[name=path_album_copy]").val(selected_path);
		
	});
	$("#jstree3-folders").bind('ready.jstree', function (event, data) {
		
		/*
		$(this).jstree("open_all");

		var span_path = $("span[data-path=\"" + get_path + "\"]");
		var closest_link = span_path.closest("a").addClass("jstree-clicked");
		*/
		
		/*
			UNCOMMENT IF WE SELECT THE CURRENT ALBUM BY DEFAULT
		if(get_path != "" && get_path != user_default_path) {
			
			$(this).jstree("open_all");
			
			var span_path = $("span[data-path=\"" + get_path + "\"]");
			var closest_link = span_path.closest("a").addClass("jstree-clicked");
			var closest_ul = span_path.closest("ul");
			
			$(closest_ul).find("li").each(function(i, elt) {
				
				$(this).jstree("close_node", $(elt), function(evt) {console.log(evt)});
				
			});
		
		} else {
			
			$(this).jstree("open_node", $("#jstree3-folders .jstree-last"));
			
			
		}
		*/
		
	});
	
	$(document).on("click", ".btn-select-all", function(e) {
		
		e.preventDefault();
		
		if($(this).hasClass("checked")) {
			
			$(this).find("i").removeClass("fas fa-check-square").addClass("far fa-square");
			$(this).removeClass("checked");
			
			$(".my_files_container .file_container").each(function(i, elt) {
				
				var file_action_check = $(elt).find(".file_action_check");
				file_action_check.closest(".file_col_container").removeClass("selected_box");
				
				if(file_action_check.hasClass("checked")) {
					file_action_check.trigger("click");
				}	
							
			});
			
			hideBottomBar();
			
		} else {
			
			$(this).find("i").removeClass("far fa-square").addClass("fas fa-check-square");
			$(this).addClass("checked");
			
			$(".my_files_container .file_container").each(function(i, elt) {
				
				var file_action_check = $(elt).find(".file_action_check");
				file_action_check.closest(".file_col_container").addClass("selected_box");
				
				if(!file_action_check.hasClass("checked")) {
					file_action_check.trigger("click");
				}	
							
			});
			
			showBottomBar();
			
		}
				
	});
	
	$(document).on("click", ".file_action_check", function(e) {
		
		e.preventDefault();
		
		var selected_file_container = $(this).closest(".file_container");
		var selected_id = selected_file_container.attr("data-id");
				
		if($(this).hasClass("checked")) {
			
			$(this).find("i").removeClass("fas fa-check-square").addClass("far fa-square");
			$(this).removeClass("checked");
			$(this).closest(".file_col_container").removeClass("selected_box");
			
			const index = selected_files_ids.indexOf(selected_id);
			if (index > -1) {
				selected_files_ids.splice(index, 1);
			}
						
		} else {
			
			$(this).find("i").removeClass("far fa-square").addClass("fas fa-check-square");
			$(this).addClass("checked");
			$(this).closest(".file_col_container").addClass("selected_box");
			
			selected_files_ids.push(selected_id);
			
		}
		
		if($(".file_container .file_action_check.checked").length) {
			showBottomBar();
		} else {
			hideBottomBar();
		}
		
		
	});
		
	$(document).on("click", ".file_container .f_container", function(e) {
						
		if(selected_files_ids.length > 0 && !$(e.target).hasClass('file_action_check') && !$(e.target).hasClass('fa-square') && !$(e.target).hasClass('fa-check-square')) {
			
			e.preventDefault();
			
			$(this).closest(".file_container").find(".file_action_check").trigger("click");
			
		}
		
	});
		
	$(document).on("click", ".btn-create-album", function(e) {
		
		e.preventDefault();
		
		$("#create-folder-modal").modal("show");
		
	});
	
	$(document).on("click", ".btn-create-album-ok", function(e) {
		
		e.preventDefault();
		
		var album_title = $("#title_album").val();
		var album_path = $("#path_album").val();
		
		/*
		if(album_path == "") {
			album_path = 
		}
		*/
		
		var that = $(this);
		that.prop("disabled", true);
		
		const formData = new FormData();
		formData.append('album_path', album_path);
		formData.append('album_title', album_title);
		
		$(".alert-create-album").hide();
		
		$.ajax('ajax/create_album.php', {
	        method: "POST",
	        data: formData,
	        processData: false,
	        contentType: false,
	        success(data) {
		        
				that.prop("disabled", false);
		        
		        data = JSON.parse(data);
		        
		        if(data.error) {
			        
			        $(".alert-create-album").html(data.error).fadeIn();
			        
		        } else if(data.success) {
			        
			        if(get_path != "") {
				        window.location = "dashboard.php?action=album_created&path=" + get_path;
				    } else {
			        	window.location = "dashboard.php?action=album_created";
			        } 
			        
		        }
		        
	        },
	        error() {
		        that.prop("disabled", false);
		        
		        alert("Error while creating this album...");
	        },
	    });
				
	});
	
	$(document).on("click", ".btn-copy-files", function(e) {
		
		e.preventDefault();
		
		if(selected_files_ids.length == 0) {
			alert("Please select at least one file");
		} else {
			$("#copy-files-modal").modal("show");
		}
		
	});
	
	$(document).on("click", ".btn-move-files", function(e) {
				
		e.preventDefault();
		
		if(selected_files_ids.length == 0) {
			alert("Please select at least one file");
		} else {
			$("#copy-files-modal .btn-copy-files-ok").attr("data-action", "move");
			$("#copy-files-modal .action-text").html("Move");
			$("#copy-files-modal").modal("show");
		}
		
	});
	
	$(document).on("click", ".btn-copy-files", function(e) {
		
		e.preventDefault();
		
		if(selected_files_ids.length == 0) {
			alert("Please select at least one file");
		} else {
			$("#copy-files-modal .btn-copy-files-ok").attr("data-action", "copy");
			$("#copy-files-modal .action-text").html("Copy");
			$("#copy-files-modal").modal("show");
		}
		
	});
	
	$(document).on("click", ".btn-copy-files-ok", function(e) {
		
		e.preventDefault();
		
		var album_path = $("#path_album_copy").val();
		
		var that = $(this);
		var that_html = that.html();
		that.prop("disabled", true);
		that.html("<i class='fas fa-circle-notch fa-spin'></i> In progress...");
		
		var action = $(this).attr("data-action");
		
		const formData = new FormData();
		formData.append('files_ids', JSON.stringify(selected_files_ids));
		formData.append('copy_path', album_path);
		formData.append('action', action);
		
		$(".alert-copy-files").hide();
		
		$.ajax('ajax/copy_files.php', {
	        method: "POST",
	        data: formData,
	        processData: false,
	        contentType: false,
	        success(data) {
		        
				that.prop("disabled", false);
				that.html(that_html);
		        
		        data = JSON.parse(data);
		        
		        if(data.error) {
			        
			        $(".alert-copy-files").html(data.error).fadeIn();
			        
		        } else if(data.success) {
			        
			        // Hide the modal
			        $("#copy-files-modal").modal("hide");
			        
			        if(action == "copy") {
		        		
		        		showSuccessToast("Your " + selected_files_ids.length + " files have been copied!");
		        		
			        } else {
				        
				        showSuccessToast("Your " + selected_files_ids.length + " files have been moved!");
				        
				        // Delete the files from this album
				        $.each( selected_files_ids, function( index, file_id ) {
					        
					    	$(".file_col_container[data-id="  + file_id + "]").remove();
					    	
					    });
							    
					    if($(".file_col_container").length == 0) {
						    
						    $(".row_files").append('<div class="col-md-12"><div class="alert alert-info text-center">No files in this directory for the moment...</div></div>');
						    
					    }

			        }
			        
			        // Reset the selection
			        resetFilesSelection();
			        			        
			        
		        }
		        
	        },
	        error() {
		        that.prop("disabled", false);
		        
		        alert("Error while copying these files...");
	        },
	    });
		
		
		
	});
	
	$(document).on("click", ".btn-delete-album", function(e) {
		
		e.preventDefault();
				
		bootbox.confirm({
		    message: "Are you sure you want to delete this album including the files and sub-albums that are inside it?",
		    buttons: {
		        confirm: {
		            label: 'Yes, delete album',
		            className: 'btn-primary'
		        },
		        cancel: {
		            label: 'No',
		            className: 'btn-danger'
		        }
		    },
		    callback: function (result) {
		        
		        if(result) {
		    
				    var that = $(this);
					that.prop("disabled", true);
										
					const formData = new FormData();
					formData.append('album_id', album_id);
										
					$.ajax('ajax/delete_album.php', {
				        method: "POST",
				        data: formData,
				        processData: false,
				        contentType: false,
				        success(data) {
					        
							that.prop("disabled", false);
					        
					        data = JSON.parse(data);
					        
					        if(data.error) {
						        
						        bootbox.alert("An error occurred while deleting these files... Please try again.");
						        
					        } else if(data.success) {
						        
							    window.location = "dashboard.php?action=album_deleted";
						        
					        }
					        
				        },
				        error() {
					        that.prop("disabled", false);
					        
					        alert("Error while deleting this album...");
				        },
				    });
				    
				}
		        
			}
		});			
	});
	
	$(document).on("click", ".btn-delete-files", function(e) {
		
		e.preventDefault();
		
		bootbox.confirm({
		    message: "Are you sure you want to delete these files?",
		    buttons: {
		        confirm: {
		            label: 'Yes, delete files',
		            className: 'btn-primary'
		        },
		        cancel: {
		            label: 'No',
		            className: 'btn-danger'
		        }
		    },
		    callback: function (result) {
		        
		        if(result) {
		    
				    var that = $(this);
					that.prop("disabled", true);
					
					var action = $(this).attr("data-action");
					
					const formData = new FormData();
					formData.append('files_ids', JSON.stringify(selected_files_ids));
					
					$(".alert-copy-files").hide();
					
					$.ajax('ajax/delete_files.php', {
				        method: "POST",
				        data: formData,
				        processData: false,
				        contentType: false,
				        success(data) {
					        
							that.prop("disabled", false);
					        
					        data = JSON.parse(data);
					        
					        if(data.error) {
						        
						        bootbox.alert("An error occurred while deleting these files... Please try again.");
						        
					        } else if(data.success) {
						        
						        showSuccessToast("Your " + selected_files_ids.length + " files have been moved to trash!");
				        
						        // Delete the files from this album
						        $.each( selected_files_ids, function( index, file_id ) {
							        
							    	$(".file_col_container[data-id="  + file_id + "]").remove();
							    	
							    });
							    
							    if($(".file_col_container").length == 0) {
								    
								    $(".row_files").append('<div class="col-md-12"><div class="alert alert-info text-center">No files in this directory for the moment...</div></div>');
								    
							    }
							    
							    resetFilesSelection();
						        
					        }
					        
				        },
				        error() {
					        that.prop("disabled", false);
					        
					        alert("Error while copying these files...");
				        },
				    });
				    
				}
		        
			}
		});			
	});
	
	var all_loaded = true;
	
	var elem = $('.my_files_container');
	var inner = $('.my_files_container > .row_files');
	var array_last_id_paginating = [];
	var loading_page = 1;
	
	// Ajax Load More
	$(window).scroll(function() {
		
	    if((($(window).scrollTop() + $(window).innerHeight()) >= $(document).height()-100) && all_loaded) {
		    
		    all_loaded = false;
		    
		    var last_file_id = $(".file_col_container").last().attr("data-id");
		    var last_file_created_at = $(".file_col_container").last().attr("data-created-at");
		    
		    if(!array_last_id_paginating.includes(last_file_id)) {
		    
			    array_last_id_paginating.push(last_file_id);
			    
			    loading_page++;

			    $.toast({
				    text: "Loading page " + loading_page,
				    showHideTransition: 'slide',
				    hideAfter: 1300,
				    position: "bottom-left"
				});
		        
		        // TODO TAKE THE SORT ALGORITHM IN CONSIDERATION
		        $.ajax('ajax/load_more_dashboard.php', {
			        method: "POST",
			        data: {
				        last_file_id: last_file_id,
				        last_file_created_at: last_file_created_at,
				        folder_path: folder_path
			        },
			        success(data) {
				        
				        if(data && data != "") {
					        $(".row_files").append(data);
					        
					        sidebar.stickySidebar('updateSticky');
					        
					        $('.lazy').Lazy({
						        scrollDirection: 'vertical',
						        effect: 'fadeIn',
						        visibleOnly: true,
						        onError: function(element) {
						            console.log('error loading ' + element.data('src'));
						        },
						        afterLoad: function() {
							        $("img.lazy").attr("style", "background-image: none;");
							        all_loaded = true;
						        }
						    });
						    
						    	
				        }
				        
				    }
				});
			
			}
			
		}
	});
	
	function showBottomBar() {
		
		// Count how many files selected and show
		var nb_selected_files = selected_files_ids.length;
		
		$(".nb_files_selected").html(nb_selected_files + " files selected");
		
		$(".bottom_actions_bar").show();
	}
	
	function hideBottomBar() {
		$(".bottom_actions_bar").hide();
	}
	
	function showSuccessToast(msg) {
		
		$.toast({
		    heading: 'Success',
		    text: msg,
		    showHideTransition: 'slide',
		    icon: 'success',
		    hideAfter: 8000,
		    position: "top-right"
		});
		
	}
	
	function showErrorToast(msg) {
		
		$.toast({
		    heading: 'Error',
		    text: msg,
		    showHideTransition: 'slide',
		    icon: 'error',
		    hideAfter: 8000,
		    position: "top-right"
		});
		
	}
	
	function resetFilesSelection() {
		
		console.log("here");
		
		selected_files_ids = [];
		hideBottomBar();
		
		$(".btn-select-all").find("i").removeClass("fas fa-check-square").addClass("far fa-square");
		$(".btn-select-all").removeClass("checked");
		
		$(".my_files_container .file_container").each(function(i, elt) {
			
			var file_action_check = $(elt).find(".file_action_check");
			file_action_check.closest(".file_col_container").removeClass("selected_box");
			
			if(file_action_check.hasClass("checked")) {
				file_action_check.trigger("click");
			}	
						
		});
		
	}
	
});