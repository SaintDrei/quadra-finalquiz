<?php 

	# checks if record is selected
	if (isset($_REQUEST['id']))
	{
		# checks if selected record is an ID value
		if (ctype_digit($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
			require($_SERVER['DOCUMENT_ROOT'] . '/lquiz/config.php');
			require($_SERVER['DOCUMENT_ROOT'] . '/lquiz/function.php');

			validateAccess();
			
			# archives existing record
			$sql_delete = "UPDATE products SET status='Archived',
				lastModified=NOW()
				WHERE productID=$id";
				
			$result = $con->query($sql_delete) or die(mysqli_error($con));
			header('location: index.php');
		}
		else
		{
			header('location: index.php');
		}
	}
	else
	{
		header('location: index.php');
	}
?>