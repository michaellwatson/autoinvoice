jQuery(document).ready(function() {
	var users = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: WPURLS.siteurl+'/wp-admin/admin-ajax.php?action=username_search&query=%QUERY'
	});
			 
	users.initialize();
	 
	jQuery('.username_search').typeahead(null, {
		name: 'users',
		displayKey: 'value',
		source: users.ttAdapter()
	}).on('typeahead:selected', function($e, datum){

		window.location = WPURLS.siteurl+'/performer/'+datum["value"];

	});
});