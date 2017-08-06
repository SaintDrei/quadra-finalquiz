<?php
	# displays total number of records from a chosen table
	function countData($table)
	{
		include 'config.php';
		$sql_count = "SELECT COUNT(*) AS total FROM $table
			WHERE Status!='Archived'";
		$result = $con->query($sql_count);

		$data = mysqli_fetch_assoc($result);
		return $data['total'];
	}

	# hides elements if customer is not logged in
	function toggleUser()
	{
		if (!isset($_SESSION['userid']))
		{
			echo 'style="display:none;"';
		}
	}

	# hides elements if customer is logged in
	function toggleGuest()
	{
		if (isset($_SESSION['userid']))
		{
			echo 'style="display:none;"';
		}
	}

	# gets path of application folder
	function getAppFolder()
	{
	    $protocol  = empty($_SERVER['HTTPS']) ? 'http' : 'https';
	    $port      = $_SERVER['SERVER_PORT'];
	    $disp_port = ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : ":$port";
	    $domain    = $_SERVER['SERVER_NAME'];

	    return "${protocol}://${domain}${disp_port}" . "/lquiz/";
	}

	# checks if user has logged in; redirects to login page if not logged in
	function validateAccess()
	{
		session_start();
		if (!isset($_SESSION['userid']))
		{
			$admin_login = getAppFolder() . 'admin/login.php';
			$lastURL = $_SERVER['REQUEST_URI'];
			header('location: ' . $admin_login .'?url=' . $lastURL);
		}
	}

	# sends a message to a chosen email address
	function sendEmail($email, $subject, $message)
	{
		require('phpmailer/PHPMailerAutoload.php');
		$mail = new PHPMailer;

		if(!$mail->validateAddress($email))
		{
			echo 'Invalid Email Address';
			exit;
		}

		$mail = new PHPMailer(); // create a new object
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465; // or 587
		$mail->IsHTML(true);
		$mail->Username = "benilde.web.development@gmail.com";
		$mail->Password = "Awebdevelopmentisfun";
		$mail->SetFrom("benilde.web.development@gmail.com");
		$mail->FromName = "The Administrator";
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->AddAddress($email);
		$mail->Send();
	}

	function getPrice($con, $pid)
	{
		$sql_price = " SELECT price from products
						WHERE productID=$pid";

		$result_price = $con->query($sql_price);
		while($row = mysqli_fetch_array($result_price))
		{
			$price = floatval($row['price']);
			
		}
		return $price;
	}
	function isExisting($con, $pid)
	{
		$sql_check = "SELECT detailID FROM orderdetails
			WHERE orderNo=0 AND userID=1 AND productID=$pid";
		$result_check = $con->query($sql_check) or die(mysqli_error($con));
		/*if(mysqli_num_rows($result_check) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}*/

		return mysqli_num_rows($result_check) > 0 ? true : false;
	}


	function addToCart($con, $pid, $qty)
	{
		if ( isset($_SESSION['userid']))
		{
			$uid = $_SESSION['userid'];
		}
		else
		{
			$uid =1;

		}
		$price = getPrice($con,$pid);
		$amount = intval($qty) * $price;
		$sql_insert = " INSERT INTO orderdetails
				VALUES('', 0, $uid, $pid, $qty, $price, $amount, NOW())";

		$sql_update = "UPDATE orderdetails
			SET quantity = quantity + $qty,
			amount = amount + $amount
			WHERE orderNo=0 AND userID=$uid AND
			productID=$pid";

		if (isExisting($con, $pid))
		{
			$result_update = $con->query($sql_update) or die(mysqli_error($con));
		}
		else
		{
		$result_insert = $con->query($sql_insert) or die(mysqli_error($con));
		}
	}

	function updateInventory($con, $pid, $qty)
	{
		$sql_update = "UPDATE products SET available = available - $qty, lastModified=NOW()
			WHERE productID = $pid";
			$con->query($sql_update) or die(mysqli_error($con));
	}

	function logMovement($con, $ref, $type, $pid, $qty)
	{
		$sql_log = "INSERT INTO logs VALUES('', $ref, '$type', $pid, $qty, NOW())";
		$con->query($sql_log) or die(mysqli_error($con));
	}

?>