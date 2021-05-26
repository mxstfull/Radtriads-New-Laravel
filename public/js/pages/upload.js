Dropzone.autoDiscover = false;

$(document).ready(function() {
	
	var uploaded_photo_id;
	var uploaded_file_unique_id;
	var uploaded_photo_url;
	var cropper;
	var uploaded_mime_type;
	var localstream;
	
	if(limit_nb_files_reached == 0 && limit_diskspace_reached == 0) {
			
		var myDropzone = new Dropzone(".card-dashboard", { // replace by "body" for all page upload (can break the preview)
			url: "ajax/upload_file.php",
			//acceptedFiles: ".xls,.xlsx,.tiff,.tif,.jpeg,.jpg,.png,.webp,.gif,.mp4,.mov,.swf,.flv,.mp3,.wav,.txt,.rtf,.html,.php,.css,.xml,.json,.pdf,.word,.java,.dat,.exe,.zip,.psd",
			autoProcessQueue: true,
			maxFilesize: max_upload_size,
			parallelUploads: 1,
			timeout: 999999,
			uploadMultiple: true,
			previewTemplate: document.getElementById('preview-template').innerHTML,
			dictDefaultMessage: "Drop your files here",
			params: {
				upload_path: $("input[name=upload_path]").val()
			},
			/*
			uploadprogress: function(file, progress, bytesSent) {
				
			    if (file.previewElement) {
				    
				    var progress_int = parseInt(progress);
				    
				    if(progress_int < 100) {
			        	$(".progress-upload .progress-bar").html(progress_int + "%").attr("aria-valuenow", progress_int).css("width", progress_int + "%");
					} else {
			        	$(".progress-upload .progress-bar").html("Processing...").attr("aria-valuenow", progress_int).css("width", progress_int + "%").removeClass("bg-primary").addClass("bg-success");
			        	
					}
			    }
			},
			*/
			init: function() {
				this.on("addedfiles", function(e) {
					
			    	if(this.files.length > max_files_upload) {
				    	this.removeAllFiles();
						$(".alert-error-upload").html("Oops. Please pick a maximum of <b>" + max_files_upload + "</b> file.").show();
			    	} else {
				    	
				    	startUploadProcess();
				    	
			    	}
					
				});
			}
		});
		
		document.onpaste = function(event) {
			var items = (event.clipboardData || event.originalEvent.clipboardData).items;
			for (index in items) {
				var item = items[index];
					if (item.kind === 'file' && item.type.indexOf("image") >= 0) {
					// adds the file to your dropzone instance
					myDropzone.addFile(item.getAsFile());
					startUploadProcess();
				}
			}
		}
		
		myDropzone.on("success", function(data) {
			
			var msgEl = $(data.previewTemplate).find('.dz-success-mark');
			msgEl.attr("style", "opacity: 1 !important;");
			
			
			if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
		
				var data = JSON.parse(data.xhr.response);
						
				console.log(data);

				if(data.error && data.error != "") {
					
					$.toast({
					    heading: 'Error',
					    text: data.error,
					    showHideTransition: 'slide',
					    icon: 'error',
					    hideAfter: 8000,
					    position: "bottom-center"
					});
					
				} else if(data.status && data.status == 1 && !data.multiple_upload) {
					
					uploaded_file_unique_id = data.file_unique_id;
					
					if(data.redirect_dir) {
						window.location = "dashboard.php?file_id=" + uploaded_file_unique_id + "&action=uploaded&path=" + data.redirect_dir + "/";
					} else {
						window.location = "dashboard.php?file_id=" + uploaded_file_unique_id + "&action=uploaded";
					}
					
				} else if(data.status && data.status == 1 && data.multiple_upload == 1) {			
					
					if(data.redirect_dir) {
						window.location = "dashboard.php?multiple_uploaded=true&nb=" + data.nb_uploaded + "&action=uploaded&path=" + data.redirect_dir + "/";
					} else {
						window.location = "dashboard.php?multiple_uploaded=true&nb=" + data.nb_uploaded + "&action=uploaded";
					}
					
				} else {
					
					$.toast({
					    heading: 'Error',
					    text: "An error occurred. Please try to upload again.",
					    showHideTransition: 'slide',
					    icon: 'error',
					    hideAfter: 8000,
					    position: "bottom-center"
					});
					
				}
	
			}
			
		});
		
		myDropzone.on("error", function(data) {
			
			console.log(data);
				
			var msgEl = $(data.previewTemplate).find('.dz-error-message');
			msgEl.text("An error occurred while uploading this image...");
			
		});
	
		// Handle upload button click
		$(".btn-upload").click(function(e) {
			
			e.preventDefault();
			
			myDropzone.hiddenFileInput.click()
			
		});
	
	}
	
	// Skip cropping the photo
	$(".btn-skip-crop").click(function(e) {
		
		e.preventDefault();
		
		window.location = "photo.php?id=" + uploaded_file_unique_id + "&action=uploaded";
		
	});
	
	// Handle crop button click
	$(".btn-crop").click(function(e) {
	
		e.preventDefault();
		
		var that = $(this);
		var that_content = that.html();
		
		cropper.getCroppedCanvas().toBlob((blob) => {

		    const formData = new FormData();
		
		    // Pass the image file name as the third parameter if necessary.
		    formData.append('cropped_img', blob);
		    formData.append('uploaded_photo_id', uploaded_photo_id);
		    
		    that.addClass("disabled");
		    that.html('<i class="fas fa-cog fa-spin"></i> Cropping');
		
		    // Use `jQuery.ajax` method for example
		    $.ajax('ajax/upload_cropped_photo.php', {
		        method: "POST",
		        data: formData,
		        processData: false,
		        contentType: false,
		        success(data) {
			        
			        data = JSON.parse(data);
			        
			        that.removeClass("disabled");
			        that.html(that_content);
			        
			        if(data.status && data.status == 1) {
				        
				        window.location = "photo.php?id=" + uploaded_file_unique_id + "&action=uploaded";
				        
			        } else {
				        
				        alert("Error while uploading the cropped version!");
				        
			        }
			        
		        },
		        error() {
			        alert("Error while uploading the cropped version!");
		        },
		    });
		}, uploaded_mime_type);
		
		
	});
	
	$(".btn-webcam").click(function(e) {
		
		e.preventDefault();
		
		var video = document.getElementById('video-webcam');
		
		// -- If we launch the webcam
		if(!$(this).hasClass("btn-capture")) {
			
			// Get access to the camera!
			if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {

			    navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
			        
			        $("#video-webcam").fadeIn();
			        $(".btn-webcam").addClass("btn-capture").removeClass("btn-primary").addClass("btn-danger").html("<i class='fas fa-camera'></i> Capture & Upload");
			        
			        localstream = stream;
			        video.srcObject = stream;
			        video.play();
			        
			        $('html,body').animate({scrollTop: $("#video-webcam").offset().top},'slow');
			    })
				.catch(function(err) {
					alert("Please enable access to your camera!");
				});
			}
		
		} 
		// -- If we capture the photo
		else {
			
			var draw = document.createElement("canvas");
			draw.width = video.videoWidth;
			draw.height = video.videoHeight;
			var context2D = draw.getContext("2d");
			context2D.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
		
		    draw.toBlob(function(blob) {
			    
			    $(".upload-container").hide();
				$(".uploading-container").fadeIn();
			    				
				const formData = new FormData();
			
			    // Pass the image file name as the third parameter if necessary.
			    formData.append('webcam_img', blob);
			    
			    $.ajax('ajax/upload_webcam_photo.php', {
			        method: "POST",
			        data: formData,
			        processData: false,
			        contentType: false,
			        success(data) {
				        
				        data = JSON.parse(data);
				        
				        if(data.status && data.status == 1) {
					        
					        uploaded_photo_url = data.photo_url;
							uploaded_photo_id = data.photo_id;
							uploaded_file_unique_id = data.photo_unique_id;
							uploaded_mime_type = data.photo_mime_type;
							
							$(".uploading-container").hide();
							$(".cropping-container .crop-upload-img").attr("src", uploaded_photo_url);
										
							cropper = new Cropper(document.getElementById("crop-upload-img"));
							
							$(".cropping-container").fadeIn();
					        
				        } else {
					        
					        alert("Error while uploading the webcam photo!");
					        
				        }
				        
				        stopWebcam();
				        
			        },
			        error() {
				        alert("Error while uploading the cropped version!");
			        },
			    });
		    });
		}
		
	});
		

	// Check if the user has a webcam...
	detectWebcam(function(hasWebcam) {
		
		if(hasWebcam) {
			
			$(".webcam_capture_container").show();
			
		}
		
	});
	
	// Stop the webcam after the upload is made...
	function stopWebcam() {
		
		var videoEl = document.getElementById('video-webcam');

		videoEl.pause();
		videoEl.src = "";
		localstream.getTracks()[0].stop();
		
	}
	
	function startUploadProcess() {
		
		$(".dropzone").hide();
		
		setTimeout(function() {
			myDropzone.processQueue();
		}, 1000);
		
		
		/*
		$(".upload-container").hide();
		$(".uploading-container").fadeIn();
		
		setTimeout(function() {
			
			console.log("test2");
			
			// Process Ajax Upload
			myDropzone.processQueue();
			
		}, 1000);
		*/
		
	}
	
});

function detectWebcam(callback) {
	let md = navigator.mediaDevices;
	if (!md || !md.enumerateDevices) return callback(false);
	md.enumerateDevices().then(devices => {
		callback(devices.some(device => 'videoinput' === device.kind));
	});
}