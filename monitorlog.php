<?php
	include "config.php";
	require_once "monitor.php";

	$monitor = new Monitor($db);
	$event_data = $monitor->getAll('monitor_event');
	$event_data = array_reverse($event_data);
	
	# export to CSV file
	if (isset($_GET['action']) && $_GET['action'] == 'getCSV') {
		$filename = './logs/log_'.date("n.j.Y").'.csv';
		header("Content-type: text/csv");
		header("Content-Disposition:attachment;filename=" . basename($filename) . "");
		header('Pragma: no-cache');
		header("Expires: 0");
		
		echo "Monitor,Status,Date-Time,Duration(in mins),Monitor URL".PHP_EOL;
		
		try {
			foreach($event_data as $event) {
				$monitor_id = $event['monitor_id'];
				$monitor_data = $monitor->getMonitor($monitor_id);
				$name = $monitor_data['name'];
				$url = $monitor_data['url'];
				$status = $event['status'];
				$datetime = $event['timestamp'];
				$duration_total = $event['duration_mins'];
				$row = $name.",".$status.",".$datetime.",".$duration_total.",".$url.PHP_EOL;
				echo $row;
			}
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}
?>