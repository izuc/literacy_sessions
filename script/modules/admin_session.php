<?php
// The purpose of this script is to allow an administrator to edit/add the form fields in the system.
session_start();
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
?>
	<script type="text/javascript">
		jQuery("#session_list").jqGrid({
			url:'script/processing/fetch_sessions.php',
			editurl:'script/processing/save_session.php',
			width: 600,
			datatype: 'json',
			colNames:['ID', 'Session Name'],
			colModel :[ 
			  {name:'session_id', index:'session_id', width:150, edittype:'text', hidden:true, editable:true}, 
			  {name:'session_name', index:'session_name', width:150, edittype:'text', editable:true}
			],
			pager: '#session_pager',
			sortname: 'session_id',
			sortorder: '',
			viewrecords: true,
			caption: 'Sessions',
			onSelectRow: function(id) {
				
			}
		});
		
		jQuery("#session_list").jqGrid('navGrid','#session_pager',
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
					jQuery("#session_list").trigger('reloadGrid');
					$("#delmodsession_list").remove();
					$(".jqmOverlay").remove();
				}
			}
		);
	</script>
	<div id="session_grid">
		<table id="session_list"></table> 
		<div id="session_pager"></div>
	</div>
	<p/>
<?php
}
?>