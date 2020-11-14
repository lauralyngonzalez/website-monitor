<!DOCTYPE html>
<html>
<head>
	<title>Website Monitor</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>

<div class="header"><h1>Website Monitor</h1></div>

<div class="grid-container">
	
	<div class="flex-container">
		<?php
			include 'monitorList.php';
			include 'form.html';
		?>
	</div>

	<?php
		include 'monitorEvents.php';
	?>

</div>
</body>
</html>