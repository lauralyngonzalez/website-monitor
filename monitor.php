
<?php
  
	$notified = false;
	  
	echo "<p>Hello!!</p>";
	$url = "http://161.35.237.130/";

	//$url = 'http://www.google.com';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
	curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT,10);
	$output = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	echo 'HTTP code: ' . $httpcode . '<br>';
	$date = date("M d, Y h:i:s A");

	if ($httpcode == 200) {
		echo 'OK';
		// status is OK so turn off notification
		if ($notified) {
			$msg = "up";
			mail("test.ping.email@gmail.com", "Back up", $msg);
			$notified = false;
		}
	} else {
		echo 'X' . $httpcode;
		// notify owner
		if (!$notified) {
			$msg = "down";
			mail("test.ping.email@gmail.com", "Down", $msg);
			$notified = true;
		}
	}

	echo $date;

	header("refresh: 30");

?> 
