$(document).ready(function() {
	
	$("#share").jsSocials({
        shares: [{ share: "twitter", label: "Twitter", logo: "fab fa-twitter", url: url + "/file.php?id=" + photo_unique_id}, { share: "email", label: "Email", logo: "fas fa-envelope", url: url + "/file.php?id=" + photo_unique_id}, { share: "facebook", label: "Facebook", logo: "fab fa-facebook", url: url + "/file.php?id=" + photo_unique_id}, { share: "pinterest", label: "Pinterest", logo: "fab fa-pinterest", url: url + "/file.php?id=" + photo_unique_id}, { share: "whatsapp", label: "Whatsapp", logo: "fab fa-whatsapp", url: url + "/file.php?id=" + photo_unique_id}]
    });
    
    if(sub_plan == "Silver") {
	    $("#photo-link").val(url + "/file.php?s=" + photo_short_id);
    }
    
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
    
	$(".link-type").change(function(e) {
	   
		var selected = $(this).val();
		
		if(selected == 0) {
			
			$("#photo-link").val(stackpath_url + "/" + photo_url);
			
		} else if(selected == 1) {
			
			$("#photo-link").val(url + "/file.php?id=" + photo_unique_id);
			
		} else if(selected == 2) {
			
			$("#photo-link").val('<a href="' + url + "/file.php?id=" + photo_unique_id + '"><img src="' + stackpath_url + '/' + photo_url + '" /></a>');
			
		} else if(selected == 3) {
			
			$("#photo-link").val('[url=' + url + '/file.php?id=' + photo_unique_id + '][img]' + stackpath_url + '/' + photo_url + '[/img][/url]');
			
		} else if(selected == 4) {
			
			$("#photo-link").val(url + "/file.php?s=" + photo_short_id);
			
		}
	    
	});
	
	var clipboard = new ClipboardJS('.btn-copy');
	
	clipboard.on('success', function(e) {
	    
	    $(".btn-copy").html("Copied!").prop("disabled", true);
	    
	    setTimeout(function() {
		    $(".btn-copy").html("Copy").prop("disabled", false);
	    }, 2000);
	
	    e.clearSelection();
	});
	
	// Delete a photo action
	$(document).on("click", ".btn-delete", function(e) {
		
		e.preventDefault();
		
		var photo_id = $(this).attr("data-id");
		
		var confirm = bootbox.confirm({
			buttons: {
				confirm : {
				    label: 'Yes, delete',
				    callback: function(result) { 
				    }
				}
			},
			message: "Are you sure you want to delete this photo?",
		    callback: function (result) {
			    
			    if(result) {
				    
				    $.ajax('ajax/delete_photo.php', {
				        method: "POST",
				        data: {
					        photo_id : photo_id
				        },
				        success(data) {
					        
					        var data = JSON.parse(data);
					        
					        if(data.error) {
						        
						      	bootbox.alert(data.error);
						        
					        } else {
						        
						        if(data.status) {
							        
							        window.location = url + "/" + back_url + "&action=file_deleted"; 
							        
							        
						        }
						        
					        }
					        
					    }
					});
				    
			    }
			    
		    }
		});

	});
	
	$(".btn-edit-title").click(function(e) {
		
		e.preventDefault();
		
		if($(this).hasClass("editing")) {
			
			savePhotoTitle();
			$(this).removeClass("editing");
			
		} else {
		
			$("html, body").animate({scrollTop : 0}, 800);
			
			$(".photo_title").hide();
			$(".photo_edit_title").fadeIn();
			
			$(this).addClass("editing");
		
		}
		
	});
	
	$(".btn-share").click(function(e) {
		
		e.preventDefault();
		
	    $('html,body').animate({scrollTop: $("#shareit").offset().top},'slow');
		
	});
	
	
	$(".input_photo_title").keyup(function(e) {
		
	    if(e.keyCode == 13)
	    {
		
			savePhotoTitle();
	        
	    }
	});
	
	function savePhotoTitle() {
		var title = $(".input_photo_title").val();
		var photo_id = $(".input_photo_title").attr("data-id");

        
        $.ajax('ajax/save_title.php', {
	        method: "POST",
	        data: {
		        title: title,
		        photo_id: photo_id
	        },
	        success(data) {
		        
		        var data = JSON.parse(data);
		        
		        if(data.error) {
			        
			        bootbox.alert(data.error);
			        $(".photo_title").fadeIn();
					$(".photo_edit_title").hide();
			        
		        } else {
			        
			        if(data.status) {
				        
				        $(".photo_title").html(title);
				        $(".photo_title").fadeIn();
						$(".photo_edit_title").hide();
				        
			        }
			        
		        }
		        
		    }
		});
	}
	
	
	$(document).on("click", ".btn-switch-community", function(e) {
		
		e.preventDefault();
		
		var photo_id = $(this).attr("data-id");
		
		$.ajax('ajax/save_switch_community.php', {
	        method: "POST",
	        data: {
		        photo_id: photo_id
	        },
	        success(data) {
		        
		        var data = JSON.parse(data);
		        
		        if(data.error) {
			        
			        alert(data.error);
			        
		        } else {
			        
			        if(data.status) {
				        
				        if(data.new_in_community == 1) {
					        
					        $(".btn-switch-community").removeClass("btn-primary").addClass("btn-danger");
					        $(".btn-switch-community").html('<i class="fas fa-minus"></i> Hide from Community');
					        
					        $(".alert-community").hide();
					        
				        } else {
					        
					        $(".btn-switch-community").removeClass("btn-danger").addClass("btn-primary");
					        $(".btn-switch-community").html('<i class="fas fa-plus"></i> Add to Community');
					        
					        $(".alert-community").fadeIn();
					        
				        }
				        
			        }
			        
		        }
		        
		    }
		});
		
	});
	
});