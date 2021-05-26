$(document).ready(function() {
	
	$(".monthly").click(function() {
		
		$(".yearly_prices").hide();
		$(".monthly_prices").show();
		
	});
	
	$(".yearly").click(function() {
		
		$(".yearly_prices").show();
		$(".monthly_prices").hide();
		
	});
	
	
	
});