<?php
	include "config.php";
	require_once "monitor.php";
	
	$monitor = new Monitor($db);

	try {
		$monitor_data = $monitor->getMonitors();

?>

<div class="monitorEvents">

<h3>Monitor Events:</h3>

<table style="width:100%">
<tr><th>status</th><th>monitor</th><th>date-time</th><th>duration</th></tr>

<?php

		foreach($monitor_data as $row) {
			$name = $row['name'];
			$url = $row['url'];
			$action = $row['action'];
			$hasStatusChanged = False;
			
			if ($action == "Paused") {
				$status = $action;
			} else if ($row['monitor_type'] == "http") {
				$status = $monitor->getStatus($url);
			} else if ($row['monitor_type'] == "keyword") {
				$keyword = $row['keyword'];
				$found = $monitor->hasKeyword($keyword, $url);
				
				// get status for keyword
				if ($found && $row['keyword_option'] == 0) {
					$status = "Up - Keyword '$keyword' Found";	// found, alert if not exists
				} else if ($found && $row['keyword_option'] == 1) {
					$status = "Down - Keyword '$keyword' Found";	// found, alert if it exists
				} else if ($row['keyword_option'] == 0) {
					$status = "Up - Keyword '$keyword' Not Found";	// not found, alert if not exists
				} else {
					$status = "Down - Keyword '$keyword' Not Found";	// not found, alert if it exists
				}
			}

			// check active monitor table
			$monitor_id = $row['id'];
			$active_monitor = $monitor->getMonitorEvent2($monitor_id);
			
			$datetime = date("Y-m-d H:i:s");
			
			// add if monitor doesn't exist
			if (empty($active_monitor)) {
				$monitor->createMonitorEvent($monitor_id, $status, $datetime);
				$duration = "0 hrs, 0 mins";
			} else {
				// monitor exists, so check if the status changed
				$last_datetime = $active_monitor['timestamp'];
				$event_id = $active_monitor['monitor_event_id'];
				$last_status = $active_monitor['status'];
				
				// status changed, so make new entry
				if ($last_status != $status) {
					$monitor->createMonitorEvent($monitor_id, $status, $datetime);
					$duration = "0 hrs, 0 mins";	
				} else {
					// Initializing the two datetime objects
					$datetime1 = new DateTime($last_datetime); 
					$datetime2 = new DateTime($datetime); 

					// Calling the diff() function on above two DateTime objects 
					$difference = $datetime1->diff($datetime2); 
					$hours = $difference->h + ($difference->days*24);
					$duration = $hours . " hrs, " . $difference->format('%i') . " mins";
					
					//$monitor->updateMonitorEvent($monitor_id, $status, $last_datetime, $hours);
					$monitor->updateMonitorEvent2($event_id, $hours);
					
					$datetime = $last_datetime; // to display in html
				}
				
			}
			
			//$monitor->writeToLogFile($name, $status, $datetime, $hours, $url);
?>

<tr>
	<td style="width:30%"><?php echo $status; ?></td>
	<td style="width:25%"><?php echo $name; ?></td>
	<td style="width:25%"><?php echo $datetime; ?></td>
	<td style="width:20%"><?php echo $duration; ?></td>
</tr>

<?php
		}
	} catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
		
	$db = null;
	
?> 

</table>
<p>Export logs</p>

</div>
