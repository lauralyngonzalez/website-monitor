<?php
	include "config.php";
	require_once "monitor.php";

	$monitor = new Monitor($db);

	// delete monitor
	if (isset($_POST['delete'])) {
		$monitor->deleteMonitor($_POST['delete']);
		echo 'deleted ' . $_POST['delete'];
	} else { // update monitor

		$monitor_type = $_POST["monitor_type"];
		$host = htmlspecialchars($_POST["host"]);
		$name = htmlspecialchars($_POST["name"]);
		$keyword = htmlspecialchars($_POST["keyword"]);
		$keyword_option = $_POST["keyword_option"];
		
		// convert keyword_option for db
		if ($keyword_option == "exists") {
			$keyword_option_bool = 1;
		} else {
			$keyword_option_bool = 0;
		}

		// Keyword
		if ($monitor_type == 'keyword') {
			$monitor->createKeywordMonitor($monitor_type, $host, $name, $keyword, $keyword_option_bool);
		} else { // HTTP
			$monitor->createHttpMonitor($monitor_type, $host, $name);
		}
		
		echo "Records inserted!";

		//close connection
		unset($db);
	}
	
	header('Refresh: 1; url=index.php');
		
?>