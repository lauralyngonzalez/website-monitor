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
<title>Edit Monitor</title>
<style>
	form {
		width: 400px;
		/* Form outline */
		padding: 1em;
		border: 1px solid #CCC;
		border-radius: 1em;
	}

	ul {
		list-style: none;
		padding: 0;
		margin: 0;
	}

	form li + li {
		margin-top: 1em;
	}

	label {
		/* Uniform size & alignment */
		display: inline-block;
		width: 90px;
		text-align: right;
	}

	input, 
	textarea {
		/* To make sure that all text fields have the same font settings
		 By default, textareas have a monospace font */
		font: 1em sans-serif;

		/* Uniform text field size */
		width: 300px;
		box-sizing: border-box;

		/* Match form field borders */
		border: 1px solid #999;
	}

	input:focus, 
	textarea:focus {
		/* Additional highlight for focused elements */
		border-color: #000;
	}

	textarea {
		/* Align multiline text fields with their labels */
		vertical-align: top;

		/* Provide space to type some text */
		height: 5em;
	}

	.button {
		/* Align buttons with the text fields */
		padding-left: 90px; /* same size as the label elements */
	}

	button {
		/* This extra margin represent roughly the same space as the space
		 between the labels and their text fields */
		margin-left: .5em;
	}
</style>
</head>
<body>

	<div id="monitorForm">

	<form name="monitor_event" action="./processMonitor.php" method="post">
		<ul>
			<li>
				<?php echo $monitor_type; ?>
			</li>
			<li>
				<label for="host">URL:</label>
				<input type="text" id="host" name="host" value="<?php echo $url;?>"/>
			</li>
			<li>
				<label for="name">Name:</label>
				<input type="text" id="name" name="name" value="<?php echo $monitor_name;?>"/>
			</li>
			
			<?php if ($monitor_type == "keyword") : ?>
	
			<li>
				<label for="keyword">Keyword:</label>
				<input type="text" id="keyword" name="keyword" value="<?php echo $monitor_data['keyword']; ?>" />
			</li>
			<li>
				<label for="alert_keyword">Alert when:</label>
				<select id="keyword_option" name="keyword_option">
				
				<?php if ($monitor_data['keyword_option'] == 1) : ?>
					<option value="exists" selected>keyword exists</option>
					<option value="doesNotExist">keyword does not exist</option>
				<?php else : ?>
					<option value="exists">keyword exists</option>
					<option value="doesNotExist" selected>keyword does not exist</option>
				<?php endif; ?>
				
				</select>
			</li>

			<?php endif; ?>
			
			<li>
				<button type="submit" onclick="changeRequired(0, '<?php echo $monitor_type; ?>')"
				name="cancel">Cancel</button>
			</li>
			<li>
				<button type="submit" name="delete" onclick="changeRequired(0, '<?php echo $monitor_type; ?>')"
				value="<?php echo $monitor_id; ?>">Delete</button>
				<button type="submit" name="save" onclick="changeRequired(1, '<?php echo $monitor_type; ?>')"
				value="<?php echo $monitor_id; ?>, <?php echo $monitor_type; ?>">Save</button>
			</li>
		</ul>
	</form>
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