
<?php
require_once 'simple_html_dom.php';

// monitor class for getting http status
class Monitor {
	protected $db;

	function __construct($db) {
		$this->db = $db;
	}

	// get the http status for a host
	public function getStatus($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
		curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT,10);
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $httpcode;
	}
	
	// look for the keyword on the given webpage using the Simple HTML DOM library
	public function hasKeyword($keyword, $url) {
		$output = file_get_html($url)->plaintext;
		if (preg_match("/$keyword/i", $output)) {
			return true;
		} else {
			return false;
		}
	}
	
	// Gets all monitors
	public function getMonitors() {
		try {
			$stmt = $this->db->prepare("SELECT * FROM monitor");
			//$stmt = $this->db->prepare("SELECT id, monitor_type, url, name FROM monitor");
			$stmt->execute();
			$monitor_data = $stmt->fetchAll();
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
		return $monitor_data;
	}
	
	// Get monitor by id
	public function getMonitor($monitor_id) {
		try {
			$stmt = $this->db->prepare("SELECT *
					FROM monitor WHERE id = '$monitor_id'");
			$stmt->execute();
			$monitor_data = $stmt->fetch();
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
		return $monitor_data;
	}
	
	// creates a monitor
	public function createMonitor($monitorType, $host, $name, $keyword, $keywordOpt) {
		try {
			$sql = "INSERT INTO monitor(monitor_type,url,name,keyword,keyword_option)
				VALUES(:monitor_type,:url,:name,:keyword,:keyword_option)";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':monitor_type', $monitorType);
			$stmt->bindParam(':url', $host);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':keyword', $keyword);
			$stmt->bindParam(':keyword_option', $keywordOpt);
			$stmt->execute();
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}
	
	// update an existing monitor
	public function updateMonitor($monitorId, $host, $name, $keyword, $keywordOpt) {
		try {
			$sql = "UPDATE monitor SET url=?, name=?, keyword=?, keyword_option=?
				WHERE id = $monitorId ";
			$stmt = $this->db->prepare($sql);
			$stmt->execute([$host, $name, $keyword, $keywordOpt]);
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}
	
	// creates a monitor event
	public function createMonitorEvent($monitor_id, $httpStatus, $datetime) {
		try {
			$sql = "INSERT INTO monitor_event(monitor_id,status,timestamp)
					VALUES(:monitor_id,:status,:timestamp)";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':monitor_id', $monitor_id);
			$stmt->bindParam(':status', $httpStatus);
			$stmt->bindParam(':timestamp', $datetime);
			$stmt->execute();
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}
	
	// Deletes a monitor from both the active monitor events and monitors
	public function deleteMonitor($monitor_id) {
		try {
			$sql = "DELETE FROM monitor_event WHERE monitor_id = '$monitor_id'";
			$this->db->exec($sql);
			$sql = "DELETE FROM monitor WHERE id = '$monitor_id'";
			$this->db->exec($sql);
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}
	
	// Gets active monitors by the monitor id
	public function getMonitorEvents($monitor_id) {
		try {
			$stmt = $this->db->prepare("SELECT status, timestamp, notified
					FROM monitor_event
					WHERE monitor_id = '$monitor_id'");
			$stmt->execute();
			$active_monitors = $stmt->fetchAll();
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
		return $active_monitors;
	}
}

/*
$notified = false;
if ($httpcode == 200) {
	echo 'OK';
	// status is OK so turn off notification
	if ($notified) {
		$msg = "up";
		//mail("test.ping.email@gmail.com", "Back up", $msg);
		$notified = false;
	}
} else {
	echo 'X' . $httpcode;
	// notify owner
	if (!$notified) {
		$msg = "down";
		//mail("test.ping.email@gmail.com", "Down", $msg);
		$notified = true;
	}
}
*/



?> 
