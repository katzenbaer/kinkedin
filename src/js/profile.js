$('#nav-profile').addClass('active');

var userId = $('#profile-userid').val();
var requestButtonSel = '#btn-profile-request';
var editButtonSel = '#btn-profile-edit';
var doneEditingButtonSel = '#btn-profile-done-editing';

$(requestButtonSel).click(function() {
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

var attributes = ['title', 'aliases', 'website', 'twitter', 'location', 'dob', 'debut', 
	'measurements', 'height', 'eyescolor', 'haircolor', 'race', 'ethnicity'];

$(editButtonSel).click(function() {
	$(editButtonSel).addClass('hidden');
	$(doneEditingButtonSel).removeClass('hidden');
	
	$.each(attributes, function(i, e) {
		var valueSel = '#profile-' + e;
		var labelSel = valueSel + '-label';
		var inputSel = valueSel + '-input';
		
		$(labelSel).addClass('hidden');
		$(valueSel).addClass('hidden');
		if ($(inputSel).is('input')) {
			$(inputSel).val($(valueSel).html());
		} else {
			$(inputSel + ' > input').val($(valueSel).html());
		}
		$(inputSel).removeClass('hidden');
	});
	
	return false;
});

$(doneEditingButtonSel).click(function() {	
	var form = {};
	$.each(attributes, function(i, e) {
		var valueSel = '#profile-' + e;
		var labelSel = valueSel + '-label';
		var inputSel = valueSel + '-input';
		
		$(labelSel).removeClass('hidden');
		$(valueSel).removeClass('hidden');
		$(inputSel).addClass('hidden');
		
		var value = null;
		if ($(inputSel).is('input')) {
			value = $(inputSel).val();
		} else {
			value = $(inputSel + ' > input').val();
		}
		form[e] = value;
		$(valueSel).html(value);
	});
	
	console.log('sending form', form)
	var request = $.ajax({
	  url: "actions/editprofile.php",
	  type: "POST",
	  data: form,
	  dataType: "json",
		beforeSend: function() {
			$(doneEditingButtonSel).addClass('disabled');
		}
	});
 
	request.done(function( result ) {
		if (result.success === true) {
			$(editButtonSel).removeClass('hidden');
			$(doneEditingButtonSel).addClass('hidden').removeClass('disabled');
		} else {
			$(doneEditingButtonSel).removeClass('disabled');
		}
	});
 
	request.fail(function( jqXHR, textStatus ) {
	  alert( "Request failed: " + textStatus );
		$(doneEditingButtonSel).removeClass('disabled');
	});
	
	return false;
});