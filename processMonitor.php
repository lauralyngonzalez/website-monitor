<?php
	include "config.php";
	require_once "monitor.php";

	if (isset($_POST['submit'])) {
		$monitor = new Monitor($db);
		$monitor_type = $_POST["monitor_type"];
		$host = htmlspecialchars($_POST["host"]);
		$name = htmlspecialchars($_POST["name"]);
		$keyword = htmlspecialchars($_POST["keyword"]);
		$keyword_opt = $_POST["keyword_option"];
				
		// Keyword
		if ($monitor_type != 'keyword') {
			$keyword = NULL;
			$keyword_opt_bool = NULL;
		} else if ($keyword_opt == 'exists') { // convert keyword_option for db
			$keyword_opt_bool = 1; //keyword 
		} else {
			$keyword_opt_bool = 0;
		}
		
		$monitor->createMonitor($monitor_type, $host, $name, $keyword, $keyword_opt_bool);
		echo "Record inserted!";
		
	} else if (isset($_POST['delete'])) {	// delete monitor
		$monitor = new Monitor($db);
		$monitor->deleteMonitor($_POST['delete']);
		
		echo 'deleted ' . $_POST['delete'];
		
	} else if (isset($_POST['save'])) { // update monitor
		$monitor = new Monitor($db);
		$monitor_arr = explode(",", $_POST["save"]);
		$monitor_id = $monitor_arr[0];
		$monitor_type = trim($monitor_arr[1]);
		$host = htmlspecialchars($_POST["host"]);
		$name = htmlspecialchars($_POST["name"]);
		$keyword = htmlspecialchars($_POST["keyword"]);
		$keyword_opt = $_POST["keyword_option"];
			
		// Keyword
		if ($monitor_type != "keyword") {
			$keyword = NULL;
			$keyword_opt_bool = NULL;
		} else if ($keyword_opt == "exists") { // convert keyword_option for db
			$keyword_opt_bool = 1; //keyword 
		} else {
			$keyword_opt_bool = 0;
		}

		$monitor->updateMonitor($monitor_id, $host, $name, $keyword, $keyword_opt_bool);
		echo "Record updated!";

	} else {	// cancel
		header("Location: index.php");
		exit;
	}
	
	header('Refresh: 1; url=index.php');
	
?>