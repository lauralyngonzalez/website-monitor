<?php
	require_once "config.php";
	
	$monitor_type = $_POST["monitor_type"];
	$host = htmlspecialchars($_POST["host"]);
	$keyword = htmlspecialchars($_POST["keyword"]);
	$keyword_option = $_POST["keyword_option"];
	
	echo $keyword_option;
	if ($keyword_option == "exists") {
		$keyword_option_bool = 1;
	} else {
		$keyword_option_bool = 0;
	}
	
	try {
		
		// Keyword
		if ($monitor_type == 'keyword') {
			$sql = "INSERT INTO monitor(monitor_type,url,keyword,keyword_option)
				VALUES(:monitor_type,:url,:keyword,:keyword_option)";
			$stmt = $pdo->prepare($sql);
			// Bind params to stmt
			$stmt->bindParam(':monitor_type', $monitor_type);
			$stmt->bindParam(':url', $host);
			$stmt->bindParam(':keyword', $keyword);
			$stmt->bindParam(':keyword_option', $keyword_option_bool);
		} else { // HTTP
			$sql = "INSERT INTO monitor(monitor_type,url)
				VALUES(:monitor_type,:url)";
			$stmt = $pdo->prepare($sql);
			// Bind params to stmt
			$stmt->bindParam(':monitor_type', $monitor_type);
			$stmt->bindParam(':url', $host);
		}
		
		$stmt->execute();
		echo "Records inserted!";
	} catch(PDOException $e) {
		die("ERROR: Could not connect. " . $e->getMessage());
	}

	//close connection
	unset($pdo);
	
	header('Refresh: 1; url=index.php');
?>