<?php
	include "config.php";
	require_once "monitor.php";

	if (isset($_POST['submit'])) {
		$monitor = new Monitor($db);
		$monitor_type = $_POST["monitor_type"];
		$host = htmlspecialchars($_POST["host"]);
		$name = htmlspecialchars($_POST["name"]);
		$keyword = htmlspecialchars($_POST["keyword"]);
		$keywordOpt = $_POST["keyword_option"];
				
		// Keyword
		if ($monitor_type != 'keyword') {
			$keyword = NULL;
			$keywordOptBool = NULL;
		} else if ($keywordOpt == 'exists') { // convert keyword_option for db
			$keywordOptBool = 1; //keyword 
		} else {
			$keywordOptBool = 0;
		}
		
		$monitor->createMonitor($monitor_type, $host, $name, $keyword, $keywordOptBool);
		echo "Record inserted!";
		
	} else if (isset($_POST['delete'])) {	// delete monitor
		$monitor = new Monitor($db);
		$monitor->deleteMonitor($_POST['delete']);
		
		echo 'deleted ' . $_POST['delete'];
		
	} else if (isset($_POST['save'])) { // update monitor
		$monitor = new Monitor($db);
		$monitorArray = explode(",", $_POST["save"]);
		$monitorId = $monitorArray[0];
		$monitorType = trim($monitorArray[1]);
		$host = htmlspecialchars($_POST["host"]);
		$name = htmlspecialchars($_POST["name"]);
		$keyword = htmlspecialchars($_POST["keyword"]);
		$keywordOpt = $_POST["keyword_option"];
			
		// Keyword
		if ($monitorType != "keyword") {
			$keyword = NULL;
			$keywordOptBool = NULL;
		} else if ($keywordOpt == "exists") { // convert keyword_option for db
			$keywordOptBool = 1; //keyword 
		} else {
			$keywordOptBool = 0;
		}

		$monitor->updateMonitor($monitorId, $host, $name, $keyword, $keywordOptBool);
		echo "Record updated!";

	} else {	// cancel
		header("Location: index.php");
		exit;
	}
	
	header('Refresh: 1; url=index.php');
	
?>