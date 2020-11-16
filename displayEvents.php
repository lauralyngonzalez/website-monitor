<?php
	include "config.php";
	require_once "monitor.php";
	
	$monitor = new Monitor($db);

	try {
		$event_data = $monitor->getAll('monitor_event');
		$event_data = array_reverse($event_data);
?>

<div class="monitorEvents">

<h3>Monitor Events:</h3>

<table style="width:100%">
<tr><th>status</th><th>monitor</th><th>date-time</th><th>duration</th></tr>

<?php

		foreach($event_data as $event) {
			$monitor_id = $event['monitor_id'];
			$monitor_data = $monitor->getMonitor($monitor_id);
			
			$name = $monitor_data['name'];
			$status = $event['status'];
			$datetime = $event['timestamp'];
			$duration_total = $event['duration_mins'];
			
			$duration_hrs = floor($duration_total / 60);
			$duration_mins = $duration_total - $duration_hrs * 60;
			$duration = $duration_hrs." hrs, ".$duration_mins." mins";

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