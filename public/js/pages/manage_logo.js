$(document).ready(function() {
	
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('.adm-website-logo').attr('src', e.target.result);
			}
		
			reader.readAsDataURL(input.files[0]);
		}
	}
	
	$("#file_logo").change(function() {
		readURL(this);
	});
	
	$(".adm-website-logo").click(function(e) {
		
		e.preventDefault();
		
		$("#file_logo").trigger("click");
		
	});
	
});