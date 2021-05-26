$(document).ready(function() {
	
	var sidebar = $(".sidebar-block").stickySidebar({
	    topSpacing: 160,
	    bottomSpacing: 0
	});
	
	// Add the content of the dashboard actions to the header menu
	$("#dash-acts-container").html($("#dashboard-actions").html());
	$(".top_header").css("height", "160px");
	
	var selected_files_ids = [];
	
	if(nb_files == 0) {
		$(".btn-select-all").addClass("disabled");
	}
	
	var jstree_init = false;
	var select_node_init = false;
	
	// For the sidebar
	$('#jstree-folders').jstree({
        'core': {
            'themes': {
                'name': 'proton',
                'responsive': true
            }
        }
	});
	
	$('.lazy').Lazy({
        scrollDirection: 'vertical',
        effect: 'fadeIn',
        visibleOnly: true,
        onError: function(element) {
            console.log('error loading ' + element.data('src'));
        },
        afterLoad: function() {
	        console.log("all loaded");
	        $("img.lazy").attr("style", "background-image: none;");
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
		
		$(".aside").fadeIn();
		
	});
	
	$('#jstree-folders').on("select_node.jstree", function (node, selected, event) {
				
		if(jstree_init) {
			console.log("here");
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
	
	$("#jstree-folders").bind('open_node.jstree', function (event, data) {
		
		if(jstree_init) {
		
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
		
	// For the creation of a new album
	$('#jstree2-folders').jstree();
	$('#jstree2-folders').on("select_node.jstree", function (node, selected, event) {
		
		var selected_path = $(selected.node.text).attr("data-path");
		$("input[name=album_path]").val(selected_path);
		
	});
	
	// For the copy
	$('#jstree3-folders').jstree();
	$('#jstree3-folders').on("select_node.jstree", function (node, selected, event) {
		
		var selected_path = $(selected.node.text).attr("data-path");
		$("input[name=path_album_copy]").val(selected_path);
		
	});
	
	var clipboard = new ClipboardJS('.btn-copy');
	
	clipboard.on('success', function(e) {
		

	    $(e.trigger).html("Copied!").prop("disabled", true);
	    
	    setTimeout(function() {
		    $(e.trigger).html("Copy").prop("disabled", false);
	    }, 2000);
	
	    e.clearSelection();
	});
	
	$(document).on("click", ".btn-select-all", function(e) {
		
		e.preventDefault();
		
		if($(this).hasClass("checked")) {
			
			$(this).find("i").removeClass("fas fa-check-square").addClass("far fa-square");
			$(this).removeClass("checked");
			
			$(".my_files_container .file_container").each(function(i, elt) {
				
				var file_action_check = $(elt).find(".file_action_check");
				
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
				
				if(!file_action_check.hasClass("checked")) {
					file_action_check.trigger("click");
				}	
							
			});
			
			showBottomBar();
			
		}
				
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
	
	$(document).on("click", ".file_action_check", function(e) {
		
		e.preventDefault();
		
		var selected_file_container = $(this).closest(".file_container");
		var selected_id = selected_file_container.attr("data-id");
				
		if($(this).hasClass("checked")) {
			
			$(this).find("i").removeClass("fas fa-check-square").addClass("far fa-square");
			$(this).removeClass("checked");
			
			const index = selected_files_ids.indexOf(selected_id);
			if (index > -1) {
				selected_files_ids.splice(index, 1);
			}
						
		} else {
			
			$(this).find("i").removeClass("far fa-square").addClass("fas fa-check-square");
			$(this).addClass("checked");
			
			selected_files_ids.push(selected_id);
			
		}
		
		if($(".file_container .file_action_check.checked").length) {
			showBottomBar();
		} else {
			hideBottomBar();
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
	
	$(document).on("click", ".btn-copy-files-ok", function(e) {
		
		e.preventDefault();
		
		var album_path = $("#path_album_copy").val();
		
		var that = $(this);
		that.prop("disabled", true);
		
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
		        
		        data = JSON.parse(data);
		        
		        if(data.error) {
			        
			        $(".alert-copy-files").html(data.error).fadeIn();
			        
		        } else if(data.success) {
			        
			        if(get_path != "") {
				        
				        if(action == "copy") {
			        		window.location = "dashboard.php?action=files_copied&path=" + get_path;
				        } else {
					        window.location = "dashboard.php?action=files_moved&path=" + get_path;
				        }

				    } else {
					    
					    if(action == "copy") {
			        		window.location = "dashboard.php?action=files_copied";
				        } else {
					        window.location = "dashboard.php?action=files_moved";
				        }
					
					}
			        
			        
			        
		        }
		        
	        },
	        error() {
		        that.prop("disabled", false);
		        
		        alert("Error while copying these files...");
	        },
	    });
		
		
		
	});
	
	$(document).on("click", ".btn-recover-files", function(e) {
		
		e.preventDefault();
		
		bootbox.confirm({
		    message: "Are you sure you want to recover these files forever?",
		    buttons: {
		        confirm: {
		            label: 'Yes, recover files',
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
					
					$.ajax('ajax/recover_files.php', {
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
						        
						        if(get_path != "") {
							        window.location = "trash.php?action=files_recovered&path=" + get_path;
						        } else {
							    	window.location = "trash.php?action=files_recovered";
						        }
						        
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
	
	$(document).on("click", ".btn-delete-permanently-files", function(e) {
		
		e.preventDefault();
		
		bootbox.confirm({
		    message: "Are you sure you want to delete these files forever?",
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
					
					$.ajax('ajax/delete_permanently_files.php', {
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
						        
						        if(get_path != "") {
							        window.location = "trash.php?action=files_deleted&path=" + get_path;
						        } else {
							    	window.location = "trash.php?action=files_deleted";
						        }
						        
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
	
	$(".file_container").click(function(e) {
						
		if(selected_files_ids.length > 0 && !$(e.target).hasClass('file_action_check') && !$(e.target).hasClass('fa-square') && !$(e.target).hasClass('fa-check-square')) {
			
			e.preventDefault();
			
			$(this).find(".file_action_check").trigger("click");
			
		}
		
	});
	
	function showBottomBar() {
		$(".bottom_delete_actions_bar").show();
	}
	
	function hideBottomBar() {
		$(".bottom_delete_actions_bar").hide();
	}
	
});