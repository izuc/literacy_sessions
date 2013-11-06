<?php
// The purpose of this script is to allow an user to enter statistics.
session_start();
require_once('../classes/User.class.php');
require_once('../classes/Field.class.php');
require_once('../classes/FieldAvailable.class.php');
require_once('../classes/SessionType.class.php');
// Ensures the user is logged in.
$user = User::getLoggedInUser();
	if ($user != null) {
		$session_type = (isset($_GET['session_type']) ? $_GET['session_type'] : 1);?>
		<div class="form-container">
			<form id="statistic_form" action="javascript:void(0);">
				<div id="info_message" style="display: none;"></div>
				<fieldset>
					<legend>General Session Information</legend>
					<div class="field">
						<label for="session">Session Type <em>*</em></label>
						<select id="session" name="session" width="180px" style="width: 180px; height: 20px;">
							<?php
								// populates the session types selectbox
								foreach(SessionType::fetchList() as $sessionType) {
									echo '<option value="'.$sessionType->getSessionID().'"'.((isset($_GET['session_type']) 
																								AND ($_GET['session_type'] == $sessionType->getSessionID())) ?
																								' selected="selected"' : '').'>'.ucwords($sessionType->getSessionName()).'</option>';
								}
							?>
						</select>
						<script type="text/javascript">
							jQuery().ready(function($) {
								$('#session').change(function() {
									load_content('add_statistics.php?session_type=' + $('#session').val());
								});
							});
						</script>
					</div>
					<div class="field">
						<label for="date">Date <em>*</em></label>
						<input id="date" name="date" type="text" size="25" value="<?php echo date("d-m-Y");?>" class="datepicker validate[required]" />
					</div>
					<div class="field">
						<label for="occurrence">Occurrence <em>*</em></label>
						<input type="text" id="occurrence" name="occurrence" value="1" size="5" class="validate[required]" />
						<span id="display_button" style="padding: 2px; font-family: Arial; font-size:.8em;">Display</span>
					</div>
				</fieldset>
				<div id="times"></div>
				<?php
				// Shows the dynamically created field elements
				if (FieldAvailable::hasFields($user->getLibraryID(), $session_type)) {?>
				<fieldset>
					<legend>Information</legend>
					<?php
					foreach(Field::fetchFieldsAvailable($user->getLibraryID(), $session_type) as $field) {
					?>
						<div class="field">
							<?php $field->showFormLabel();?>								
							<?php $field->showFormField($user->getLibraryID());?>	
						</div>
					<?php
					}
					?>
				</fieldset>
				<?php
				}
				?>
				<div class="buttonrow">
					<input class="buttonSubmit" id="submit_form" type="button" value="Submit" /> &nbsp;
					<input class="buttonSubmit" id="clear_form" type="button" value="Clear" />
				</div>
			</form>
		</div>
		<script type="text/javascript">
		jQuery().ready(function($) {
			var display = false;
			$('.datepicker').datepicker({ dateFormat: 'dd-mm-yy' });
			// Displays a session entry (the starting time, the ending time, and attendees) for each occurrence.
			$('#display_button').button().click(function() {
				$(".formError").remove();
				$('#times').empty();
				var html_code = '<fieldset>';
				html_code += '<legend>Session Times</legend>';
				html_code += '<table class="form_table" style="margin-left: 95px;">';
				html_code += '<thead><tr><td></td><td>Start</td><td>End</td><td>Attendees</td></tr></thead>';
				html_code += '<tbody>';
				// Iterates for each occurrence (being the value entered by the user).
				for (var i = 1; i <= $('#occurrence').spinner('value'); i++) {
					html_code += '<tr>';
					html_code += '<td style="padding-right: 5px;">Session '+ i +' <em>*</em></td>';
					html_code += '<td><input id="starttime'+ i +'" name="starttime'+ i +'" type="text" size="5" value="" class="timepicker validate[required]"></td>';
					html_code += '<td><input id="endtime'+ i +'" name="endtime'+ i +'" type="text" size="5" value="" class="timepicker validate[required]"></td>';
					html_code += '<td><input id="attendees'+ i +'" name="attendees'+ i +'" type="text" size="5" value="0" class="attendees validate[required]"></td>';
					html_code += '</tr>';
				}
				html_code += '</tbody>';
				html_code += '</table>';
				html_code += '</fieldset>';
				$('#times').html(html_code);
				$('.attendees').spinner({ min: 1, max: 99 });
				$('.timepicker').timepickr({trigger: 'focus'});
				display = true;
			});
			$('#occurrence').spinner({ min: 1, max: 15 });
			
			// creates the form validation
			function formValidation() {
				$('#statistic_form').validationEngine({
					success : function() {
						var parameters = $('#statistic_form').serializeForm();
						parameters.oper = 'add';
						process_request('save_statistic', parameters, submit_response);
						formValidation();
					}
				});
			}
			formValidation();
			$('#submit_form').click(function() {
				if (display) {
					$('#statistic_form').submit();
				} else {
					$.growlUI('Error', 'Please press the display button', 'failed');
				}
			});
			// Clears the form
			$('#clear_form').click(function() {
				display = false;
				$(".formError").remove();
				$('#times').empty();
				$(':input','#statistic_form').not(':button, :submit, :reset, :hidden, #occurrence').val('').removeAttr('checked').removeAttr('selected');
				$('#info_message').empty().hide();
			});
		});
		</script>
	<?php
	} else {
		echo '<img src ="template/images/message_please_login.png" width="450px" height="25px" border="0px" />';
	}
?>