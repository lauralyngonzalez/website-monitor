<?php 
	include "config.php";
	require_once "monitor.php";

	$monitor = new Monitor($db);

	$monitor_id = $_POST['id'];
	$monitor_data = $monitor->getMonitor($monitor_id);
	$monitorType = $monitor_data['monitor_type'];
	$monitorName = $monitor_data['name'];
	$url = $monitor_data['url'];

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
<body onload="keywordSelected()">

	<div id="monitorForm">

	<form name="monitor_event" action="./processMonitor.php" onsubmit="return keywordSelected()" method="post">
		<ul>
			<li>
				<?php echo $monitorType; ?>
			</li>
			<li>
				<label for="host">URL:</label>
				<input type="text" id="host" name="host" value="<?php echo $url;?>" required/>
			</li>
			<li>
				<label for="name">Name:</label>
				<input type="text" id="name" name="name" value="<?php echo $monitorName;?>" required/>
			</li>
			
			<?php 
			
			if ($monitorType == "keyword") {
				echo '<li id="keywordText">' .
					'<label for="keyword">Keyword:</label>' .
					'<input type="text" id="keyword" name="keyword" value="';
				echo $monitor_data['keyword'];
				echo '" required/>' .
					'</li>';
				echo '<li id="keywordExistOptions">' .
					'<label for="alert_keyword">Alert when:</label>' .
					'<select id="keyword_option" name="keyword_option">' .
						'<option value="exists"';
				if ($monitor_data['keyword_option']	== 1) {
					echo "selected";
				}
				echo '>keyword exists</option>' .
						'<option value="doesNotExist"';
				if ($monitor_data['keyword_option']	== 0) {
					echo "selected";
				}	
				echo '>keyword does not exist</option>' .
					'</select>' .
					'</li>';
			}
			?>
			
			<li>
				<button type="submit" name="delete" value="<?php echo $monitor_id; ?>">Delete</button>
				<input type="submit" name="save" value="Save">
			</li>
		</ul>
	</form>
	</div>

</body>
</html>