window.rcmail && rcmail.addEventListener('init', function(evt) {

	rcmail.register_command('plugin.enhance_password-save', function() {

		var input_newpasswd = rcube_find_object('_newpasswd'),
		input_confpasswd = rcube_find_object('_confpasswd');

		if (input_newpasswd && input_newpasswd.value == '') {
			
			rcmail.alert_dialog(rcmail.get_label('nopassword', 'enhance_password'), function() {

				input_newpasswd.focus();
				return true;

			});

		}
		else if (input_confpasswd && input_confpasswd.value == '') {

			rcmail.alert_dialog(rcmail.get_label('nopassword', 'enhance_password'), function() {

				input_confpasswd.focus();
				return true;

			});

		}
		else if (input_newpasswd && input_confpasswd && input_newpasswd.value != input_confpasswd.value) {

			rcmail.alert_dialog(rcmail.get_label('passwordsdonotmatch', 'enhance_password'), function() {

				input_newpasswd.focus();
				return true;

			});

		}
		else {

			rcmail.gui_objects.passform.submit();

		}

	}, true);

	$('input:not(:hidden)').first().focus();

});
