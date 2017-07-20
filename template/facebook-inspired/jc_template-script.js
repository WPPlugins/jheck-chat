jQuery(document).ready(function() {   
	var jc_wrapper = jQuery('.jheck_chat_main_wrapper');
	var toggle_button = jc_wrapper.find('.jc_header button');

	jQuery(toggle_button).on('click', function(){
		jQuery(jc_wrapper).find('.jc_hidden_div').slideToggle();
	})	
});