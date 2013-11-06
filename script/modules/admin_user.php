<?php
session_start();
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
// Validates is logged in, and has an administrator user account type.
if ($user != null AND ($user->isAdminUser())) {
?>
	<script type="text/javascript">
		// Creates a grid using the jQGrid plugin, which fetches the data from the processing script (using ajax)
		// and populates the table with the results.
		jQuery("#user_list").jqGrid({
			url:'script/processing/fetch_users.php',
			editurl:'script/processing/save_user.php',
			width: 600,
			datatype: 'json',
			colNames:['ID', 'Account Name', 'Library', 'Account Password', 'Account Type'],
			colModel :[ 
			  {name:'user_id', index:'user_id', width:150, edittype:'text', hidden:true, editable:true}, 
			  {name:'staff_account', index:'staff_account', width:150, edittype:'text', editable:true},
			  {name:'library_id', index:'library_id', width:150, edittype:'select', editable:true, formatter:'select', editoptions:{value: getLibraries()}}, 
			  {name:'account_password', index:'account_password', width:150, edittype:'password', editable:true}, 
			  {name:'account_type', index:'account_type', width:150, edittype:'select', editable:true, formatter:'select', editoptions:{value:{1:'Normal', 2:'Super', 3:'Admin'}}}
			],
			pager: '#user_pager',
			sortname: 'user_id',
			sortorder: '',
			viewrecords: true,
			caption: 'User Accounts'
		});
		
		// The pager used for the jQGrid
		jQuery("#user_list").jqGrid('navGrid','#user_pager',
			{add:true,edit:true,del:true}, 
			{
				closeAfterAdd: true, 
				closeAfterEdit: true,
				reloadAfterSubmit: true
			}, 
			{
				closeAfterAdd: true, 
				closeAfterEdit: true,
				reloadAfterSubmit: true
			}, 
			{
				afterSubmit: function() {
					jQuery("#user_list").trigger('reloadGrid');
					$("#delmoduser_list").remove();
					$(".jqmOverlay").remove();
				}
			}
		);
		
		// Fetches the libraries
		function getLibraries() {
			var result = jQuery.ajax({url:'script/processing/fetch_dropdown_data.php?type=libraries', dataType:'json', async:false, cache: true}).responseText;
			return jQuery.parseJSON(result);
		}
	</script>
	<div id="user_grid">
		<table id="user_list"></table> 
		<div id="user_pager"></div>
	</div>
	<p/>
<?php
}
?>