<?php
	$page_title = "Checkout";
	include_once('../includes/header.php');

		$sql_cart = "SELECT od.DetailID, od.ProductID, p.Image,
			p.Name, c.Name AS Category, p.Price, od.Quantity,
			od.Amount FROM orderdetails od
			INNER JOIN products p ON od.productID = p.productID
			INNER JOIN categories c ON p.catID = c.CatID
			WHERE od.orderNo=0 AND od.userID=1";
	$result_cart = $con->query($sql_cart) or die(mysqli_error($con));
	if (mysqli_num_rows($result_cart) == 0)
	{
		header('location: ../products.php');
	}

	$list_cart = "";
	while ($row = mysqli_fetch_array($result_cart))
	{
		$did = $row['DetailID'];
		$pid = $row['ProductID'];
		$image = $row['Image'];
		$pname = $row['Name'];
		$cat = $row['Category'];
		$price = number_format($row['Price'], 2, '.', ',');
		$qty = $row['Quantity'];
		$amount = number_format($row['Amount'], 2, '.', ',');

		$list_cart .=  "<tr>
							<td><img src='../images/products/$image' width='150' /></td>
							<td><h3>$pname</h3>
								<small><em>$cat</em></small>
							</td>
							<td>P$price</td>
							<td>$qty<td/>
							<td>P$amount</td>
						</tr>";
	}

	$sql_compute = "SELECT SUM(amount) FROM orderdetails
		WHERE orderNo=0 AND userID=1";
	$result_compute = $con->query($sql_compute) or die(mysqli_error($con));
	while ($row2 = mysqli_fetch_array($result_compute))
	{
		$total = $row2[0];
		$gross = $total * .88;
		$VAT = $total * .12;
	}

	$userID = isset($_SESSION['userid']) ? SESSION['userid'] : 1;

	$sql_user = "SELECT firstName, lastName, email,
		street, municipality, cityID, landline,
		mobile FROM users WHERE userID=$userID";

	$result_user = $con->query($sql_user) or die(mysqli_error($con));
	while ($row4 = mysqli_fetch_array($result_user))
	{
		$firstName = $row4['firstName'];
		$lastName = $row4['lastName'];
		$emailAdd = $row4['email'];
		$street = $row4['street'];
		$municipality = $row4['municipality'];
		$cityID = $row4['cityID'];
		$landline = $row4['landline'];
		$mobile = $row4['mobile'];
	}

	$sql_cities = "SELECT cityID, name FROM cities
					WHERE regionID=1";
	$result_cities = $con->query($sql_cities);
	$list_cities = "";
	while ($row3 = mysqli_fetch_array($result_cities))
	{
		$cID = $row3['cityID'];
		$cityName = $row3['name'];
		$selected = $cID == $cityID ? "selected" : "";
		$list_cities .= "<option value='$cID' $selected>$cityName</option>";
	}

	if (isset($_POST['checkout']))
	{
		# Step 1: Update User Information
		$fn = mysqli_real_escape_string($con, $_POST['fn']);
		$ln = mysqli_real_escape_string($con, $_POST['ln']);
		$email = mysqli_real_escape_string($con, $_POST['email']);
		$st = mysqli_real_escape_string($con, $_POST['st']);
		$muni = mysqli_real_escape_string($con, $_POST['muni']);
		$city = mysqli_real_escape_string($con, $_POST['cities']);
		$phone = mysqli_real_escape_string($con, $_POST['phone']);
		$mobile = mysqli_real_escape_string($con, $_POST['mobile']);

		$sql_update = "UPDATE users SET firstName='$fn',
			lastName='$ln', email='$email',
			street='$st', municipality='$muni',
			cityID=$city, landline='$phone', mobile='$mobile',
			lastModified=NOW()
			WHERE userID=$userID";
		$con->query($sql_update) or die(mysqli_error($con));

		# Step 2: Insert Order Record then get last record
		$payment = $_POST['payment'];

		$sql_order = "INSERT INTO orders VALUES ('', '$payment',
			NOW(), NULL, 'Pending')";
		$con->query ($sql_order) or die(mysqli_error($con));

		$orderNo = $con->insert_id;

		# Step 3: Update Cart Items / Clear cart
		$sql_update_cart = "UPDATE orderDetails SET orderNo=$orderNo
			WHERE orderNo=0 AND userID=$userID";
		$con->query($sql_update_cart) or die(mysqli_error($con));

		# Step 4:
		$sql_delivery = "INSERT INTO deliveries VALUES ('', $orderNo,
			NOW() + INTERVAL 7 DAY, NULL, '', 'Pending')";
		$con->query($sql_delivery) or die(mysqli_error($con));


		# Step 5: Get Product + Order Details from last order

		$sql_order_items = "SELECT p.Name, p.Price, od.Quantity,
			od.Amount FROM orderdetails od
			INNER JOIN products p ON od.productID = p.productID
			WHERE od.orderNo=$orderNo";
		$result_order_items = $con->query($sql_order_items) or die(mysqli_error($con));
		$list_order_items = "";
		while ($row5 = mysqli_fetch_array($result_order_items))
		{
			$productName = $row5['Name'];
			$productPrice = number_format($row5['Price'], 2, '.', ',');
			$orderQty = $row5['Quantity'];
			$orderAmount = number_format($row5['Amount'], 2, '.', ',');

			$list_order_items .= 
				"<tr>
					<td>$productName</td>
					<td align='right'>$productPrice</td>
					<td align='right'>$orderQty</td>
					<td align='right'>$orderAmount</td>
				</tr>";
		}

		# Step 6: Send Email to Customer
		$subject = "Your Order #$orderNo Has Been Received";
		$currentDate = date(DATE_RFC2822);
		$totalPayment = number_format($total, 2, '.', ',');
		$url = getAppFolder() . 'account/orders/details.php?no=' . $orderNo;
		$message = "Dear $fn $ln,<br/><br/>
			We have received your order.<br/>
			Here are the details:<br/><br/>
			<strong>Order #:</strong> $orderNo<br/>
			<strong>Order Date:</strong> $currentDate<br/>
			<strong>Payment Method:</strong> $payment<br/>
			<strong>Address:</strong> $st, $muni</br><br/>
			<strong>Landline:</strong> $phone<br/>
			<strong>Mobile:</strong> $mobile<br/><br/>
			<table width='70%' border='1'>
				<thead>
					<th>Name</th>
					<th>Price</th>
					<th>Quantity</th>
					<th>Amount</th>
				</thead>
				<tbody>
					$list_order_items
					<tr>
						<td colspan='3' align='right'>
							<strong>Total Amount</strong>
						</td>
						<td align='right'>
							<strong>Php $totalPayment</strong>
						</td>
					</tr>
				</tbody>
			</table><br/><br/>
			Click here to view your order:<br/>
			<a href='$url'>$url</a><br/><br/>
			Thank you!";
		sendEmail($email, $subject, $message);
		header('location: orders');
	}
?>
	<form class="form-horizontal" method="POST">
	<div class="col-lg-8">
		
			<table class="table table-hover">
				<thead>
					<th colspan="2">Item</th>
					<th>Price</th>
					<th>Quantity</th>
					<th>Amount</th>
				</thead>
				<tbody>
					<?php echo $list_cart; ?>
				</tbody>
			</table>
			<hr/>
			<h2>Billing and Delivery Details</h2>
			<div class="col-lg-6">
				<div class="form-group">
					<label class="control-label col-lg-4">First Name</label>
					<div class="col-lg-8">
						<input name="fn" type="text" class="form-control" 
						value="<?php echo $firstName ?>" required />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-4">Last Name</label>
					<div class="col-lg-8">
						<input name="ln" type="text" class="form-control" 
						value="<?php echo $lastName ?>"required />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-4">Email Address</label>
					<div class="col-lg-8">
						<input name="email" type="email" class="form-control"
						value="<?php echo $emailAdd ?>" required />
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<label class="control-label col-lg-4">Street</label>
					<div class="col-lg-8">
						<input name="st" type="text" class="form-control" 
						value="<?php echo $street ?>" required />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-4">Municipality</label>
					<div class="col-lg-8">
						<input name="muni" type="text" class="form-control"
						value="<?php echo $municipality ?>" required />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-4">City</label>
					<div class="col-lg-8">
						<select name="cities" class="form-control" required>
							<option value="">Select one...</option>
							<?php echo $list_cities; ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-4">Landline</label>
					<div class="col-lg-8">
						<input name="phone" type="text" class="form-control"
						value="<?php echo $landline ?>" required />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-4">Mobile</label>
					<div class="col-lg-8">
						<input name="mobile" type="text" class="form-control"
						value="<?php echo $mobile ?>" required />
					</div>
				</div>
			</div>
	</div>
	<div class="col-lg-4">
		<div class="well">
			<h3 class="text-center">Order Summary</h3>
			<table class="table table-hover">
				<tr>
					<td>Gross Amount</td>
					<td align='right'>P<?php echo number_format($gross, 2, '.', ','); ?></td>
				</tr>
				<tr>
					<td>VAT</td>
					<td align='right'><?php echo number_format($VAT, 2, '.', ','); ?></td>
				</tr>
				<tr>
					<td><b>Total Amount</b></td>
					<td align='right'><b>P<?php echo number_format($total, 2, '.', ','); ?></b></td>
				</tr>
			</table>
			<hr/>
			<select name='payment' class='form-control'>
				<option value='Cash on Delivery'>Cash on Delivery</option>
				<option value='Bank Deposit'>Bank Deposit</option>
			</select>
			<br/>
			<label><input type='checkbox' required/> I have agreed to the terms and conditions.</label>
			<button name='checkout' class='btn btn-success btn-block btn-lg'
				onclick='return confirm("Submit order?");'>
				Checkout
			</button>
		</div>
	</div>
	</form>
	<div class='row'></div>

<?php
	include_once('../includes/footer.php');
?>