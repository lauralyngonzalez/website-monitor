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
		echo "<tr><th>status</th><th>name</th></tr>";
		
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
			
			// add if monitor doesn't exist
			if (empty($active_monitors)) {
				echo 'empty';
				// have to adjust this to DATETIME
				$date = date("M d, Y h:i:s A");
				
				$sql = "INSERT INTO monitor_event(monitor_id,status)
					VALUES(:monitor_id,:status)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':monitor_id', $monitor_id);
				$stmt->bindParam(':status', $httpStatus);
				$stmt->execute();	
			}
			
			echo "<tr><td style='width:150px;border:1px solid black;'>" . $httpStatus . "</td>" .
			"<td style='width:150px;border:1px solid black;'>" . $name . "</td></tr>"; 
		}
	} catch(PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
	
	$pdo = null;
	
	echo "</table>";
?> 