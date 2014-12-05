$('#nav-logout').click(function() {
	var request = $.ajax({
	  url: "actions/logout.php",
	  type: "POST",
		dataType: 'json'
	});
 
	request.done(function( result ) {
		if (result.success === true) {
			location.reload(true);
		} else {
			console.log('Unable to logout? Is this Sword Art Online?');
		}
	});
 
	request.fail(function( jqXHR, textStatus ) {
	  alert( "Request failed: " + textStatus );
	});
});