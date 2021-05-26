$(document).ready(function() {
	
	var all_loaded = true;
	
	// Ajax Load More
	$(window).scroll(function() {
		
	    if((($(window).scrollTop() + $(window).innerHeight()) >= $(document).height()-100) && all_loaded) {
		    
		    all_loaded = false;
		    
		    var load_toast = $.toast({
			  text : "<i class='fas fa-spinner fa-spin'></i> Loading more files...",
			  hideAfter : false,
			  position: 'bottom-center'
			})
		    
		    var last_photo_id = $(".card-photo").last().attr("data-id");
	        
	        $.ajax('ajax/load_more_admin_photos', {
		        method: "POST",
		        data: {
			        last_photo_id : last_photo_id
		        },
		        success(data) {
			        var data = JSON.parse(data);
			        
			        if(data.status && data.status == 1) {
				        				        
				        var photos = data.photos;
				        
				        if(photos.length == 0) {
					        
				        } else {
					        var photos_html = "";
				        
					        $.each(photos, function(index, photo) {
						        
						        var is_picture = photo.is_picture;
						        						        
						        
						        photos_html += '<div class="col-md-2 card-photo" data-id="' + photo.id + '">';
								photos_html += '<div class="card shadow">';
								photos_html += '<div class="card-photo-container">';
								
								if(is_picture == 1) {
									photos_html += '<a href="' + frontend_url + '/photo-details?id=' + photo.unique_id + '"><img src="' + url + '/files/'+jsEncode(photo.url) + '" class="card-img-top" alt=""></a>';								
								} else {
									photos_html += '<a href="' + frontend_url + '/photo-details?id=' + photo.unique_id + '"><img class="card-img-top" src="img/file_2.png" /></a>';								
								}
								
								photos_html += '</div>';
								photos_html += '<div class="card-body">';
								photos_html += '<h5 class="card-title">' + photo.title + '</h5>';
								photos_html += '<h5 class="photo_date">' + photo.created_at + '<h5>';
								
								if(photo.is_protected == 1) {
									photos_html += '<h6><span class="badge badge-secondary badge-privacy">Private</span></h6>';
								} else if(photo.is_protected == 2) {
									photos_html += '<h6><span class="badge badge-warning badge-privacy">Password Protected</span></h6>';
								} else {
									photos_html += '<h6><span class="badge badge-success badge-privacy">Public</span></h6>';
								}
								
								photos_html += '<div class="row no-gutters">';
											    
								photos_html += '<div class="col-md-12">';
								photos_html += '<a href="#" class="btn btn-danger btn-block btn-delete"><i class="fas fa-times"></i> Delete</a>';
								photos_html += '</div>';
								photos_html += '</div>';
								photos_html += '</div>';
								photos_html += '</div>';
								photos_html += '</div>';
														        
					        });
								
							$(".grid").append(photos_html);
							load_toast.reset();
					        
				        }
					        					        
					        all_loaded = true;
				        
			        }
			        
			    }
			    
			});
	        
	    }
	});
	
	// Delete a photo action
	$(document).on("click", ".btn-delete", function(e) {
		
		e.preventDefault();
		
		var photo_id = $(this).closest(".card-photo").attr("data-id");
		
		var confirm = bootbox.confirm({
			buttons: {
				confirm : {
				    label: 'Yes, delete',
				    callback: function(result) { 
				    }
				}
			},
			message: "Are you sure you want to delete this file?",
		    callback: function (result) {
			    
			    if(result) {
				    
				    $.ajax('ajax/delete_photo', {
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
							        
							        confirm.modal("hide");
							        $(".card-photo[data-id=" + photo_id + "]").remove();
							        
							        setTimeout(function () {
								    	$(".grid").masonry('reloadItems');
										$(".grid").masonry();
								  	}, 100);
							        
						        }
						        
					        }
					        
					    }
					});
				    
			    }
			    
		    }
		});

	});
	function jsEncode(param) {
		if(param == null || param == "" ) return "";
		let re = /\//gi;
		param = param.replace(re, '>');
		return param;
	}
});
