function refresh_panel() {
    $('#menu_repeat').load("script/processing/load_panel.php", {random: Math.random()});
	load_content('add_statistics.php');
}

function clear_screen() {
	// Clears all form Validation Error Messages
	$('body').find(".tipsy").remove();
	$('body').find(".formError").remove(); 
}

function load_content(file_name) {
	clear_screen();
	$('#main_content').load("script/modules/" + file_name, {random: Math.random()});
}

function process_request(file, parameters, fn) {
	parameters.random = Math.random();
	$.ajax({  
	  type: 'POST',
	  url: 'script/processing/' + file + '.php',
	  data: parameters,
	  success: fn
	});
}

function submit_response(result) {
	var json = $.evalJSON(result);
	$('#info_message').empty().animate({"height": "hide"}, { duration: 500 }).animate({"height": "show"}, { duration: 1000 }).html(json.message);
	$.growlUI('Statistic Entry', json.message, ((json.success) ? 'success' : 'failed'));
}

function login_response(result) {
	var json = $.evalJSON(result);
	$.growlUI(json.title, json.message, ((json.success) ? 'success' : 'failed'));
	refresh_panel();
}

refresh_panel();