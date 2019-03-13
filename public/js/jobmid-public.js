(function( $ ) {
	'use strict';

	var jobMidCountDown = function(i){
		var int = setInterval(function(){
			if(i > 0)
			{ $('#jobmid-countdown').html(i);}

			i--;

			if(1 > i) {
				clearInterval(int);
				$('.jobmid-message-holder .jobmid-message-content .jobmid-button').fadeIn('fast');
				window.location.href = jobMidRedirect;
			}
		},1000);
	}

	$(document).ready(function(){
		jobMidCountDown(5)
	});

})( jQuery );
