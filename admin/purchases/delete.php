
<?php 
    
	# checks if record is selected
	if (isset($_REQUEST['ref']))
	{
		# checks if selected record is an ID value
		if (ctype_digit($_REQUEST['ref']))
		{
			$ref = $_REQUEST['ref'];
			require($_SERVER['DOCUMENT_ROOT'] . '/lquiz/config.php');
			require($_SERVER['DOCUMENT_ROOT'] . '/lquiz/function.php');

			validateAccess();
			
			# archives existing record
            $sql_delete = "DELETE from purchase_details WHERE refNo = $ref";
				
			$result = $con->query($sql_delete) or die(mysqli_error($con));
			header('location: add.php');
		}
		else
		{
			header('location: add.php');
		}
	}
	else
	{
		header('location: add.php');
	}
?>