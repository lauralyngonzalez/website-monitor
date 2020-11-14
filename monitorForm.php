<?php 
	include "config.php";
	require_once "monitor.php";

	$monitor = new Monitor($db);

	$monitor_id = $_POST['id'];
	$monitor_data = $monitor->getMonitor($monitor_id);
	$monitor_type = $monitor_data['monitor_type'];
	$monitor_name = $monitor_data['name'];
	$url = $monitor_data['url'];

	$data = array($monitor_id, $monitor_type);
?>


<html>
<head>
	<title>Website Monitor</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="flex-container">
	<div id="monitorForm">
	<h3>Edit monitor:</h3>

	<form name="monitor_event" action="./processMonitor.php" method="post">
		
			<div class="row">
				<h4><?php echo $monitor_type; ?></h4>
			</div>
			<div class="row">
				<div class="col-25">
					<label for="host">URL:</label>
				</div>
				<div class="col-75">
					<input type="text" id="host" name="host" value="<?php echo $url;?>"/>
				</div>
			</div>
			<div class="row">
				<div class="col-25">
					<label for="name">Name:</label>
				</div>
				<div class="col-75">
					<input type="text" id="name" name="name" value="<?php echo $monitor_name;?>"/>
				</div>
			</div>
			
			<?php if ($monitor_type == "keyword") : ?>
	
			<div class="row">
				<div class="col-25">
					<label for="keyword">Keyword:</label>
				</div>
				<div class="col-75">
					<input type="text" id="keyword" name="keyword" value="<?php echo $monitor_data['keyword']; ?>" />
				</div>
			</div>
			<div class="row">
				<div class="col-25">
					<label for="alert_keyword">Alert when:</label>
				</div>
				<div class="col-75">
					<select id="keyword_option" name="keyword_option">
				
				<?php if ($monitor_data['keyword_option'] == 1) : ?>
					<option value="exists" selected>keyword exists</option>
					<option value="doesNotExist">keyword does not exist</option>
				<?php else : ?>
					<option value="exists">keyword exists</option>
					<option value="doesNotExist" selected>keyword does not exist</option>
				<?php endif; ?>
				
					</select>
				</div>
			</div>

			<?php endif; ?>
			
			<div class="row">
					<button type="submit" onclick="changeRequired(0, '<?php echo $monitor_type; ?>')"
				name="cancel">Cancel</button>
			</div>
			<div class="row">
				<button type="submit" name="delete" onclick="changeRequired(0, '<?php echo $monitor_type; ?>')"
				value="<?php echo $monitor_id; ?>">Delete</button>
				<button type="submit" name="save" onclick="changeRequired(1, '<?php echo $monitor_type; ?>')"
				value="<?php echo $monitor_id; ?>, <?php echo $monitor_type; ?>">Save</button>
			</div>
		
	</form>
	</div>
</div>
<script>
	// set required fields based on which buttons are pushed
	function changeRequired(req, monitor_type) {
		if (req == 1) {
			document.getElementById("host").setAttribute("required", "");
			document.getElementById("name").setAttribute("required", "");
			// keyword 
			if (monitor_type == "keyword") {
				document.getElementById("keyword").setAttribute("required", "");
			}
		} else {
			document.getElementById("host").removeAttribute("required");
			document.getElementById("name").removeAttribute("required");
			if (monitor_type == "keyword") {
				document.getElementById("keyword").removeAttribute("required");
			}
		}

	}
</script>

</body>
</html>