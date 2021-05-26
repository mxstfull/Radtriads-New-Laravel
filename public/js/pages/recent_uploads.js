$(document).ready(function() {
	
	$('.lazy').Lazy({
        scrollDirection: 'vertical',
        effect: 'fadeIn',
        visibleOnly: true,
        onError: function(element) {
            console.log('error loading ' + element.data('src'));
        },
        afterLoad: function() {
	        console.log("all loaded");
	        $("img.lazy").attr("style", "background-image: none;");
        }
    });
    
    var clipboard = new ClipboardJS('.btn-copy');
	
	clipboard.on('success', function(e) {
		

	    $(e.trigger).html("Copied!").prop("disabled", true);
	    
	    setTimeout(function() {
		    $(e.trigger).html("Copy").prop("disabled", false);
	    }, 2000);
	
	    e.clearSelection();
	});
	
});