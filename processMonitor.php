<?php
	require_once "config.php";
	
	$monitor_type = $_POST["monitor_type"];
	$host = htmlspecialchars($_POST["host"]);
	$name = htmlspecialchars($_POST["name"]);
	$keyword = htmlspecialchars($_POST["keyword"]);
	$keyword_option = $_POST["keyword_option"];
	
	// convert keyword_option for db
	if ($keyword_option == "exists") {
		$keyword_option_bool = 1;
	} else {
		$keyword_option_bool = 0;
	}
	
	try {
		// Keyword
		if ($monitor_type == 'keyword') {
			$sql = "INSERT INTO monitor(monitor_type,url,name,keyword,keyword_option)
				VALUES(:monitor_type,:url,:name,:keyword,:keyword_option)";
			$stmt = $pdo->prepare($sql);
			// Bind params to stmt
			$stmt->bindParam(':monitor_type', $monitor_type);
			$stmt->bindParam(':url', $host);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':keyword', $keyword);
			$stmt->bindParam(':keyword_option', $keyword_option_bool);
		} else { // HTTP
			$sql = "INSERT INTO monitor(monitor_type,url,name)
				VALUES(:monitor_type,:url,:name)";
			$stmt = $pdo->prepare($sql);
			// Bind params to stmt
			$stmt->bindParam(':monitor_type', $monitor_type);
			$stmt->bindParam(':url', $host);
			$stmt->bindParam(':name', $name);
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