
<?php
	require_once 'simple_html_dom.php';
  
	// monitor class for getting http status
	class monitor {

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

	header("refresh: 60");

?> 
