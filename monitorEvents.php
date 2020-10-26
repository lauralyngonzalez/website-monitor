<?php
	include "config.php";
	require_once "monitor.php";
	
	header("refresh: 60;");
	
	$monitor = new Monitor($db);

	try {
		$monitor_data = $monitor->getMonitors();

?>

<div id="activeMonitors">

<h3>Active Monitors:</h3><table style='border: solid 1px black;'>
<tr><th>status</th><th>monitor</th><th>date-time</th><th>duration</th></tr>

<?php

		//echo "<h3>Active Monitors:</h3><table style='border: solid 1px black;'>";
		//echo "<tr><th>status</th><th>monitor</th><th>date-time</th><th>duration</th></tr>";

		foreach($monitor_data as $row) {
			$name = $row['name'];
			$url = $row['url'];
			
			// get http status
			if($row['monitor_type'] == "http") {
				$status = $monitor->getStatus($url);
			} else if($row['monitor_type'] == "keyword") {
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
			$active_monitor = $monitor->getMonitorEvent($monitor_id);
			
			$datetime = date("Y-m-d H:i:s"); 
			
			// add if monitor doesn't exist
			if (empty($active_monitor)) {
				$monitor->createMonitorEvent($monitor_id, $status, $datetime);
				$duration = "0 hrs, 0 mins";
			} else {
				// monitor exists, so check if the status changed
				$datetime = $active_monitor['timestamp'];
				$current_datetime = date("Y-m-d H:i:s");
				
				// Initializing the two datetime objects
				$datetime1 = new DateTime($datetime); 
				$datetime2 = new DateTime($current_datetime); 

				// Calling the diff() function on above 
				// two DateTime objects 
				$difference = $datetime1->diff($datetime2); 
				//$duration = $difference->format('%d') . " days, " . $difference->format('%h') . " hrs, " . $difference->format('%i') . " mins";
				$hours = $difference->h + ($difference->days*24);
				$duration = $hours . " hrs, " . $difference->format('%i') . " mins";
				
				$monitor->updateMonitorEvent($monitor_id, $status, $datetime, $hours);
			}
			
			//$monitor->writeToLogFile($name, $status, $datetime, $hours, $url);
?>

<tr>
	<td style='width:200px;border:1px solid black;'><?php echo $status; ?></td>
	<td style='width:200;border:1px solid black;'><?php echo $name; ?></td>
	<td style='width:200;border:1px solid black;'><?php echo $datetime; ?></td>
	<td style='width:200;border:1px solid black;'><?php echo $duration; ?></td>
	<td style='width:200;border:1px solid black;'>
		<form style="border:none;margin:0px;padding:0px;display:inline" action="monitorForm.php" method="post">
		<button type="submit" name="id" value="<?php echo $monitor_id; ?>">Edit</button>
		</form>
	</td>
</tr>

<?php
		}
	} catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
		
	$db = null;
	
?> 

</table></div>