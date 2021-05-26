$(document).ready(function() {
	
	$(document).on("click", "input[name=auto_deletion]", function(e) {
				
		if($(this).is(":checked")) {
			$(".auto_deletion_container").fadeIn();
		} else {
			$(".auto_deletion_container").hide();
		}
		
	});
	
});