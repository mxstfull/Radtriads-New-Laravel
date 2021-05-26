$(document).ready(function() {
	
	$(".btn-edit-user").click(function(e) {
		
		e.preventDefault();
		
		var user_id = $(this).attr("data-id");
		var username = $(this).attr("data-username");
		var email = $(this).attr("data-email");
		var rank = $(this).attr("data-rank");
		
		$(".user-edit-username").val(username);
		$(".user-edit-email").val(email);
		$(".user-edit-rank").val(rank);
		
		$("#edit-user-modal").find(".btn-confirm-edit").attr("data-id", user_id);
		$("#edit-user-modal").modal("show");
		
	});
	
	$(".btn-confirm-edit").click(function(e) {
		
		e.preventDefault();
		
		var user_id = $(this).attr("data-id");
		var new_username = $(".user-edit-username").val();
		var new_email = $(".user-edit-email").val();
		var new_rank = $(".user-edit-rank").val();
		
		var that = $(this);
		that.prop("disabled", true);
		
		$.ajax('ajax/edit_user.php', {
	        method: "POST",
	        data: {
		        user_id : user_id,
		        username : new_username,
		        email : new_email,
		        rank : new_rank
	        },
	        success(data) {
				that.prop("disabled", false);
		        
		        var data = JSON.parse(data);
					        
		        if(data.error) {
			        
			        bootbox.alert(data.error);
			        
		        } else {
			     
			    	if(data.status && data.status == 1) {  
				    
				    	that.html("<i class='fas fa-check'></i> User Updated").addClass("btn-success").removeClass("btn-primary");	
				    	$(".user-line[data-id=" + user_id + "] td:first-child").html(new_username);
				    	$(".user-line[data-id=" + user_id + "] td:nth-child(2)").html(new_email);
				    	$(".btn-edit-user[data-id=" + user_id + "]").attr("data-username", new_username);
				    	$(".btn-edit-user[data-id=" + user_id + "]").attr("data-email", new_email);
				    	$(".btn-edit-user[data-id=" + user_id + "]").attr("data-rank", new_rank);
				    	
				    	setTimeout(function() {
					    						    	
					    	that.html("Update Account").addClass("btn-primary").removeClass("btn-success");	
					    	$("#edit-user-modal").modal("hide");
					    	
				    	}, 3000);
				    		
					}
			        
			    }
		        
		    }
		    
		});
		
		
	});
	
	$(".btn-delete").click(function(e) {
		
		e.preventDefault();
		
		var user_id = $(this).attr("data-id");
		
		var confirm = bootbox.confirm({
			buttons: {
				confirm : {
				    label: 'Yes, delete',
				    callback: function(result) { 
				    }
				}
			},
			message: "Are you sure you want to delete this user and his photos?",
		    callback: function (result) {
			    
			    if(result) {
				    
				    $.ajax('ajax/delete_user', {
				        method: "POST",
				        data: {
					        user_id : user_id
				        },
				        success(data) {
					        
					        var data = JSON.parse(data);
					        
					        if(data.error) {
						        
						        bootbox.alert(data.error);
						        
					        } else {
						        
						        if(data.status && data.status == 1) {
							        
							        $(".user-line[data-id=" + user_id + "]").remove();
							          
						        }
						        
					        }
					        
					    }
					});
				    
			    }
			    
		    }
		});
		
	});
	
});