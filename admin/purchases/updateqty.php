
<?php 
    
	# checks if record is selected
	if (isset($_REQUEST['ref']))
	{
      
       
		# checks if selected record is an ID value
		if (ctype_digit($_REQUEST['ref']))
		{
                $qty = $_REQUEST['qty'];
            	$id = $_REQUEST['ref'];
            
			require($_SERVER['DOCUMENT_ROOT'] . '/lquiz/config.php');
			require($_SERVER['DOCUMENT_ROOT'] . '/lquiz/function.php');

            
			validateAccess();
			
			# archives existing record
           $sql_update = "UPDATE purchase_details SET quantity=$qty	WHERE refNo=$id";
				
				
			$result = $con->query($sql_update) or die(mysqli_error($con));
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