$(document).ready(function() {


	$(".btn-delete").click(function(e) {
		
		e.preventDefault();
		
		var page_id = $(this).attr("data-id");
		var allow_delete = $(this).attr("data-deletion");
		
		if(allow_delete == 0) {
			alert("You are not allowed to delete this page.")
		} else {
			
			var confirm = bootbox.confirm({
				buttons: {
					confirm : {
					    label: 'Yes, delete',
					    callback: function(result) { 
					    }
					}
				},
				message: "Are you sure you want to delete this page?",
			    callback: function (result) {
				    
				    if(result) {
					    
					    $.ajax('ajax/delete_page', {
					        method: "POST",
					        data: {
						        page_id : page_id
					        },
					        success(data) {
						        
						        var data = JSON.parse(data);
						        
						        if(data.error) {
							        
							        bootbox.alert(data.error);
							        
						        } else {
							        
							        if(data.status && data.status == 1) {
								        $(".page-line[data-id=" + page_id + "]").remove();
							        }
							        
						        }
						        
						    }
						});
					    
				    }
				    
			    }
			});
		
		}
		
	});
	
});