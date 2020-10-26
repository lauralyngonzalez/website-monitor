
<?php
require_once 'simple_html_dom.php';

// monitor class for getting http status
class Monitor {
	protected $db;
	private $alertedOwner;

	function __construct($db) {
		$this->db = $db;
		$this->alertedOwner = false;
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
			$stmt = $this->db->prepare("SELECT * FROM monitor WHERE id = '$monitor_id'");
			$stmt->execute();
			$monitor_data = $stmt->fetch();
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
		return $monitor_data;
	}
	
	// creates a monitor
	public function createMonitor($monitor_type, $host, $name, $keyword, $keyword_opt) {
		try {
			$sql = "INSERT INTO monitor(monitor_type,url,name,keyword,keyword_option)
				VALUES(:monitor_type,:url,:name,:keyword,:keyword_option)";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':monitor_type', $monitor_type);
			$stmt->bindParam(':url', $host);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':keyword', $keyword);
			$stmt->bindParam(':keyword_option', $keyword_opt);
			$stmt->execute();
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}
	
	// update an existing monitor
	public function updateMonitor($monitor_id, $host, $name, $keyword, $keyword_opt) {
		try {
			$sql = "UPDATE monitor SET url=?, name=?, keyword=?, keyword_option=?
				WHERE id = $monitor_id ";
			$stmt = $this->db->prepare($sql);
			$stmt->execute([$host, $name, $keyword, $keyword_opt]);
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}
	
	// creates a monitor event
	public function createMonitorEvent($monitor_id, $http_status, $datetime) {
		try {
			$sql = "INSERT INTO monitor_event(monitor_id,status,timestamp)
					VALUES(:monitor_id,:status,:timestamp)";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':monitor_id', $monitor_id);
			$stmt->bindParam(':status', $http_status);
			$stmt->bindParam(':timestamp', $datetime);
			$stmt->execute();
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}
	
	// update an existing monitor
	public function updateMonitorEvent($monitor_id, $status, $datetime, $duration) {
		try {
			$sql = "UPDATE monitor_event SET status=?, timestamp=?, duration=?
				WHERE monitor_id = $monitor_id ";
			$stmt = $this->db->prepare($sql);
			$stmt->execute([$status, $datetime, $duration]);
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
	public function getMonitorEvent($monitor_id) {
		try {
			$stmt = $this->db->prepare("SELECT status, timestamp, duration, notified
					FROM monitor_event
					WHERE monitor_id = '$monitor_id'");
			$stmt->execute();
			$active_monitor = $stmt->fetch();
		} catch(PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
		return $active_monitor;
	}
	
	// Write to log file
	public function writeToLogFile($monitor_name, $status, $datetime, $duration, $url) {
		$filename = './logs/log_'.date("n.j.Y").'.csv';
		
		// include the header
		if (!file_exists($filename)) {
			$header = "Monitor,Status,Date-Time,Duration(in hrs),Monitor URL".PHP_EOL;
			file_put_contents($filename, $header, FILE_APPEND);
		}
		
		$log = $monitor_name.",".$status.",".$datetime.",".$duration.",".$url.PHP_EOL;
		file_put_contents($filename, $log, FILE_APPEND);
	}
	/*
	// Checks status and alerts owner if status is down
	public function checkStatus($monitor_id, $) {
		// TODO 
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
	}
	
	// Alerts owner if monitor status is down
	public function alertOwner($monitor_id) {
		//TODO
	}
	

	*/
}

?> 
