<?php
// The purpose of this script is to allow an administrator to edit/add the form fields.
session_start();
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
?>
	<script type="text/javascript">
		var field_id;
		jQuery("#field_list").jqGrid({
			url:'script/processing/fetch_field.php',
			editurl:'script/processing/save_field.php',
			width: 600,
			datatype: 'json',
			colNames:['ID', 'Label', 'Type', 'Mandatory'],
			colModel :[ 
			  {name:'field_id', index:'field_id', width:150, edittype:'text', hidden:true, editable:true}, 
			  {name:'field_label', index:'field_label', width:150, edittype:'text', editable:true}, 
			  {name:'field_type', index:'field_type', width:150, edittype:'select', editable:true, formatter:'select', editoptions:{value:{1:'Textbox', 2:'Selectbox', 3:'Textarea', 4:'Datepicker'}}},
			  {name:'field_required', index:'field_required', width:150, edittype:'checkbox', editable:true, editoptions: { value: 'True:False' }, formatter: function(cellvalue, options, rowObject) {
					return ((cellvalue) ? "True" : "False");
				}}
			],
			pager: '#field_pager',
			sortname: 'field_id',
			sortorder: '',
			viewrecords: true,
			caption: 'Form Fields',
			onSelectRow: function(id) {
				field_id = jQuery("#field_list").jqGrid('getCell', id, 'field_id');
				if (jQuery("#field_list").jqGrid('getCell', id, 'field_type') == 2) {
					$("#field_values_grid").show();
					jQuery("#field_values_list").jqGrid('setGridParam',{editurl:"script/processing/save_field_values.php?fieldID=" + field_id});
					jQuery("#field_values_list").jqGrid('setGridParam',{url:"script/processing/fetch_field_values.php?field_id=" + field_id, page:1}).trigger('reloadGrid');
				} else {
					$("#field_values_grid").hide();
				}
				$("#field_available_grid").show();
				jQuery("#field_list").jqGrid('setGridParam',{editurl:"script/processing/save_field.php?field_id=" + field_id});
				jQuery("#field_available_list").jqGrid('setGridParam',{url:"script/processing/fetch_fields_available.php?field_id=" + field_id, page:1}).trigger('reloadGrid');
				jQuery("#field_available_list").jqGrid('setGridParam',{editurl:"script/processing/save_fields_available.php?field_id=" + field_id});
			}
		});
		
		jQuery("#field_list").jqGrid('navGrid','#field_pager',
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
					jQuery("#field_list").trigger('reloadGrid');
					$("#field_values_grid").hide();
					$("#field_available_grid").hide();
					$("#delmodfield_list").remove();
					$(".jqmOverlay").remove();
				}
			}
		);
		
		jQuery("#field_values_list").jqGrid({
			url:'script/processing/fetch_field_values.php',
			width: 600,
			datatype: 'json',
			colNames: ['ID','Field','Library','Value'],
			colModel: [
				{name:"valueID",index:"valueID",width:150, edittype:'text', editable:true, hidden:true},
				{name:"fieldID",index:"fieldID",width:150, edittype:'text', editable:true, hidden:true},
				{name:"libraryID",index:"libraryID",width:150, edittype:'select', editable:true, formatter:'select', editoptions:{value: getLibraries()}},
				{name:"fieldValue",index:"fieldValue",width:150, edittype:'text', editable:true}
			],
			pager: '#field_values_pager',
			sortname: 'valueID',
			sortorder: '',
			viewrecords: true,
			caption: 'Field Values',
			onSelectRow: function(id) {
				var subfield_id = jQuery("#field_values_list").jqGrid('getCell', id, 'valueID');
				jQuery("#field_values_list").jqGrid('setGridParam',{editurl:'script/processing/save_field_values.php?fieldID=' + field_id + '&valueID=' + subfield_id});
			}
		});
		
		jQuery("#field_values_list").jqGrid('navGrid','#field_values_pager',
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
			}
		);
		$("#field_values_grid").hide();

		jQuery("#field_available_list").jqGrid({
			url:'script/processing/fetch_fields_available.php',
			width: 600,
			datatype: 'json',
			colNames: ['ID','Library','Session','Field'],
			colModel: [
				{name:"available_id",index:"available_id",width:150, edittype:'text', editable:true, hidden:true},
				{name:"library_id",index:"library_id",width:150, edittype:'select', editable:true, formatter:'select', editoptions:{value: getLibraries()}},
				{name:"session_id",index:"session_id",width:150, edittype:'select', editable:true, formatter:'select', editoptions:{value: getSessions()}},
				{name:"field_id",index:"field_id",width:150, edittype:'text', editable:true, hidden:true}
			],
			pager: '#field_available_pager',
			sortname: 'valueID',
			sortorder: '',
			viewrecords: true,
			caption: 'Fields Available',
			onSelectRow: function(id) {
				var libfield_id = jQuery("#field_available_list").jqGrid('getCell', id, 'available_id');
				jQuery("#field_available_list").jqGrid('setGridParam',{editurl:'script/processing/save_fields_available.php?field_id=' + field_id + '&available_id=' + libfield_id});
			}
		});
		
		jQuery("#field_available_list").jqGrid('navGrid','#field_available_pager',
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
			}
		);
		$("#field_available_grid").hide();
		
		function getSessions() {
			var result = jQuery.ajax({url:'script/processing/fetch_dropdown_data.php?type=session', dataType:'json', async:false, cache: true}).responseText;
			return jQuery.parseJSON(result);
		}
		function getLibraries() {
			var result = jQuery.ajax({url:'script/processing/fetch_dropdown_data.php?type=libraries', dataType:'json', async:false, cache: true}).responseText;
			return jQuery.parseJSON(result);
		}
	</script>
	<div id="field_grid">
		<table id="field_list"></table> 
		<div id="field_pager"></div>
	</div>
	<p/>
	<div id="field_values_grid">
		<table id="field_values_list"></table> 
		<div id="field_values_pager"></div>
	</div>
	<p/>
	<div id="field_available_grid">
		<table id="field_available_list"></table> 
		<div id="field_available_pager"></div>
	</div>
<?php
}
?>