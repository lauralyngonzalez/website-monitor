<?php
	require_once "config.php";
	
	// monitor class for getting http status
	class monitor {

		function getStatus($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
			curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT,10);
			$output = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			return $httpcode;
		}
		
	}

	$monitor = new monitor;

	try {
		$stmt = $pdo->prepare("SELECT id, monitor_type, url, name FROM monitor");
		$stmt->execute();
		$monitor_data = $stmt->fetchAll();
		
		echo "<table style='border: solid 1px black;'>";
		echo "<tr><th>status</th><th>monitor</th><th>date-time</th><th>duration</th></tr>";
		
		foreach($monitor_data as $row) {
			$name = $row['name'];
			
			// get http status
			if($row['monitor_type'] == "http") {
				$url = $row['url'];
				$httpStatus = $monitor->getStatus($url);
			}
			
			// check active monitor table
			$monitor_id = $row['id'];
			$stmt = $pdo->prepare("SELECT status, timestamp, notified
					FROM monitor_event
					WHERE monitor_id = '$monitor_id'");
			$stmt->execute();
			$active_monitors = $stmt->fetchAll();
			$datetime = date("Y-m-d H:i:s"); 
			
			// add if monitor doesn't exist
			if (empty($active_monitors)) {
				
				
				$sql = "INSERT INTO monitor_event(monitor_id,status,timestamp)
					VALUES(:monitor_id,:status,:timestamp)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':monitor_id', $monitor_id);
				$stmt->bindParam(':status', $httpStatus);
				$stmt->bindParam(':timestamp', $datetime);
				$stmt->execute();
			} else {
				// monitor exists, so check if the status changed
				$datetime = $active_monitors[0]['timestamp'];
				$current_datetime = date("Y-m-d H:i:s");
				
				// Initialising the two datetime objects
				$datetime1 = new DateTime($datetime); 
				$datetime2 = new DateTime($current_datetime); 
  
				// Calling the diff() function on above 
				// two DateTime objects 
				$difference = $datetime1->diff($datetime2); 
				$duration = $difference->format('%h') . " hrs, " . $difference->format('%i') . " mins";
			}
			
			echo "<tr><td style='width:150px;border:1px solid black;'>" . $httpStatus . "</td>" .
			"<td style='width:150px;border:1px solid black;'>" . $name . "</td>" .
			"<td style='width:150px;border:1px solid black;'>" . $datetime . "</td>" .
			"<td style='width:150px;border:1px solid black;'>" . $duration . "</td>" .
			"</tr>"; 
		}
	} catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
	
	$pdo = null;
	
	echo "</table>";
?> 
