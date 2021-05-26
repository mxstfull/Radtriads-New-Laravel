$(document).ready(function() {
	
	var all_loaded = false;
	
	if($(".card-photo").length) {
		$(".grid").imagesLoaded().done( function( instance ) {

			var grid = $(".grid").masonry({
				itemSelector: '.card-photo',
				isAnimated: true
			});
			
			$(".card-photo").fadeIn();
			
			grid.masonry('reloadItems');
			grid.masonry();

		});
	}
	
	// Ajax Load More
	$(window).scroll(function() {
		
	    if(($(window).scrollTop() == $(document).height() - $(window).height()) && !all_loaded) {
		    
		    var last_photo_id = $(".card-photo").last().attr("data-id");
	        
	        $.ajax('ajax/load_more_my_photos.php', {
		        method: "POST",
		        data: {
			        last_photo_id : last_photo_id
		        },
		        success(data) {
			        
			        var data = JSON.parse(data);
			        
			        if(data.status && data.status == 1) {
				        				        
				        var photos = data.photos;
				        
				        if(photos.length == 0) {
					        					        
					        all_loaded = true;
					        
				        } else {
					        var photos_html = "";
				        
					        $.each(photos, function(index, photo) {
						        						        
						        
						        photos_html += '<div class="col-md-4 card-photo" data-id="' + photo.id + '">';
								photos_html += '<div class="card shadow">';
								photos_html += '<div class="card-photo-container">';
								photos_html += '<a href="' + url + '/photo.php?id=' + photo.unique_id + '"><img src="' + url + '/' + photo.url + '" class="card-img-top" alt=""></a>';								
								photos_html += '</div>';
								photos_html += '<div class="card-body">';
								photos_html += '<h5 class="card-title">' + photo.title + '</h5>';
								photos_html += '<div class="row no-gutters">';
								photos_html += '<div class="col-md-6">';
								photos_html += '<a href="' + url + '/photo.php?id=' + photo.unique_id + '" class="btn btn-primary btn-block"><i class="fas fa-pencil-alt"></i> View / Edit</a>';
								photos_html += '</div>';
											    
								photos_html += '<div class="col-md-6">';
								photos_html += '<a href="#" class="btn btn-danger btn-block btn-delete"><i class="fas fa-times"></i> Delete</a>';
								photos_html += '</div>';
								photos_html += '</div>';
								photos_html += '</div>';
								photos_html += '</div>';
								photos_html += '</div>';
														        
					        });
								
							$(".grid").append(photos_html);
							setTimeout(function () {
					        	$(".card-photo").show();
						    	$(".grid").masonry('reloadItems');
								$(".grid").masonry();
						  	}, 400);
					        
				        }
				        
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
	
});