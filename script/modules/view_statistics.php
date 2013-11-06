<?php
session_start();
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
// Checks that the user is logged in
if ($user != null) {
?>

<div class="tabs" style="font-size: 12px; font-family: Arial;">
    <ul>
        <li><a href="#graph"><span>Graph</span></a></li>
		<li><a href="#view"><span>Data Grid</span></a></li>
    </ul>
	<div id="graph">
		<form id="graph_form" class="form-container" action="javascript:void(0);">
				<label for="start_date" style="margin-right: 5px;">Start Date</label>
				<input type="text" id="start_date" class="datepicker" value="<?php echo date("d-m-Y", strtotime("-1 month +1 day"));?>" />
				<label for="end_date" style="margin-left: 25px; margin-right: 5px;">End Date</label>
				<input type="text" id="end_date" class="datepicker" value="<?php echo date("d-m-Y", strtotime("+1 day"));?>" />
				<button id="graph_button" style="font-family: Arial;">Display</button>
		</form>
		<div id="graph_region"></div>
    </div>
	<div id="view">
		<div id="statistic_data_grid">
			<table id="data_list"></table> 
			<div id="data_pager"></div>
		</div>
    </div>
</div>
<script type="text/javascript">
	// Creates a JQGrid used to display the summarised graph data.
	jQuery().ready(function($) {
		jQuery("#data_list").jqGrid({
			width: 600,
			datatype: 'json',
			colNames:['Date', 'Sessions', 'Students'],
			colModel :[ 
			  {name:'date', index:'date', width:150, hidden:false},
			  {name:'sessions', index:'sessions', width:150, hidden:false},
			  {name:"students",index:"students",width:150, hidden:false}
			],
			pager: '#data_pager',
			sortname: 'date',
			sortorder: '',
			viewrecords: true,
			caption: 'Statistic Data'
		});
		jQuery("#data_list").jqGrid('navGrid','#data_pager', {add:false,edit:false,del:false});
		
		// Used to load the graph
		function loadGraph() {
			// Adds an iframe of the view graph script into the graph region passing the date range.
			var html = '<iframe src="script/modules/view_graph.php?start_date=';
			html += $('#start_date').val() + '&end_date=' + $('#end_date').val() + '" width="100%" height="310px" scrolling="no" frameborder="0"></iframe>';
			$('#graph_region').empty().html(html);
			// Sets the processing url on the grid containing the date range.
			jQuery("#data_list").jqGrid('setGridParam',{url:"script/processing/fetch_statisticview_data.php?start_date=" + $('#start_date').val() + "&end_date=" + $('#end_date').val(), page:1});
		}
		
		$('.tabs').tabs(); // Creates the tabs according to the strutured html.
		$('.datepicker').datepicker({ dateFormat: 'dd-mm-yy' });
		// Click event of the graph button, loads graph when clicked.
		$('#graph_button').button().click(function() {
			loadGraph();
		});
		loadGraph(); // Defaultly loads graph
		
		// On the datagrid view tab event, reload the datagrid
		$('.tabs').bind('tabsshow', function(event, ui) {
			if (ui.panel.id == "view") {
				jQuery("#data_list").trigger('reloadGrid');
			}
		});
	});
</script>
<?php
}
?>