$(function () {
  $('[data-toggle="popover"]').popover()
})

$('#loginForm').submit(function(e) {
	e.preventDefault(); // don't actually submit the form.
	
	var email = $('#loginEmail').val();
	var password = $('#loginPassword').val();
	
	var request = $.ajax({
	  url: "actions/login.php",
	  type: "POST",
	  data: { 
			email : email,
			password : password,
		},
	  dataType: "json",
		beforeSend: function() {
			$('#loginEmail').prop('disabled', true).closest('.form-group').removeClass('has-error');
			$('#loginPassword').prop('disabled', true).closest('.form-group').removeClass('has-error');
		}
	});
 
	request.done(function( result ) {
		if (result.success === true) {
			location.reload(true);
		} else {
			$('#loginEmail').prop('disabled', false).closest('.form-group').addClass('has-error');
			$('#loginPassword').prop('disabled', false).closest('.form-group').addClass('has-error');
			$('#loginEmailInputGroup').popover('show');
		}
	});
 
	request.fail(function( jqXHR, textStatus ) {
	  alert( "Request failed: " + textStatus );
	});
});

$('#signupForm').submit(function(e) {
	e.preventDefault();
	
	var firstNameSel = $('#signupFirstName');
	var lastNameSel = $('#signupLastName');
	var emailSel = $('#signupEmail');
	var passwordSel = $('#signupPassword');
	var fields = [firstNameSel, lastNameSel, emailSel, passwordSel];
	
	var firstName = firstNameSel.val();
	var lastName = lastNameSel.val();
	var email = emailSel.val();
	var password = passwordSel.val();
	
	var messageAlertSel = $('#signupMessageAlert');
	var missingFieldsAlertSel = $('#signupMissingFieldsAlert');
	var hideAlerts = function() {
		messageAlertSel.addClass('hidden');
		missingFieldsAlertSel.addClass('hidden');
	}
	
	var request = $.ajax({
	  url: "actions/signup.php",
	  type: "POST",
	  data: { 
			firstName: firstName,
			lastName: lastName,
			email: email,
			password: password,
		},
	  dataType: "json",
		beforeSend: function() {
			$.each(fields, function(i, sel) {
				hideAlerts();
				sel.prop('disabled', true);
				sel.closest('.form-group').removeClass('has-error');
			});
		}
	});
 
	request.done(function( result ) {
		if (result.success === true) {
			location.reload(true);
		} else {
			var missingFields = [];
			$.each(result.errors, function(i, sel) {
				$(sel).closest('.form-group').addClass('has-error');
				missingFields.push($(sel).prop('placeholder'));
			});

			if (missingFields.length > 0) {
				$('#signupMissingFields').html(missingFields.join(', '));
				missingFieldsAlertSel.removeClass('hidden');
			}
			
			if (result.message != null) {
				messageAlertSel.html(result.message);
				messageAlertSel.removeClass('hidden');
			}

			$.each(fields, function(i, sel) {
				sel.prop('disabled', false);
			});
		}
	});
 
	request.fail(function( jqXHR, textStatus ) {
	  alert( "Request failed: " + textStatus );
	});
});
