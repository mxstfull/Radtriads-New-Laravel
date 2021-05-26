$(document).ready(function() {
	
	$(".btn-delete-account").click(function(e) {
	
		e.preventDefault();
				
		var confirm = bootbox.confirm({
			buttons: {
				confirm : {
				    label: 'Yes, delete',
				    callback: function(result) { 
				    }
				}
			},
			message: "Are you sure you want to delete your account and all associated data?",
		    callback: function (result) {
			    
			    if(result) {
				    
				    window.location = "delete-account.php";
				    
				}
				
			}
		});
		
	});
	
	$(".btn-update-privacy").click(function(e) {
		
		e.preventDefault();
		
		var confirm = bootbox.confirm({
			buttons: {
				confirm : {
				    label: 'Yes, update privacy',
				    callback: function(result) { 
				    }
				}
			},
			message: "Updating privacy for your account will reset <b><u>all of your albums privacy</u></b> according to the option you choose here. Do you want to continue?",
		    callback: function (result) {
			    
			    if(result) {
				    
				    $(".user_update_privacy").submit();
				    
				}
				
			}
		});
		
	});
	
});