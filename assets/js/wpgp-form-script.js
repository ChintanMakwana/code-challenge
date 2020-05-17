/* global params */
(function($, params){

	jQuery(document).ready(function($){
        init();
    });

	/**
	 * Init other JS functions
	 */
    function init(){
		// Rules for the fields
		var rules = {
				'wpgp_post_title' : {
					required: true
				},
				'wpgp_post_type' : {
					required: true
				},
				'wpgp_post_content' : {
					required: true
				},
				'wpgp_post_excerpt' : {
					required: true
				},
				'wpgp_post_thumbnail' : {
					required: true,
					accept: "jpg,jpeg,png,gif"
				}
			};

		// Messages for the fields
		var messages = {
			    'wpgp_post_title' : {
					required: params.messages.post_title.required
				},
				'wpgp_post_type' : {
					required: params.messages.post_type.required
				},
				'wpgp_post_content' : {
					required: params.messages.post_content.required
				},
				'wpgp_post_excerpt' : {
					required: params.messages.post_excerpt.required
				},
				'wpgp_post_thumbnail' : {
					required: params.messages.post_thumbnail.required,
					accept: params.messages.post_thumbnail.accept
				}
			};

		// Validate the form
		var formData = '';

		jQuery('.wpgp-form').each(function( index ){
			var $this = $(this); // current form
			var $response_div = $( '.wpgp-response', $this );

			$this.submit(function(event){
				
				formData = new FormData(this);
				$response_div.find('p').removeClass().html('');

			}).validate({
				ignore: [],
				rules: rules,
				messages: messages,
				submitHandler: function(form) {

					tinyMCE.triggerSave();

				    $.ajax({
						url: params.ajaxurl,
						method: 'POST',
						data: formData,
						contentType: false,
						processData: false,
						beforeSend: function(){
							$this.find('.wpgp-loader').show();
						},
						success: function(response){							
							if(response.success){
								$response_div.find('p').addClass('success').html(response.data.msg);
								// reset the form fields
								$this.trigger('reset');
							} else {
								$response_div.find('p').addClass('error').html(response.data.msg);
							}
						},
						error: function(jqXHR, textStatus, errorThrown){
							console.log( textStatus );
						},
						complete: function(){
							$this.find('.wpgp-loader').hide();
						},
					});
				}
			});
		});
	}

	/**
	 * Callback function for wp_editor instance
	 * @param  object editor Instance of the editor 
	 */
	window.wpgp_editor_callback = function(editor) {
        editor.on("blur", function(){
        	tinyMCE.triggerSave();     
        });
    }

})(jQuery, params)