<?php
// The purpose of this script is to allow an administrator to edit/add libraries in the system.
session_start();
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
?>
	<script type="text/javascript">
		jQuery("#library_list").jqGrid({
			url:'script/processing/fetch_libraries.php',
			editurl:'script/processing/save_library.php',
			width: 600,
			datatype: 'json',
			colNames:['ID', 'Library Name'],
			colModel :[ 
			  {name:'library_id', index:'library_id', width:150, edittype:'text', hidden:true, editable:true}, 
			  {name:'library_name', index:'library_name', width:150, edittype:'text', editable:true}
			],
			pager: '#library_pager',
			sortname: 'library_id',
			sortorder: '',
			viewrecords: true,
			caption: 'Libraries',
			onSelectRow: function(id) {
				
			}
		});
		
		jQuery("#library_list").jqGrid('navGrid','#library_pager',
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
					jQuery("#library_list").trigger('reloadGrid');
					$("#delmodlibrary_list").remove();
					$(".jqmOverlay").remove();
				}
			}
		);
	</script>
	<div id="library_grid">
		<table id="library_list"></table> 
		<div id="library_pager"></div>
	</div>
	<p/>
<?php
}
?>