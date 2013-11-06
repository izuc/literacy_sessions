<?php
// The purpose of this script is to allow an administrator to view/delete statistics.
session_start();
require_once('../classes/User.class.php');
require_once('../classes/Library.class.php');
$user = User::getLoggedInUser();

if ($user != null AND ($user->isAdminUser() || $user->isSuperUser())) {
	$library_id = ((isset($_GET['library_id']) AND is_numeric($_GET['library_id'])) ? $_GET['library_id']: $user->getLibraryID());
	if ($user->isAdminUser()) {
?>
	<p>
	<select id="library_id" width="180px" style="width: 180px; height: 20px;">
		<?php 
			foreach (Library::fetchList() as $library) {
				echo '<option value="'.$library->getLibraryID().'" '.
						(($library_id == $library->getLibraryID()) ? ' selected="selected"' : '').'>'.$library->getLibraryName().'</option>';
			}
		?>
	</select>
	<script type="text/javascript">
		jQuery().ready(function($) {
			$('#library_id').change(function() {
				load_content('admin_statistics.php?library_id=' + $('#library_id').val());
			});
		});
	</script>
	</p>
	<?php
	} 
	?>
	<script type="text/javascript">
		jQuery("#statistic_list").jqGrid({
			url:'script/processing/fetch_statistics.php?library_id=<?php echo $library_id;?>',
			editurl:'script/processing/save_statistic.php',
			width: 600,
			datatype: 'json',
			colNames:['ID', 'Library', 'Session', 'User', 'Date Lodged', 'Date Occurred'],
			colModel :[ 
			  {name:'statistic_id', index:'statistic_id', width:150, edittype:'text', hidden:true, editable:true}, 
			  {name:'library_id', index:'library_id', width:150, edittype:'text', hidden:true, editable:true},
			  {name:"session_id",index:"session_id",width:150, edittype:'select', editable:true, formatter:'select', editoptions:{value: getSessions()}},
			  {name:"user_id",index:"user_id",width:150, edittype:'select', editable:true, formatter:'select', editoptions:{value: getUsers()}}, 
			  {name:'date_lodged', index:'date_lodged', width:150, edittype:'text', editable:true},
			  {name:'date_occurred', index:'date_occurred', width:150, edittype:'text', editable:true}
			],
			pager: '#statistic_pager',
			sortname: 'statistic_id',
			sortorder: '',
			viewrecords: true,
			caption: 'Statistics',
			onSelectRow: function(id) {
				var statistic_id = jQuery("#statistic_list").jqGrid('getCell', id, 'statistic_id');
				jQuery("#time_list").jqGrid('setGridParam',{url:"script/processing/fetch_statistic_times.php?statistic_id=" + statistic_id, page:1}).trigger('reloadGrid');
				jQuery("#data_list").jqGrid('setGridParam',{url:"script/processing/fetch_statistic_data.php?statistic_id=" + statistic_id, page:1}).trigger('reloadGrid');
				$("#statistic_times_grid").show();
				$("#statistic_data_grid").show();
			}
		});
		jQuery("#statistic_list").jqGrid('navGrid','#statistic_pager',
			{add:false,edit:false,del:true}, {},{},
			{
				afterSubmit: function() {
					jQuery("#statistic_list").trigger('reloadGrid');
					$("#statistic_times_grid").hide();
					$("#statistic_data_grid").hide();
					$("#delmodstatistic_list").remove();
					$(".jqmOverlay").remove();
				}
			}
		);
		
		jQuery("#time_list").jqGrid({
			width: 600,
			datatype: 'json',
			colNames:['ID', 'Attendees', 'Start Time', 'End Time'],
			colModel :[ 
			  {name:'time_id', index:'time_id', width:150, hidden:true}, 
			  {name:'attendees', index:'attendees', width:150, hidden:false},
			  {name:"start_time",index:"start_time",width:150, hidden:false},
			  {name:"end_time",index:"end_time",width:150, hidden:false}
			],
			pager: '#time_pager',
			sortname: 'time_id',
			sortorder: '',
			viewrecords: true,
			caption: 'Statistic Times'
		});
		jQuery("#time_list").jqGrid('navGrid','#time_pager', {add:false,edit:false,del:false});
		
		jQuery("#data_list").jqGrid({
			width: 600,
			datatype: 'json',
			colNames:['ID', 'Field', 'Value'],
			colModel :[ 
			  {name:'data_id', index:'data_id', width:150, hidden:true}, 
			  {name:'field', index:'field', width:150, hidden:false},
			  {name:"field_value",index:"field_value",width:150, hidden:false}
			],
			pager: '#data_pager',
			sortname: 'data_id',
			sortorder: '',
			viewrecords: true,
			caption: 'Statistic Data'
		});
		jQuery("#data_list").jqGrid('navGrid','#data_pager', {add:false,edit:false,del:false});
		
		function getSessions() {
			var result = jQuery.ajax({url:'script/processing/fetch_dropdown_data.php?type=session', dataType:'json', async:false, cache: true}).responseText;
			return jQuery.parseJSON(result);
		}
		
		function getUsers() {
			var result = jQuery.ajax({url:'script/processing/fetch_dropdown_data.php?type=users', dataType:'json', async:false, cache: true}).responseText;
			return jQuery.parseJSON(result);
		}
		
		$("#statistic_times_grid").hide();
		$("#statistic_data_grid").hide();
	</script>
	<div id="statistic_grid">
		<table id="statistic_list"></table> 
		<div id="statistic_pager"></div>
	</div>
	<br />
	<div id="statistic_times_grid">
		<table id="time_list"></table> 
		<div id="time_pager"></div>
	</div>
	<br />
	<div id="statistic_data_grid">
		<table id="data_list"></table> 
		<div id="data_pager"></div>
	</div>
<?php
}
?>