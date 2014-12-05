$('#nav-profile').addClass('active');

var userId = $('#profile-userid').val();
var requestButtonSel = '#btn-profile-request';
var editButtonSel = '#btn-profile-edit';
var doneEditingButtonSel = '#btn-profile-done-editing';

$(requestButtonSel).click(function() {
	console.log('Sending request to', userId);
	var request = $.ajax({
	  url: "actions/request.php",
	  type: "POST",
	  data: { 
			user : userId,
		},
	  dataType: "json",
		beforeSend: function() {
			$(requestButtonSel).addClass('disabled');
		}
	});
 
	request.done(function( result ) {
		location.reload(true);
	});
 
	request.fail(function( jqXHR, textStatus ) {
	  alert( "Request failed: " + textStatus );
	});
	
	return false;
});

$(editButtonSel).click(function() {
	$(editButtonSel).addClass('hidden');
	$(doneEditingButtonSel).removeClass('hidden');
	
	// stub
	
	return false;
});

$(doneEditingButtonSel).click(function() {
	$(editButtonSel).removeClass('hidden');
	$(doneEditingButtonSel).addClass('hidden');
	
	// stub
	
	return false;
});