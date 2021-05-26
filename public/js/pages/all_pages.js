$(document).ready(function() {
	
	show_10_days_left_popup = parseInt(show_10_days_left_popup);
	
	if(show_10_days_left_popup == 1) {
		$("#ten_days_left_modal").modal("show");
		
		$('#ten_days_left_modal').on('shown.bs.modal', function () {
			
			// Send an ajax request to update the user account and prevent the popup to be shown again
			$.ajax('ajax/disable_10_days_popup.php', {
		        method: "POST",
		        dataType: "json",
		        success(data) {
			        
					console.log("POPUP DISABLED");
			        
		        }
		    });
			
			
		});
	}
	
	$(document).on("click", ".mobile-menu a", function(e) {
		
		e.preventDefault();
		
		$(".menu-right").slideToggle();
		
		if($(".menu-right").hasClass("opened")) {
			
			$(".menu-right").removeClass("opened");
			
			$(this).html("<i class='fas fa-bars'></i> Menu");
			
		} else {
			
			$(".menu-right").addClass("opened");
			
			$(this).html("<i class='fas fa-times'></i> Close");
			
		}
			
	});
	
	
});