function formatPostResults ( post ) {
	
	var post_title = formatPostTitle( post );
	
	if ( post.loading ) {
		return post.text;
	}
	
    if ( typeof post_title === 'undefined' ) {
		post_title = 'Page without Title';
    }
	
	var markup = "<div class='select2-result-post clearfix'>" +
	"<div class='select2-result-post__meta'>" +
	  "<div class='select2-result-post__title'>" + post.id + " : " + post_title + "</div>";

	markup += "</div></div>";

	return markup;
}

function formatPostTitle (post) {
	return post.post_title || post.text;
}

jQuery( function ( $ ) {
	
	if ( $('#divicomingsoon_settings').length ) {
		
		$('select[name="dcs_settings[dcs_redirectto]"]').select2({
			dropdownParent: $('#divicomingsoon_settings'),
			width: '100%',
			theme: "bootstrap",
			ajax: {
				url: ajaxurl,
				dataType: 'json',
				delay: 250,
				method: 'POST',
				data: function (params) {
				  return {
					action: 'ajax_dcs_listposts',
					q: params.term,
					page: params.page,
					json: 1
				  };
				},
				processResults: function (data, params) {
				  params.page = params.page || 1;
				  
				  return {
					results: data.items,
					pagination: {
					  more: (params.page * 7) < data.total_count
					}
				  };
				},
				cache: true
			},
			allowClear: true,
			minimumInputLength: 1,
			escapeMarkup: function (markup) { return markup; },
			templateResult: formatPostResults,
			templateSelection: formatPostTitle
		});
	}
});