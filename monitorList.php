<?php
	include "config.php";

	try {
		$stmt = $pdo->prepare("SELECT id, monitor_type, url, name FROM monitor");
		$stmt->execute();
		$monitor_data = $stmt->fetchAll();
		
		foreach($monitor_data as $row) {
			$name = $row['name'];
			
			echo $name . " " . $row['monitor_type'] . " " . $url . "<br>";
		}
		
		
	} catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
	
	$pdo = null;
	
?> 
