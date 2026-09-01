// jQuery(function($){
// 	var canBeLoaded = true, // this param allows to initiate the AJAX call only if necessary
// 	    bottomOffset = 2000; // the distance (in px) from the page bottom when you want to load more posts
 
// 	$(window).scroll(function(){
// 		$('.new-loaded').each(function(){
// 			$(this).removeClass('new-loaded');
// 		})
// 		var data = {
// 			'action': 'loadmore',
// 			'query': my_load_params.posts,
// 			'page' : my_load_params.current_page,
// 			// 'cat' : $('.selected').child().attr('class')
// 		};
// 		if( $(document).scrollTop() > ( $(document).height() - bottomOffset ) && canBeLoaded == true ){
// 			$.ajax({
// 				url : my_load_params.ajaxurl,
// 				data:data,
// 				type:'POST',
// 				beforeSend: function( xhr ){
// 					// you can also add your own preloader here
// 					// you see, the AJAX call is in process, we shouldn't run it again until complete
// 					canBeLoaded = false; 
// 				},
// 				success:function(data){
// 					if( data ) {
// 						$('.ajax-call').last('.ajax-call').after( data ); // where to insert posts
// 						canBeLoaded = true; // the ajax is completed, now we can run it again
// 						my_load_params.current_page++;
// 						TweenMax.from(".new-loaded", .7, {
// 							opacity: 0,
// 							y: 50,
// 							delay:.3
// 						});
// 					}
// 				}
// 			});
// 		}
// 	});
// });

// $(function() {
// 	$('.lazy').show().lazy({
// 		enableThrottle: true,
// 		throttle: 250
// 	});
// });