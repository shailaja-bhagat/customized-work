function validateform() {
	var referrer = document.referrer;
	var iChars = "!@#$%^&*()+=-[]\\\';,./{}|\":<>?";
	
	let validationError = 0;

	jQuery('#eacf_contact_form input').each( function(){
		
		let input = jQuery(this);
		let inputVal = input.val();
		let inputId = input.attr('id');
		
		if(input.attr('type')=="text" || input.attr('type')=="email") {
			if(inputVal.trim()=="") {
				validationError++;
				jQuery(this).closest('input').addClass('validation_error');
				jQuery('#'+inputId+'_error').removeAttr('style');
			} 
			else{
				if(input.attr('type')=='email') {
					jQuery('#'+inputId+'_error').css("display", "none");
					var contact_form_email = jQuery('#contact_form_email').val();
					var filter = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				
					if (!filter.test(contact_form_email)) {
						validationError++;
						jQuery('#errorValidcontact_form_email').show();
						jQuery(this).closest('input').addClass('validation_error');
					} else {
						jQuery('#errorValidcontact_form_email').hide();
						jQuery(this).closest('input').removeClass('validation_error');
					}
				}
				else{
					jQuery(this).closest('input').removeClass('validation_error');
					jQuery('#'+inputId+'_error').css("display", "none");
				}							
			} 
		}	            
	});

		
	if (jQuery('#contact_form_enquiry').val() == "") {
		jQuery('#contact_form_enquiry').addClass('validation_error');
		jQuery('#contact_form_enquiry_error').show();
		return false;
	} else {
		jQuery('#contact_form_enquiry_error').hide();
		jQuery('#contact_form_enquiry').removeClass('validation_error');
	}	
	
	if(validationError > 0) {
		return false;
	}
	else{
		
		jQuery('#contact_form_submit').addClass('loading');
		jQuery('.contact_form_div').addClass('loading_container');
		var data = jQuery("#eacf_contact_form").serialize();
		var redirect_page_id = jQuery('#redirect_page_id').val();
		jQuery.ajax({
			url		: contact.ajax_url,
			type	: 'post',
			data	: {
				action      : 'eacf_form_data_process',
				form_data   : data
			},
			success	: function(result){
				if(result == 'redirect_please') {
					window.location.href = redirect_page_id;
				} else {
					jQuery('.contact_form_container #result').html(result).fadeIn(500);
				}
			}
		});
	}
}

jQuery(document).ready(function(){

	jQuery("#contact_form_fname").keypress(function(event){
		var inputValue = event.which;
		// allow letters and whitespaces only.
		if(!(inputValue >= 65 && inputValue <= 120) && (inputValue != 32 && inputValue != 0)) { 
			event.preventDefault(); 
		}
	});

	jQuery("#contact_form_phone").keypress(function(event){
		var inputValue = event.which;
		// allow numbers only.
		if(!(inputValue >= 48 && inputValue <= 57)) { 
			event.preventDefault(); 
		}
	});

	jQuery('#eacf_contact_form').submit(function (e) {
		e.preventDefault();
		
	});

});